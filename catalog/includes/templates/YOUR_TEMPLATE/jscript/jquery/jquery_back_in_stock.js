$(document).ready(function () {
    $("a#back-in-stock-popup-link").fancybox({
        afterShow: function () {
            $(document).on('submit', '#back-in-stock-popup-wrapper form[name="back_in_stock"]', function () {
                $('#contact_messages').empty();
                $.post('ajax/back_in_stock_subscribe_pop_up.php', $('#back-in-stock-popup-wrapper form[name="back_in_stock"]').serialize(), function (data) {
                    $('#contact_messages').html(data);
                    if ($('.messageStackSuccess').length) {
                        $('.back-in-stock-popup-wrapper-button-row').hide();
                        $('.back-in-stock-popup-content-wrapper').hide();
                    }
                });
                return false;
            });
        }
    });
});