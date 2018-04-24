<?php

session_start();
include '../../services/connection.php';
include '../../services/userService.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
$userService = new UserService($conn);

switch ($requestMethod) {
    case 'POST':
        $function = $_POST["function"];
        if ($function == 'logout') {
            $userService->logout();
            header("Location: ../../pages/authentication/login.php");
        }
        break;
}

?>