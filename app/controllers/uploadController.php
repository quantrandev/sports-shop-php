<?php

$type = $_GET["type"];

$target_dir = "../../assets/images/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);

switch ($type) {
    case 'product':
        $target_dir .= "products/new/";
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $_FILES['file']['name'])) {
            $status = 1;
        }
        break;
}
?>