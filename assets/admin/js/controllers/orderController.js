var orderController = {
    shippingStatusModalDOM: null,
    customerInfoModal: null,
    init: function () {
        orderController.registerConfirmations();
        // orderController.registerDataTable();
        orderController.shippingStatusModalDOM = $('#shipping-status-modal');
        orderController.customerInfoModal = $('#customer-info-modal');
        orderController.events();
    },
    events: function () {
        orderController.onOpenShippingStatusModal();
        orderController.onSaveShippingStatusChange();
        orderController.onSeenStatusChange();
        orderController.onOpenCustomerInfoModal();
        orderController.onSaveCustomerInfo();
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
    }
};

$(function () {
    orderController.init();
});