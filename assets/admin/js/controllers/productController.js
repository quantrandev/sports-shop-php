var productController = {
    imagesModalDOM: null,
    init: function () {
        productController.imagesModalDOM = $('#images-modal');
        productController.setImagesTracksWidth();
        productController.events();
    },
    events: function () {
        productController.onViewImages();
    },
    onViewImages: function () {
        $(document).on('click', '.js-view-images', function () {
            productController.imagesModalDOM.modal();
        });
    },
    //helpers
    setImagesTracksWidth: function () {
        let imagesTrack = productController.imagesModalDOM.find('.images-track');
        let totalWidth = 0;
        productController.imagesModalDOM.find('.image-wrapper').each(function (index, value) {
            totalWidth += 175;
        });

        imagesTrack.width(totalWidth);
    }
};

$(function () {
    productController.init();
});