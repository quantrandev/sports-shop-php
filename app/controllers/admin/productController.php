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
            case 'getImages':
                $responseData = $productService->getImages($id);
                break;
            case 'getProduct':
                $responseData = $productService->get($id);
                break;
        }
        break;
    case 'POST':
        $function = $_POST["function"];
        switch ($function) {
            case 'updateSale':
                $responseData = $productService->updateSale($_POST);
                break;
        }
        break;
    case 'PUT':
        $id = $_GET["id"];
        $function = $_GET["function"];
        $data = null;
        parse_str(file_get_contents("php://input"), $data);
        switch ($function) {
            case 'updateImages':
                $error = $productService->updateImages($id, $data["data"]);
                $responseData = array(
                    "error" => $error
                );
                break;
            case 'updateProduct':
                $error = !$productService->update($id, $data);
                $responseData = array(
                    "error" => $error
                );
                break;
        }
        break;
    case 'DELETE':
        break;
}
echo json_encode($responseData);
?>