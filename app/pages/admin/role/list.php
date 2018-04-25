<?php
session_start();

include '../../../services/connection.php';
include '../../../viewModels/userViewModel.php';

include '../../../services/roleService.php';
include '../../../services/userService.php';
include '../../../constants.php';
$userService = new UserService($conn);

if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý quyền'))
    header("Location: ../../authentication/login.php");

$allRoles = $userService->getAllRoles();

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
                        Danh sách quyền
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Quyền</th>
                            <th>Kích hoạt</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($allRoles as $role): ?>
                            <tr>
                                <td><?php echo $role["name"] ?></td>
                                <td><?php echo $role["isActive"] ? '<span class="label label-success">Kích hoạt</span>' : '<span class="label label-danger">Khóa</span>' ?></td>
                                <td class="text-center">
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <a href="/sports-shop-final/app/pages/admin/role/edit.php?id=<?php echo $role["id"]; ?>"
                                           class="btn btn-xs btn-info">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                            Sửa
                                        </a>
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





