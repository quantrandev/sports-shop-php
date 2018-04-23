var productService = {
    getImages: function (id, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/productController.php?id=' + id + '&function=getImages',
            type: 'get',
            success: success,
            error: error
        });
    }
};