<div id="navbar" class="navbar navbar-default ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a href="index.html" class="navbar-brand">
                <small>
                    <i class="fa fa-leaf"></i>
                    Elite Sport
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="/sports-shop-final/assets/admin/images/avatars/avatar4.png" alt="Jason's Photo"/>
                        <span class="user-info">
									<small>Welcome,</small>
                            <?php

                            $user = unserialize($_SESSION["user"]);
                            echo $user["firstName"] . " " . $user["lastName"];

                            ?>
								</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="javascript:void(0)" onclick="document.querySelector('#logoutForm').submit()">
                                <form action="/sports-shop-final/app/controllers/admin/userController.php"
                                      id="logoutForm" method="post">
                                    <input type="hidden" name="function" value="logout">

                                    <i class="ace-icon fa fa-power-off"></i>
                                    Đăng xuất
                                </form>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>