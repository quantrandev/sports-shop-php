$(function () {
    $(document).on('change', '.products-filter', function () {
        $(this).closest('form').submit();
    });
});
