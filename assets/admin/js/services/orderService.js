var orderService = {
    changeShippingStatus: function (id, data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/orderController.php?id=' + id,
            type: 'put',
            data: {data: data, function: 'changeShippingStatus'},
            success: success,
            error: error
        });
    },
    changeSeenStatus: function (id, data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/orderController.php?id=' + id,
            type: 'put',
            data: {data: data, function: 'changeSeenStatus'},
            success: success,
            error: error
        });
    },
    getCustomerInfo: function (id, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/orderController.php?id=' + id + "&function=getCustomerInfo",
            type: 'get',
            success: success,
            error: error
        });
    },
    getProducts: function (id, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/orderController.php?id=' + id + "&function=getProducts",
            type: 'get',
            success: success,
            error: error
        });
    },
    changeCustomerInfo: function (id, data, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/orderController.php?id=' + id,
            type: 'put',
            data: {data: data, function: 'changeCustomerInfo'},
            success: success,
            error: error
        });
    },
    changeOrderItems: function (id, items, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/admin/orderController.php?id=' + id,
            type: 'put',
            data: {data: items, function: 'changeOrderItems'},
            success: success,
            error: error
        });
    }
};