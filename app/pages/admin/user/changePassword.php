<?php
session_start();

include '../../../services/connection.php';

include '../../../services/userService.php';
include '../../../viewModels/userViewModel.php';
include '../../../constants.php';
$userService = new UserService($conn);
$allRoles = $userService->getAllRoles();

if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý người dùng'))
    header("Location: ../../authentication/login.php");

$editedUser = $userService->getUser($_GET["id"]);

$confirmPasswordFailErrorMessage = "";
$emptyErrorMessage = "";
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $invalid = false;

    $userName = $_POST["userName"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    if (empty($password) || empty($confirmPassword)) {
        $emptyErrorMessage = "Vui lòng nhập đầy đủ thông tin";
        $invalid = true;
    }

    if ($password != $confirmPassword) {
        $confirmPasswordFailErrorMessage = "Mật khẩu nhập lại không khớp";
        $invalid = true;
    }
    if (!$invalid) {
        $error = !$userService->update($userName, array(
            "userName" => $userName,
            "password" => $password
        ));

        if ($error)
            $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";
        else {
            $_SESSION["flashMessage"] = "Cập nhật thành công";
            if ($editedUser->userName == unserialize($_SESSION["user"])["userName"]) {
                $userService->logout();
                header("Location: ../../authentication/login.php");
            }
        }
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
                <li class="active">Quản lý người dùng</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý người dùng
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Đổi mật khẩu
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-10">
                    <form action="" class="form-horizontal" method="post">
                        <input type="hidden" name="userName" value="<?php echo $editedUser->userName; ?>">
                        <?php if (!empty($emptyErrorMessage)): ?>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label"></label>
                                <div class="col-md-4">
                                    <label class="text-danger control-label"><?php echo $emptyErrorMessage; ?></label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Mật khẩu mới *</label>
                            <div class="col-md-4">
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Xác nhận mật khẩu *</label>
                            <div class="col-md-4">
                                <input type="password" class="form-control" name="confirmPassword" required>
                            </div>
                            <div class="col-md-4">
                                <label class="text-danger control-label"><?php echo $confirmPasswordFailErrorMessage; ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label"></label>
                            <div class="col-md-4">
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





