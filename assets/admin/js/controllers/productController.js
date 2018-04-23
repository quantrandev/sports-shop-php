var productController = {
    imagesModalDOM: null,
    deletedImages: null,
    init: function () {
        productController.imagesModalDOM = $('#images-modal');
        productController.deletedImages = [];
        productController.events();
    },
    events: function () {
        productController.onViewImages();
        productController.onToggleUpload();
        productController.onImagesModalClose();
        productController.onDeleteImage();
    },
    onViewImages: function () {
        $(document).on('click', '.js-view-images', function () {
            let button = $(this);
            let productId = $(this).closest('tr').attr('data-product-id');
            productController.imagesModalDOM.find('input#productId').val(productId);

            productController.toggleButtonStatus(button, 'loading', 'Ảnh')
            productService.getImages(productId, function (res) {
                res = JSON.parse(res);
                productController.setDataForImagesContainer(res);
                productController.setImagesTracksWidth();

                productController.toggleButtonStatus(button, 'image', 'Ảnh')
            }, function (err) {
            });

            productController.imagesModalDOM.modal();
        });
    },
    onToggleUpload: function () {
        productController.imagesModalDOM.on('click', '.js-toggle-upload', function () {
            productController.imagesModalDOM.find('.upload-area').toggleClass('hide');
        });
    },
    onDeleteImage: function () {
        productController.imagesModalDOM.on('click', '.js-delete', function () {
            let deleteButton = $(this);
            let id = deleteButton.attr('data-id');

            utilities.animate(deleteButton.closest('.image-wrapper'), 'zoomOut');
            setTimeout(() => {
                deleteButton.closest('.image-wrapper').remove();
            }, 450);

            productController.deleteImage(id);
        });
    },
    onImagesModalClose: function () {
        productController.imagesModalDOM.on('hide.bs.modal', function () {
            for (let i = 0; i < files.length; i++) {
                myDropzone.removeFile(files[i]);
            }
        });
    },
    setDataForImagesContainer: function (images) {
        let imagesContainer = productController.imagesModalDOM.find('.images-track');
        imagesContainer.html("");
        for (let i = 0; i < images.length; i++) {
            let item = `
                <div class=\"image-wrapper\">
                  <img src=\"/sports-shop-final/assets` + images[i].source + `\"
                           alt=\"\">
                  <div class="btn-delete">
                    <i class=\"fa fa-close js-delete\" data-id=\"` + images[i].id + `\"></i>
                  </div>
                </div>
            `;

            imagesContainer.append(item);
        }
    },
    //helpers
    setImagesTracksWidth: function () {
        let imagesTrack = productController.imagesModalDOM.find('.images-track');
        let totalWidth = 0;
        productController.imagesModalDOM.find('.image-wrapper').each(function (index, value) {
            totalWidth += 220;
        });

        imagesTrack.width(totalWidth);
    },
    deleteImage: function (id) {
        productController.deletedImages.push(id);
    },
    toggleButtonStatus: function (button, option, text) {
        switch (option) {
            case 'loading':
                button.html("<i class=\"ace-icon bigger-120 fa fa-spinner fa-spin\"></i> " + text);
                break;
            case 'loaded':
                button.html("<i class=\"ace-icon bigger-120 fa fa-check\"></i> " + text);
                break;
            case 'edited':
                button.html("<i class=\"ace-icon bigger-120 fa fa-pencil\"></i> " + text);
                break;
            case 'search':
                button.html("<i class=\"ace-icon bigger-120 fa fa-search\"></i> " + text);
                break;
            case 'error':
                button.html("<i class=\"ace-icon bigger-120 fa fa-close\"></i> " + text);
                break;
            case 'image':
                button.html("<i class=\"ace-icon bigger-120 fa fa-image\"></i> " + text);
                break;
            case 'hide':
                button.addClass('hide');
                break;
            case 'show':
                button.removeClass('hide');
                break;
            case 'text-only':
                button.html(text);
                break;
        }
    }
};

$(function () {
    productController.init();
});