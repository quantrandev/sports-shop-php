var categoryService = {
    get: function (id, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/categoryController.php?id=' + id,
            type: 'get',
            success: success,
            error: error
        });
    },
    add: function (data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/categoryController.php',
            type: 'post',
            data: {data: data},
            success: success,
            error: error
        });
    },
    edit: function (id, data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/categoryController.php?id=' + id,
            type: 'put',
            data: {data: data},
            success: success,
            error: error
        });
    },
    delete: function (id, success, error) {
        $.ajax({
            url: '/admin/categories/delete/' + id,
            type: 'delete',
            success: success,
            error: error
        });
    },
    batchDelete: function (deletedItems, success, error) {
        $.ajax({
            url: '/admin/categories/batchDelete/',
            type: 'delete',
            data: {deletedItems: deletedItems},
            success: success,
            error: error
        });
    }
}