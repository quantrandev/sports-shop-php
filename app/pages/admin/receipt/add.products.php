<?php
session_start();

include '../../../services/connection.php';

include '../../../services/userService.php';
$userService = new UserService($conn);
if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý sản phẩm'))
    header("Location: ../../authentication/login.php");

if (!isset($_SESSION["receiptInfo"])) {
    header("Location: add.info.php");
    exit;
}

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

//process receipt products
include '../../../services/receiptService.php';
$receiptService = new ReceiptService($conn);

if (isset($_SESSION["receiptProducts"])) {
    $receiptProducts = unserialize($_SESSION["receiptProducts"]);
} else {
    $receiptProducts = array();
}

$isBreak = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $function = $_POST["function"];
    $queryString = $_POST["p"];
    switch ($function) {
        case 'add':
            $productId = $_POST["product"];
            foreach ($receiptProducts as $item) {
                if ($productId == $item["id"]) {
                    $isBreak = true;
                    $_SESSION["errorMessage"] = "Sản phẩm đã thêm, vui lòng chọn sản phẩm khác";
                }
            }

            if ($isBreak)
                break;

            $product = $productService->get($productId);
            $receiptItem = array(
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->basicPrice
            );

            array_push($receiptProducts, $receiptItem);
            $_SESSION["receiptProducts"] = serialize($receiptProducts);
            break;

        case 'delete':
            $productId = $_POST["product"];
            $deletedIndex = -1;
            for ($i = 0; $i < count($receiptProducts); $i++) {
                if ($productId == $receiptProducts[$i]["id"]) {
                    $deletedIndex = $i;
                    break;
                }
            }

            if ($deletedIndex != -1) {
                array_splice($receiptProducts, $deletedIndex, 1);
                $_SESSION["receiptProducts"] = serialize($receiptProducts);
            }
            break;
        case 'update':
            $productId = $_POST["product"];
            $quantity = $_POST["qty"];
            $price = $_POST["price"];
            $updatedIndex = -1;
            for ($i = 0; $i < count($receiptProducts); $i++) {
                if ($productId == $receiptProducts[$i]["id"]) {
                    $updatedIndex = $i;
                    break;
                }
            }
            if ($updatedIndex != -1) {
                $receiptProducts[$updatedIndex]["quantity"] = $quantity;
                $receiptProducts[$updatedIndex]["price"] = $price;
                $_SESSION["receiptProducts"] = serialize($receiptProducts);
            }

            break;
        case 'addReceipt':
            $error = $receiptService->insert(array(
                "info" => unserialize($_SESSION["receiptInfo"]),
                "items" => unserialize($_SESSION["receiptProducts"])
            ));
            if ($error)
                $_SESSION["errorMessage"] = "Có lỗi xảy ra, vui lòng thử lại";
            else {
                unset($_SESSION["receiptInfo"]);
                unset($_SESSION["receiptProducts"]);
                $_SESSION["flashMessage"] = "Thêm thành công hóa đơn";
            }
            break;
    }
    header("Location: " . $_SERVER["REQUEST_URI"] . "?" . $queryString);
    exit;
}

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
                <li class="active">Quản lý nhập hàng</li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    Quản lý nhập hàng
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Chọn sản phẩm
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-1 p-0">
                        <div class="form-group">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#search-modal">
                                <i class="fa fa-search m-r-5"></i>
                                Tìm kiếm
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4" style="padding-left: 0">
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
                    <div class="col-md-7">
                        <div class="pull-right">
                            <button class="btn btn-success btn-sm js-save-receipt">
                                <i class="fa fa-save m-r-5"></i>
                                Lưu hóa đơn nhập
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 p-0">
                    <div class="col-md-5">
                        <table class="table table-bordered table-hover m-0" id="products-table">
                            <thead>
                            <tr>
                                <th colspan="3" class="text-center">Sản phẩm trong hệ thống</th>
                            </tr>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Giá gốc (đ)</th>
                                <th>Công cụ</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr data-product-id="<?php echo $product->id; ?>">
                                    <td class="p-name"><?php echo $product->name; ?></td>
                                    <td class="p-old-price"><?php echo number_format($product->basicPrice); ?></td>
                                    <td>
                                        <?php if (in_array($product->id, array_map(function ($value) {
                                            return $value["id"];
                                        }, $receiptProducts))): ?>
                                            <button class="btn btn-success btn-minier btn-block" disabled>
                                                <i class="fa fa-check m-r-5"></i>
                                                Thêm
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-primary btn-minier btn-block js-add-product">
                                                <i class="fa fa-plus m-r-5"></i>
                                                Thêm
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-7">
                        <table class="table table-bordered table-hover" id="receipt-items-table">
                            <thead>
                            <tr>
                                <th colspan="4" class="text-center">Sản phẩm trong hóa đơn nhập</th>
                            </tr>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Công cụ</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($receiptProducts as $item): ?>
                                <tr data-id="<?php echo $item["id"]; ?>">
                                    <td><?php echo $item["name"]; ?></td>
                                    <td><input style="width: 80px;" type="number" class="js-qty" min="1"
                                               value="<?php echo $item["quantity"]; ?>">
                                    </td>
                                    <td><input style="width: 120px;" type="number" class="js-price" min="500" step="100"
                                               value="<?php echo $item["price"]; ?>">
                                    </td>
                                    <td>
                                        <div class="dropdown" style="display: inline-block;">
                                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button"
                                                    data-toggle="dropdown">
                                                <i class="fa fa-pencil-square m-r-5"></i>
                                                Công cụ
                                                <i class="fa fa-caret-down m-l-5"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a role="button" class="js-save-changes">
                                                        <i class="fa fa-save m-r-5"></i>
                                                        Lưu
                                                    </a></li>
                                                <li><a role="button" class="js-delete">
                                                        <i class="fa fa-trash m-r-5"></i>
                                                        Xóa
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-offset-1 col-md-4">
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

<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="frm-add-product">
    <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
    <input type="hidden" name="product" class="product">
    <input type="hidden" name="function" value="add">
</form>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="frm-delete-product">
    <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
    <input type="hidden" name="product" class="product">
    <input type="hidden" name="function" value="delete">
</form>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="frm-update-product">
    <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
    <input type="hidden" name="product" class="product">
    <input type="hidden" name="qty" class="qty">
    <input type="hidden" name="price" class="price">
    <input type="hidden" name="function" value="update">
</form>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="frm-add-receipt">
    <input type="hidden" name="p" value="<?php echo $_SERVER["QUERY_STRING"]; ?>">
    <input type="hidden" name="function" value="addReceipt">
</form>

<?php
include '../templates/footer.php';
?>

<script src="/sports-shop-final/assets/admin/js/controllers/receiptController.js"></script>

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





