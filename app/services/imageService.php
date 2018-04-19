<?php

class ImageService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getMany($productId)
    {
        $stmt = $this->db
            ->prepare("select * from images "
                . " where productId = " . $productId);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }

        return $result;
    }
}