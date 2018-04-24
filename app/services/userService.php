<?php

class UserService
{
    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function login($userName, $password)
    {
        $query = "select * from users where userName = '" . $userName . "' and password = '" . $password . "'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch();

        if (empty($result))
            return false;

        $_SESSION["user"] = serialize(array(
            "userName" => $result["userName"],
            "firstName" => $result["firstName"],
            "lastName" => $result["lastName"]
        ));
        return true;
    }

    public function logout()
    {
        unset($_SESSION["user"]);
    }

    public function getRoles($userName)
    {
        $query = "SELECT roles.id, roles.name FROM `roles` INNER join user_roles on roles.id = user_roles.roleId
inner join users on user_roles.userId = users.userName where userName = '" . $userName . "'";

        $roles = array();
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            array_push($roles, $row);
        }

        return $roles;
    }

    public function isAuthenticate()
    {
        if (isset($_SESSION["user"]))
            return true;
        return false;
    }

    public function isAuthorize($role)
    {
        $userName = $_SESSION["user"];
        $currentUserRoles = $this->getRoles($userName);

        if (in_array($role, $currentUserRoles))
            return true;

        return false;
    }
}

?>