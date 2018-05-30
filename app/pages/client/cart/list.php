<?php
include '../../../services/connection.php';

include '../../../services/categoryService.php';
$categoryService = new CategoryService($conn);

include '../../../services/shippingService.php';
$shippingMethodService = new ShippingMethodService($conn);
$shippingMethods = $shippingMethodService->all();

include '../../../services/paymentService.php';
$paymentService = new paymentService($conn);
$payments = $paymentService->all();

include '../template/head.php';
include '../template/topheader.php';
include '../../../viewModels/cartViewModel.php';
include '../template/header.php';
include '../template/navigation.php';

?>

<!-- BREADCRUMB -->
<div id="breadcrumb">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="/sports-shop-final/app/pages/client">Trang chủ </a></li>
            <li><a href="#" class="active">Giỏ hàng</a></li>
        </ul>
    </div>
</div>
<!-- /BREADCRUMB -->
<div class="section">
    <div class="container">
        <div class="row">
            <form id="checkout-form" class="clearfix" action="/sports-shop-final/app/controllers/orderController.php" method="post">
                <?php if (!empty($cart)): ?>
                    <div class="col-md-6">
                        <div class="billing-details">
                            <div class="section-title">
                                <h4 class="title">Thông tin khách hàng</h4>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="name" placeholder="Tên khách hàng" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="address" placeholder=" Địa chỉ nhận hàng"
                                       required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="tel" name="mobile" placeholder="Số điện thoại" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="note" name="note"
                                       placeholder="Yêu cầu khác (không bắt buộc)">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="shiping-methods">
                            <div class="section-title">
                                <h4 class="title">Phương thức giao hàng</h4>
                            </div>
                            <?php foreach ($shippingMethods as $shippingMethod): ?>
                                <?php if ($shippingMethod["id"] == $cart->shippingMethod["id"]): ?>
                                    <div class="input-checkbox">
                                        <input type="radio" name="shipping"
                                               id="shipping-<?php echo $shippingMethod["id"] ?>"
                                               class="js-select-shipping-method"
                                               value="<?php echo $shippingMethod["id"] ?>"
                                               checked>
                                        <label for="shipping-<?php echo $shippingMethod["id"] ?>"><?php echo $shippingMethod["name"] ?>
                                            - <?php echo number_format($shippingMethod["cost"]) ?> đ</label>
                                        <div class="caption">
                                            <p>
                                                <?php echo $shippingMethod["description"] ?>
                                            <p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="input-checkbox">
                                        <input type="radio" name="shipping"
                                               id="shipping-<?php echo $shippingMethod["id"] ?>"
                                               class="js-select-shipping-method"
                                               value="<?php echo $shippingMethod["id"] ?>">
                                        <label for="shipping-<?php echo $shippingMethod["id"] ?>"><?php echo $shippingMethod["name"] ?>
                                            - <?php echo number_format($shippingMethod["cost"]) ?> đ</label>
                                        <div class="caption">
                                            <p>
                                                <?php echo $shippingMethod["description"] ?>
                                            <p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="payments-methods">
                            <div class="section-title">
                                <h4 class="title">Phương thức thanh toán</h4>
                            </div>
                            <?php foreach ($payments as $key => $payment): ?>
                                <?php if ($key == 0): ?>
                                    <div class="input-checkbox">
                                        <input type="radio" name="payments" id="payments-<?php echo $payment["id"] ?>"
                                               checked>
                                        <label for="payments-1"><?php echo $payment["name"] ?></label>
                                        <div class="caption">
                                            <p>
                                                <?php echo $payment["description"] ?>
                                            <p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="input-checkbox">
                                        <input type="radio" name="payments" id="payments-<?php echo $payment["id"] ?>">
                                        <label for="payments-1"><?php echo $payment["name"] ?></label>
                                        <div class="caption">
                                            <p>
                                                <?php echo $payment["description"] ?>
                                            <p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-md-12">
                    <div class="order-summary clearfix">
                        <div class="section-title">
                            <h3 class="title">Giỏ hàng của bạn</h3>
                        </div>
                        <table class="shopping-cart-table table">
                            <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th></th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($cart)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Không có sản phẩm <a href="/sports-shop-final/app/pages/client">Mua hàng</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cart->items as $item): ?>
                                    <tr>
                                        <td class="thumb"><img src="/sports-shop-final/assets<?php echo $item->image ?>"
                                                               alt="<?php echo $item->name ?>"></td>
                                        <td class="details">
                                            <a href="/sports-shop-final/app/pages/client/product/show.php?id=<?php echo $item->id ?>"><?php echo $item->name ?></a>
                                        </td>
                                        <td class="price"><strong><?php echo number_format($item->price) ?> đ</strong>
                                        </td>
                                        <td class="qty">
                                            <div class="flex flex-sa">
                                                <input class="input js-quantity" type="number" min="1"
                                                       value="<?php echo $item->quantity ?>">
                                                <button type="button" class="main-btn icon-btn js-update-cart hide"
                                                        data-id="<?php echo $item->id ?>"><i class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="total"><strong
                                                    class="primary-color js-single-total"><?php echo number_format(intval($item->quantity)
                                                    * intval($item->price)) ?>
                                                đ</strong></td>
                                        <td class="text-right">
                                            <button type="button" class="main-btn icon-btn js-delete-cart-item"
                                                    data-id="<?php echo $item->id ?>"><i class="fa fa-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <?php if (!empty($cart)): ?>
                                <tfoot>
                                <tr>
                                    <th class="empty" colspan="3"></th>
                                    <th>Tổng tiền sản phẩm</th>
                                    <th colspan="2" class="sub-total"
                                        data-value="<?php echo $cart->subtotal ?>"><?php echo number_format($cart->subtotal) ?>
                                        đ
                                    </th>
                                </tr>
                                <tr>
                                    <th class="empty" colspan="3"></th>
                                    <th>Giao hàng</th>
                                    <td colspan="2"
                                        class="js-shipping-method"><?php echo $cart->shippingMethod["name"] ?>
                                        - <?php echo number_format($cart->shippingMethod["cost"]) ?> đ
                                    </td>
                                </tr>
                                <tr>
                                    <th class="empty" colspan="3"></th>
                                    <th>Tổng tiền hóa đơn</th>
                                    <th colspan="2"
                                        class="final-total"><?php echo number_format(intval($cart->subtotal) +
                                            intval($cart->shippingMethod["cost"])) ?>
                                        đ
                                    </th>
                                </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                        <?php if (!empty($cart)): ?>
                            <div class="pull-right">
                                <button class="primary-btn btn-order" type="submit">Đặt hàng</button>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../template/footer.php' ?>
