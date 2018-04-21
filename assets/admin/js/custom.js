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
});