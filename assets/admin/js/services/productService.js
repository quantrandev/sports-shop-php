var productService = {
    getProduct: function (id, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/productController.php?id=' + id + '&function=getProduct',
            type: 'get',
            success: success,
            error: error
        });
    },
    getImages: function (id, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/productController.php?id=' + id + '&function=getImages',
            type: 'get',
            success: success,
            error: error
        });
    },
    updateImages: function (id, data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/productController.php?id=' + id + '&function=updateImages',
            type: 'put',
            data: {data: data},
            success: success,
            error: error
        });
    },
    update: function (id, data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/productController.php?id=' + id + '&function=updateProduct',
            type: 'put',
            data: {data: data},
            success: success,
            error: error
        });
    }
};