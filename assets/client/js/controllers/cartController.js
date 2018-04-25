var cartController = {
    quantityDOM: null,
    subtotalDOM: null,
    finalTotalDOM: null,
    init: function () {
        cartController.quantityDOM = $('.js-qty');
        cartController.subtotalDOM = $('.sub-total');
        cartController.finalTotalDOM = $('.final-total');
        cartController.events();
    },
    events: function () {
        cartController.onAdd();
        cartController.onSelectShippingMethod();
        cartController.onUpdateCart();
        cartController.onQuantityChange();
        cartController.onDeleteCartItem();
        cartController.onLikes();
    },
    onAdd: function () {
        $(document).on('click', '.js-add-cart', function () {
            var button = $(this);
            var productId = $(this).attr('data-id');
            var productName = $(this).attr('data-name');
            var quantity = $(this).siblings('.qty-input').find('input[type=number]').val() ?
                parseInt($(this).siblings('.qty-input').find('input[type=number]').val()) : 1;

            cartController.toggleButtonStatus(button, 'loading', 'Mua hàng');

            cartService.add(productId, quantity, function (res) {
                res = JSON.parse(res);
                cartController.quantityDOM.text(res.quantity);
                cartController.finalTotalDOM.text(utilities.formatThousand(res.total) + ' đ');

                if (res.error) {
                    cartController.toggleButtonStatus(button, 'available', 'Mua hàng');
                    swal(productName, res.message, "error");
                }
                else {
                    cartController.toggleButtonStatus(button, 'loaded', 'Đã thêm');
                    cartController.disableButton(button);
                    swal(productName, res.message, "success");
                }

            }, function (err) {
            });
        });
    },
    onSelectShippingMethod: function () {
        $(document).on('change', '.js-select-shipping-method', function () {
            var shippingMethodId = $(this).val();
            if ($(this).prop('checked'))
                cartService.changeShippingMethod(shippingMethodId, function (res) {
                    res = JSON.parse(res);
                    cartController.updateCart(res.quantity, res.subtotal, res.total);
                    cartController.updateShippingMethod(res.shippingMethod);
                }, function (err) {
                });
        });
    },
    onUpdateCart: function () {
        $(document).on('click', '.js-update-cart', function () {
            var button = $(this);
            var cartItemId = button.attr('data-id');
            var newQuantity = button.siblings('.js-quantity').val();
            cartController.toggleButtonStatus(button, 'loading', '');

            cartService.update(cartItemId, newQuantity, function (res) {
                res = JSON.parse(res);
                if (!res.error) {
                    cartController.toggleButtonStatus(button, 'loaded', '');
                    cartController.toggleButtonStatus(button, 'hide', '');
                    cartController.updateSingleTotal(button.closest("td").next().find('.js-single-total'), res.singleTotal);
                    cartController.updateCart(res.quantity, res.subtotal, res.total);
                }
                else
                    cartController.toggleButtonStatus(button, 'error', '');
            }, function (err) {
            });
        });
    },
    onQuantityChange: function () {
        $(document).on('change', '.js-quantity', function (e) {
            cartController.toggleButtonStatus($(this).siblings('.js-update-cart'), 'show', '');
        });
    },
    onDeleteCartItem: function () {
        $(document).on('click', '.js-delete-cart-item', function () {
            var button = $(this);
            var cartItemId = button.attr('data-id');

            cartController.animate(button.closest('tr'), 'zoomOut');
            setTimeout(function () {
                button.closest('tr').remove();
                if (cartController.isEmpty())
                    cartController.resetCartTable();
            }, 450);

            cartService.delete(cartItemId, function (res) {
                res = JSON.parse(res);
                if (!res.error)
                    cartController.updateCart(res.quantity, res.subtotal, res.total);
                else
                    swal('Có lỗi xảy ra', 'Xóa sản phẩm không thành công, vui lòng thử lại', 'error');
            }, function (err) {
                swal('Có lỗi xảy ra', 'Xóa sản phẩm không thành công, vui lòng thử lại', 'error');
            });
        });
    },
    onLikes: function () {
        $(document).on('click', '.js-likes', function () {
            let button = $(this);
            let productId = button.attr('data-product-id');
            cartController.animate(button.find('.fa'), 'zoomIn');
            if (!button.hasClass('active'))
                button.addClass('active');

            cartService.like(productId, function (res) {
                let likesCountDOM = button.closest('.product-single').find('.product-feature').find('.js-likes-count');
                cartController.animate(likesCountDOM.prev(), 'tada');
                likesCountDOM.text(res);
            }, function (error) {
            });

            cartService.view(productId, function (res) {
                let viewsCountDOM = button.closest('.product-single').find('.product-feature').find('.js-views-count');
                cartController.animate(viewsCountDOM.prev(), 'tada');
                viewsCountDOM.text(res);
            }, function (error) {
            });
        });
    },
    toggleButtonStatus: function (button, option, text) {
        switch (option) {
            case 'loading':
                button.html("<i class=\"fa fa-spinner fa-spin\"></i> " + text);
                break;
            case 'loaded':
                button.html("<i class=\"fa fa-check\"></i> " + text);
                break;
            case 'available':
                button.html("<i class=\"fa fa-shopping-cart\"></i> " + text);
                break;
            case 'error':
                button.html("<i class=\"fa fa-close\"></i> " + text);
                break;
            case 'hide':
                button.addClass('hide');
                break;
            case 'show':
                button.removeClass('hide');
                break;
        }
    },
    disableButton: function (button) {
        button.removeClass('js-add-cart');
        button.css({'cursor': 'default'});
    },
    updateShippingMethod: function (shippingMethod) {
        $('.js-shipping-method')
            .text(shippingMethod.name + ' - ' + utilities.formatThousand(shippingMethod.cost) + ' đ');
    },
    updateCart: function (quantity, subtotal, total) {
        cartController.subtotalDOM.text(utilities.formatThousand(subtotal) + ' đ');
        cartController.finalTotalDOM.text(utilities.formatThousand(total) + ' đ');
        cartController.quantityDOM.text(quantity);
    },
    updateSingleTotal: function (el, singleTotal) {
        el.text(utilities.formatThousand(singleTotal) + ' đ');
    },
    isEmpty: function () {
        if ($('.shopping-cart-table').find('tbody').find('tr').length === 0)
            return true;
        return false;
    },
    resetCartTable: function () {
        $('.shopping-cart-table')
            .find('tbody')
            .append('<tr><td colspan="5" class="text-center">Không có sản phẩm <a href="/sports-shop-final/app/pages/client">Mua hàng</a></td></tr>');
        $('.shopping-cart-table')
            .find('tfoot').remove();
        $('.btn-order').remove();
        $('.shiping-methods').closest('div').remove();
        $('.payments-methods').closest('div').remove();
        $('.billing-details').closest('div').remove();
    },
    animate: function (el, animationName) {
        el.removeClass(animationName);
        el.hide();
        el.show();
        el.addClass("animated");
        el.addClass(animationName);
        el.hide();
        el.show();
    }
};

$(function () {
    cartController.init();
});