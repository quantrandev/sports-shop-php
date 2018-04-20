<?php

include '../../services/connection.php';
include '../../viewModels/cartViewModel.php';
include '../../viewModels/orderInfoViewModel.php';
include '../../services/orderService.php';

session_start();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$orderService = new OrderService($conn);

switch ($requestMethod) {
    case 'POST':
        break;
    case 'PUT':
        $id = $_GET["id"];
        $data = null;
        parse_str(file_get_contents("php://input"), $data);
        $function = $data["function"];
        switch ($function) {
            case 'changeShippingStatus':
                $error = !$orderService->update($id, $data);
                $responseData = array(
                    "error" => $error
                );
                break;
            case 'changeSeenStatus':
                $error = !$orderService->update($id, $data);
                $responseData = array(
                    "error" => $error,
                    "seenAt" => date("d-m-Y")
                );
                break;
        }
        break;
    case 'DELETE':
        break;
}
echo json_encode($responseData);
?>