<?php

include '../services/connection.php';
include '../viewModels/cartViewModel.php';
include '../viewModels/orderInfoViewModel.php';
include '../services/orderService.php';

session_start();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$orderService = new OrderService($conn);

switch ($requestMethod) {
    case 'POST':
        $data = $orderService->add($_POST);
        break;
    case 'PUT':
        break;
    case 'DELETE':
        break;
}
header("Location: ../pages/client/order/show.php?code=" . $data->code);
?>