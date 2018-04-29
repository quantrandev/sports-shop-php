<?php
session_start();

include '../../../services/connection.php';

include '../../../services/userService.php';
$userService = new UserService($conn);
if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý sản phẩm'))
    header("Location: ../../authentication/login.php");

include '../../../services/productService.php';
include '../../../services/categoryService.php';
include '../../../services/imageService.php';
include '../../../constants.php';
$searchCategories = empty($_GET["category"]) ? array() : $_GET["category"];
$categoryService = new CategoryService($conn);
$menus = $categoryService::menus($categoryService->allIncludedInactive());

include '../../../services/receiptService.php';
$receiptService = new ReceiptService($conn);
$result = $receiptService->all(isset($_GET["page"]) ? $_GET["page"] : 1, 10, $_GET);
$receipts = $result["receipts"];
$count = $result["count"];
$dateRange = isset($_GET["range"]) ? $_GET["range"] : '';

$page = empty($_GET["page"]) ? 1 : $_GET["page"];
$queryStringArr = array();
parse_str($_SERVER["QUERY_STRING"], $queryStringArr);
unset($queryStringArr["page"]);
$queryString = http_build_query($queryStringArr);

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
                        Danh sách nhập hàng
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-8 p-0">
                        <form action="">
                            <div class="col-md-4 p-0">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>
                                        <input class="form-control" type="text" name="range"
                                               id="date-range-picker"
                                               value="<?php echo $dateRange; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div>
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-search m-r-5"></i>
                                            Tìm kiếm
                                        </button>
                                        <button class="btn btn-danger btn-sm js-batch-delete hide">
                                            <i class="fa fa-trash m-r-5"></i>
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 p-0">
                        <div class="form-group pull-right">
                            <ul class="pages">
                                <li><span class="text-uppercase">Page:</span></li>
                                <li class="<?php if ($page == 1) echo 'hide'; ?>">
                                    <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . ($page - 1); ?>">
                                        <i class="fa fa-caret-left"></i>
                                    </a>
                                </li>
                                <?php if (ceil($count / 12) < 20): ?>
                                    <?php for ($i = 1; $i <= ceil($count / 12); $i++): ?>
                                        <?php if ($page == $i): ?>
                                            <li class="active"><?php echo $i; ?></li>
                                        <?php else: ?>
                                            <li>
                                                <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . $i; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($page == $i): ?>
                                            <li class="active"><?php echo $i; ?></li>
                                        <?php else: ?>
                                            <li>
                                                <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . $i; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <li class="active">...</li>
                                    <?php for ($i = 6; $i <= ceil($count / 12) - 5; $i++): ?>
                                        <?php if ($page == $i): ?>
                                            <li class="active"><?php echo $i; ?></li>
                                            <li class="active">...</li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <?php for ($i = ceil($count / 12) - 4; $i <= ceil($count / 12); $i++): ?>
                                        <?php if ($page == $i): ?>
                                            <li class="active"><?php echo $i; ?></li>
                                        <?php else: ?>
                                            <li>
                                                <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . $i; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                <?php endif; ?>
                                <li class="<?php if ($page == ceil($count / 12)) echo 'hide'; ?>">
                                    <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . ($page + 1); ?>">
                                        <i class="fa fa-caret-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="receipts-table">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">
                                <?php if (count($receipts) > 0): ?>
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-all"/>
                                        <span class="lbl"></span>
                                    </label>
                                <?php endif; ?>
                            </th>
                            <th>Người nhập hàng</th>
                            <th>Ngày nhập hàng</th>
                            <th>Công cụ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td class="text-center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-item"/>
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td class="p-name"><?php echo $receipt["signatory"]; ?></td>
                                <td class="p-old-price"><?php echo date('d-m-Y', strtotime($receipt["createdDate"])); ?></td>
                                <td class="text-center">
                                    <div class="dropdown" style="display: inline-block;">
                                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button"
                                                data-toggle="dropdown">
                                            <i class="fa fa-pencil-square m-r-5"></i>
                                            Chỉnh sửa
                                            <i class="fa fa-caret-down m-l-5"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="edit.products.php?code=<?php echo $receipt["code"]; ?>">
                                                    <i class="fa fa-soccer-ball-o m-r-5"></i>
                                                    Sản phẩm
                                                </a></li>
                                            <li><a href="edit.info.php?code=<?php echo $receipt["code"]; ?>">
                                                    <i class="fa fa-bars m-r-5"></i>
                                                    Thông tin khác
                                                </a></li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-xs btn-danger js-delete-receipt">
                                        <i class="ace-icon fa fa-trash bigger-120"></i>
                                        Xóa
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="pull-right">
                        <ul class="pages">
                            <li><span class="text-uppercase">Page:</span></li>
                            <li class="<?php if ($page == 1) echo 'hide'; ?>">
                                <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . ($page - 1); ?>">
                                    <i class="fa fa-caret-left"></i>
                                </a>
                            </li>
                            <?php if (ceil($count / 12) < 20): ?>
                                <?php for ($i = 1; $i <= ceil($count / 12); $i++): ?>
                                    <?php if ($page == $i): ?>
                                        <li class="active"><?php echo $i; ?></li>
                                    <?php else: ?>
                                        <li>
                                            <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php else: ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($page == $i): ?>
                                        <li class="active"><?php echo $i; ?></li>
                                    <?php else: ?>
                                        <li>
                                            <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <li class="active">...</li>
                                <?php for ($i = 6; $i <= ceil($count / 12) - 5; $i++): ?>
                                    <?php if ($page == $i): ?>
                                        <li class="active"><?php echo $i; ?></li>
                                        <li class="active">...</li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?php for ($i = ceil($count / 12) - 4; $i <= ceil($count / 12); $i++): ?>
                                    <?php if ($page == $i): ?>
                                        <li class="active"><?php echo $i; ?></li>
                                    <?php else: ?>
                                        <li>
                                            <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php endif; ?>
                            <li class="<?php if ($page == ceil($count / 12)) echo 'hide'; ?>">
                                <a href="<?php echo $_SERVER["PHP_SELF"] . "?" . $queryString . "&page=" . ($page + 1); ?>">
                                    <i class="fa fa-caret-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<?php
include '../templates/footer.php';
?>

<script>
    $('.js-delete-receipt').confirmation({
        rootSelector: '.js-delete-receipt',
        title: 'Xóa hóa đơn này',
        singleton: true,
        popout: true,
        onConfirm: function () {

        }
    });
    $('.js-batch-delete').confirmation({
        rootSelector: '.js-batch-delete',
        title: 'Xóa những hóa đơn đã chọn',
        singleton: true,
        popout: true,
        onConfirm: function () {

        }
    });
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





