<?php

class ShippingMethodService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all()
    {
        $stmt = $this->db->prepare("select * from shipping_methods");
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }
        return $result;
    }

    public function get($id)
    {
        $stmt = $this->db->prepare("select * from shipping_methods where id = " . $id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result;
    }
}

?>