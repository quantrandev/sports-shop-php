<?php
include '../../../services/connection.php';

include '../../../services/categoryService.php';
$categoryService = new CategoryService($conn);

include '../../../services/imageService.php';

include '../../../services/productService.php';
$productService = new ProductService($conn);
$product = $productService->get($_GET["id"]);

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
            <li><a href="#">Sản phẩm</a></li>
            <li class="active"><?php echo $product->name ?></li>
        </ul>
    </div>
</div>
<!-- /BREADCRUMB -->

<!-- section -->
<div class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <!--  Product Details -->
            <div class="product product-details clearfix">
                <div class="col-md-6">
                    <div id="product-main-view">
                        <?php foreach ($product->images as $image): ?>
                            <div class="product-view">
                                <img src="/sports-shop-final/assets<?php echo $image["source"] ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="product-view">
                        <?php foreach ($product->images as $image): ?>
                            <div class="product-view">
                                <img src="/sports-shop-final/assets<?php echo $image["source"] ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-body">
                        <div class="product-label">
                            <?php if ($product->isSale()): ?>
                                <span>Sale</span>
                                <span class="sale">
                                    <?php echo $product->salePercentage; ?>
                                    %
                                </span>
                            <?php endif; ?>
                            <div style="display: inline-block; margin-left: 10px;">
                                <span class="m-r-5">
                                    <i class="fa fa-thumbs-up likes-count js-likes"
                                       data-product-id="<?php echo $product->id; ?>"></i>
                                    <span class="js-likes-count"><?php echo $product->likes; ?></span>
                                </span>
                                <span class="m-r-5">
                                    <i class="fa fa-eye views-count"></i>
                                    <span class="js-views-count"><?php echo $product->views; ?></span>
                                </span>
                            </div>
                        </div>
                        <h2 class="product-name"><?php echo $product->name ?></h2>
                        <?php if ($product->isSale()): ?>
                            <h3 class="product-price">
                                <?php echo number_format($product->getSalePrice()) ?> đ
                                <del class="product-old-price">
                                    <?php echo number_format($product->basicPrice) ?> đ
                                </del>
                            </h3>
                        <?php else: ?>
                            <h3 class="product-price">
                                <?php echo number_format($product->getSalePrice()) ?> đ
                            </h3>
                        <?php endif ?>
                        <p><strong>Tình trạng:</strong>
                            <?php if ($product->quantity > 0): ?>
                                Còn hàng
                            <?php else: ?>
                                Tạm hết hàng
                            <?php endif; ?>
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                            laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure
                            dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit
                            anim id est laborum.</p>
                        <div class="product-btns">
                            <div class="qty-input">
                                <span class="text-uppercase">Số lượng: </span>
                                <input class="input" type="number" value="1">
                            </div>
                            <button class="primary-btn add-to-cart js-add-cart"
                                    data-id="<?php echo $product->id; ?>" data-name="<?php echo $product->name; ?>"><i
                                        class="fa fa-shopping-cart"></i> Mua hàng
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="product-tab">
                        <ul class="tab-nav">
                            <li class="active"><a data-toggle="tab" href="#tab1">Mô tả</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab1" class="tab-pane fade in active">
                                <?php echo $product->description; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /Product Details -->
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>
<!-- /section -->


<?php include '../template/footer.php' ?>

<script>
    let productId = "<?php echo $_GET["id"];?>";
    cartService.view(productId, function (res) {
        let viewsCountDOM = $('.js-views-count');
        viewsCountDOM.text(res);
    }, function (error) {
    });
</script>
