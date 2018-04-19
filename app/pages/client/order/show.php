<?php
include '../../../viewModels/cartViewModel.php';
include '../../../viewModels/orderInfoViewModel.php';
include '../../../constants.php';

include '../../../services/connection.php';

include '../../../services/categoryService.php';
$categoryService = new CategoryService($conn);

include '../../../services/shippingService.php';
include '../../../services/paymentService.php';
include '../../../services/imageService.php';
include '../../../services/productService.php';
include '../../../services/orderService.php';

$orderCode = $_GET["code"];
$orderService = new OrderService($conn);
$orderInfo = $orderService->getWithProduct($orderCode);

include '../template/head.php';
include '../template/topheader.php';
include '../template/header.php';
include '../template/navigation.php';
?>

<!-- BREADCRUMB -->
<div id="breadcrumb">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="/sports-shop-final/app/pages/client">Trang chủ </a></li>
            <li><a href="#">Đơn hàng</a></li>
            <li class="active"><?php echo $_GET["code"]; ?></li>
        </ul>
    </div>
</div>
<!-- /BREADCRUMB -->

<div class="section">
    <div class="container">
        <div class="row">
            <h1 class="text-center">Thông tin đơn hàng</h1>
            <p class="text-warning text-center"><i class="fa fa-warning"></i> Vui lòng ghi nhớ mã đơn hàng để kiểm tra
                tình trạng của đơn hàng</p>
            <p class="text-warning text-center">Mã đơn hàng: <strong><?php echo $_GET["code"]; ?></strong></p>
            <table class="shopping-cart-table table">
                <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th></th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orderInfo->items as $item): ?>
                    <tr>
                        <td class="thumb"><img src="/sports-shop-final/assets<?php echo $item->image; ?>"
                                               alt="<?php echo $item->name ?>"></td>
                        <td class="details">
                            <a href="/sports-shop-final/app/pages/client/product/show.php?id=<?php echo $item->id; ?>"><?php echo $item->name; ?></a>
                        </td>
                        <td class="price"><strong><?php echo number_format($item->price); ?> đ</strong>
                        </td>
                        <td class="qty">
                            <?php echo $item->quantity; ?>
                        </td>
                        <td class="total"><strong
                                    class="primary-color js-single-total"><?php echo number_format(intval($item->quantity) *
                                    intval($item->price)); ?>
                                đ</strong></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4">Tổng tiền sản phẩm</th>
                    <th><?php echo number_format($orderInfo->getSubtotal()) ?> đ</th>
                </tr>
                <tr>
                    <th colspan="4">Phương thức giao hàng</th>
                    <th><?php echo $orderInfo->shippingMethod["name"] . " - " . number_format($orderInfo->shippingMethod["cost"]) ?>
                        đ
                    </th>
                </tr>
                <tr>
                    <th colspan="4">Tổng tiền hóa đơn</th>
                    <th><?php echo number_format($orderInfo->getSubtotal() + $orderInfo->shippingMethod["cost"]) ?>đ
                    </th>
                </tr>
                <tr>
                    <th colspan="4">Trạng thái đơn hàng</th>
                    <th>
                        <?php

                        switch ($orderInfo->shippingStatus) {
                            case $constants["shippingStatus"]["placed"]:
                                echo "Mới đặt hàng";
                                break;
                            case $constants["shippingStatus"]["onProgress"]:
                                echo "Đang đóng gói";
                                break;
                            case $constants["shippingStatus"]["shipped"]:
                                echo "Đang vận chuyển";
                                break;
                            case $constants["shippingStatus"]["done"]:
                                echo "Đã nhận hàng";
                                break;
                            case $constants["shippingStatus"]["returned"]:
                                echo "Đã trả hàng";
                                break;
                        }

                        ?>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include '../template/footer.php' ?>
