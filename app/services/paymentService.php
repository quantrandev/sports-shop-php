<?php

class PaymentService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all()
    {
        $stmt = $this->db->prepare("select * from payments");
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }
        return $result;
    }
}

?>