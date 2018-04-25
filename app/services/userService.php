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

    public function getAllRoles()
    {
        $query = "select * from roles";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $roles = array();
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
        $userName = unserialize($_SESSION["user"])["userName"];
        $currentUserRoles = $this->getRoles($userName);

        if (in_array($role, array_map(function ($value) {
            return $value["name"];
        }, $currentUserRoles)))
            return true;

        return false;
    }

    public function isDuplicateUserName($userName)
    {
        $query = "select * from users where userName = '" . $userName . "'";
        $stmt = $this->db->prepare($query);

        try {
            $stmt->execute();
        } catch (Exception $e) {
        }

        $result = $stmt->fetch();

        if (empty($result))
            return false;

        return true;
    }

    //CRUD functions
    public function add($data)
    {
        $userName = $data["userName"];
        $password = $data["password"];
        $firstName = $data["firstName"];
        $lastName = $data["lastName"];

        $insertedData = array(
            "userName" => "'" . $userName . "'",
            "password" => "'" . $password . "'",
            "firstName" => "N'" . $firstName . "'",
            "lastName" => "N'" . $lastName . "'"
        );

        if ((strlen($userName) != strlen(utf8_decode($userName))) || (strlen($password) != strlen(utf8_decode($password))))
            return false;

        $query = $this->buildInsertQuery($insertedData);
        $stmt = $this->db->prepare($query);

        $result = $stmt->execute();

        return $result;
    }

    public function update($userName, $data)
    {
        $sql = "update users set ";

        if (!empty($data["firstName"]))
            $sql .= "firstName = N'" . $data["firstName"] . "',";
        if (!empty($data["lastName"]))
            $sql .= "lastName = N'" . $data["lastName"] . "',";
        if (!empty($data["password"]))
            $sql .= "password = '" . $data["password"] . "',";

        $sql = substr($sql, 0, strlen($sql) - 1) . " where userName = '" . $userName . "'";
        $result = $this->db->exec($sql);
        return true;
    }

    public function updateRoles($userName, $roles)
    {
        $error = false;
        $deleteQuery = "delete from user_roles where userId = '" . $userName . "'";
        $result = $this->db->exec($deleteQuery);
        if (empty($result))
            $error = true;

        $result = $this->attachRoles($userName, $roles);
        if (!$result)
            $error = true;

        return !$error;
    }

    public function attachRoles($userName, $roles)
    {
        if (empty($roles))
            return false;

        $query = "insert into user_roles (userId, roleId) values ";
        foreach ($roles as $role) {
            $query .= "('" . $userName . "', " . $role . "),";
        }
        $query = substr($query, 0, strlen($query) - 1);

        $result = $this->db->exec($query);

        return empty($result) ? false : true;
    }

    public function getUsers($roles)
    {
        $query = "";
        if (empty($roles))
            $query = "SELECT * FROM `users` users";
        else {
            $query = "SELECT * FROM `users` INNER JOIN user_roles where user_roles.roleId in (";

            foreach ($roles as $role) {
                $query .= $roles . ",";
            }
            $query = substr($query, 0, strlen($query) - 1) . ")";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $users = array();
        while ($row = $stmt->fetch()) {
            $user = new UserViewModel();
            $user->userName = $row["userName"];
            $user->firstName = $row["firstName"];
            $user->lastName = $row["lastName"];

            $user->roles = $this->getRoles($row["userName"]);

            array_push($users, $user);
        }
        return $users;
    }

    public function getUser($userName)
    {
        $query = "select * from users where userName = '" . $userName . "'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $data = $stmt->fetch();
        $user = new UserViewModel();
        $user->userName = $data["userName"];
        $user->firstName = $data["firstName"];
        $user->lastName = $data["lastName"];
        $user->roles = $this->getRoles($data["userName"]);

        return $user;
    }

    //helpers
    public function buildInsertQuery($columns)
    {
        $query = "insert into users";
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
}

?>