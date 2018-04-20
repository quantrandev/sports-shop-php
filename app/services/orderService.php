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

    public function all($dateRange)
    {
        $sql = "select * from orders where createdDate >= '" . $dateRange["from"] . "' and createdDate <= '" . $dateRange["to"] . "'";
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

        $productsQuery = "select order_details.productId, order_details.quantity from order_details where orderId = '" . $code . "'";
        $stmt = $this->db->prepare($productsQuery);
        $stmt->execute();

        $orderItems = array();
        while ($row = $stmt->fetch()) {
            $product = $productService->get($row["productId"]);
            $cartItem = new CartItemViewModel(
                $product->id,
                $product->images[0]["source"],
                $product->name,
                $product->currentPrice,
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
        $insertedColumns = substr($insertedColumns, 0, strlen($insertedColumns) - 1) . ")";
        $insertedData = substr($insertedData, 0, strlen($insertedData) - 1) . ")";

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
        $query = substr($query, 0, strlen($query) - 1);
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

        $sql = substr($sql, 0, strlen($sql) - 1) . " where code = '" . $id . "'";
        $result = $this->db->exec($sql);
        return empty($result) ? false : true;
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
}

?>