<?php
session_start();

include '../../../services/connection.php';
include '../../../viewModels/userViewModel.php';

include '../../../services/roleService.php';
include '../../../services/userService.php';
include '../../../constants.php';
$userService = new UserService($conn);
$allRoles = $userService->getAllRoles();

$rolesFromClient = isset($_GET["role"]) ? $_GET["role"] : array();
$users = $userService->getUsers($rolesFromClient);

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
                        Danh sách người dùng
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Tên người dùng</th>
                            <th>Tên</th>
                            <th>Họ</th>
                            <th>Quyền</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user->userName; ?></td>
                                <td><?php echo $user->firstName; ?></td>
                                <td><?php echo $user->lastName; ?></td>
                                <td>
                                    <?php foreach ($user->roles as $role): ?>
                                        <span class="label label-info m-r-5"><?php echo $role["name"]; ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <button class="btn btn-xs btn-info">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                            Sửa
                                        </button>
                                        <button class="btn btn-xs btn-danger">
                                            <i class="ace-icon fa fa-trash bigger-120"></i>
                                            Xóa
                                        </button>
                                    </div>
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





