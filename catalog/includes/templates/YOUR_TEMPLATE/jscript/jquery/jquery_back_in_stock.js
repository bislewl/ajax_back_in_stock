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
    $('a.back-in-stock-listing-popup-link').click(function (event) {
        event.preventDefault();
        var productDiv = $(this).parent();
        $('#back-in-stock-product-image img').attr('src', $(productDiv).find('.img').attr('src'));
        $('#productName').html($(productDiv).find('span.itemTitle').text());
        $('input[name="product_id"]').attr('value', $(productDiv).find('input[name="bis-product-id"]').attr('value'));
        $('.back-in-stock-popup-wrapper-button-row').show();
        $('.back-in-stock-popup-content-wrapper').show();
        $('#contact_messages').html('');
        $.fancybox({
            href: '#back-in-stock-popup-wrapper',
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
});