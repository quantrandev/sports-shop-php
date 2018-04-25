<?php

include '../services/connection.php';
include '../services/cartService.php';

session_start();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$cartService = new CartService($conn);
$productService = new ProductService($conn);

switch ($requestMethod) {
    case 'POST':
        $function = $_POST["function"];
        switch ($function) {
            case 'add':
                $responseData = $cartService->add($_POST);
                break;
            case 'setShippingMethod':
                $responseData = $cartService->setShippingMethod($_POST);
                break;
            case 'like':
                $responseData = $cartService->like($_POST["productId"]);
                break;
            case 'view':
                $responseData = $cartService->view($_POST["productId"]);
                break;
        }
        break;
    case 'PUT':
        $id = $_GET["id"];
        $data = null;
        parse_str(file_get_contents("php://input"), $data);
        $responseData = $cartService->update($id, $data);
        break;
    case 'DELETE':
        $id = $_GET["id"];
        $responseData = $cartService->delete($id);
        break;
}
echo json_encode($responseData);
?>