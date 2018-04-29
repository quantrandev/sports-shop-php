var orderController = {
    shippingStatusModalDOM: null,
    customerInfoModal: null,
    productsModal: null,
    products: null,
    init: function () {
        orderController.registerConfirmations();
        // orderController.registerDataTable();
        orderController.shippingStatusModalDOM = $('#shipping-status-modal');
        orderController.customerInfoModal = $('#customer-info-modal');
        orderController.productsModal = $('#products-modal');
        orderController.events();
    },
    events: function () {
        orderController.onOpenShippingStatusModal();
        orderController.onSaveShippingStatusChange();
        orderController.onSeenStatusChange();
        orderController.onOpenCustomerInfoModal();
        orderController.onSaveCustomerInfo();
        orderController.onOpenProductsModal();
        //products events
        orderController.onQuantityChange();
        orderController.onDeleteOrderItem();
        orderController.onSaveOrderItems();
        //print events
        orderController.onPrintOrders();
        orderController.onPrintInvoices();
        orderController.onPrintAllInvoices();
    },
    registerDataTable: function () {
        $('#orders-table').DataTable({
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "language": {
                "decimal": "",
                "emptyTable": "Không có đơn hàng",
                "info": "Hiển thị _START_ đến _END_ trong _TOTAL_ đơn hàng",
                "infoEmpty": "",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Sau",
                    "previous": "Trước"
                }
            }
        });
    },
    registerConfirmations: function () {
        $('.js-delete-order').confirmation({
            rootSelector: '.js-delete-order',
            title: 'Xóa đơn hàng này',
            singleton: true,
            popout: true,
            onConfirm: orderController.deleteCategory
        });
        $('.js-batch-delete').confirmation({
            rootSelector: '.js-batch-delete',
            title: 'Xóa những đơn hàng đã chọn',
            singleton: true,
            popout: true,
            onConfirm: orderController.batchDeleteCategory
        });
    },
    //events
    onOpenShippingStatusModal: function () {
        $(document).on('click', '.js-update-shipping-status', function () {
            var button = $(this);
            orderController.shippingStatusModalDOM.find('#js-sl-shipping-status').val(button.attr('data-ship-id'));
            orderController.shippingStatusModalDOM.find('#orderId').val(button.attr('data-order-id'));
            orderController.shippingStatusModalDOM.modal();
        });
    },
    onSaveShippingStatusChange: function () {
        $('#shipping-status-modal').on('click', '.js-save-changes', function () {
            var button = $(this);
            orderController.toggleButtonStatus(button, 'loading', 'Lưu thay đổi');

            var modalData = orderController.getShippingStatusModalData();
            orderService.changeShippingStatus(modalData.id, modalData.data, function (res) {
                res = JSON.parse(res);

                if (res.error)
                    utilities.notify("Thông báo", "Có lỗi xảy ra", 'gritter-error', false);
                else {
                    var statusDOM = $('button.js-update-shipping-status[data-order-id=' + modalData.id + ']').next();
                    statusDOM.prev().attr('data-ship-id', modalData.data.shippingStatus);
                    statusDOM.text(modalData.name);
                    var statusClass = "";
                    switch (modalData.name) {
                        case 'Mới đặt hàng':
                            statusClass = "text-warning";
                            break;
                        case 'Đang đóng gói':
                            statusClass = "text-info";
                            break;
                        case 'Đang vận chuyển':
                            statusClass = "text-primary";
                            break;
                        case 'Đã nhận hàng':
                            statusClass = "text-success";
                            break;
                        case 'Đã trả hàng':
                            statusClass = "text-danger";
                            break;
                    }
                    statusDOM
                        .attr('class', statusClass);
                    orderController.toggleButtonStatus(button, 'text-only', 'Lưu thay đổi');
                }
            }, function (error) {
            });
        });
    },
    onSeenStatusChange: function () {
        $("#orders-table").on('click', 'tbody tr', function () {
            let row = $(this);
            let orderCode = row.attr('data-order-id');
            let isSeen = row.attr('data-seen');
            if (isSeen == 1)
                return;

            let data = {
                isSeen: 1
            };
            row.attr('data-seen', 1);
            orderService.changeSeenStatus(orderCode, data, function (res) {
                res = JSON.parse(res);
                if (!res.error) {
                    row.removeClass('info');
                    let seenStatusCell = row.find('td.seenStatus');
                    seenStatusCell.children('span').attr('class', 'text-success');
                    seenStatusCell.children('span').html('<i class="fa fa-check m-r-5"></i>' + res.seenAt);
                    orderController.animate(seenStatusCell.children('span'), 'fadeIn');
                }
            }, function (err) {
            });
        });
    },
    onOpenCustomerInfoModal: function () {
        $(document).on('click', '.js-edit-order', function () {
            let button = $(this);
            orderController.toggleButtonStatus(button, 'loading', 'Sửa');

            //call service
            orderService.getCustomerInfo(button.attr('data-id'), function (res) {
                res = JSON.parse(res);
                orderController.setDataForCustomerInfoModal(res);

                orderController.customerInfoModal.modal();
                orderController.toggleButtonStatus(button, 'edited', 'Sửa');
            }, function (err) {
            });

            // orderController.customerInfoModal.modal();
        });
    },
    onSaveCustomerInfo: function () {
        orderController.customerInfoModal.on('click', '.js-save-changes', function () {
            let button = $(this);
            orderController.toggleButtonStatus(button, 'loading', 'Lưu thay đổi');

            let dataFromCustomerInfoModal = orderController.getDataFromCustomerInfoModal();
            //call service
            orderService.changeCustomerInfo(dataFromCustomerInfoModal.code, dataFromCustomerInfoModal.data, function (res) {
                res = JSON.parse(res);
                //update DOM
                if (!res.error) {
                    let row = $('tr[data-order-id=' + dataFromCustomerInfoModal.code + ']');
                    let detailRow = row.next();

                    row.find('td.js-customer-name').text(dataFromCustomerInfoModal.data.customerName);
                    row.find('td.js-customer-address').text(dataFromCustomerInfoModal.data.customerAddress);
                    detailRow.find('span.js-customer-mobile').text(dataFromCustomerInfoModal.data.customerMobile);
                    detailRow.find('span.js-note').text(dataFromCustomerInfoModal.data.note);

                    //notify
                    utilities.notify('Thông báo', 'Đã cập nhật thành công', 'gritter-success', false);
                }
                orderController.toggleButtonStatus(button, 'text-only', 'Lưu thay đổi');
                orderController.customerInfoModal.modal('hide');
            }, function (err) {
            });
        });
    },
    onOpenProductsModal: function () {
        $(document).on('click', '.js-view-product', function () {
            let button = $(this);
            orderController.toggleButtonStatus(button, 'loading', 'Sản phẩm');

            orderService.getProducts(button.attr('data-id'), function (res) {
                res = JSON.parse(res);

                orderController.products = res.items;
                orderController.setDataForProductModal(res);
                orderController.productsModal.modal();
                orderController.toggleButtonStatus(button, 'search', 'Sản phẩm');
            }, function (err) {
            });
        });
    },
    //products events
    onQuantityChange: function () {
        $('#products-modal').on('change', '.js-qty', function () {
            let id = $(this).closest('tr').attr('data-id');
            let newQuantity = $(this).val();
            orderController.updateQuantity(id, newQuantity);
        });
    },
    onDeleteOrderItem: function () {
        $('#products-modal').on('click', '.js-delete-order-item', function () {
            let table = $(this).closest('tbody');
            let id = $(this).closest('tr').attr('data-id');
            //remove DOM
            let deletedRow = $(this).closest('tr');
            orderController.animate(deletedRow, 'zoomOut');
            setTimeout(() => {
                deletedRow.remove();
                //check if no items left

                if (table.find('tr').length === 0)
                    table.append('<tr><td colspan="5" class="text-center">Không có sản phẩm trong đơn hàng này</td></tr>');
            }, 450);

            //actually delete order item
            orderController.deleteOrderItem(id);
        });
    },
    onSaveOrderItems: function () {
        orderController.productsModal.on('click', '.js-save-changes', function () {
            let button = $(this);
            let orderCode = button.attr('data-order-id');
            orderController.toggleButtonStatus(button, 'loading', 'Lưu thay đổi');

            orderService.changeOrderItems(orderCode, orderController.products, function (res) {
                res = JSON.parse(res);

                if (!res.error) {
                    orderController.toggleButtonStatus(button, 'text-only', 'Lưu thay đổi');
                    orderController.productsModal.modal('hide');
                    utilities.notify('Thông báo', 'Đã cập nhật thành công', 'gritter-success', false);
                }
                else
                    utilities.notify('Thông báo', 'Có lỗi xảy ra, vui lòng thử lại', 'gritter-error', false);
            }, function (err) {
            });
        });
    },
    onPrintOrders: function () {
        $(document).on('click', '.js-print-orders', function () {
            $('form#printOrders').submit();
        });
    },
    onPrintInvoices: function () {
        $(document).on('click', '.js-print-invoices', function () {
            let selectedOrders = orderController.getSelected();
            if (!selectedOrders.length) {
                utilities.notify('Thông báo', 'Vui lòng chọn ít nhất 1 đơn hàng', 'gritter-error', false);
                return;
            }

            $('form#printInvoices').find('input#orders').val(selectedOrders);
            $('form#printInvoices').submit();
        });
    },
    onPrintAllInvoices: function () {
        $(document).on('click', '.js-print-invoices-all', function () {
            $('form#printAllInvoices').submit();
        });
    },
    //helpers
    setDataForCustomerInfoModal: function (data) {
        orderController.customerInfoModal.find('#code').val(data.code);
        orderController.customerInfoModal.find('#customerName').val(data.customerName);
        orderController.customerInfoModal.find('#customerAddress').val(data.customerAddress);
        orderController.customerInfoModal.find('#customerMobile').val(data.customerMobile);
        orderController.customerInfoModal.find('#note').val(data.note);
    },
    getDataFromCustomerInfoModal: function () {
        return {
            code: orderController.customerInfoModal.find('#code').val(),
            data: {
                customerName: orderController.customerInfoModal.find('#customerName').val(),
                customerAddress: orderController.customerInfoModal.find('#customerAddress').val(),
                customerMobile: orderController.customerInfoModal.find('#customerMobile').val(),
                note: orderController.customerInfoModal.find('#note').val()
            }
        };
    },
    getShippingStatusModalData: function () {
        var shippingStatusName = "";
        orderController
            .shippingStatusModalDOM
            .find("#js-sl-shipping-status")
            .find('option')
            .each(function (index, value) {
                if ($(value).prop('selected')) {
                    shippingStatusName = $(value).text();
                }
            });

        var shippingStatusId = orderController.shippingStatusModalDOM.find('#js-sl-shipping-status').val();
        var orderId = orderController.shippingStatusModalDOM.find('#orderId').val();
        return {
            id: orderId,
            name: shippingStatusName,
            data:
                {
                    shippingStatus: shippingStatusId
                }
        };
    },
    setDataForProductModal: function (data) {
        //set orderCode for save changes button
        orderController.productsModal.find('.js-save-changes').attr('data-order-id', data.code);

        let table = orderController.productsModal.find('table').find('tbody');
        //clear table
        table.html("");

        if (data.items.length == 0) {
            table.append('<tr><td colspan="5" class="text-center">Không có sản phẩm trong đơn hàng này</td></tr>');
            return;
        }

        for (let i = 0; i < data.items.length; i++) {
            let row = `
                <tr data-id=\"` + data.items[i].id + `\">
                    <td><img src=\"/sports-shop-final/assets` + data.items[i].image + `\" /></td>
                    <td>` + data.items[i].name + `</td>
                    <td>` + utilities.formatThousand(data.items[i].price) + ` đ</td>
                    <td class=\"w-100\">
                        <input type=\"number\" class=\"form-control js-qty\" min=\"1\" value=\"` + data.items[i].quantity + `\"/>
                    </td>
                    <td class=\"text-center\"><button class=\"btn btn-minier btn-danger js-delete-order-item\"><i class=\"fa fa-trash\"></i></button></td>
                </tr>
            `;
            table.append(row);
        }
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
    },
    animate: function (el, animationName) {
        el.addClass("animated");
        el.addClass(animationName);
        el.hide();
        el.show();
    },
    //products modal helpers
    updateQuantity: function (productId, newQuantity) {
        let updatedItem = orderController.products.filter(value => {
            return value.id == productId;
        })[0];

        updatedItem.quantity = newQuantity;
        updatedItem.updated = true;
    },
    deleteOrderItem: function (id) {
        for (let i = 0; i < orderController.products.length; i++) {
            if (orderController.products[i].id == id)
                orderController.products[i].deleted = true;
        }
    },
    //orders print helpers
    getSelected: function () {
        let selectedItem = [];
        $('.js-check-item').each(function (index, value) {
            if (!$(value).prop('checked'))
                return;

            selectedItem.push($(value).closest('tr').attr('data-order-id'));
        });

        return selectedItem;
    }
};

$(function () {
    orderController.init();
});