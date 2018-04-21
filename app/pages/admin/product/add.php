<?php
session_start();
include '../templates/head.php';
include '../templates/navigation.php';
include '../templates/sidebar.php';

include '../../../services/connection.php';
include '../../../services/productService.php';
include '../../../services/categoryService.php';
include '../../../constants.php';
$categoryService = new CategoryService($conn);
$menus = $categoryService::menus($categoryService->allIncludedInactive());

$childCategories = array();
foreach ($menus as $category) {
    if (count($category->children) > 0)
        $childCategories = array_merge($childCategories, $category->children);
}

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
                    <form action="" class="form-horizontal">
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Tên sản phẩm</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Giá gốc</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="oldPrice">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Giá khuyến mãi</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="currentPrice">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Số lượng</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="quantity">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Thuộc danh mục</label>
                            <div class="col-md-6">
                                <select name="categoryId" class="form-control">
                                    <?php foreach ($childCategories as $category): ?>
                                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Hình ảnh</label>
                            <div class="col-md-10">
                                <div id="dropzone" class="dropzone">
                                    <div class="fallback">
                                        <input name="images" type="file" multiple/>
                                    </div>
                                </div>
                                <div id="preview-template" class="hide">
                                    <div class="dz-preview dz-file-preview">
                                        <div class="dz-image">
                                            <img data-dz-thumbnail="" />
                                        </div>

                                        <div class="dz-details">
                                            <div class="dz-size">
                                                <span data-dz-size=""></span>
                                            </div>

                                            <div class="dz-filename">
                                                <span data-dz-name=""></span>
                                            </div>
                                        </div>

                                        <div class="dz-progress">
                                            <span class="dz-upload" data-dz-uploadprogress=""></span>
                                        </div>

                                        <div class="dz-error-message">
                                            <span data-dz-errormessage=""></span>
                                        </div>

                                        <div class="dz-success-mark">
											<span class="fa-stack fa-lg bigger-150">
												<i class="fa fa-circle fa-stack-2x white"></i>

												<i class="fa fa-check fa-stack-1x fa-inverse green"></i>
											</span>
                                        </div>

                                        <div class="dz-error-mark">
											<span class="fa-stack fa-lg bigger-150">
												<i class="fa fa-circle fa-stack-2x white"></i>

												<i class="fa fa-remove fa-stack-1x fa-inverse red"></i>
											</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Mô tả</label>
                            <div class="col-md-10"><textarea name="description" id="txtDescription"
                                                             class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

<?php
include '../templates/footer.php';
?>

<script>
    let editor = CKEDITOR.replace('txtDescription');
    CKFinder.setupCKEditor(editor);


    var myDropzone = new Dropzone('#dropzone', {
        previewTemplate: $('#preview-template').html(),
        url: "/file/post",
        thumbnailHeight: 120,
        thumbnailWidth: 120,
        maxFilesize: 10,

        //addRemoveLinks : true,
        //dictRemoveFile: 'Remove',

        dictDefaultMessage:
            '<span class="bigger-150 bolder"><i class="ace-icon fa fa-caret-right red"></i> Drop files</span> to upload \
            <span class="smaller-80 grey">(or click)</span> <br /> \
            <i class="upload-icon ace-icon fa fa-cloud-upload blue fa-3x"></i>'
        ,

        thumbnail: function (file, dataUrl) {
            if (file.previewElement) {
                $(file.previewElement).removeClass("dz-file-preview");
                var images = $(file.previewElement).find("[data-dz-thumbnail]").each(function () {
                    var thumbnailElement = this;
                    thumbnailElement.alt = file.name;
                    thumbnailElement.src = dataUrl;
                });
                setTimeout(function () {
                    $(file.previewElement).addClass("dz-image-preview");
                }, 1);
            }
        }

    });


    //simulating upload progress
    var minSteps = 6,
        maxSteps = 60,
        timeBetweenSteps = 100,
        bytesPerStep = 100000;

    myDropzone.uploadFiles = function (files) {
        var self = this;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

            for (var step = 0; step < totalSteps; step++) {
                var duration = timeBetweenSteps * (step + 1);
                setTimeout(function (file, totalSteps, step) {
                    return function () {
                        file.upload = {
                            progress: 100 * (step + 1) / totalSteps,
                            total: file.size,
                            bytesSent: (step + 1) * file.size / totalSteps
                        };

                        self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
                        if (file.upload.progress == 100) {
                            file.status = Dropzone.SUCCESS;
                            self.emit("success", file, 'success', null);
                            self.emit("complete", file);
                            self.processQueue();
                        }
                    };
                }(file, totalSteps, step), duration);
            }
        }
    }

</script>

<?php if (isset($_SESSION["flashMessage"])): ?>
    <script>
        utilities.notify("Thông báo", '<?php echo $_SESSION["flashMessage"];?>', "gritter-success");
    </script>
    <?php unset($_SESSION["flashMessage"]); ?>
<?php endif; ?>

<?php
include '../templates/bottom.php';
?>





