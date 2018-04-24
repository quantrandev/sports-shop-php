<?php

session_start();
include '../../services/connection.php';
include '../../services/userService.php';
include '../../constants.php';
$userService = new UserService($conn);

if (!$userService->isAuthenticate())
    header("Location: ../authentication/login.php");

include 'templates/head.php';
include 'templates/navigation.php';
include 'templates/sidebar.php';

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Trang quản trị</a>
                </li>
                <li class="active">Tổng quan</li>
            </ul><!-- /.breadcrumb -->

            <div class="nav-search" id="nav-search">
                <form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input"
                                           id="nav-search-input" autocomplete="off"/>
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
                </form>
            </div><!-- /.nav-search -->
        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Trang quản trị
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        tổng quan &amp; thống kê
                    </small>
                </h1>
            </div><!-- /.page-header -->

        </div><!-- /.page-content -->
    </div>
</div>

<?php
include 'templates/footer.php';
include 'templates/bottom.php';
?>
