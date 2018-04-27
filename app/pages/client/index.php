<?php
include '../../services/connection.php';

include '../../services/categoryService.php';
$categoryService = new CategoryService($conn);

include '../../services/imageService.php';

include '../../services/productService.php';
$productService = new ProductService($conn);
$sales = $productService->sales(0, 16);
$newest = $productService->newComings(0, 1)[0];
$newComings = $productService->newComings(1, 16);
$favorites = $productService->favorites(0, 8);
$bestSellers = $productService->bestSellers(0, 4);

include '../../services/adService.php';
$adService = new AdService($conn);
$ad = $adService->get();

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
                    <img src="/sports-shop-final/assets<?php echo $ad["content"] ?>" alt="">
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
                                        <span class="sale"><?php echo $product->salePercentage; ?> %</span>
                                    </div>
                                    <div class="product-feature" style="display: inline-block">
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
                                    <a href="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                       class="main-btn quick-view"><i class="fa fa-search-plus"></i> Phóng to
                                    </a>
                                    <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                         alt=""
                                         class="product-thumbnail-sm">
                                </div>
                                <div class="product-body">
                                    <?php if ($product->isSale()): ?>
                                        <h3 class="product-price">
                                            <?php echo number_format($product->getSalePrice()) ?> đ
                                            <del class="product-old-price">
                                                <?php echo number_format($product->currentPrice) ?> đ
                                            </del>
                                        </h3>
                                    <?php else: ?>
                                        <h3 class="product-price">
                                            <?php echo number_format($product->getSalePrice()) ?> đ
                                        </h3>
                                    <?php endif ?>
                                    <h2 class="product-name"><a
                                                href="product/show.php?id=<?php echo $product->id ?>"><?php echo $product->name ?></a>
                                    </h2>
                                    <div class="product-btns text-center">
                                        <button class="primary-btn add-to-cart js-add-cart"
                                                data-id="<?php echo $product->id ?>"
                                                data-name="<?php echo $product->name ?>"><i
                                                    class="fa fa-shopping-cart"></i>
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
                            <span>New</span>
                            <?php if ($newest->isSale()): ?>
                                <span class="sale">-<?php echo $newest->salePercentage; ?>
                                    %</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-feature" style="display: inline-block">
                                        <span class="m-r-5">
                                            <i class="fa fa-thumbs-up likes-count js-likes"
                                               data-product-id="<?php echo $newest->id; ?>"></i>
                                            <span class="js-likes-count"><?php echo $newest->likes; ?></span>
                                        </span>
                            <span class="m-r-5">
                                            <i class="fa fa-eye views-count"></i>
                                            <span class="js-views-count"><?php echo $newest->views; ?></span>
                                        </span>
                        </div>
                        <a href="/sports-shop-final/assets<?php echo $newest->images[0]["source"] ?>"
                           class="main-btn quick-view"><i class="fa fa-search-plus"></i> Phóng to
                        </a>
                        <img src="/sports-shop-final/assets<?php echo $newest->images[0]["source"] ?>" alt=""
                             class="product-thumbnail-sm">
                    </div>
                    <div class="product-body">
                        <?php if ($newest->isSale()): ?>
                            <h3 class="product-price">
                                <?php echo number_format($newest->getSalePrice()) ?> đ
                                <del class="product-old-price">
                                    <?php echo number_format($newest->currentPrice) ?> đ
                                </del>
                            </h3>
                        <?php else: ?>
                            <h3 class="product-price">
                                <?php echo number_format($newest->getSalePrice()) ?> đ
                            </h3>
                        <?php endif ?>
                        <h2 class="product-name"><a
                                    href="product/show.php?id=<?php echo $newest->id ?>"><?php echo $newest->name ?></a>
                        </h2>
                        <div class="product-btns text-center">
                            <button class="primary-btn add-to-cart js-add-cart"
                                    data-id="<?php echo $newest->id ?>" data-name="<?php echo $newest->name ?>"><i
                                        class="fa fa-shopping-cart"></i>
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
                                        <?php if ($product->isSale()): ?>
                                            <span class="sale">-<?php echo $product->salePercentage; ?>
                                                %</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-feature" style="display: inline-block">
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
                                    <a href="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                       class="main-btn quick-view"><i class="fa fa-search-plus"></i> Phóng to
                                    </a>
                                    <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                         alt=""
                                         class="product-thumbnail-sm">
                                </div>
                                <div class="product-body">
                                    <?php if ($product->isSale()): ?>
                                        <h3 class="product-price">
                                            <?php echo number_format($product->getSalePrice()) ?> đ
                                            <del class="product-old-price">
                                                <?php echo number_format($product->currentPrice) ?> đ
                                            </del>
                                        </h3>
                                    <?php else: ?>
                                        <h3 class="product-price">
                                            <?php echo number_format($product->getSalePrice()) ?> đ
                                        </h3>
                                    <?php endif ?>
                                    <h2 class="product-name"><a
                                                href="product/show.php?id=<?php echo $product->id ?>"><?php echo $product->name ?></a>
                                    </h2>
                                    <div class="product-btns text-center">
                                        <button class="primary-btn add-to-cart js-add-cart"
                                                data-id="<?php echo $product->id ?>"
                                                data-name="<?php echo $product->name ?>"><i
                                                    class="fa fa-shopping-cart"></i>
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

<div class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title">Sản phẩm yêu thích</h2>
                </div>
            </div>
            <!-- section title -->

            <!-- Product Single -->
            <?php foreach ($favorites as $product): ?>
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="product product-single">
                        <div class="product-thumb">
                            <div class="product-label">
                                <span>Hot</span>
                                <?php if ($product->isSale()): ?>
                                    <span class="sale">-<?php echo $product->salePercentage; ?>
                                        %</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-feature" style="display: inline-block">
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
                            <a href="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                               class="main-btn quick-view"><i class="fa fa-search-plus"></i> Phóng to
                            </a>
                            <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                 alt=""
                                 class="product-thumbnail-sm">
                        </div>
                        <div class="product-body">
                            <?php if ($product->isSale()): ?>
                                <h3 class="product-price">
                                    <?php echo number_format($product->getSalePrice()) ?> đ
                                    <del class="product-old-price">
                                        <?php echo number_format($product->currentPrice) ?> đ
                                    </del>
                                </h3>
                            <?php else: ?>
                                <h3 class="product-price">
                                    <?php echo number_format($product->getSalePrice()) ?> đ
                                </h3>
                            <?php endif ?>
                            <h2 class="product-name"><a
                                        href="product/show.php?id=<?php echo $product->id ?>"><?php echo $product->name ?></a>
                            </h2>
                            <div class="product-btns text-center">
                                <button class="primary-btn add-to-cart js-add-cart"
                                        data-id="<?php echo $product->id ?>"
                                        data-name="<?php echo $product->name ?>"><i
                                            class="fa fa-shopping-cart"></i>
                                    Mua hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- /Product Single -->
        </div>
        <!-- /row -->
        <!-- row -->
        <div class="row">
            <!-- section title -->
            <div class="col-md-12">
                <div class="section-title">
                    <h2 class="title">Mua nhiều nhất</h2>
                </div>
            </div>
            <!-- section title -->

            <!-- Product Single -->
            <?php foreach ($bestSellers as $product): ?>
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="product product-single">
                        <div class="product-thumb">
                            <div class="product-label">
                                <span>Hot</span>
                                <?php if ($product->isSale()): ?>
                                    <span class="sale">-<?php echo ceil($product->salePercentage); ?>
                                        %</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-feature" style="display: inline-block">
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
                            <a href="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                               class="main-btn quick-view"><i class="fa fa-search-plus"></i> Phóng to
                            </a>
                            <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                 alt=""
                                 class="product-thumbnail-sm">
                        </div>
                        <div class="product-body">
                            <?php if ($product->isSale()): ?>
                                <h3 class="product-price">
                                    <?php echo number_format($product->getSalePrice()) ?> đ
                                    <del class="product-old-price">
                                        <?php echo number_format($product->currentPrice) ?> đ
                                    </del>
                                </h3>
                            <?php else: ?>
                                <h3 class="product-price">
                                    <?php echo number_format($product->getSalePrice()) ?> đ
                                </h3>
                            <?php endif ?>
                            <h2 class="product-name"><a
                                        href="product/show.php?id=<?php echo $product->id ?>"><?php echo $product->name ?></a>
                            </h2>
                            <div class="product-btns text-center">
                                <button class="primary-btn add-to-cart js-add-cart"
                                        data-id="<?php echo $product->id ?>"
                                        data-name="<?php echo $product->name ?>"><i
                                            class="fa fa-shopping-cart"></i>
                                    Mua hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- /Product Single -->
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>

<?php include 'template/footer.php' ?>
