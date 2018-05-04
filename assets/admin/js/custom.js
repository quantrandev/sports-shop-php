$(function () {

    $(document).on('click', '.togglable', function () {
        $(this).closest('tr').next().toggle();
    });

    $(document).on('change', '.js-check-all', function () {
        if ($(this).prop('checked')) {
            $(this).closest('table')
                .children('tbody')
                .children('tr')
                .children('td:first-child')
                .children('label')
                .children('.js-check-item')
                .prop('checked', true);
            $('.js-batch-delete').removeClass('hide');
        }
        else {
            $(this).closest('table')
                .children('tbody')
                .children('tr')
                .children('td:first-child')
                .children('label')
                .children('.js-check-item')
                .prop('checked', false);

            let hasChecked = false;
            $('.js-check-item').each(function (index, value) {
                if ($(value).prop('checked')) {
                    hasChecked = true;
                    return;
                }
            });

            if (hasChecked)
                $('.js-batch-delete').removeClass('hide');
            else
                $('.js-batch-delete').addClass('hide');
        }
    });

    $(document).on('change', '.js-check-item', function () {
        let hasChecked = false;
        $('.js-check-item').each(function (index, value) {
            if ($(value).prop('checked')) {
                hasChecked = true;
                return;
            }
        });

        if (hasChecked)
            $('.js-batch-delete').removeClass('hide');
        else
            $('.js-batch-delete').addClass('hide');
    });

    $('#date-range-picker').daterangepicker({
        'autoUpdateInput': false,
        'applyClass': 'btn-sm btn-success',
        'cancelClass': 'btn-sm btn-default',
        locale: {
            applyLabel: 'Áp dụng',
            cancelLabel: 'Bỏ qua',
        }
    })
        .prev().on(ace.click_event, function () {
        $(this).next().focus();
    });

    $('#date-range-picker').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('#date-range-picker').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

    $('#orders-table').on('click', 'tr', function () {
        $(this).closest('table').find('tr').removeClass('active');
        $(this).addClass('active');
    });
    $('#products-table').on('click', 'tr', function () {
        $(this).closest('table').find('tr').removeClass('active');
        $(this).addClass('active');
    });

    $('.multiselect-category').multiselect({
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn btn-white btn-primary',
        maxHeight: 300,
        buttonWidth: '100%',
        numberDisplayed: 0,
        nonSelectedText: 'Chọn danh mục',
        enableClickableOptGroups: true,
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> &nbsp;<b class="fa fa-caret-down"></b></button>',
            ul: '<ul class="multiselect-container dropdown-menu"></ul>',
            filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
            filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default btn-white btn-grey multiselect-clear-filter" type="button"><i class="fa fa-times-circle red2"></i></button></span>',
            li: '<li><a tabindex="0"><label></label></a></li>',
            divider: '<li class="multiselect-item divider"></li>',
            liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
        }
    });
    $('.multiselect-roles').multiselect({
        enableHTML: true,
        buttonClass: 'btn btn-white btn-primary',
        maxHeight: 300,
        numberDisplayed: 2,
        nonSelectedText: 'Chọn quyền',
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> &nbsp;<b class="fa fa-caret-down"></b></button>',
            ul: '<ul class="multiselect-container dropdown-menu"></ul>',
            filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
            filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default btn-white btn-grey multiselect-clear-filter" type="button"><i class="fa fa-times-circle red2"></i></button></span>',
            li: '<li><a tabindex="0"><label></label></a></li>',
            divider: '<li class="multiselect-item divider"></li>',
            liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
        }
    });

    $('.quick-view').magnificPopup({
        type: 'image'
        // other options
    });

    $('.date-range-picker').daterangepicker({
        'autoUpdateInput': false,
        'applyClass': 'btn-sm btn-success',
        'cancelClass': 'btn-sm btn-default',
        locale: {
            applyLabel: 'Áp dụng',
            cancelLabel: 'Bỏ qua',
        }
    })
        .prev().on(ace.click_event, function () {
        $(this).next().focus();
    });

    $('.date-range-picker').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('.date-range-picker').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

    $('.select2').css('width', '200px').select2({allowClear: true, width: '100%'});
});