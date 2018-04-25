<?php
session_start();

include '../../../services/connection.php';
include '../../../services/productService.php';
include '../../../services/categoryService.php';
include '../../../constants.php';
$categoryService = new CategoryService($conn);
$menus = $categoryService::menus($categoryService->allIncludedInactive());

$productService = new ProductService($conn);
if (isset($_POST["btnSubmit"])) {
    $result = $productService->add($_POST);
    if (!$result) {
        $_SESSION["flashMessage"] = "Đã thêm thành công sản phẩm " . $_POST["name"];
    }
}

include '../../../services/userService.php';
$userService = new UserService($conn);

if (!$userService->isAuthenticate())
    header("Location: ../../authentication/login.php");
if (!$userService->isAuthorize('Quản lý sản phẩm'))
    header("Location: ../../authentication/login.php");

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
                    Quản lý sản phẩm
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Thêm sản phẩm
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-9">
                    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data"
                          method="post">
                        <div class="form-horizontal">
                            <input type="hidden" id="images" name="images">
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Tên sản phẩm *</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" autofocus required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Giá gốc *</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="oldPrice" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Giá khuyến mãi</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="currentPrice">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Số lượng *</label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="quantity" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Thuộc danh mục</label>
                                <div class="col-md-6">
                                    <select name="categoryId" class="form-control">
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
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Hình ảnh</label>
                                <div class="col-md-10">
                                    <button class="btn btn-primary" type="button" data-toggle="modal"
                                            data-target="#images-upload-modal">Thêm ảnh
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Mô tả</label>
                                <div class="col-md-10"><textarea name="description" id="txtDescription"
                                                                 class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label"></label>
                                <div class="col-md-10">
                                    <button class="btn btn-success" type="submit" name="btnSubmit">Lưu sản phẩm</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<div id="images-upload-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Tải lên hình ảnh</h4>
            </div>
            <div class="modal-body overflow-auto p-0">
                <form action="/sports-shop-final/app/controllers/uploadController.php?type=product" method="post"
                      id="my-dropzone" class="dropzone well m-0">
                    <div class="fallback">
                        <input name="file" type="file" multiple/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng
                </button>
            </div>
        </div>

    </div>
</div>

<?php
include '../templates/footer.php';
?>

<script>
    let editor = CKEDITOR.replace('txtDescription');
    CKFinder.setupCKEditor(editor);
</script>
<script src="/sports-shop-final/assets/admin/js/dropzone-init.js"></script>

<?php if (isset($_SESSION["flashMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["flashMessage"];?>', "gritter-success");
    </script>
    <?php unset($_SESSION["flashMessage"]); ?>
<?php endif; ?>

<?php
include '../templates/bottom.php';
?>





