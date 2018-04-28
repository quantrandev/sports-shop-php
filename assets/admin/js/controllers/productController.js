var productController = {
    imagesModalDOM: null,
    categoryModal: null,
    productModal: null,
    saleModal: null,
    saleAllModal: null,
    deletedImages: null,
    selectedItems: [],
    init: function () {
        productController.imagesModalDOM = $('#images-modal');
        productController.categoryModal = $('#category-modal');
        productController.productModal = $('#product-modal');
        productController.saleModal = $('#sale-modal');
        productController.saleAllModal = $('#sale-all-modal');
        productController.deletedImages = [];
        productController.registerConfirmations();
        productController.events();
    },
    events: function () {
        productController.onViewImages();
        productController.onToggleUpload();
        productController.onImagesModalClose();
        productController.onDeleteImage();
        productController.onSaveUpdatedImages();
        productController.onChangeCategory();
        productController.onSaveCategoryChange();
        productController.onEditProduct();
        productController.onSaveProductChange();
        productController.onOpenSaleModal();
        productController.onSaveSaleChanges();
    },
    registerConfirmations: function () {
        $('.js-delete-product').confirmation({
            rootSelector: '.js-delete-product',
            title: 'Xóa sản phẩm này',
            singleton: true,
            popout: true,
            onConfirm: productController.deleteCategory
        });
        $('.js-batch-delete').confirmation({
            rootSelector: '.js-batch-delete',
            title: 'Xóa những sản phẩm đã chọn',
            singleton: true,
            popout: true,
            onConfirm: productController.batchDeleteCategory
        });
    },
    onViewImages: function () {
        $(document).on('click', '.js-view-images', function () {
            let button = $(this);
            let productId = $(this).closest('tr').attr('data-product-id');
            productController.imagesModalDOM.find('input#productId').val(productId);

            productController.toggleButtonStatus(button, 'loading', 'Ảnh')
            productService.getImages(productId, function (res) {
                res = JSON.parse(res);
                productController.setDataForImagesContainer(res);
                productController.setImagesTracksWidth();

                productController.toggleButtonStatus(button, 'image', 'Ảnh')
            }, function (err) {
            });

            productController.imagesModalDOM.modal();
        });
    },
    onToggleUpload: function () {
        productController.imagesModalDOM.on('click', '.js-toggle-upload', function () {
            productController.imagesModalDOM.find('.upload-area').toggleClass('hide');
        });
    },
    onDeleteImage: function () {
        productController.imagesModalDOM.on('click', '.js-delete', function () {
            let deleteButton = $(this);
            let id = deleteButton.attr('data-id');

            utilities.animate(deleteButton.closest('.image-wrapper'), 'zoomOut');
            setTimeout(() => {
                deleteButton.closest('.image-wrapper').remove();
            }, 450);

            productController.deleteImage(id);
        });
    },
    onImagesModalClose: function () {
        productController.imagesModalDOM.on('hide.bs.modal', function () {
            for (let i = 0; i < files.length; i++) {
                myDropzone.removeFile(files[i]);
                productController.clearImages();
            }
        });
    },
    onSaveUpdatedImages: function () {
        productController.imagesModalDOM.on('click', '.js-save-changes', function () {
            let button = $(this);

            let productId = productController.imagesModalDOM.find('form').find('#productId').val();
            let addedImages = productController.imagesModalDOM.find('form').find('#images').val();
            let uploadData = {
                deletedImages: productController.deletedImages,
                addedImages: addedImages
            };
            productController.toggleButtonStatus(button, 'loading', 'Lưu thay đổi');
            productService.updateImages(productId, uploadData, function (res) {
                productController.clearImages();
                res = JSON.parse(res);

                //update DOM
                productController.toggleButtonStatus(button, 'text-only', 'Lưu thay đổi');

                if (!res.error) {
                    productController.imagesModalDOM.modal('hide');
                    utilities.notify('Thông báo', 'Đã cập nhật thành công', 'gritter-success', false);
                }
                else
                    utilities.notify('Thông báo', 'Có lỗi xảy ra', 'gritter-error', false);
            }, function (error) {
            });
        });
    },
    onChangeCategory: function () {
        $(document).on('click', '.js-edit-category', function () {
            let button = $(this);
            let productId = button.closest('tr').attr('data-product-id');
            let currentCategoryId = button.attr('data-category-id');
            productController.categoryModal.find('form').find('#productId').val(productId);
            productController.categoryModal.find('form').find('#currentCategory').val(currentCategoryId);
            productController.categoryModal.find('form').find('#js-sl-category').val(currentCategoryId);

            productController.categoryModal.modal();

        });
    },
    onSaveCategoryChange: function () {
        productController.categoryModal.on('click', '.js-save-changes', function () {
            let updatedProduct = productController.isCategoryChange();
            if (updatedProduct) {
                let updateData = {
                    categoryId: productController.categoryModal.find('form').find('#js-sl-category').val()
                };

                //call service
                productService.update(updatedProduct, updateData, function (res) {
                    res = JSON.parse(res);
                    if (!res.error) {
                        utilities.notify('Thông báo', 'Đã cập nhật thành công', 'gritter-success', false);
                        productController.categoryModal.modal('hide');
                    }
                    else
                        utilities.notify('Thông báo', 'Có lỗi xảy ra', 'gritter-error', false);
                }, function (err) {
                });

                $('#products-table').find('tr[data-product-id=' + updatedProduct + ']').remove();
            }
        });
    },
    onEditProduct: function () {
        $(document).on('click', '.js-edit', function () {
            let button = $(this);
            let productId = button.closest('tr').attr('data-product-id');
            productController.toggleButtonStatus(button, 'loading', 'Thông tin khác');
            productService.getProduct(productId, function (res) {
                res = JSON.parse(res);

                productController.setDataForEditProductModal(res);
                productController.toggleButtonStatus(button, 'edited', 'Thông tin khác');
            }, function (err) {
            });

            productController.productModal.modal();
        });
    },
    onSaveProductChange: function () {
        productController.productModal.on('click', '.js-save-changes', function () {
            let editData = productController.getDataFromEditProductModal();
            productService.update(editData.id, editData.data, function (res) {
                res = JSON.parse(res);

                if (!res.error) {
                    window.location.reload();
                }
                else
                    utilities.notify('Thông báo', 'Có lỗi xảy ra', 'gritter-error', false);
            }, function (error) {
            });
        });
    },
    onOpenSaleModal: function () {
        $(document).on('click', '.js-sale', function () {
            productController.selectedItems = productController.getSelected();
            if (!productController.selectedItems.length) {
                utilities.notify('Thông báo', 'Vui lòng chọn ít nhất 1 sản phẩm', 'gritter-error', false);
                return;
            }

            if (!$(this).hasClass('js-unsale')) {
                productController.saleModal.modal();
            }
            else {
                bootbox.confirm({
                    message: 'Hủy giảm giá các sản phẩm hiện tại',
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-success btn-sm'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-danger btn-sm'
                        }
                    },
                    callback: function (result) {
                        if (result)
                            productService.unsale(productController.selectedItems, function (res) {
                                res = JSON.parse(res);

                                if (!res.error)
                                    window.location.reload();
                                else
                                    utilities.notify('Thông báo', 'Có lỗi xảy ra, vui lòng thử lại', 'gritter-error', false);
                            }, function (error) {
                            });
                    }
                });
            }
        });
    },
    onSaveSaleChanges: function () {
        productController.saleModal.on('click', '.js-save-changes', function () {
            let saleData = productController.getSaleModalData();
            if (!saleData) {
                utilities.notify('Thông báo', 'Vui lòng điền đầy đủ thông tin', 'gritter-error', false);
                return;
            }

            productService.updateSale(productController.selectedItems, saleData.range, saleData.percentage, function (res) {
                res = JSON.parse(res);

                if (!res.error)
                    window.location.reload();
                else
                    utilities.notify('Thông báo', 'Có lỗi xảy ra, vui lòng thử lại', 'gritter-error', false);
            }, function (error) {
            });
        });
        productController.saleAllModal.on('click', '.js-save-changes', function (e) {
            e.preventDefault();
            let saleData = productController.getSaleAllModalData();
            if (!saleData) {
                utilities.notify('Thông báo', 'Vui lòng điền đầy đủ thông tin', 'gritter-error', false);
                return;
            }

            $(this).closest('form').submit();
        });
    },
    setDataForImagesContainer: function (images) {
        let imagesContainer = productController.imagesModalDOM.find('.images-track');
        imagesContainer.html("");
        for (let i = 0; i < images.length; i++) {
            let src = '/sports-shop-final/assets' + images[i].source;
            let item = `
                <div class=\"image-wrapper\" style=\"background-image: url(` + src + `)\">
                  <div class="btn-delete">
                    <i class=\"fa fa-close js-delete\" data-id=\"` + images[i].id + `\"></i>
                  </div>
                </div>
            `;

            imagesContainer.append(item);
        }
    },
    //helpers
    setImagesTracksWidth: function () {
        let imagesTrack = productController.imagesModalDOM.find('.images-track');
        let totalWidth = 0;
        productController.imagesModalDOM.find('.image-wrapper').each(function (index, value) {
            totalWidth += 170;
        });

        imagesTrack.width(totalWidth);
    },
    setDataForEditProductModal: function (data) {
        productController.productModal.find('#productId').val(data.id);
        productController.productModal.find('#productName').val(data.name);
        productController.productModal.find('#productbasicPrice').val(data.basicPrice);
        CKEDITOR.instances['productDescription'].setData(data.description);
    },
    getDataFromEditProductModal: function () {
        return {
            id: productController.productModal.find('#productId').val(),
            data: {
                name: productController.productModal.find('#productName').val(),
                basicPrice: productController.productModal.find('#productbasicPrice').val(),
                description: CKEDITOR.instances['productDescription'].getData()
            }
        };
    },
    deleteImage: function (id) {
        productController.deletedImages.push(id);
    },
    clearImages: function () {
        productController.deletedImages = [];
        productController.imagesModalDOM.find('form').find('#images').val('');
    },
    isCategoryChange: function () {
        let currentCategory = productController.categoryModal.find('form').find('#currentCategory').val();
        let newCategory = productController.categoryModal.find('form').find('#js-sl-category').val();
        if (currentCategory !== newCategory)
            return productController.categoryModal.find('form').find('#productId').val();

        return null;
    },
    getSelected: function () {
        let selectedItem = [];
        $('.js-check-item').each(function (index, value) {
            if (!$(value).prop('checked'))
                return;

            selectedItem.push($(value).closest('tr').attr('data-product-id'));
        });

        return selectedItem;
    },
    getSaleModalData: function () {
        let salePercentage = productController.saleModal.find('#salePercentage').val();
        let dateRangeString = productController.saleModal.find('#date-range-picker').val();
        if (!salePercentage || !dateRangeString)
            return null;

        return {
            percentage: salePercentage,
            range: dateRangeString
        };
    },
    getSaleAllModalData: function () {
        let salePercentage = productController.saleAllModal.find('#salePercentage').val();
        let dateRangeString = productController.saleAllModal.find('.date-range-picker').val();
        if (!salePercentage || !dateRangeString)
            return null;

        return {
            percentage: salePercentage,
            range: dateRangeString
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
            case 'search':
                button.html("<i class=\"ace-icon bigger-120 fa fa-search\"></i> " + text);
                break;
            case 'error':
                button.html("<i class=\"ace-icon bigger-120 fa fa-close\"></i> " + text);
                break;
            case 'image':
                button.html("<i class=\"ace-icon bigger-120 fa fa-image\"></i> " + text);
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
    }
};

$(function () {
    productController.init();
});