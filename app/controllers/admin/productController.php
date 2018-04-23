<?php

include '../../services/connection.php';
include '../../services/productService.php';
include '../../services/imageService.php';

session_start();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$productService = new ProductService($conn);

switch ($requestMethod) {
    case 'GET':
        $id = $_GET["id"];
        $function = $_GET["function"];
        switch ($function) {

        }
        break;
    case 'POST':
        break;
    case 'PUT':
        $id = $_GET["id"];
        $data = null;
        parse_str(file_get_contents("php://input"), $data);
        $function = $data["function"];
        switch ($function) {

        }
        break;
    case 'DELETE':
        break;
}
echo json_encode($responseData);
?>