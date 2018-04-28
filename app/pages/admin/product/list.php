<?php
session_start();

include '../../../services/connection.php';

include '../../../services/userService.php';
$userService = new UserService($conn);
if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý sản phẩm'))
    header("Location: ../../authentication/login.php");

include '../../../services/productService.php';
include '../../../services/categoryService.php';
include '../../../services/imageService.php';
include '../../../constants.php';
$searchCategories = empty($_GET["category"]) ? array() : $_GET["category"];
$categoryService = new CategoryService($conn);
$menus = $categoryService::menus($categoryService->allIncludedInactive());

$productService = new ProductService($conn);
$result = $productService->search(empty($_GET["page"]) ? 1 : $_GET["page"], 10, $_GET);

$products = $result["products"];
$count = $result["count"];

$page = empty($_GET["page"]) ? 1 : $_GET["page"];
$queryStringArr = array();
parse_str($_SERVER["QUERY_STRING"], $queryStringArr);
unset($queryStringArr["page"]);
$queryString = http_build_query($queryStringArr);

include '../templates/head.php';
include '../templates/navigation.php';
include '../templates/sidebar.php';

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Trang quản trị</a>
                </li>
                <li class="active">Quản lý sản phẩm</li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý sản phẩm
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Danh sách sản phẩm
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-8 p-0">
                        <div class="form-group">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#search-modal">
                                <i class="fa fa-search m-r-5"></i>
                                Tìm kiếm
                            </button>
                            <div class="dropdown" style="display: inline-block;">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown">
                                    <i class="fa fa-bolt m-r-5"></i>
                                    Giảm giá
                                    <i class="fa fa-caret-down m-l-5"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a role="button" class="js-sale-all" data-toggle="modal"
                                           data-target="#sale-all-modal">Tất cả</a></li>
                                    <li><a href="javascript:void(0)" class="js-sale">Sản phẩm được chọn</a></li>
                                </ul>
                            </div>
                            <div class="dropdown" style="display: inline-block;">
                                <button class="btn btn-warning btn-sm dropdown-toggle" type="button"
                                        data-toggle="dropdown">
                                    <i class="fa fa-bolt m-r-5"></i>
                                    Hủy giảm giá
                                    <i class="fa fa-caret-down m-l-5"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a role="button" class="js-unsale-all" data-toggle="modal"
                                           data-target="#unsale-all-modal">Tất cả</a></li>
                                    <li><a href="javascript:void(0)" class="js-sale js-unsale">Sản phẩm được chọn</a>
                                    </li>
                                </ul>
                            </div>
                            <button class="btn btn-danger btn-sm js-batch-delete hide">
                                <i class="fa fa-trash m-r-5"></i>
                                Xóa
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 p-0">
                        <div class="form-group pull-right">
                            <ul class="pages">
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
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="products-table">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">
                                <?php if (count($products) > 0): ?>
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-all"/>
                                        <span class="lbl"></span>
                                    </label>
                                <?php endif; ?>
                            </th>
                            <th rowspan="2">Tên sản phẩm</th>
                            <th rowspan="2">Giá gốc (đ)</th>
                            <th colspan="4" class="text-center">Khuyến mãi</th>
                            <th rowspan="2" colspan="3">Công cụ</th>
                        </tr>
                        <tr>
                            <th>Mức (%)</th>
                            <th>Giá khuyến mãi (đ)</th>
                            <th>Bắt đầu (ngày)</th>
                            <th>Kết thúc (ngày)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr data-product-id="<?php echo $product->id; ?>" class="<?php echo $product->isSale()?'info':''?>">
                                <td class="text-center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-item"/>
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td class="p-name"><?php echo $product->name; ?></td>
                                <td class="p-old-price"><?php echo number_format($product->basicPrice); ?></td>
                                <td>
                                    <?php if ($product->isSale()): ?>
                                        <span class="label label-success"><?php echo $product->salePercentage; ?></span>
                                    <?php else: ?>
                                        <?php if (empty($product->salePercentage)): ?>

                                        <?php else: ?>
                                            <span class="text-danger"><?php echo $product->salePercentage; ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="p-basicPrice">
                                    <?php if ($product->isSale()): ?>
                                        <span class="label label-success"><?php echo number_format($product->getSalePrice()); ?></span>
                                    <?php else: ?>
                                        <?php if (empty($product->salePercentage)): ?>

                                        <?php else: ?>
                                            <span class="text-danger"><?php echo number_format($product->getSalePrice()); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product->isSale()): ?>
                                        <span class="label label-success"><?php echo date('d-m-Y', strtotime($product->saleFrom)); ?></span>
                                    <?php else: ?>
                                        <?php if (empty($product->saleFrom)): ?>

                                        <?php else: ?>
                                            <span class="text-danger"><?php echo date('d-m-Y', strtotime($product->saleFrom)); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product->isSale()): ?>
                                        <span class="label label-success"><?php echo date('d-m-Y', strtotime($product->saleTo)); ?></span>
                                    <?php else: ?>
                                        <?php if (empty($product->saleTo)): ?>

                                        <?php else: ?>
                                            <span class="text-danger"><?php echo date('d-m-Y', strtotime($product->saleTo));?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown" style="display: inline-block;">
                                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button"
                                                data-toggle="dropdown">
                                            <i class="fa fa-pencil-square m-r-5"></i>
                                            Chỉnh sửa
                                            <i class="fa fa-caret-down m-l-5"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a role="button" class="js-view-images">
                                                    <i class="fa fa-image m-r-5"></i>
                                                    Ảnh
                                                </a></li>
                                            <li><a role="button" class="js-edit-category"
                                                   data-category-id="<?php echo $product->categoryId; ?>">
                                                    <i class="fa fa-bars m-r-5"></i>
                                                    Danh mục
                                                </a></li>
                                            <li><a role="button" class="js-edit"
                                                   data-id="<?php echo $product->id; ?>">
                                                    <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    Thông tin khác
                                                </a></li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-xs btn-danger js-delete-product">
                                        <i class="ace-icon fa fa-trash bigger-120"></i>
                                        Xóa
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="pull-right">
                        <ul class="pages">
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
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<div id="images-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Hình ảnh</h4>
            </div>
            <div class="modal-body">
                <div class="images-container">
                    <div class="images-track"></div>
                </div>
                <div class="text-center m-t-15 m-b-15">
                    <button class="btn btn-primary btn-xs js-toggle-upload">
                        <i class="fa fa-upload m-r-5"></i>
                        Thêm ảnh
                    </button>
                </div>
                <div class="upload-area hide">
                    <form action="/sports-shop-final/app/controllers/uploadController.php?type=product" method="post"
                          id="my-dropzone" class="dropzone well m-0">
                        <input type="hidden" id="productId">
                        <input type="hidden" id="images">
                        <div class="fallback">
                            <input name="file" type="file" multiple/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm js-save-changes">Lưu thay đổi
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Đóng
                    </button>
            </div>
        </div>

    </div>
</div>

<div id="category-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thay đổi danh mục</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <input type="hidden" id="productId">
                    <input type="hidden" id="currentCategory">
                    <div class="form-group">
                        <label for="">Danh mục mới</label>
                        <select id="js-sl-category" class="form-control">
                            <?php foreach ($menus as $category): ?>
                                <optgroup label="<?php echo $category->name ?>"></optgroup>
                                <?php if (count($category->children) > 0): ?>
                                    <?php foreach ($category->children as $child): ?>
                                        <option value="<?php echo $child->id ?>"><?php echo $child->name; ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="<?php echo $category->id ?>"><?php echo $category->name; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm js-save-changes">Lưu thay đổi
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Đóng
                    </button>
            </div>
        </div>

    </div>
</div>

<div id="product-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thông tin sản phẩm</h4>
            </div>
            <div class="modal-body">
                <form action="" class="form-horizontal">
                    <input type="hidden" id="productId">
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Tên sản phẩm *</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Giá gốc *</label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="productbasicPrice" min="500" step="500"
                                   required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Mô tả</label>
                        <div class="col-md-10">
                            <textarea id="productDescription"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm js-save-changes">Lưu thay đổi
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Đóng
                    </button>
            </div>
        </div>

    </div>
</div>
<div id="search-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="col-md-12 p-0" id="frm-search">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tìm kiếm</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Danh mục</label>
                        <select name="category[]" class="form-control multiselect-category" multiple>
                            <?php foreach ($menus as $category): ?>
                                <optgroup label="<?php echo $category->name ?>"></optgroup>
                                <?php if (count($category->children) > 0): ?>
                                    <?php foreach ($category->children as $child): ?>
                                        <?php if (in_array($child->id, $searchCategories)): ?>
                                            <option value="<?php echo $child->id ?>"
                                                    selected><?php echo $child->name; ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo $child->id ?>"><?php echo $child->name; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if (in_array($category->id, $searchCategories)): ?>
                                        <option value="<?php echo $category->id ?>"
                                                selected><?php echo $category->name; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $category->id ?>"><?php echo $category->name; ?></option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Giảm giá</label>
                        <select name="isSale" class="form-control">
                            <option value>Chọn</option>
                            <option value="0" <?php echo isset($_GET["isSale"]) ? ($_GET["isSale"] == 0 ? 'selected' : '') : ''; ?>>
                                Chưa giảm giá
                            </option>
                            <option value="1" <?php echo isset($_GET["isSale"]) ? ($_GET["isSale"] == 1 ? 'selected' : '') : ''; ?>>
                                Đang giảm giá
                            </option>
                            <option value="2" <?php echo isset($_GET["isSale"]) ? ($_GET["isSale"] == 2 ? 'selected' : '') : ''; ?>>
                                Đã giảm giá
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tên sản phẩm</label>
                        <input type="text" class="form-control" name="name"
                               value="<?php echo isset($_GET["name"]) ? $_GET["name"] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="">Giá từ</label>
                        <input type="number" class="form-control" name="price-from" min="500"
                               value="<?php echo isset($_GET["price-from"]) ? $_GET["price-from"] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="">Giá đến</label>
                        <input type="number" class="form-control" name="price-to" min="500"
                               value="<?php echo isset($_GET["price-to"]) ? $_GET["price-to"] : ''; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm js-submit">
                        <i class="fa fa-search m-r-5"></i>
                        Tìm kiếm
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Đóng
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="sale-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Giảm giá</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Thời gian</label>
                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>
                        <input class="form-control" type="text" name="range"
                               id="date-range-picker"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Mức giảm giá (%)</label>
                    <input type="number" min="0" max="100" step="0.1" id="salePercentage" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm js-save-changes">
                    Lưu thay đổi
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<div id="sale-all-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <form method="get"
              action="/sports-shop-final/app/controllers/admin/productController.php">
            <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
            <input type="hidden" name="function" value="updateSaleAll">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Giảm giá</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Thời gian</label>
                        <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar bigger-110"></i>
                                    </span>
                            <input class="form-control date-range-picker" type="text" name="range"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Mức giảm giá (%)</label>
                        <input type="number" name="salePercentage" min="0" max="100" step="0.1" id="salePercentage"
                               class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm js-save-changes">
                        Lưu thay đổi
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Đóng
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="unsale-all-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <form method="get"
              action="/sports-shop-final/app/controllers/admin/productController.php">
            <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
            <input type="hidden" name="function" value="UnsaleAll">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Hủy giảm giá</h4>
                </div>
                <div class="modal-body">
                    <p>Hủy giảm giá tất cả sản phẩm trong kết quả tìm kiếm này ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm js-save-changes">
                        Yes
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        No
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
include '../templates/footer.php';
?>

<script>
    let editor = CKEDITOR.replace('productDescription');
    CKFinder.setupCKEditor(editor);
</script>
<script src="/sports-shop-final/assets/admin/js/dropzone-init.js"></script>
<script src="/sports-shop-final/assets/admin/js/services/productService.js"></script>
<script src="/sports-shop-final/assets/admin/js/controllers/productController.js"></script>

<?php if (isset($_SESSION["flashMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["flashMessage"];?>', "gritter-success");
    </script>
    <?php unset($_SESSION["flashMessage"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["errorMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["errorMessage"];?>', "gritter-error");
    </script>
    <?php unset($_SESSION["errorMessage"]); ?>
<?php endif; ?>

<?php
include '../templates/bottom.php';
?>





