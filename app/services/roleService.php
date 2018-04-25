<?php

class RoleService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //CRUD functions
    public function getRole($id)
    {
        $query = "select * from roles where id = " . $id;
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $role = $stmt->fetch();
        return $role;
    }

    public function activate($id)
    {
        $query = "update roles set isActive = 1 where id = " . $id;
        $result = $this->db->exec($query);

        return empty($result) ? false : true;
    }

    public function deactivate($id)
    {
        $query = "update roles set isActive = 0 where id = " . $id;
        $result = $this->db->exec($query);

        return empty($result) ? false : true;
    }
}

?>