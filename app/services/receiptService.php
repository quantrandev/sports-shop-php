<?php

class ReceiptService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all($page, $pageSize, $queryString)
    {
        $dateRangeQuery = $this->buildDateRangeQuery($queryString);

        $condition = "";
        if (!empty($dateRangeQuery)) {
            $condition .= " where "
                . (empty($dateRangeQuery) ? 'true' : $dateRangeQuery);
        } else
            $condition .= " where createdDate = '" .
                getdate()["year"] . "-" . getdate()["mon"] . "-" . getdate()["mday"]
                . "'";

        $sql = "select * from receipts"
            . $condition;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $receipts = array();

        while ($row = $stmt->fetch()) {
            array_push($receipts, $row);
        }

        //get count
        $query = "select count(*) as count from receipts" . $condition;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $countResult = $stmt->fetch();

        return array(
            "receipts" => $receipts,
            "count" => intval($countResult["count"])
        );
    }

    public function get($code)
    {
        $query = "select * from receipts where code = '" . $code . "'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result;
    }

    public function getWithProduct($code)
    {
        $productService = new ProductService($this->db);

        $query = "select * from receipt_details where receiptCode = '" . $code . "'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $receipt = array(
            "code" => $code
        );
        $receiptItems = array();
        while ($row = $stmt->fetch()) {
            $product = $productService->get($row["productId"]);
            $receiptItem = array(
                "id" => $product->id,
                "image" => $product->images[0]["source"],
                "name" => $product->name,
                "price" => $row["price"],
                "quantity" => $row["quantity"]
            );

            array_push($receiptItems, $receiptItem);
        }
        $receipt["items"] = $receiptItems;
        return $receipt;
    }

    public function insert($receipt)
    {
        $info = $receipt["info"];
        $items = $receipt["items"];

        $createdDate = getdate()["year"] . "-" . getdate()["mon"] . "-" . getdate()["mday"];
        $lastReceiptInDay = $this->last($createdDate);
        $code = $this->generateCode($lastReceiptInDay);
        $signatory = $info["signatory"];
        $note = $info["note"];

        $query = $this->buildQuery(array(
            "code" => "'" . $code . "'",
            "signatory" => "N'" . $signatory . "'",
            "note" => "N'" . $note . "'",
            "createdDate" => "'" . $createdDate . "'"
        ));
        $insertResult = $this->db->exec($query);
        $error = empty($insertResult) ? true : false;

        $query = $this->attach($code, $items);
        $attachResult = $this->db->exec($query);
        $error = empty($attachResult) ? true : $error;

        return $error;
    }

    public function attach($receiptCode, $items)
    {
        $query = "insert into receipt_details value ";
        foreach ($items as $item) {
            $query .= "('" . $receiptCode . "', " . $item["id"] . ", " . $item["price"] . "," . $item["quantity"] . "),";
        }
        $query = substr(trim($query), 0, strlen($query) - 1);
        return $query;
    }

    public function update($code, $columns)
    {

        $sql = "update receipts set ";

        if (!empty($columns["signatory"]))
            $sql .= "signatory = N'" . $columns["signatory"] . "',";
        if (!empty($columns["note"]))
            $sql .= "note = '" . $columns["note"] . "',";

        $sql = substr(trim($sql), 0, strlen($sql) - 1) . " where code = '" . $code . "'";
        $result = $this->db->exec($sql);
        return empty($result) ? false : true;
    }

    //get
    public function last($createdDate)
    {
        $stmt = $this->db->prepare("select * from receipts where createdDate = '" . $createdDate . "' order by code desc limit 1 offset 0");
        $stmt->execute();

        $result = $stmt->fetch();
        return $result;
    }

    //helpers
    public function generateCode($lastReceiptInDay)
    {
        if (empty($lastReceiptInDay)) {
            $code = "REC"
                . substr(getdate()["year"], 2, 2)
                . getdate()["mon"]
                . getdate()["mday"]
                . "00000";
            return $code;
        }

        //code of last order in current day
        $lastReceiptCode = substr($lastReceiptInDay["code"], 8);
        $newReceiptNth = strval((intval($lastReceiptCode) + 1));
        while (strlen($newReceiptNth) < 5) {
            $newReceiptNth = "0" . $newReceiptNth;
        }

        $code = "REC"
            . substr(getdate()["year"], 2, 2)
            . getdate()["mon"]
            . getdate()["mday"]
            . $newReceiptNth;

        return $code;
    }

    public function buildDateRangeQuery($condition)
    {
        $rangeQueryString = isset($condition["range"]) ? $condition["range"] : null;
        if (empty($rangeQueryString))
            return '';

        $range = explode("-", $rangeQueryString);

        $from = getdate(strtotime($range[0]));
        $to = getdate(strtotime($range[1]));

        $dateRange = array(
            "from" => $from["year"] . "-" . $from["mon"] . "-" . $from["mday"],
            "to" => $to["year"] . "-" . $to["mon"] . "-" . $to["mday"]
        );

        $query = "createdDate >= '" . $dateRange["from"] . "' and createdDate <= '" . $dateRange["to"] . "'";
        return $query;
    }

    public function buildQuery($columns)
    {
        $query = "insert into receipts";
        $insertedColumns = "(";
        $insertedData = "(";
        foreach ($columns as $key => $value) {
            $insertedColumns .= $key . ",";
            $insertedData .= $value . ",";
        }
        $insertedColumns = substr(trim($insertedColumns), 0, strlen($insertedColumns) - 1) . ")";
        $insertedData = substr(trim($insertedData), 0, strlen($insertedData) - 1) . ")";

        $query .= " " . $insertedColumns . " values " . $insertedData;

        return $query;
    }

}

?>