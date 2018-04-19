<?php
include '../../../services/connection.php';

include '../../../services/categoryService.php';
$categoryService = new CategoryService($conn);

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
            <li class="active">Kiểm tra đơn hàng</li>
        </ul>
    </div>
</div>
<!-- /BREADCRUMB -->

<div class="section">
    <div class="container">
        <div class="row">
            <h1 class="text-center">Kiểm tra đơn hàng</h1>
            <div class="col-md-offset-3 col-md-6">
                <form action="/sports-shop-final/app/pages/client/order/show.php" method="get">
                    <div class="form-group">
                        <input class="input" type="text" name="code" placeholder="Nhập mã đơn hàng ..." required>
                    </div>
                    <div class="form-group text-center">
                        <button class="primary-btn" type="submit">Kiểm tra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../template/footer.php' ?>
