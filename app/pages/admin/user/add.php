<?php
session_start();

include '../../../services/connection.php';
include '../../../services/orderService.php';
include '../../../constants.php';

include '../../../services/userService.php';
include '../../../constants.php';
$userService = new UserService($conn);
$allRoles = $userService->getAllRoles();

$confirmPasswordFailErrorMessage = "";
$duplicateUserNameErrorMessage = "";
$emptyErrorMessage = "";
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $invalid = false;

    $userName = $_POST["userName"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $roles = empty($_POST["role"]) ? array() : $_POST["role"];
    $firstName = empty($_POST["firstName"]) ? '' : $_POST["firstName"];
    $lastName = empty($_POST["lastName"]) ? '' : $_POST["lastName"];

    if (empty($userName) || empty($password) || empty($confirmPassword)) {
        $emptyErrorMessage = "Vui lòng nhập đầy đủ thông tin";
        $invalid = true;
    }

    if ($password != $confirmPassword) {
        $confirmPasswordFailErrorMessage = "Mật khẩu nhập lại không khớp";
        $invalid = true;
    }
    if ($userService->isDuplicateUserName($userName)) {
        $duplicateUserNameErrorMessage = "Tên tài khoản đã tồn tại";
        $invalid = true;
    }

    if (!$invalid) {
        $error = !$userService->add(array(
            "userName" => $userName,
            "password" => $password,
            "firstName" => $firstName,
            "lastName" => $lastName
        ));

        $attachRolesResult = $userService->attachRoles($userName, $roles);
        $error = $attachRolesResult ? $error : true;

        if ($error)
            $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";
        else
            $_SESSION["flashMessage"] = "Thêm thành công người dùng mới";
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
                        Thêm người dùng
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-10">
                    <form action="" class="form-horizontal" method="post">
                        <?php if (!empty($emptyErrorMessage)): ?>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label"></label>
                                <div class="col-md-4">
                                    <label class="text-danger control-label"><?php echo $emptyErrorMessage; ?></label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Tên đăng nhập *</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="userName" required>
                            </div>
                            <div class="col-md-4">
                                <label class="text-danger control-label"><?php echo $duplicateUserNameErrorMessage; ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Mật khẩu *</label>
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
                            <label for="" class="col-md-2 control-label">Phân quyền</label>
                            <div class="col-md-4">
                                <select name="role[]" class="multiselect-roles" multiple>
                                    <?php foreach ($allRoles as $roles): ?>
                                        <option value="<?php echo $roles["id"] ?>"><?php echo $roles["name"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Tên</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="firstName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Họ</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="lastName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label"></label>
                            <div class="col-md-4">
                                <button class="btn btn-success" type="submit">Thêm người dùng</button>
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





