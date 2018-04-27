<?php

include '../../services/connection.php';
include '../../services/productService.php';
include '../../services/categoryService.php'; //for update sale - search products
include '../../services/imageService.php';

session_start();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$productService = new ProductService($conn);

switch ($requestMethod) {
    case 'GET':
        $function = $_GET["function"];
        switch ($function) {
            case 'getImages':
                $id = $_GET["id"];
                $responseData = $productService->getImages($id);
                break;
            case 'getProduct':
                $id = $_GET["id"];
                $responseData = $productService->get($id);
                break;
            case 'updateSaleAll':
                $condition = array();
                parse_str($_GET["p"], $condition);
                $condition["range"] = $_GET["range"];
                $condition["salePercentage"] = $_GET["salePercentage"];

                $products = array_map(function ($value) {
                    return $value->id;
                }, $productService->all($condition));

                $error = !$productService->updateSale(array(
                    "products" => $products,
                    "range" => $_GET["range"],
                    "salePercentage" => $_GET["salePercentage"]
                ));
                if (!$error)
                    $_SESSION["flashMessage"] = "Đã cập nhật thành công";
                else
                    $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";

                $queryStringArr = array();
                parse_str($_GET["p"], $queryStringArr);
                unset($queryStringArr["function"]);
                $queryString = http_build_query($queryStringArr);
                header("Location: ../../pages/admin/product/list.php?" . $queryString);
                break;
        }
        break;
    case 'POST':
        $function = $_POST["function"];
        switch ($function) {
            case 'updateSale':
                $error = !$productService->updateSale($_POST);
                $responseData = array(
                    "error" => $error
                );
                if (!$error)
                    $_SESSION["flashMessage"] = "Đã cập nhật thành công";
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
                if (!$error)
                    $_SESSION["flashMessage"] = "Đã cập nhật thành công";
                break;
        }
        break;
    case 'DELETE':
        break;
}
echo json_encode($responseData);
?>