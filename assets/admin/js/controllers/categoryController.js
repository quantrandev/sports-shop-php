var categoryController = {
    editModalDOM: null,
    addModalDOM: null,
    categoriesTableDOM: null,
    init: function () {
        categoryController.editModalDOM = $('#edit-modal');
        categoryController.addModalDOM = $('#add-modal');
        categoryController.categoriesTableDOM = $('#categories-table');
        categoryController.registerConfirmations();

        categoryController.events();
    },
    events: function () {
        categoryController.onEditModalOpen();
        categoryController.onSaveEditChanges();
        categoryController.onSaveNewCategory();
    },
    registerConfirmations: function () {
        $('.js-delete-category').confirmation({
            rootSelector: '.js-delete-category',
            title: 'Xóa danh mục này',
            singleton: true,
            popout: true,
            onConfirm: categoryController.deleteCategory
        });
        $('.js-batch-delete').confirmation({
            rootSelector: '.js-batch-delete',
            title: 'Xóa những danh mục đã chọn',
            singleton: true,
            popout: true,
            onConfirm: categoryController.batchDeleteCategory
        });
    },
    onEditModalOpen: function () {
        $(document).on('click', '.js-open-edit-modal', function () {
            var button = $(this);
            var categoryId = button.attr('data-id');

            categoryController.toggleButtonStatus(button, 'loading', 'Sửa');

            categoryService.get(categoryId, function (res) {
                res = JSON.parse(res);
                categoryController.setDataForEditForm(res);
                categoryController.editModalDOM.modal();
                categoryController.toggleButtonStatus(button, 'edited', 'Sửa');
            }, function (error) {
            });
        });
    },
    onSaveEditChanges: function () {
        $(document).on('click', '.js-save-changes', function () {
            let button = $(this);
            categoryController.toggleButtonStatus(button, 'loading', 'Lưu thay đổi');

            let dataFromEditForm = categoryController.getDataFromEditForm();
            dataFromEditForm.data.parentId = isNaN(dataFromEditForm.data.parentId) ? 0 : dataFromEditForm.data.parentId;
            categoryService.edit(dataFromEditForm.id, dataFromEditForm.data, function (res) {
                categoryController.editModalDOM.modal('hide');

                if (res.error) {
                    categoryController.toggleButtonStatus(button, 'text-only', 'Lưu thay đổi');
                    utilities.notify("Có lỗi xảy ra", res.message, "gritter-error", false);
                }
                else
                    window.location.reload();
            }, function (error) {
            });
        });
    },
    onSaveNewCategory: function () {
        $(document).on('click', '.js-save-new-category', function () {
            let button = $(this);
            let data = categoryController.getDataFromAddForm();
            data.parentId = isNaN(data.parentId) ? 0 : data.parentId;
            categoryController.toggleButtonStatus(button, 'loading', 'Thêm mới');

            categoryService.add(data, function (res) {
                categoryController.toggleButtonStatus(button, 'text-only', 'Thêm mới');
                categoryController.addModalDOM.modal('hide');

                if (res.error)
                    utilities.notify('Có lỗi xảy ra', res.message, 'gritter-error', false);
                else
                    window.location.reload();
            }, function (error) {
            });
        });
    },
    deleteCategory: function () {
        let categoryId = $(this).attr('data-id');
        categoryService.delete(categoryId, function (res) {
            window.location.reload();
        }, function (error) {
        });
    },
    batchDeleteCategory: function () {
        let deletedItems = categoryController.getSelectedCategories();
        categoryService.batchDelete(deletedItems, function (res) {
            console.log(res);
        }, function (error) {
        });
    },
    setDataForEditForm: function (data) {
        categoryController.editModalDOM.find('#category_id').val(data.id);
        categoryController.editModalDOM.find('#category_name').val(data.name);
        categoryController.editModalDOM.find('#category_is_active').prop('checked', data.isActive);

        var categoryParentDOM = categoryController.editModalDOM.find('#category_parent');
        categoryParentDOM.html("");
        categoryParentDOM.append('<option>Chọn danh mục cha</option>');

        let mainCategories = data.categories.filter(value => {
            return value.parentId == 0;
        });
        for (i = 0; i < mainCategories.length; i++) {
            if (mainCategories[i].id != data.id) {
                if (mainCategories[i].id == data.parentId)
                    categoryParentDOM.append('<option value="' + mainCategories[i].id + '" selected>' + mainCategories[i].name + '</option>');
                else
                    categoryParentDOM.append('<option value="' + mainCategories[i].id + '">' + mainCategories[i].name + '</option>');
            }
        }
    },
    getDataFromEditForm: function () {
        let id = categoryController.editModalDOM.find('#category_id').val();
        let name = categoryController.editModalDOM.find('#category_name').val();
        let isActive = categoryController.editModalDOM.find('#category_is_active').prop('checked') ?
            1 : 0;
        let parent = categoryController.editModalDOM.find('#category_parent').val();
        return {
            id: id,
            data: {
                name: name,
                isActive: isActive,
                parentId: parent
            }
        };
    },
    getDataFromAddForm: function () {
        let name = $('.frm-add-category').find('#category_name').val();
        let isActive = $('.frm-add-category').find('#category_is_active').prop('checked') ?
            1 : 0;
        let parent = $('.frm-add-category').find('#category_parent').val();
        return {
            name: name,
            isActive: isActive,
            parentId: parent
        };
    },
    getSelectedCategories: function () {
        let deletedItems = [];
        $('.js-check-item').each(function (index, value) {
            if ($(value).prop('checked'))
                deletedItems.push($(value).attr('data-id'));
        });

        return deletedItems;
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
};

$(function () {
    categoryController.init();
});