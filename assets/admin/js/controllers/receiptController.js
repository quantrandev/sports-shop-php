var receiptController = {
    frmAddProduct: null,
    frmDeleteProduct: null,
    frmUpdateProduct: null,
    frmAddReceipt: null,
    init: function () {
        receiptController.frmAddProduct = $('#frm-add-product');
        receiptController.frmDeleteProduct = $('#frm-delete-product');
        receiptController.frmUpdateProduct = $('#frm-update-product');
        receiptController.frmAddReceipt = $('#frm-add-receipt');
        receiptController.events();
    },
    events: function () {
        receiptController.onAddProduct();
        receiptController.onDeleteProduct();
        receiptController.onUpdateProduct();
        receiptController.onSaveReceipt();
        receiptController.onInputChange();
    },
    onAddProduct: function () {
        $(document).on('click', '.js-add-product', function () {
            receiptController.frmAddProduct.find('.product').val($(this).closest('tr').attr('data-product-id'));
            receiptController.frmAddProduct.submit();
        });
    },
    onDeleteProduct: function () {
        $(document).on('click', '.js-delete', function () {
            receiptController.frmDeleteProduct.find('.product').val($(this).closest('tr').attr('data-id'));
            receiptController.frmDeleteProduct.submit();
        });
    },
    onUpdateProduct: function () {
        $(document).on('click', '.js-save-changes', function () {
            receiptController.frmUpdateProduct.find('.product').val($(this).closest('tr').attr('data-id'));
            receiptController.frmUpdateProduct.find('.qty').val($(this).closest('tr').find('.js-qty').val());
            receiptController.frmUpdateProduct.find('.price').val($(this).closest('tr').find('.js-price').val());
            receiptController.frmUpdateProduct.submit();
        });
    },
    onSaveReceipt: function () {
        $(document).on('click', '.js-save-receipt', function () {
            receiptController.frmAddReceipt.submit();
        });
    },
    onInputChange: function () {
        $(document).on('input', 'input', function () {
            if (!$(this).closest('tr').hasClass('info'))
                $(this).closest('tr').addClass('info');
        });
    }
};

$(function () {
    receiptController.init();
});