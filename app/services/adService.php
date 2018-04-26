<?php

class AdService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get()
    {
        $stmt = $this->db
            ->prepare("select * from ads where isActive = 1");
        $stmt->execute();

        $result = $stmt->fetch();
        return $result;
    }

    public function getAll()
    {
        $stmt = $this->db
            ->prepare("select * from ads");
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            array_push($result, $row);
        }
        return $result;
    }

    public function add($content)
    {
        $this->deactivateAll();

        $query = "insert into ads (content, isActive) values ('" . $content . "', 1)";
        $this->db->exec($query);
    }

    public function deactivateAll()
    {
        $query = "update ads set isActive = 0";
        $this->db->exec($query);
    }

    public function activate($id)
    {
        $this->deactivateAll();

        $query = "update ads set isActive = 1 where id = " . $id;
        $this->db->exec($query);
    }

}

?>