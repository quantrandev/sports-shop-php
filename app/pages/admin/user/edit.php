<?php
session_start();

include '../../../services/connection.php';
include '../../../constants.php';

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

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $invalid = false;

    $userName = $_POST["userName"];
    $roles = empty($_POST["role"]) ? array() : $_POST["role"];
    $firstName = empty($_POST["firstName"]) ? '' : $_POST["firstName"];
    $lastName = empty($_POST["lastName"]) ? '' : $_POST["lastName"];

    $error = !$userService->update($userName, array(
        "firstName" => $firstName,
        "lastName" => $lastName
    ));

    $updateRolesResult = $userService->updateRoles($userName, $roles);
    $error = $updateRolesResult ? $error : true;

    if ($error)
        $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";
    else{
        $_SESSION["flashMessage"] = "Cập nhật thành công";
        $editedUser = $userService->getUser($_GET["id"]);
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
                        Chỉnh sửa thông tin
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-8">
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
                            <label for="" class="col-md-2 control-label">Tên</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="firstName"
                                       value="<?php echo $editedUser->firstName; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Họ</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="lastName"
                                       value="<?php echo $editedUser->lastName; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Phân quyền</label>
                            <div class="col-md-4">
                                <select name="role[]" class="multiselect-roles" multiple>
                                    <?php foreach ($allRoles as $role): ?>
                                        <?php if (in_array($role["id"], array_map(function ($value) {
                                            return $value["id"];
                                        }, $editedUser->roles))): ?>
                                            <option value="<?php echo $role["id"] ?>"
                                                    selected><?php echo $role["name"] ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo $role["id"] ?>"><?php echo $role["name"] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label"></label>
                            <div class="col-md-4">
                                <button class="btn btn-success" type="submit">Cập nhật</button>
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





