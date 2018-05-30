<?php

$menus = $categoryService::menus($categoryService->all());

?>
<!-- NAVIGATION -->
<div id="navigation">
    <!-- container -->
    <div class="container">
        <div id="responsive-nav">
            <!-- category nav -->
            <div class="category-nav show-on-click">
                <span class="category-header">Danh mục <i class="fa fa-list"></i></span>
                <ul class="category-list">
                    <?php foreach ($menus as $menu):
                        if (count($menu->children) > 0): ?>
                            <li class="dropdown side-dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown"
                                   aria-expanded="true"><?php echo $menu->name ?>
                                    <i
                                            class="fa fa-angle-right"></i></a>
                                <div class="custom-menu">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <ul class="list-links">
                                                <li>
                                                    <h3 class="list-links-title"><a
                                                                href="/sports-shop-final/app/pages/client/product/list.php?category=<?php echo $menu->id ?>"><?php echo $menu->name ?></a>
                                                    </h3></li>
                                                <?php foreach ($menu->children as $child): ?>
                                                    <li>
                                                        <a href="/sports-shop-final/app/pages/client/product/list.php?category=<?php echo $child->id ?>"><?php echo $child->name ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <hr class="hidden-md hidden-lg">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php else: ?>
                            <li><a href="/sports-shop-final/app/pages/client/product/list.php?category=<?php echo $menu->id ?>"><?php echo $menu->name ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- /category nav -->

            <!-- menu nav -->
            <div class="menu-nav">
                <span class="menu-header">Menu <i class="fa fa-bars"></i></span>
                <ul class="menu-list">
                    <li><a href="/sports-shop-final/app/pages/client">Trang chủ</a></li>
                    <li><a href="/sports-shop-final/app/pages/client/product/list.php">Sản phẩm</a></li>
                    <li><a href="/sports-shop-final/app/pages/client/order/check.php">Kiểm tra đơn hàng</a></li>
                </ul>
            </div>
            <!-- menu nav -->
        </div>
    </div>
    <!-- /container -->
</div>
<!-- /NAVIGATION -->
