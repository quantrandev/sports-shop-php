<?php
session_start();
include '../templates/head.php';
include '../templates/navigation.php';
include '../templates/sidebar.php';

include '../../../services/connection.php';
include '../../../services/categoryService.php';
$categoryService = new CategoryService($conn);
$categories = $categoryService::menus($categoryService->allIncludedInactive());

?>

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Trang quản trị</a>
                </li>
                <li class="active">Quản lý danh mục</li>
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
                    Quản lý danh mục
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Danh sách danh mục
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <!--page content-->
            <div class="row">
                <div class="clear-fix">
                    <div class="pull-right form-group">
                        <button class="btn btn-minier btn-primary" data-toggle="modal" data-target="#add-modal">
                            <i class="fa fa-plus"></i>
                            Thêm
                        </button>
                        <button class="btn btn-minier btn-danger js-batch-delete hide">
                            <i class="fa fa-trash"></i>
                            Xóa
                        </button>
                    </div>
                </div>

                <table id="categories-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace js-check-all"/>
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>Tên danh mục</th>
                        <th>Tình trạng</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="center">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace js-check-item"
                                           data-id="<?php echo $category->id ?>"/>
                                    <span class="lbl"></span>
                                </label>
                            </td>

                            <td>

                                <?php if (count($category->children) > 0): ?>
                                    <span role="button" class="togglable"><i
                                                class="fa fa-angle-down"></i> <?php echo $category->name; ?></span>
                                <?php else: ?>
                                    <?php echo $category->name; ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($category->isActive): ?>
                                    <span class="label label-success arrowed">Kích hoạt</span>
                                <?php else: ?>
                                    <span class="label label-danger arrowed">Khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="hidden-sm hidden-xs btn-group">
                                    <button class="btn btn-xs btn-info js-open-edit-modal"
                                            data-id="<?php echo $category->id ?>">
                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                        Sửa
                                    </button>

                                    <button class="btn btn-xs btn-danger js-delete-category"
                                            data-id="<?php echo $category->id ?>">
                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php if (count($category->children) > 0): ?>
                            <tr class="detail-row">
                                <td colspan="5">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th class="center">
                                                <label class="pos-rel">
                                                    <input type="checkbox" class="ace js-check-all"/>
                                                    <span class="lbl"></span>
                                                </label>
                                            </th>
                                            <th>Tên danh mục</th>
                                            <th>Tình trạng</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($category->children as $child): ?>
                                            <tr>
                                                <td class="center">
                                                    <label class="pos-rel">
                                                        <input type="checkbox" class="ace js-check-item"
                                                               data-id="<?php echo $child->id ?>"/>
                                                        <span class="lbl"></span>
                                                    </label>
                                                </td>

                                                <td>
                                                    <?php echo $child->name; ?>
                                                </td>

                                                <td>
                                                    <?php if ($child->isActive): ?>
                                                        <span class="label label-success arrowed">Kích hoạt</span>
                                                    <?php else: ?>
                                                        <span class="label label-danger arrowed">Khóa</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="hidden-sm hidden-xs btn-group">
                                                        <button class="btn btn-xs btn-info js-open-edit-modal"
                                                                data-id="<?php echo $child->id ?>">
                                                            <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                            Sửa
                                                        </button>

                                                        <button class="btn btn-xs btn-danger js-delete-category"
                                                                data-id="<?php echo $child->id ?>">
                                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                            Xóa
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div id="add-modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Thêm danh mục</h4>
                            </div>
                            <div class="modal-body">
                                <form action="" class="form-horizontal frm-add-category">
                                    <div class="form-group">
                                        <label for="" class="col-md-3 control-label">Tên danh mục</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="category_name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 control-label">Kích hoạt</label>
                                        <div class="col-md-9">
                                            <label style="margin-top: 10px">
                                                <input name="switch-field-1" class="ace ace-switch" type="checkbox"
                                                       id="category_is_active"/>
                                                <span class="lbl"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 control-label">Danh mục cha</label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="category_parent">
                                                <option>Chọn danh mục cha</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary js-save-new-category">Thêm mới</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="edit-modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Chỉnh sửa danh mục</h4>
                            </div>
                            <div class="modal-body">
                                <form action="" class="form-horizontal">
                                    <input type="hidden" id="category_id">
                                    <div class="form-group">
                                        <label for="" class="col-md-3 control-label">Tên danh mục</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" id="category_name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 control-label">Kích hoạt</label>
                                        <div class="col-md-9">
                                            <label style="margin-top: 10px">
                                                <input name="switch-field-1" class="ace ace-switch" type="checkbox"
                                                       id="category_is_active"/>
                                                <span class="lbl"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-md-3 control-label">Danh mục cha</label>
                                        <div class="col-md-9">
                                            <select id="category_parent" class="form-control"></select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary js-save-changes">Lưu thay đổi</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- /.page-content -->
    </div>
</div>

<?php
include '../templates/footer.php';
?>

<script src="/sports-shop-final/assets/admin/js/services/categoryService.js"></script>
<script src="/sports-shop-final/assets/admin/js/controllers/categoryController.js"></script>

<?php if (isset($_SESSION["flashMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["flashMessage"];?>', "gritter-success");
    </script>
    <?php unset($_SESSION["flashMessage"]); ?>
<?php endif; ?>

<?php
include '../templates/bottom.php';
?>





