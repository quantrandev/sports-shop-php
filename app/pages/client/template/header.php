<?php
session_start();
$menus = $categoryService::menus($categoryService->all());

$cart = empty($_SESSION["cart"]) ? null : unserialize($_SESSION["cart"]);
?>
<!-- header -->
<div id="header">
    <div class="container">
        <div class="pull-left">
            <!-- Logo -->
            <div class="header-logo p-t-10">
                <h2><a class="logo" href="#">Elite Sport</a></h2>
            </div>
            <!-- /Logo -->

            <!-- Search -->
            <div class="header-search">
                <form action="/sports-shop-final/app/pages/client/product/list.php">
                    <input class="input search-input" type="text" name="name" placeholder="Nhập tên sản phẩm ..."
                           value="<?php echo empty($_GET["name"]) ? '' : $_GET["name"] ?>">
                    <select class="input search-categories" name="category">
                        <option value="0">Tất cả</option>
                        <?php foreach ($menus as $category): ?>
                            <?php if (in_array($category->id, is_array($_GET["category"]) ? $_GET["category"] : array($_GET["category"]))): ?>
                                <option value="<?php echo $category->id ?>"
                                        selected><?php echo $category->name ?></option>
                            <?php else: ?>
                                <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <!-- /Search -->
        </div>
        <div class="pull-right">
            <ul class="header-btns">
                <!-- Cart -->
                <li class="header-cart">
                    <a href="/sports-shop-final/app/pages/client/cart/list.php">
                        <div class="header-btns-icon">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="qty js-qty">
                                <?php echo !empty($cart) ? $cart->quantity : '0' ?>
                            </span>
                        </div>
                        <strong class="text-uppercase">Giỏ hàng:</strong>
                        <br>
                        <span class="final-total"><?php echo !empty($cart) ? number_format($cart->subtotal) : '0' ?>
                            đ</span>
                    </a>
                </li>
                <!-- /Cart -->

                <!-- Mobile nav toggle-->
                <li class="nav-toggle">
                    <button class="nav-toggle-btn main-btn icon-btn"><i class="fa fa-bars"></i></button>
                </li>
                <!-- / Mobile nav toggle -->
            </ul>
        </div>
    </div>
    <!-- header -->
</div>
<!-- container -->
</header>
<!-- /HEADER -->