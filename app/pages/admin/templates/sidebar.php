<?php

$userRoles = $userService->getRoles(unserialize($_SESSION["user"])["userName"]);

?>
<div class="main-container ace-save-state" id="main-container">
    <div id="sidebar" class="sidebar responsive ace-save-state">
        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                <button class="btn btn-success">
                    <i class="ace-icon fa fa-signal"></i>
                </button>

                <button class="btn btn-info">
                    <i class="ace-icon fa fa-pencil"></i>
                </button>

                <button class="btn btn-warning">
                    <i class="ace-icon fa fa-users"></i>
                </button>

                <button class="btn btn-danger">
                    <i class="ace-icon fa fa-cogs"></i>
                </button>
            </div>

            <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>

                <span class="btn btn-info"></span>

                <span class="btn btn-warning"></span>

                <span class="btn btn-danger"></span>
            </div>
        </div><!-- /.sidebar-shortcuts -->

        <ul class="nav nav-list">
            <li class="active">
                <a href="/admin">
                    <i class="menu-icon fa fa-tachometer"></i>
                    <span class="menu-text"> Trang quản trị </span>
                </a>

                <b class="arrow"></b>
            </li>

            <?php if (in_array('Quản lý danh mục', array_map(function ($value) {
                return $value["name"];
            }, $userRoles))): ?>
                <li class="">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-desktop"></i>
                        <span class="menu-text">
								Quản lý danh mục
							</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/category/add.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Thêm danh mục
                            </a>

                            <b class="arrow"></b>
                        </li>
                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/category/list.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Danh sách danh mục
                            </a>

                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array('Quản lý sản phẩm', array_map(function ($value) {
                return $value["name"];
            }, $userRoles))): ?>
                <li class="">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-list"></i>
                        <span class="menu-text"> Quản lý sản phẩm </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/product/add.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Thêm sản phẩm
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/product/list.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Danh sách sản phẩm
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array('Quản lý đơn hàng', array_map(function ($value) {
                return $value["name"];
            }, $userRoles))): ?>
                <li class="">
                    <a href="/sports-shop-final/app/pages/admin/order/list.php">
                        <i class="menu-icon fa fa-pencil-square-o"></i>
                        <span class="menu-text"> Quản lý đơn hàng</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array('Quản lý người dùng', array_map(function ($value) {
                return $value["name"];
            }, $userRoles))): ?>
                <li class="">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-list"></i>
                        <span class="menu-text"> Quản lý người dùng </span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/product/add.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Thêm người dùng
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/product/list.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Danh sách người dùng
                            </a>

                            <b class="arrow"></b>
                        </li>

                        <li class="">
                            <a href="/sports-shop-final/app/pages/admin/product/list.php">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Quản lý quyền
                            </a>

                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

        </ul><!-- /.nav-list -->

        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state"
               data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
    </div>