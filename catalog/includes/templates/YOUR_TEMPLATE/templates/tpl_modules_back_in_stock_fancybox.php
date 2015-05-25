<?php
/**
 * @copyright Copyright 2010-2015  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
// auto complete when logged in
if ($_SESSION['customer_id']) {
    $sql = "SELECT customers_id, customers_firstname, customers_lastname, customers_email_address 
            FROM " . TABLE_CUSTOMERS . "
            WHERE customers_id = :customersID";

    $sql = $db->bindVars($sql, ':customersID', $_SESSION['customer_id'], 'integer');
    $check_customer = $db->Execute($sql);
    $customer_email = $check_customer->fields['customers_email_address'];
    $customer_name = $check_customer->fields['customers_firstname'] . ' ' . $check_customer->fields['customers_lastname'];
}
if(!defined(CSS_JS_LOADER_VERSION)){
    echo '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('jquery.fancybox.css',DIR_WS_TEMPLATE, $current_page_base,'css') . '/jquery.fancybox.css" />'."\n";
    echo '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('back_in_stock.css',DIR_WS_TEMPLATE, $current_page_base,'css') . '/back_in_stock.css" />'."\n";
    echo '<script type="text/javascript">
        if (typeof jQuery == \'undefined\') {  
  document.write("<scr" + "ipt type=\"text/javascript\" src=\"//code.jquery.com/jquery-1.11.3.min.js\"></scr" + "ipt>");
  }
</script>'."\n";
    
    echo '<script type="text/javascript" src="' .  $template->get_template_dir('jquery.fancybox.js',DIR_WS_TEMPLATE, $current_page_base,'jscript/jquery') . '/jquery.fancybox.js"></script>'."\n";
    echo '<script type="text/javascript" src="' .  $template->get_template_dir('jquery_back_in_stock.js',DIR_WS_TEMPLATE, $current_page_base,'jscript/jquery') . '/jquery_back_in_stock.js"></script>'."\n";
}
?>
<div style="display: none;">
    <div id="back-in-stock-popup-wrapper">
        <?php if (BACK_IN_STOCK_SHOW_PRODUCT_INFO == "true") { ?>
            <div id="back-in-stock-product">
                <div id="back-in-stock-product-name">
                    <h2 id="productName">
                        <?php
                        echo zen_get_products_name(zen_db_prepare_input($_GET[products_id]));
                        ?>
                    </h2>
                </div>
                <div id="back-in-stock-product-image">
                    <?php
                    echo zen_get_products_image(zen_db_prepare_input($_GET[products_id]));
                    ?>
                </div>
            </div>
            <div class="clearBoth"></div>
        <?php } ?>
        <div id="back-in-stock-popup-headline-wrapper">
            <h2><?php echo BACK_IN_STOCK_POPUP_HEADING; ?></h2>
            <h3><?php echo BACK_IN_STOCK_POPUP_SUBHEADING; ?></h3>
            <div class="clearBoth"></div>
        </div>
        <?php echo zen_draw_form('back_in_stock', zen_href_link(FILENAME_BACK_IN_STOCK, 'action=send', ($_SERVER['HTTPS'] == 'on' ? 'SSL' : 'NONSSL'))); ?>
        <div class="back-in-stock-popup-content-wrapper">
            <?php echo zen_draw_input_field('customer_name', ($customer_name), ' size="40" id="customer_name" placeholder="' . BACK_IN_STOCK_NAME . '"'); ?>
            <div class="clearBoth"></div>
            <?php echo zen_draw_input_field('email', ($customer_email), ' size="40" id="email-address" placeholder="' . BACK_IN_STOCK_EMAIL . '"'); ?>
        </div>
        <?php echo zen_draw_hidden_field('product_id', zen_db_prepare_input($_GET[products_id])); ?>
        <?php echo zen_draw_input_field('should_be_empty', '', ' size="40" style="visibility:hidden; display:none;" autocomplete="off"'); ?>
        <div class="clearBoth"></div>
        <div id="contact_messages">
        </div>
        <div class="back-in-stock-popup-wrapper-button-row">
            <?php
            if (BACK_IN_STOCK_POPUP_NOTIFY_IMG == '') {
                ?>
                <button type="submit" value="<?php echo BACK_IN_STOCK_SEND_TEXT; ?>"><?php echo BACK_IN_STOCK_SEND_TEXT; ?></button>
                <?php
            } else {
                echo zen_image_submit(BACK_IN_STOCK_POPUP_NOTIFY_IMG);
            }
            ?>
            <div class="clearBoth"></div>
        </div>
        <div class="clearBoth"></div>
        </form>
        <div class="clearBoth"></div>
    </div>
</div>
