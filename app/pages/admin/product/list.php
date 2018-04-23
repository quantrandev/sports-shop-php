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
                            <th>Mô tả</th>
                            <th>Ảnh</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="text-center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace js-check-item"/>
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $product->name; ?></td>
                                <td><?php echo empty($product->oldPrice) ? number_format($product->currentPrice) . " đ" : number_format($product->oldPrice) . " đ"; ?></td>
                                <td><?php echo empty($product->oldPrice) ? '<span>Không có</span>' : number_format($product->currentPrice) . " đ" ?></td>
                                <td class="text-center">
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <button class="btn btn-xs btn-primary">
                                            <i class="fa fa-pencil m-r-5"></i>
                                            Mô tả
                                        </button>
                                    </div>
                                </td>
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
                                        <button class="btn btn-xs btn-info"
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
            <div class="modal-body overflow-auto p-0">
                <div class="images-container">
                    <div class="images-track">
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                        <div class="image-wrapper">
                            <img src="/sports-shop-final/assets/images/products/new/chicago_city_skyscrapers-wallpaper-1366x768.jpg"
                                 alt="">
                            <i class="fa fa-trash"></i>
                        </div>
                    </div>
                </div>
                <div class="upload-area hide">
                    <form action="/sports-shop-final/app/controllers/uploadController.php?type=product" method="post"
                          id="my-dropzone" class="dropzone well m-0">
                        <div class="fallback">
                            <input name="file" type="file" multiple/>
                        </div>
                    </form>
                </div>
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
    // let editor = CKEDITOR.replace('txtDescription');
    // CKFinder.setupCKEditor(editor);
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





