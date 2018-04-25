<?php
session_start();

include '../../../services/connection.php';
include '../../../constants.php';

include '../../../services/userService.php';
include '../../../services/roleService.php';
include '../../../viewModels/userViewModel.php';
include '../../../constants.php';
$userService = new UserService($conn);
$allRoles = $userService->getAllRoles();

if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý quyền'))
    header("Location: ../../authentication/login.php");
if ($_GET["id"] == 5)
    header("Location: ../index.php");

$roleService = new RoleService($conn);
$role = $roleService->getRole($_GET["id"]);

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $error = false;
    $roleId = $_POST["id"];
    $isActive = isset($_POST["isActive"]) ? $_POST["isActive"] : null;
    if (empty($isActive))
        $error = !$roleService->deactivate($roleId);
    else
        $error = !$roleService->activate($roleId);

    if ($error)
        $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";
    else {
        $_SESSION["flashMessage"] = "Cập nhật thành công";
        $role = $roleService->getRole($_GET["id"]);
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
                <li class="active">Quản lý quyền</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý quyền
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Chỉnh sửa thông tin
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-8">
                    <form action="" class="form-horizontal" method="post">
                        <input type="hidden" name="id" value="<?php echo $role["id"]; ?>">
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Tên quyền</label>
                            <div class="col-md-5">
                                <div style="margin-top: 6px;">
                                    <label for="" class="label label-success"><?php echo $role["name"]; ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Trạng thái</label>
                            <div class="col-md-5">
                                <label style="margin-top: 6px">
                                    <input name="isActive" class="ace ace-switch" type="checkbox"
                                        <?php echo $role["isActive"] ? 'checked' : '' ?>/>
                                    <span class="lbl"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label"></label>
                            <div class="col-md-5">
                                <button class="btn btn-success" type="submit">Lưu thay đổi</button>
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





