<?php
session_start();

include '../../../services/connection.php';
include '../../../services/userService.php';
include '../../../services/adService.php';
include '../../../constants.php';
$userService = new UserService($conn);
$adService = new AdService($conn);

if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý quyền'))
    header("Location: ../../authentication/login.php");

$allRoles = $userService->getAllRoles();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    //upload
    $target_dir = "../../../../assets/images/ads/";
    if (move_uploaded_file($_FILES["content"]["tmp_name"], $target_dir . $_FILES['content']['name'])) {
        $status = 1;
    } else {
        $status = 0;
    }

    //insert
    $adService->add("/images/ads/" . $_FILES['content']['name']);

    if (empty($status))
        $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";
    else
        $_SESSION["flashMessage"] = "Thêm thành công quảng cáo";
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
                <li class="active">Quản lý quảng cáo</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý quảng cáo
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Danh sách quảng cáo
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-5">
                    <form action="" class="form-horizontal" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Ảnh</label>
                            <div class="col-md-9">
                                <input type="file" name="content" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <button class="btn btn-success" type="submit">Thêm mới</button>
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





