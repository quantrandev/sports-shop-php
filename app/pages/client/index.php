<?php
include '../../services/connection.php';

include '../../services/categoryService.php';
$categoryService = new CategoryService($conn);

include '../../services/imageService.php';

include '../../services/productService.php';
$productService = new ProductService($conn);
$sales = $productService->sales(0, 16);
$newComings = $productService->newComings(0, 16);

include 'template/head.php';
include 'template/topheader.php';
include '../../viewModels/cartViewModel.php';
include 'template/header.php';
include 'template/navigation.php';
?>

<?php ?>
<!-- section -->
<div class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <!-- section-title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title">Giảm giá</h2>
                    <div class="pull-right">
                        <div class="product-slick-dots-1 custom-dots"></div>
                    </div>
                </div>
            </div>
            <!-- /section-title -->

            <!-- banner -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="banner banner-2">
                    <img src="/sports-shop-ci/images/banner14.jpg" alt="">
                    <div class="banner-caption">
                        <h2 class="white-color">NEW<br>COLLECTION</h2>
                        <button class="primary-btn">Shop Now</button>
                    </div>
                </div>
            </div>
            <!-- /banner -->

            <!-- Product Slick -->
            <div class="col-md-9 col-sm-6 col-xs-6">
                <div class="row">
                    <div id="product-slick-1" class="product-slick">
                        <?php foreach ($sales as $product): ?>
                            <!-- Product Single -->
                            <div class="product product-single">
                                <div class="product-thumb">
                                    <div class="product-label">
                                        <span>Sale</span>
                                        <span class="sale">-<?php echo ceil(($product->oldPrice - $product->currentPrice) / $product->oldPrice * 100) ?>
                                            %</span>
                                    </div>
                                    <button class="main-btn quick-view"><i class="fa fa-search-plus"></i> Quick view
                                    </button>
                                    <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>" alt=""
                                         class="product-thumbnail-sm">
                                </div>
                                <div class="product-body">
                                    <h3 class="product-price"><?php echo number_format($product->currentPrice) ?> đ
                                        <?php if ($product->oldPrice != 0): ?>
                                            <del class="product-old-price">
                                                <?php echo number_format($product->oldPrice) ?> đ
                                            </del>
                                        <?php endif ?>
                                    </h3>
                                    <h2 class="product-name"><a
                                                href="product/show.php?id=<?php echo $product->id ?>"><?php echo $product->name ?></a>
                                    </h2>
                                    <div class="product-btns text-center">
                                        <button class="primary-btn add-to-cart js-add-cart"
                                        data-id="<?php echo $product->id?>" data-name="<?php echo $product->name ?>"><i class="fa fa-shopping-cart"></i>
                                            Mua hàng
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /Product Single -->
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- /Product Slick -->
        </div>
        <!-- /row -->

        <!-- row -->
        <div class="row">
            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title">Sản phẩm mới</h2>
                    <div class="pull-right">
                        <div class="product-slick-dots-2 custom-dots">
                        </div>
                    </div>
                </div>
            </div>
            <!-- section title -->

            <!-- Product Single -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="product product-single product-hot">
                    <div class="product-thumb">
                        <div class="product-label">
                            <span>Sale</span>
                            <?php if ($newComings[0]->oldPrice != 0): ?>
                                <span class="sale">-<?php echo ceil(($newComings[0]->oldPrice - $newComings[0]->currentPrice) / $newComings[0]->oldPrice * 100) ?>
                                    %</span>
                            <?php endif; ?>
                        </div>
                        <button class="main-btn quick-view"><i class="fa fa-search-plus"></i> Quick view
                        </button>
                        <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>" alt=""
                             class="product-thumbnail-sm">
                    </div>
                    <div class="product-body">
                        <h3 class="product-price"><?php echo number_format($newComings[0]->currentPrice) ?> đ
                            <?php if ($newComings[0]->oldPrice != 0): ?>
                                <del class="product-old-price">
                                    <?php echo number_format($newComings[0]->oldPrice) ?> đ
                                </del>
                            <?php endif ?>
                        </h3>
                        <h2 class="product-name"><a
                                    href="/sports-shop-ci/products/<?php echo $newComings[0]->id ?>"><?php echo $newComings[0]->name ?></a>
                        </h2>
                        <div class="product-btns text-center">
                            <button class="primary-btn add-to-cart js-add-cart"
                                    data-id="<?php echo $product->id?>" data-name="<?php echo $product->name ?>"><i class="fa fa-shopping-cart"></i>
                                Mua hàng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Product Single -->

            <!-- Product Slick -->
            <div class="col-md-9 col-sm-6 col-xs-6">
                <div class="row">
                    <div id="product-slick-2" class="product-slick">
                        <?php foreach ($newComings as $product): ?>
                            <!-- Product Single -->
                            <div class="product product-single">
                                <div class="product-thumb">
                                    <div class="product-label">
                                        <span>New</span>
                                        <?php if ($product->oldPrice != 0): ?>
                                            <span class="sale">-<?php echo ceil(($product->oldPrice - $product->currentPrice) / $product->oldPrice * 100) ?>
                                                %</span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="main-btn quick-view"><i class="fa fa-search-plus"></i> Quick view
                                    </button>
                                    <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>" alt=""
                                         class="product-thumbnail-sm">
                                </div>
                                <div class="product-body">
                                    <h3 class="product-price"><?php echo number_format($product->currentPrice) ?> đ
                                        <?php if ($product->oldPrice != 0): ?>
                                            <del class="product-old-price">
                                                <?php echo number_format($product->oldPrice) ?> đ
                                            </del>
                                        <?php endif ?>
                                    </h3>
                                    <h2 class="product-name"><a
                                                href="product/show.php?id=<?php echo $product->id ?>"><?php echo $product->name ?></a>
                                    </h2>
                                    <div class="product-btns text-center">
                                        <button class="primary-btn add-to-cart js-add-cart"
                                                data-id="<?php echo $product->id?>" data-name="<?php echo $product->name ?>"><i class="fa fa-shopping-cart"></i>
                                            Mua hàng
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /Product Single -->
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- /Product Slick -->
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>
<!-- /section -->

<?php include 'template/footer.php' ?>
