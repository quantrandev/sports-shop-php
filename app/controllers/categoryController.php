<?php

include '../services/connection.php';
include '../services/categoryService.php';

session_start();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$categoryService = new CategoryService($conn);

switch ($requestMethod) {
    case 'GET':
        $category = $categoryService->get($_GET["id"]);
        $responseData = array(
            "id" => $category["id"],
            "name" => $category["name"],
            "isActive" => $category["isActive"] == 0 ? false : true,
            "parentId" => $category["parentId"],
            "categories" => $categoryService->allIncludedInactive()
        );
        break;
    case 'POST':
        $error = !$categoryService->add($_POST);
        if (!$error)
            $_SESSION["flashMessage"] = "Đã thêm thành công";
        $responseData = array(
            "error" => $error
        );
        break;
    case 'PUT':
        $id = $_GET["id"];
        $data = null;
        parse_str(file_get_contents("php://input"), $data);
        $error = !$categoryService->update($id, $data);

        if (!$error)
            $_SESSION["flashMessage"] = "Đã cập nhật thành công";

        $responseData = array(
            "error" => $error
        );
        break;
    case 'DELETE':
        $id = $_GET["id"];
        $responseData = $cartService->delete($id);
        break;
}
echo json_encode($responseData);
?>