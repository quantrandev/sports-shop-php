<?php
session_start();
include '../../../services/connection.php';
include '../../../services/productService.php';
include '../../../services/imageService.php';
include '../../../services/categoryService.php';
include '../../../constants.php';
$categoryService = new CategoryService($conn);
$menus = $categoryService::menus($categoryService->allIncludedInactive());

include '../../../services/userService.php';
$userService = new UserService($conn);

if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý sản phẩm'))
    header("Location: ../../authentication/login.php");

include '../../../services/receiptService.php';
$receiptService = new ReceiptService($conn);

$receiptCode = isset($_GET["code"]) ? $_GET["code"] : null;
if (!empty($receiptCode)) {
    $receipt = $receiptService->getWithProduct($receiptCode);
}

include '../templates/head.php';
include '../templates/navigation.php';
include '../templates/sidebar.php';

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Trang quản trị</a>
                </li>
                <li class="active">Quản lý nhập hàng</li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý nhập hàng
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Chỉnh sửa hóa đơn
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="receipt-product-table">
                        <thead>
                        <tr>
                            <th style="width: 80px"></th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá nhập (đ)</th>
                            <th>Công cụ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($receipt["items"] as $item): ?>
                            <tr>
                                <td><img src="/sports-shop-final/assets<?php echo $item["image"];?>" alt="" style="max-width: 100%"></td>
                                <td><?php echo $item["name"]?></td>
                                <td><input type="number" class="form-control js-qty" value="<?php echo $item["quantity"]?>"></td>
                                <td><input type="number" class="form-control js-price" value="<?php echo $item["price"]?>"></td>
                                <td>
                                    <button class="btn btn primary btn-sm hide">
                                        <i class="fa fa-pencil m-r-5"></i>
                                        Lưu
                                    </button>
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash m-r-5"></i>
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<?php
include '../templates/footer.php';
?>

<script>
    let editor = CKEDITOR.replace('txtNote');
    CKFinder.setupCKEditor(editor);

    CKEDITOR.instances['txtNote'].setData(`<?php echo $receipt["note"];?>`);
</script>

<?php if (isset($_SESSION["flashMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["flashMessage"];?>', "gritter-success");
    </script>
    <?php unset($_SESSION["flashMessage"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["errorMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["errorMessage"];?>', "gritter-error");
    </script>
    <?php unset($_SESSION["errorMessage"]); ?>
<?php endif; ?>

<?php
include '../templates/bottom.php';
?>





