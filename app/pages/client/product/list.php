<?php
include '../../../services/connection.php';

include '../../../services/categoryService.php';
$categoryService = new CategoryService($conn);

include '../../../services/imageService.php';
include '../../../services/productService.php';
$productService = new ProductService($conn);
$searchResult = $productService
    ->search(empty($_GET["page"]) ? 1 : $_GET["page"], 12, $_GET);
$products = $searchResult["products"];
$count = $searchResult["count"];

$page = empty($_GET["page"]) ? 1 : $_GET["page"];
$queryStringArr = array();
parse_str($_SERVER["QUERY_STRING"], $queryStringArr);
unset($queryStringArr["page"]);
$queryString = http_build_query($queryStringArr);

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
            <li class="active">Sản phẩm</li>
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
            <!-- ASIDE -->
            <div id="aside" class="col-md-3">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
                    <div class="aside">
                        <button class="primary-btn">Tìm kiếm</button>
                    </div>
                    <!-- aside widget -->
                    <div class="aside">
                        <h3 class="aside-title">Tên sản phẩm:</h3>
                        <input type="text" name="name" class="input search-input"
                               placeholder="Nhập tên sản phẩm ..."
                               value="<?php echo empty($_GET["name"]) ? '' : $_GET["name"] ?>">
                    </div>
                    <!-- /aside widget -->
                    <!-- aside widget -->
                    <div class="aside">
                        <h3 class="aside-title">Danh mục:</h3>
                        <ul class="filter-category">
                            <?php foreach ($menus as $parent): ?>
                                <li>
                                    <label>
                                        <?php if (empty($_GET["category"])): ?>
                                            <input type="checkbox" name="category[]" value="<?php echo $parent->id ?>">
                                        <?php else: ?>
                                            <?php if (in_array($parent->id, is_array($_GET["category"]) ? $_GET["category"] : array($_GET["category"]))): ?>
                                                <input type="checkbox" name="category[]"
                                                       value="<?php echo $parent->id ?>"
                                                       checked="checked">
                                            <?php else: ?>
                                                <input type="checkbox" name="category[]"
                                                       value="<?php echo $parent->id ?>">
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php echo $parent->name ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- /aside widget -->
                    <!-- aside widget -->
                    <div class="aside">
                        <h3 class="aside-title">Giá:</h3>
                        <table class="filter-table">
                            <tr>
                                <td>Giá từ</td>
                                <td><input type="number" name="price-from" class="input search-input price-from"
                                           step="1000"
                                           value="<?php echo $_GET["price-from"]; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td>Giá đến</td>
                                <td><input type="number" name="price-to" class="input search-input price-to"
                                           step="1000"
                                           value="<?php echo $_GET["price-to"]; ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- /aside widget -->
                    <div class="aside">
                        <button class="primary-btn">Tìm kiếm</button>
                    </div>
                </form>
            </div>
            <!-- /ASIDE -->

            <!-- MAIN -->
            <div id="main" class="col-md-9">
                <?php if (count($products) > 0): ?>
                    <!-- store top filter -->
                    <div class="store-filter clearfix">
                        <div class="pull-right">
                            <ul class="store-pages">
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
                    <!-- /store top filter -->
                <?php endif; ?>

                <?php if (count($products) > 0): ?>
                    <!-- STORE -->
                    <div id="store">
                        <!-- row -->
                        <div class="row flex flex-l flex-w">
                            <?php foreach ($products as $product): ?>
                                <!-- Product Single -->
                                <div class="col-md-4 col-sm-6 col-xs-6">
                                    <div class="product product-single">
                                        <div class="product-thumb">
                                            <div class="product-label">
                                                <?php if ($product->oldPrice != 0): ?>
                                                    <span>New</span>
                                                    <span class="sale">-<?php echo ceil(($product->oldPrice - $product->currentPrice) / $product->oldPrice * 100); ?>
                                                        %</span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>" class="main-btn quick-view"><i class="fa fa-search-plus"></i>
                                                Phóng to
                                            </a>
                                            <img src="/sports-shop-final/assets<?php echo $product->images[0]["source"] ?>"
                                                 alt=""
                                                 class="product-thumbnail-sm">
                                        </div>
                                        <div class="product-body">
                                            <h3 class="product-price"><?php echo number_format($product->currentPrice) ?>
                                                đ
                                                <?php if ($product->oldPrice != 0): ?>
                                                    <del class="product-old-price"><?php echo number_format($product->oldPrice); ?>
                                                        đ
                                                    </del>
                                                <?php endif; ?>
                                            </h3>
                                            <h2 class="product-name"><a
                                                        href="/sports-shop-final/app/pages/client/product/show.php?id=<?php echo $product->id; ?>"><?php echo $product->name; ?></a>
                                            </h2>
                                            <div class="product-btns text-center">
                                                <button class="primary-btn js-add-cart"
                                                        data-id="<?php echo $product->id; ?>"
                                                        data-name="<?php echo $product->name; ?>">
                                                    <i class="fa fa-shopping-cart"></i> Mua hàng
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Product Single -->
                            <?php endforeach; ?>
                        </div>
                        <!-- /row -->
                    </div>
                    <!-- /STORE -->
                <?php else: ?>
                    <div>
                        <h4>Sản phẩm bạn tìm kiếm không có</h4>
                    </div>
                <?php endif; ?>

                <?php if (count($products) > 0): ?>
                    <!-- store top filter -->
                    <div class="store-filter clearfix">
                        <div class="pull-right">
                            <ul class="store-pages">
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
                    <!-- /store top filter -->
                <?php endif; ?>
            </div>
            <!-- /MAIN -->
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>
<!-- /section -->

<?php include '../template/footer.php' ?>
