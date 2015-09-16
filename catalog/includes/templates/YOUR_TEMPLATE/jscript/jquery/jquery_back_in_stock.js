$(document).ready(function () {
    $("a#back-in-stock-popup-link").fancybox({
        afterShow: function () {
            $(document).on('submit', '#back-in-stock-popup-wrapper form[name="back_in_stock"]', function (event) {
                event.preventDefault();
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
        $('#contact_messages').empty();
        $('#back-in-stock-product-image img').attr('src', $(productDiv).find('.listingProductImage').attr('src'));
        if ($(productDiv).find('h3.itemTitle')) {
            $('#productName').html($(productDiv).find('h3.itemTitle').text());
        } else {
            $('#productName').html($(productDiv).find('span.itemTitle').text());
        }
        $('input[name="product_id"]').attr('value', $(productDiv).find('input[name="bis-product-id"]').attr('value'));
        $.fancybox({
            href: '#back-in-stock-popup-wrapper',
            afterShow: function () {
                $(document).on('submit', '#back-in-stock-popup-wrapper form[name="back_in_stock"]', function () {
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