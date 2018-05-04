<?php
//include '../viewModels/cartViewModel.php';
//include '../viewModels/orderInfoViewModel.php';

class OrderService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function add($data)
    {
        $cart = empty($_SESSION["cart"]) ? null : unserialize($_SESSION["cart"]);
        if (empty($cart))
            return;

        $name = $data["name"];
        $address = $data["address"];
        $mobile = $data["mobile"];
        $note = empty($data["note"]) ? '' : $data["note"];
        $createdDate = getdate()["year"] . "-" . getdate()["mon"] . "-" . getdate()["mday"];

        if (empty($name) || empty($address) || empty($mobile))
            return;

        //process code
        $lastOrderInDay = $this->last($createdDate);
        $code = $this->generateCode($lastOrderInDay);

        //insert order
        $query = $this->buildQuery(array(
            "code" => "'" . $code . "'",
            "customerName" => "N'" . $name . "'",
            "customerAddress" => "N'" . $address . "'",
            "customerMobile" => "'" . $mobile . "'",
            "note" => "N'" . $note . "'",
            "createdDate" => "'" . $createdDate . "'",
            "shippingMethod" => $cart->shippingMethod["id"],
            "payType" => $cart->payment["id"]
        ));

        $this->insert($query);

        //insert pivot table
        $pivotTableData = array();
        foreach ($cart->items as $item) {
            $pivotTableData[$item->id] = $item->quantity;
        }
        $query = $this->attach($code, $pivotTableData);
        $this->insert($query);

        //remove cart
        unset($_SESSION["cart"]);

        return new OrderInfoViewModel(array(
            "code" => $code,
            "items" => $cart->items,
            "subtotal" => $cart->getSubtotal(),
            "shippingMethod" => $cart->shippingMethod,
            "shippingStatus" => 1,
            "total" => $cart->getSubtotal() + $cart->shippingMethod["cost"]
        ));
    }

    public function all($page, $pageSize, $condition)
    {
        $dateRangeQuery = $this->buildDateRangeQuery($condition);
        $codeQuery = $this->buildCodeQuery($condition);
        $shippingStatusQuery = $this->buildShippingStatusQuery($condition);
        $customerNameQuery = $this->buildNameQuery($condition);
        $seenQuery = $this->buildSeenQuery($condition);
        $locationQuery = $this->buildLocationQuery($condition);

        $condition = "";
        if (!empty($dateRangeQuery) || !empty($codeQuery) || !empty($shippingStatusQuery) || !empty($customerNameQuery) || !empty($seenQuery) || !empty($locationQuery)) {
            $condition .= " where "
                . (empty($customerNameQuery) ? 'true' : $customerNameQuery)
                . " and "
                . (empty($codeQuery) ? 'true' : $codeQuery)
                . " and "
                . (empty($shippingStatusQuery) ? 'true' : $shippingStatusQuery)
                . " and "
                . (empty($dateRangeQuery) ? 'true' : $dateRangeQuery)
                . " and "
                . (empty($seenQuery) ? 'true' : $seenQuery)
                . " and "
                . (empty($locationQuery) ? 'true' : $locationQuery);
        } else
            $condition .= " where createdDate = '" .
                getdate()["year"] . "-" . getdate()["mon"] . "-" . getdate()["mday"]
                . "'";

        $sql = "select * from orders"
            . $condition
            . " limit " . $pageSize . " offset " . (($page - 1) * $pageSize);

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }

        //get count
        $query = "select count(*) as count from orders" . $condition;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $countResult = $stmt->fetch();

        return array(
            "orders" => $result,
            "count" => $countResult["count"]
        );

    }

    public function search($condition)
    {
        $dateRangeQuery = $this->buildDateRangeQuery($condition);
        $codeQuery = $this->buildCodeQuery($condition);
        $shippingStatusQuery = $this->buildShippingStatusQuery($condition);
        $customerNameQuery = $this->buildNameQuery($condition);
        $seenQuery = $this->buildSeenQuery($condition);
        $locationQuery = $this->buildLocationQuery($condition);

        $condition = "";
        if (!empty($dateRangeQuery) || !empty($codeQuery) || !empty($shippingStatusQuery) || !empty($customerNameQuery) || !empty($seenQuery) || !empty($locationQuery)) {
            $condition .= " where "
                . (empty($customerNameQuery) ? 'true' : $customerNameQuery)
                . " and "
                . (empty($codeQuery) ? 'true' : $codeQuery)
                . " and "
                . (empty($shippingStatusQuery) ? 'true' : $shippingStatusQuery)
                . " and "
                . (empty($dateRangeQuery) ? 'true' : $dateRangeQuery)
                . " and "
                . (empty($seenQuery) ? 'true' : $seenQuery)
                . " and "
                . (empty($locationQuery) ? 'true' : $locationQuery);
        } else
            $condition .= " where createdDate = '" .
                getdate()["year"] . "-" . getdate()["mon"] . "-" . getdate()["mday"]
                . "'";

        $sql = "select * from orders"
            . $condition;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }

        return $result;
    }

    public function last($createdDate)
    {
        $stmt = $this->db->prepare("select * from orders where createdDate = '" . $createdDate . "' order by code desc limit 1 offset 0");
        $stmt->execute();

        $result = $stmt->fetch();
        return $result;
    }

    public function get($code)
    {
        $stmt = $this->db->prepare("select * from orders where code = '" . $code . "'");
        $stmt->execute();

        $result = $stmt->fetch();
        return $result;
    }

    public function getWithProduct($code)
    {
        $productService = new ProductService($this->db);
        $shippingService = new ShippingMethodService($this->db);

        $order = $this->get($code);

        $productsQuery = "select order_details . productId, order_details . quantity from order_details where orderId = '" . $code . "'";
        $stmt = $this->db->prepare($productsQuery);
        $stmt->execute();

        $orderItems = array();
        while ($row = $stmt->fetch()) {
            $product = $productService->get($row["productId"]);
            $cartItem = new CartItemViewModel(
                $product->id,
                $product->images[0]["source"],
                $product->name,
                $product->basicPrice,
                $row["quantity"]
            );
            array_push($orderItems, $cartItem);
        }

        $orderInfo = new OrderInfoViewModel(array(
            "code" => $code,
            "items" => $orderItems,
            "shippingMethod" => $shippingService->get($order["shippingMethod"]),
            "shippingStatus" => $order["shippingStatus"]
        ));

        return $orderInfo;

    }

    public function buildQuery($columns)
    {
        $query = "insert into orders";
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

    public function insert($query)
    {
        $this->db->exec($query);
    }

    public function attach($order, $pivotData)
    {
        $query = "insert into order_details value ";
        foreach ($pivotData as $key => $value) {
            $query .= "('" . $order . "'," . $key . "," . $value . "),";
        }
        $query = substr(trim($query), 0, strlen($query) - 1);
        return $query;
    }

    public function update($id, $data)
    {
        $columns = $data["data"];

        $sql = "update orders set ";

        if (!empty($columns["shippingStatus"]))
            $sql .= "shippingStatus = " . $columns["shippingStatus"] . ",";
        if (!empty($columns["isSeen"])) {
            $sql .= "isSeen = " . $columns["isSeen"] . ",";
            $sql .= "seenAt = '" . getdate()["year"] . "-" . getdate()["mon"] . "-" . getdate()["mday"] . "',";
        }
        if (!empty($columns["customerName"]))
            $sql .= "customerName = '" . $columns["customerName"] . "',";
        if (!empty($columns["customerAddress"]))
            $sql .= "customerAddress = '" . $columns["customerAddress"] . "',";
        if (!empty($columns["customerMobile"]))
            $sql .= "customerMobile = '" . $columns["customerMobile"] . "',";
        if (!empty($columns["note"]))
            $sql .= "note = '" . $columns["note"] . "',";

        $sql = substr(trim($sql), 0, strlen($sql) - 1) . " where code = '" . $id . "'";
        $result = $this->db->exec($sql);
        return empty($result) ? false : true;
    }

    public function updateOrderItems($id, $data)
    {
        $error = false;
        $items = $data["data"];

        $deletedItems = array_map(function ($value) {
            return $value["id"];
        }, array_filter($items, function ($value) {
            return isset($value["deleted"]) && $value["deleted"] == "true";
        }));

        $updatedItems = array_filter($items, function ($value) {
            return isset($value["updated"]) && $value["updated"] == "true";
        });

        if (!empty($deletedItems)) {
            $sql = "delete from order_details where orderId = '" . $id . "' and productId in(";
            foreach ($deletedItems as $item) {
                $sql .= $item . ",";
            }
            $sql = substr(trim($sql), 0, strlen($sql) - 1) . ")";
            $result = $this->db->exec($sql);
            $error = empty($result) ? true : $error;
        }

        if (!empty($updatedItems)) {
            foreach ($updatedItems as $item) {
                $sql = "update order_details set quantity = " . $item["quantity"] . " where orderId = '" . $id . "' and productId = " . $item["id"];
                $result = $this->db->exec($sql);
                $error = empty($result) ? true : $error;
            }
        }

        return $error;
    }

    //helpers
    public function generateCode($lastOrderInDay)
    {
        if (empty($lastOrderInDay)) {
            $code = "ORD"
                . substr(getdate()["year"], 2, 2)
                . getdate()["mon"]
                . getdate()["mday"]
                . "00000";
            return $code;
        }

        //code of last order in current day
        $lastOrderCode = substr($lastOrderInDay["code"], 8);
        $newOrderNth = strval((intval($lastOrderCode) + 1));
        while (strlen($newOrderNth) < 5) {
            $newOrderNth = "0" . $newOrderNth;
        }

        $code = "ORD"
            . substr(getdate()["year"], 2, 2)
            . getdate()["mon"]
            . getdate()["mday"]
            . $newOrderNth;

        return $code;
    }

    //search query builders
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

    public function buildNameQuery($condition)
    {
        $searchName = isset($condition["customerName"]) ? $condition["customerName"] : null;
        if (empty($searchName))
            return '';

        //name
        $nameQuery = "";
        $nameParts = explode(" ", $searchName);
        if (count($nameParts) != 0) {
            foreach ($nameParts as $part) {
                $nameQuery .= "customerName like N'%" . $part . "%' and ";
            }
            $nameQuery = substr($nameQuery, 0, strlen($nameQuery) - 4);
        }

        return $nameQuery;
    }

    public function buildLocationQuery($condition)
    {
        $searchName = isset($condition["customerAddress"]) ? $condition["customerAddress"] : null;
        if (empty($searchName))
            return '';

        //name
        $locationQuery = "customerAddress like N'%" . trim($searchName) . "%'";

        return $locationQuery;
    }

    public function buildShippingStatusQuery($condition)
    {
        $searchShippingStatus = isset($condition["shippingStatus"]) ? $condition["shippingStatus"] : null;
        if (empty($searchShippingStatus))
            return '';

        $query = "shippingStatus = " . $searchShippingStatus;
        return $query;
    }

    public function buildCodeQuery($condition)
    {
        $searchCode = isset($condition["code"]) ? $condition["code"] : null;
        if (empty($searchCode))
            return '';

        $query = "code = '" . $searchCode . "'";
        return $query;
    }

    public function buildSeenQuery($condition)
    {
        $isSeen = isset($condition["isSeen"]) ? $condition["isSeen"] : null;
        if ($isSeen == null)
            return '';

        $query = "isSeen = " . $isSeen;
        return $query;
    }
}

?>