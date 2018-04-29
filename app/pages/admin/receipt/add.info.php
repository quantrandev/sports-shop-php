<?php
session_start();
include '../../../services/connection.php';
include '../../../services/productService.php';
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

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $signatory = $_POST["signatory"];
    $note = $_POST["note"];

    if (empty($signatory))
        $_SESSION["errorMessage"] = "Vui lòng nhập đầy đủ thông tin";
    else {
        $_SESSION["receiptInfo"] = serialize(array(
            "signatory" => $signatory,
            "note" => $note
        ));

        header("Location: add.products.php");
    }
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
                        Thêm hóa đơn
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-9">
                    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-horizontal">
                            <input type="hidden" id="images" name="images">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Người nhập hàng *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="signatory" autofocus required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Ghi chú</label>
                                <div class="col-md-10"><textarea name="note" id="txtNote"
                                                                 class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label"></label>
                                <div class="col-md-10">
                                    <button class="btn btn-success btn-sm" type="submit" name="btnSubmit">
                                        <i class="fa fa-arrow-circle-right m-r-5"></i>
                                        Chọn sản phẩm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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





