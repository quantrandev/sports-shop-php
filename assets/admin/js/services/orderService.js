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
    }
};