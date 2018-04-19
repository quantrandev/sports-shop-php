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
                            <?php if ($product->oldPrice != 0): ?>
                                <span>Sale</span>
                                <span class="sale">
                                    <?php echo ceil(($product->oldPrice - $product->currentPrice) / $product->oldPrice * 100); ?>
                                    %
                                </span>
                            <?php endif; ?>
                        </div>
                        <h2 class="product-name"><?php echo $product->name ?></h2>
                        <h3 class="product-price">
                            <?php echo number_format($product->currentPrice) ?>đ
                            <?php if ($product->oldPrice != 0): ?>
                                <del class="product-old-price"><?php echo number_format($product->oldPrice) ?>đ</del>
                            <?php endif; ?>
                        </h3>
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
                                    data-id="<?php echo $product->id; ?>" data-name="<?php echo $product->name; ?>"><i class="fa fa-shopping-cart"></i> Mua hàng
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="product-tab">
                        <ul class="tab-nav">
                            <li class="active"><a data-toggle="tab" href="#tab1">Description</a></li>
                            <li><a data-toggle="tab" href="#tab1">Details</a></li>
                            <li><a data-toggle="tab" href="#tab2">Reviews (3)</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab1" class="tab-pane fade in active">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute
                                    irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
                                    pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                                    deserunt mollit anim id est laborum.</p>
                            </div>
                            <div id="tab2" class="tab-pane fade in">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="product-reviews">
                                            <div class="single-review">
                                                <div class="review-heading">
                                                    <div><a href="#"><i class="fa fa-user-o"></i> John</a></div>
                                                    <div><a href="#"><i class="fa fa-clock-o"></i> 27 DEC 2017 / 8:0 PM</a>
                                                    </div>
                                                    <div class="review-rating pull-right">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o empty"></i>
                                                    </div>
                                                </div>
                                                <div class="review-body">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                                        eiusmod tempor incididunt ut labore et dolore magna aliqua.Ut
                                                        enim ad minim veniam, quis nostrud exercitation ullamco laboris
                                                        nisi ut aliquip ex ea commodo consequat.Duis aute
                                                        irure dolor in reprehenderit in voluptate velit esse cillum
                                                        dolore eu fugiat nulla pariatur.</p>
                                                </div>
                                            </div>

                                            <div class="single-review">
                                                <div class="review-heading">
                                                    <div><a href="#"><i class="fa fa-user-o"></i> John</a></div>
                                                    <div><a href="#"><i class="fa fa-clock-o"></i> 27 DEC 2017 / 8:0 PM</a>
                                                    </div>
                                                    <div class="review-rating pull-right">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o empty"></i>
                                                    </div>
                                                </div>
                                                <div class="review-body">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                                        eiusmod tempor incididunt ut labore et dolore magna aliqua.Ut
                                                        enim ad minim veniam, quis nostrud exercitation ullamco laboris
                                                        nisi ut aliquip ex ea commodo consequat.Duis aute
                                                        irure dolor in reprehenderit in voluptate velit esse cillum
                                                        dolore eu fugiat nulla pariatur.</p>
                                                </div>
                                            </div>

                                            <div class="single-review">
                                                <div class="review-heading">
                                                    <div><a href="#"><i class="fa fa-user-o"></i> John</a></div>
                                                    <div><a href="#"><i class="fa fa-clock-o"></i> 27 DEC 2017 / 8:0 PM</a>
                                                    </div>
                                                    <div class="review-rating pull-right">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-o empty"></i>
                                                    </div>
                                                </div>
                                                <div class="review-body">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                                        eiusmod tempor incididunt ut labore et dolore magna aliqua.Ut
                                                        enim ad minim veniam, quis nostrud exercitation ullamco laboris
                                                        nisi ut aliquip ex ea commodo consequat.Duis aute
                                                        irure dolor in reprehenderit in voluptate velit esse cillum
                                                        dolore eu fugiat nulla pariatur.</p>
                                                </div>
                                            </div>

                                            <ul class="reviews-pages">
                                                <li class="active">1</li>
                                                <li><a href="#">2</a></li>
                                                <li><a href="#">3</a></li>
                                                <li><a href="#"><i class="fa fa-caret-right"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="text-uppercase">Write Your Review</h4>
                                        <p>Your email address will not be published.</p>
                                        <form class="review-form">
                                            <div class="form-group">
                                                <input class="input" type="text" placeholder="Your Name"/>
                                            </div>
                                            <div class="form-group">
                                                <input class="input" type="email" placeholder="Email Address"/>
                                            </div>
                                            <div class="form-group">
                                                <textarea class="input" placeholder="Your review"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <div class="input-rating">
                                                    <strong class="text-uppercase">Your Rating: </strong>
                                                    <div class="stars">
                                                        <input type="radio" id="star5" name="rating" value="5"/><label
                                                                for="star5"></label>
                                                        <input type="radio" id="star4" name="rating" value="4"/><label
                                                                for="star4"></label>
                                                        <input type="radio" id="star3" name="rating" value="3"/><label
                                                                for="star3"></label>
                                                        <input type="radio" id="star2" name="rating" value="2"/><label
                                                                for="star2"></label>
                                                        <input type="radio" id="star1" name="rating" value="1"/><label
                                                                for="star1"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="primary-btn">Submit</button>
                                        </form>
                                    </div>
                                </div>


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
