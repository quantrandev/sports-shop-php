var cartService = {
    add: function (productId, quantity, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/cartController.php',
            type: 'post',
            data: {id: productId, quantity: quantity, function: 'add'},
            success: success,
            error: error
        });
    },
    update: function (cartItemId, newQuantity, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/cartController.php?id=' + cartItemId,
            type: 'put',
            data: {quantity: newQuantity, function: 'update'},
            success: success,
            error: error
        });
    },
    delete: function (cartItemId, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/cartController.php?id=' + cartItemId,
            type: 'delete',
            success: success,
            error: error
        });
    },
    changeShippingMethod: function (shippingMethodId, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/cartController.php',
            type: 'post',
            data: {shippingMethodId: shippingMethodId, function: 'setShippingMethod'},
            success: success,
            error: error
        });
    },
    like: function (productId, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/cartController.php',
            type: 'post',
            data: {productId: productId, function: 'like'},
            success: success,
            error: error
        });
    },
    view: function (productId, success, error) {
        $.ajax({
            url: '/sports-shop-final/app/controllers/cartController.php',
            type: 'post',
            data: {productId: productId, function: 'view'},
            success: success,
            error: error
        });
    }
}