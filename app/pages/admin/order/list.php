<?php
session_start();
include '../templates/head.php';
include '../templates/navigation.php';
include '../templates/sidebar.php';

include '../../../services/connection.php';
include '../../../services/orderService.php';
include '../../../constants.php';
$get_name = isset($_GET["customerName"]) ? $_GET["customerName"] : null;
$get_code = isset($_GET["code"]) ? $_GET["code"] : null;
$get_shipping_status = isset($_GET["shippingStatus"]) ? $_GET["shippingStatus"] : null;
$get_date_range = isset($_GET["range"]) ? $_GET["range"] : null;

$orderService = new OrderService($conn);

$orders = $orderService->all($_GET);
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

            <div class="nav-search" id="nav-search">
                <form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input"
                                           id="nav-search-input" autocomplete="off"/>
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
                </form>
            </div><!-- /.nav-search -->
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
                <div class="clear-fix">
                    <form action="" class="col-md-12 p-0" id="frm-search">
                        <div class="col-md-2">
                            <label for="">Mã đơn hàng</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="code" placeholder="Nhập mã đơn hàng ..."
                                       value="<?php echo $get_code; ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="">Tên khách hàng</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="customerName"
                                       placeholder="Nhập tên khách hàng ..."
                                       value="<?php echo $get_name; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Thời gian</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>
                                    <input class="form-control" type="text" name="range"
                                           id="date-range-picker"
                                           value="<?php echo $get_date_range; ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="">Trạng thái</label>
                            <div class="form-group">
                                <select name="shippingStatus" class="form-control">
                                    <option value>Tất cả</option>
                                    <option value="<?php echo $constants["shippingStatus"]["placed"] ?>"
                                        <?php echo $get_shipping_status == $constants["shippingStatus"]["placed"] ? 'selected' : ''; ?>>
                                        Mới đặt hàng
                                    </option>
                                    <option value="<?php echo $constants["shippingStatus"]["onProgress"] ?>"
                                        <?php echo $get_shipping_status == $constants["shippingStatus"]["onProgress"] ? 'selected' : ''; ?>>
                                        Đang đóng
                                        gói
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
                                    <option value="<?php echo $constants["shippingStatus"]["returned"] ?>"
                                        <?php echo $get_shipping_status == $constants["shippingStatus"]["returned"] ? 'selected' : ''; ?>>
                                        Đã trả hàng
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="" class="visible-hidden">dsadsa</label>
                            <div class="form-group">
                                <button class="btn btn-sm btn-primary">
                                    <i class="fa fa-search"></i>
                                    Tìm kiếm
                                </button>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label for="" class="visible-hidden">dsadsa</label>
                            <div class="form-group">
                                <button class="btn btn-sm btn-danger pull-right js-batch-delete hide">
                                    <i class="fa fa-trash"></i>
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12">
                    <table id="orders-table" class="table table-bordered table-hover">
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
                            <th></th>
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
                                        <button class="btn btn-minier btn-primary js-update-shipping-status
m-r-5"
                                                data-ship-id="<?php echo $order["shippingStatus"]; ?>"
                                                data-order-id="<?php echo $order["code"]; ?>">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <span>
                                        <?php
                                        switch ($order["shippingStatus"]) {
                                            case $constants["shippingStatus"]["placed"]:
                                                echo "<span class='text-warning'>Mới đặt hàng</span>";
                                                break;
                                            case $constants["shippingStatus"]["onProgress"]:
                                                echo "<span class='text-info'>Đang đóng gói</span>";
                                                break;
                                            case $constants["shippingStatus"]["shipped"]:
                                                echo "<span class='text-primary'>Đang vận chuyển</span>";
                                                break;
                                            case $constants["shippingStatus"]["done"]:
                                                echo "<span class='text-success'>Đã nhận hàng</span>";
                                                break;
                                            case $constants["shippingStatus"]["returned"]:
                                                echo "<span class='text-danger'>Đã trả hàng</span>";
                                                break;
                                        }
                                        ?>
                                    </span>
                                    </td>
                                    <td class="seenStatus">
                                        <?php echo $order["isSeen"] ? '<span class="text-success"><i class="fa fa-check m-r-5"></i>' . date_format(new DateTime($order["seenAt"]), 'd-m-Y') . '</span>' : '<span>Chưa xem</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="hidden-sm hidden-xs btn-group">
                                            <button class="btn btn-xs btn-info"
                                                    data-id="<?php echo $order->id ?>">
                                                <i class="ace-icon fa fa-search bigger-120"></i>
                                                Sản phẩm
                                            </button>
                                            <button class="btn btn-xs btn-default togglable"
                                                    data-id="<?php echo $order->id ?>">
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
                    <option value="<?php echo $constants["shippingStatus"]["onProgress"] ?>">Đang đóng gói</option>
                    <option value="<?php echo $constants["shippingStatus"]["shipped"] ?>">Đang vận chuyển</option>
                    <option value="<?php echo $constants["shippingStatus"]["done"] ?>">Đã nhận hàng</option>
                    <option value="<?php echo $constants["shippingStatus"]["returned"] ?>">Đã trả hàng</option>
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




