<?php

class ReceiptService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
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