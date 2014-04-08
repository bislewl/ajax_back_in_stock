jQuery(document).ready(function() {
	
	jQuery("a#back-in-stock-popup-link").fancybox({
		afterShow: function() {
			jQuery(document).on('submit', '#back-in-stock-popup-wrapper form[name="back_in_stock"]', function() {
				jQuery('#contact_messages').empty();
				jQuery.post('ajax/back_in_stock_subscribe_pop_up.php', jQuery('#back-in-stock-popup-wrapper form[name="back_in_stock"]').serialize(), function(data) {
					jQuery('#contact_messages').html(data);
				});
				return false;
			});
		}
	});
});