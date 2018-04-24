<?php
session_start();
include '../../services/connection.php';
include '../../services/userService.php';

$userService = new UserService($conn);

$userNameErrorMessage = "";
$passwordErrorMessage = "";
$loginErrorMessage = "";
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $userName = $_POST["userName"];
    $password = $_POST["password"];

    if (empty($userName) || empty($password)) {
        $userNameErrorMessage = "Vui lòng nhập tên tài khoản";
        $passwordErrorMessage = "Vui lòng nhập tên mật khẩu";
    } else {
        if ($userService->login($userName, $password))
            header("Location: ../admin/index.php");
        else
            $loginErrorMessage = "Tên tài khoản hoặc mật khẩu không chính xác";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>Trang đăng nhập</title>

    <meta name="description" content="User login page"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="/sports-shop-final/assets/admin/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/sports-shop-final/assets/admin/font-awesome/4.7.0/css/font-awesome.min.css"/>

    <!-- text fonts -->
    <link rel="stylesheet" href="/sports-shop-final/assets/admin/css/fonts.googleapis.com.css"/>

    <!-- ace styles -->
    <link rel="stylesheet" href="/sports-shop-final/assets/admin/css/ace.min.css"/>
    <link rel="stylesheet" href="/sports-shop-final/assets/admin/css/custom.css"/>
</head>

<body class="login-layout">
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <i class="ace-icon fa fa-leaf green"></i>
                            <span class="red">Admin</span>
                            <span class="white" id="id-text2">Dashboard</span>
                        </h1>
                        <h4 class="blue" id="id-company-text">&copy; Elite Sport</h4>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="ace-icon fa fa-coffee green"></i>
                                        Vui lòng đăng nhập
                                    </h4>

                                    <div class="space-6"></div>

                                    <form action="" method="POST">
                                        <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control"
                                                                   placeholder="Username" name="userName" required
                                                                   autofocus/>
															<i class="ace-icon fa fa-user"></i>
														</span>
                                        </label>
                                        <span class="text-danger"><?php echo $userNameErrorMessage; ?></span>
                                        <label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" class="form-control"
                                                                   placeholder="Password" name="password" required/>
															<i class="ace-icon fa fa-lock"></i>
														</span>
                                        </label>
                                        <span class="text-danger"><?php echo $passwordErrorMessage; ?></span>
                                        <span class="text-danger"><?php echo $loginErrorMessage; ?></span>
                                        <div class="space"></div>

                                        <div class="clearfix text-center">
                                            <button type="submit" name="btnSubmit"
                                                    class="width-35 btn btn-sm btn-primary">
                                                <i class="ace-icon fa fa-key"></i>
                                                <span class="bigger-110">Login</span>
                                            </button>
                                        </div>

                                        <div class="space-4"></div>
                                    </form>
                                </div><!-- /.widget-main -->

                            </div><!-- /.widget-body -->
                        </div><!-- /.login-box -->

                    </div><!-- /.position-relative -->
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.main-content -->
</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="/sports-shop-final/assets/admin/js/jquery-2.1.4.min.js"></script>


</body>
</html>
