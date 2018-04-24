<?php
session_start();
include '../templates/head.php';
include '../templates/navigation.php';
include '../templates/sidebar.php';

include '../../../services/connection.php';
include '../../../services/productService.php';
include '../../../services/categoryService.php';
include '../../../services/imageService.php';
include '../../../constants.php';

$categoryService = new CategoryService($conn);
$menus = $categoryService::menus($categoryService->allIncludedInactive());

$productService = new ProductService($conn);
$results = $productService->search(1, 10, $_GET);

$products = $results["products"];
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
                        Danh sách sản phẩm
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="col-md-9">
                    <div class="clear-fix">
                        <form action="" class="col-md-12 p-0" id="frm-search">
                            <div class="col-md-3">
                                <label for="">Danh mục</label>
                                <div>
                                    <select name="category[]" class="form-control multiselect-category" multiple>
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
                            <div class="col-md-2">
                                <label for="" class="visible-hidden">dsadsa</label>
                                <div class="form-group">
                                    <button class="btn btn-sm btn-primary">
                                        <i class="fa fa-search"></i>
                                        Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="" class="visible-hidden">dsadsa</label>
                    <div class="form-group">
                        <button class="btn btn-sm btn-danger pull-right js-batch-delete hide">
                            <i class="fa fa-trash"></i>
                            Xóa
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="products-table">
                        <thead>
                        <tr>
                            <th class="text-center">
                                <?php if (count($products) > 0): ?>
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-all"/>
                                        <span class="lbl"></span>
                                    </label>
                                <?php endif; ?>
                            </th>
                            <th>Tên sản phẩm</th>
                            <th>Giá gốc</th>
                            <th>Giá khuyến mãi</th>
                            <th>Ảnh</th>
                            <th>Danh mục</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr data-product-id="<?php echo $product->id; ?>">
                                <td class="text-center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-item"/>
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td class="p-name"><?php echo $product->name; ?></td>
                                <td class="p-old-price"><?php echo empty($product->oldPrice) ? number_format($product->currentPrice) . " đ" : number_format($product->oldPrice) . " đ"; ?></td>
                                <td class="p-current-price"><?php echo empty($product->oldPrice) ? '<span>Không có</span>' : number_format($product->currentPrice) . " đ" ?></td>
                                <td class="text-center">
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <button class="btn btn-xs btn-primary js-view-images">
                                            <i class="fa fa-image m-r-5"></i>
                                            Ảnh
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <button class="btn btn-xs btn-primary js-edit-category"
                                                data-category-id="<?php echo $product->categoryId; ?>">
                                            <i class="fa fa-bars m-r-5"></i>
                                            Danh mục
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <button class="btn btn-xs btn-info js-edit"
                                                data-id="<?php echo $product->id; ?>">
                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                            Sửa
                                        </button>
                                        <button class="btn btn-xs btn-danger">
                                            <i class="ace-icon fa fa-trash bigger-120"></i>
                                            Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
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
                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng
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
                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng
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
                        <label for="" class="col-md-2 control-label">Tên sản phẩm</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="productName">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Giá gốc</label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="productOldPrice">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Giá khuyến mãi</label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="productCurrentPrice">
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
                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi
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

<?php
include '../templates/bottom.php';
?>





