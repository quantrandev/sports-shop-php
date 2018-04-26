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
$ads = $adService->getAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adId = $_POST["ad"];

    $adService->activate($adId);

    $ads = $adService->getAll();
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
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Nội dung</th>
                            <th>Kích hoạt</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ads as $ad): ?>
                            <tr>
                                <td style="width: 60px">
                                    <img src="/sports-shop-final/assets<?php echo $ad["content"]; ?>" alt=""
                                         style="max-width: 100%;">
                                </td>
                                <td><?php echo $ad["isActive"] ? '<span class="label label-success">Kích hoạt</span>' : '<span class="label label-danger">Khóa</span>' ?></td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="ad" value="<?php echo $ad["id"]; ?>">
                                        <div class="hidden-sm hidden-xs btn-group">
                                            <a href="/sports-shop-final/assets<?php echo $ad["content"]; ?>"
                                               class="btn btn-xs btn-default quick-view">
                                                <i class="ace-icon fa fa-search bigger-120"></i>
                                                Phóng to
                                            </a>
                                            <button type="submit" class="btn btn-xs btn-primary">
                                                <i class="ace-icon fa fa-bolt bigger-120"></i>
                                                Kích hoạt
                                            </button>
                                        </div>
                                    </form>
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





