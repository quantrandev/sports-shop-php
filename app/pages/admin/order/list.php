<?php
session_start();

include '../../../services/connection.php';

include '../../../services/userService.php';
include '../../../constants.php';
$userService = new UserService($conn);
if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý đơn hàng'))
    header("Location: ../../authentication/login.php");

include '../../../services/orderService.php';
$get_name = isset($_GET["customerName"]) ? $_GET["customerName"] : null;
$get_code = isset($_GET["code"]) ? $_GET["code"] : null;
$get_shipping_status = isset($_GET["shippingStatus"]) ? $_GET["shippingStatus"] : null;
$get_date_range = isset($_GET["range"]) ? $_GET["range"] : null;
$get_address = isset($_GET["customerAddress"]) ? $_GET["customerAddress"] : null;

$orderService = new OrderService($conn);

$result = $orderService->all(empty($_GET["page"]) ? 1 : $_GET["page"], 10, $_GET);

$orders = $result["orders"];
$count = $result["count"];

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
                <li class="active">Quản lý danh mục</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý đơn hàng
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Danh sách đơn hàng
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12 p-0 m-b-15">
                    <div class="col-md-12 p-0">
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#search-modal">
                                <i class="fa fa-search m-r-5"></i>
                                Tìm kiếm
                            </button>
                            <button class="btn btn-info btn-sm js-print-orders">
                                <i class="fa fa-print m-r-5"></i>
                                In danh sách
                            </button>
                            <div class="dropdown" style="display: inline-block;">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown">
                                    <i class="fa fa-print m-r-5"></i>
                                    In hóa đơn
                                    <i class="fa fa-caret-down m-l-5"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)" class="js-print-invoices-all">Tất cả</a></li>
                                    <li><a href="javascript:void(0)" class="js-print-invoices">Hóa đơn được chọn</a>
                                    </li>
                                </ul>
                            </div>
                            <button class="btn btn-danger btn-sm js-batch-delete hide">
                                <i class="fa fa-trash m-r-5"></i>
                                Xóa
                            </button>
                        </div>
                        <div class="col-md-6">
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
                </div>
                <div class="col-md-12">
                    <table id="orders-table" class="table table-bordered table-hover m-0">
                        <thead>
                        <tr>
                            <th class="center">
                                <?php if (count($orders) > 0): ?>
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-all"/>
                                        <span class="lbl"></span>
                                    </label>
                                <?php endif; ?>
                            </th>
                            <th>Mã đơn hàng</th>
                            <th>Tên khách hàng</th>
                            <th>Địa chỉ khách hàng</th>
                            <th>Tình trạng đơn hàng</th>
                            <th>Đã xem</th>
                            <th style="max-width: 100px"></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr data-order-id="<?php echo $order["code"]; ?>"
                                    data-seen="<?php echo $order["isSeen"] ?>"
                                    class="<?php echo $order["isSeen"] == 0 ? 'info' : '' ?>">
                                    <td class="center">
                                        <label class="pos-rel">
                                            <input type="checkbox" class="ace js-check-item"/>
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td><?php echo $order["code"] ?></td>
                                    <td class="js-customer-name"><?php echo $order["customerName"] ?></td>
                                    <td class="js-customer-address"><?php echo $order["customerAddress"] ?></td>
                                    <td>
                                        <?php if ($order["shippingStatus"] != $constants["shippingStatus"]["done"]): ?>
                                            <button class="btn btn-minier btn-primary js-update-shipping-status
m-r-5"
                                                    data-ship-id="<?php echo $order["shippingStatus"]; ?>"
                                                    data-order-id="<?php echo $order["code"]; ?>">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        <?php endif; ?>
                                        <span>
                                        <?php
                                        switch ($order["shippingStatus"]) {
                                            case $constants["shippingStatus"]["placed"]:
                                                echo "<span class='text-warning'>Mới đặt hàng</span>";
                                                break;
                                            case $constants["shippingStatus"]["shipped"]:
                                                echo "<span class='text-primary'>Đang vận chuyển</span>";
                                                break;
                                            case $constants["shippingStatus"]["done"]:
                                                echo "<span class='text-success'>Đã nhận hàng</span>";
                                                break;
                                        }
                                        ?>
                                    </span>
                                    </td>
                                    <td class="seenStatus">
                                        <?php echo $order["isSeen"] ? '<span class="text-success"><i class="fa fa-check m-r-5"></i>' . date_format(new DateTime($order["seenAt"]), 'd-m-Y') . '</span>' : '<span>Chưa xem</span>' ?>
                                    </td>
                                    <td class="text-center" style="max-width: 130px">
                                        <div class="hidden-sm hidden-xs btn-group">
                                            <button class="btn btn-xs btn-info js-view-product"
                                                    data-id="<?php echo $order["code"] ?>">
                                                <i class="ace-icon fa fa-search bigger-120"></i>
                                                Sản phẩm
                                            </button>
                                            <button class="btn btn-xs btn-default togglable">
                                                <i class="ace-icon fa fa-angle-down bigger-120"></i>
                                                Xem thêm
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="detail-row">
                                    <td colspan="7">
                                        <ul class="common-list">
                                            <li>Số điện thoại: <span
                                                        class="pull-right js-customer-mobile"><?php echo $order["customerMobile"]; ?></span>
                                            </li>
                                            <li>Ngày
                                                đặt: <span
                                                        class="pull-right"><?php echo date_format(new DateTime($order["createdDate"]), 'd-m-Y'); ?></span>
                                            </li>
                                            <li>Ghi chú: <span
                                                        class="pull-right js-note"><?php echo $order["note"]; ?></span>
                                            </li>
                                            <li>
                                                <div class="hidden-sm hidden-xs btn-group">
                                                    <button class="btn btn-xs btn-info js-edit-order"
                                                            data-id="<?php echo $order["code"]; ?>">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                        Sửa
                                                    </button>
                                                    <button class="btn btn-xs btn-danger js-delete-order"
                                                            data-id="<?php echo $order["code"]; ?>">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                        Xóa
                                                    </button>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    Không có đơn hàng
                                </td>
                            </tr>
                        <?php endif; ?>
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

<!--shipping status update modal-->
<div id="shipping-status-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Trạng thái đơn hàng</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="orderId">
                <label for="">Thay đổi trạng thái</label>
                <select id="js-sl-shipping-status" class="form-control">
                    <option value="<?php echo $constants["shippingStatus"]["placed"] ?>">Mới đặt hàng</option>
                    <option value="<?php echo $constants["shippingStatus"]["shipped"] ?>">Đang vận chuyển</option>
                    <option value="<?php echo $constants["shippingStatus"]["done"] ?>">Đã nhận hàng</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>

    </div>
</div>

<!--edit modal-->
<div id="customer-info-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Chỉnh sửa thông tin</h4>
            </div>
            <div class="modal-body overflow-auto">
                <form action="" class="col-md-12">
                    <input type="hidden" id="code">
                    <table class="form-table">
                        <tr>
                            <td>Tên khách hàng</td>
                            <td>
                                <input type="text" class="form-control" id="customerName">
                            </td>
                        </tr>
                        <tr>
                            <td>Địa chỉ</td>
                            <td><input type="text" class="form-control" id="customerAddress"></td>
                        </tr>
                        <tr>
                            <td>Số điện thoại</td>
                            <td><input type="text" class="form-control" id="customerMobile"></td>
                        </tr>
                        <tr>
                            <td>Ghi chú</td>
                            <td><input type="text" class="form-control" id="note"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>

    </div>
</div>

<!--products modal-->
<div id="products-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Danh sách sản phẩm</h4>
            </div>
            <div class="modal-body overflow-auto p-0">
                <table class="table table-hover table-bordered m-0">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>

    </div>
</div>

<div id="search-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="col-md-12 p-0" id="frm-search">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tìm kiếm</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-4">
                        <label for="">Mã đơn hàng</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="code"
                                   value="<?php echo $get_code; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="">Tên khách hàng</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="customerName"
                                   value="<?php echo $get_name; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="">Địa chỉ</label>
                        <div class="form-group">
                            <select name="customerAddress" class="select2 form-control">
                                <option value>Chọn</option>
                                <?php foreach ($provinces as $province): ?>
                                    <?php if (trim($get_address) == trim($province)): ?>
                                        <option value="<?php echo $province; ?> "
                                                selected><?php echo $province; ?> </option>
                                    <?php else: ?>
                                        <option value="<?php echo $province; ?> "><?php echo $province; ?> </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Thời gian</label>
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>
                                <input class="form-control" type="text" name="range"
                                       id="date-range-picker"
                                       value=" <?php echo $get_date_range; ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="">Trạng thái</label>
                        <div class="form-group">
                            <select name="shippingStatus" class="form-control">
                                <option value>Tất cả</option>
                                <option value="<?php echo $constants["shippingStatus"]["placed"] ?>"
                                    <?php echo $get_shipping_status == $constants["shippingStatus"]["placed"] ? 'selected' : ''; ?>>
                                    Mới đặt hàng
                                </option>
                                <option value="<?php echo $constants["shippingStatus"]["shipped"] ?>"
                                    <?php echo $get_shipping_status == $constants["shippingStatus"]["shipped"] ? 'selected' : ''; ?>>
                                    Đang vận
                                    chuyển
                                </option>
                                <option value="<?php echo $constants["shippingStatus"]["done"] ?>"
                                    <?php echo $get_shipping_status == $constants["shippingStatus"]["done"] ? 'selected' : ''; ?>>
                                    Đã nhận hàng
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="">Tình trạng duyệt</label>
                        <select name="isSeen" class="form-control">
                            <option value>Chọn</option>
                            <option value="0">Chưa xem</option>
                            <option value="1">Đã xem</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary js-submit">
                        <i class="fa fa-search m-r-5"></i>
                        Tìm kiếm
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form target="_blank" action="/sports-shop-final/app/controllers/admin/printController.php" id="printOrders"
      method="post">
    <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
    <input type="hidden" name="function" value="orders">
</form>
<form target="_blank" action="/sports-shop-final/app/controllers/admin/printController.php" id="printAllInvoices"
      method="post">
    <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
    <input type="hidden" name="function" value="invoicesAll">
</form>
<form target="_blank" action="/sports-shop-final/app/controllers/admin/printController.php" id="printInvoices"
      method="post">
    <input type="hidden" name="function" value="invoices">
    <input type="hidden" name="orders" id="orders">
</form>
<?php
include '../templates/footer.php';
?>

<script src="/sports-shop-final/assets/admin/js/services/orderService.js"></script>
<script src="/sports-shop-final/assets/admin/js/controllers/orderController.js"></script>

<?php if (isset($_SESSION["flashMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["flashMessage"];?>', "gritter-success");
    </script>
    <?php unset($_SESSION["flashMessage"]); ?>
<?php endif; ?>

<?php
include '../templates/bottom.php';
?>





