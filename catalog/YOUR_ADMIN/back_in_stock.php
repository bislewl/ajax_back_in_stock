<?php
/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

if ($_GET['bis_selected'] != '') {
    $bis_selected = zen_db_prepare_input($_GET['bis_selected']);
} else {
    $bis_selected = 0;
}

$convert_get = zen_db_prepare_input($_GET['convert']);
if ($convert_get == true) {
    $conversion_offered = ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'confirm_convert=true') . '">Confirm Converison from CEON Back In Stock?</a><br/>';
}
$confirm_convert_get = zen_db_prepare_input($_GET['confirm_convert']);
if ($confirm_convert_get == true) {
    back_in_stock_convert();
    $conversion_offered = "Conversion Complete";
}
$table_exists_query = 'SHOW TABLES LIKE "' .
        TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';
$table_exists_result = $db->Execute($table_exists_query);
if (!$table_exists_result->EOF) {
    $ceon_bis_table_present = true;
}
if ($confirm_convert_get != true && $convert_get != true && $ceon_bis_table_present == true) {
    $conversion_offered = ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'convert=true') . '">Convert from CEON Back In Stock?</a><br/>';
}

$bis_show = zen_db_prepare_input($_GET['filter']);
$product_id = zen_db_prepare_input($_POST['pid']);
$subscriber = zen_db_prepare_input($_POST['sub_email']);
$start_sql = "SELECT * FROM " . TABLE_BACK_IN_STOCK . " bis LEFT JOIN " . TABLE_PRODUCTS . " p on(bis.product_id = p.products_id) LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on(p.products_id = pd.products_id) ";
switch ($bis_show) {
    case "all":
        $sql_statement = $start_sql;
        $header_comment = "showing all active and non active subscriptions";
        break;
    case "product":
        $sql_statement = $start_sql . " WHERE bis.product_id=" . $product_id . " AND bis.sub_active = 1";
        $header_comment = "showing all active subscriptions to " . zen_get_products_name($product_id);
        break;
    case "subscriber":
        $sql_statement = $start_sql . " WHERE bis.email='" . $subscriber . "' AND bis.sub_active = 1";
        $header_comment = "showing all active Subscriptions for " . $subscriber;
        break;
    default:
        $sql_statement = $start_sql . " WHERE bis.sub_active = 1";
        $header_comment = "showing all active subscriptions";
        break;
}

$sort = zen_db_prepare_input($_GET['sort']);
$sort_o = zen_db_prepare_input($_GET['sort_o']);
if ($sort != '') {
    if ($sort_o != 'desc') {
        $order_by = " ORDER BY " . $sort . " ASC";
    } else {
        $order_by = " ORDER BY " . $sort . " DESC";
    }
} else {
    $order_by = " ";
}
$subscribers_query_raw = $sql_statement . $order_by;

// Split Page
// reset page when page is unknown
if (($_GET['page'] == '' or $_GET['page'] == '1')) {
    $check_page = $db->Execute($subscribers_query_raw);
    $check_count = 1;
    if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) {
        while (!$check_page->EOF) {
            if ($check_page->fields['customers_id'] == $_GET['cID']) {
                break;
            }
            $check_count++;
            $check_page->MoveNext();
        }
        $_GET['page'] = round((($check_count / MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) + (fmod_round($check_count, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) != 0 ? .5 : 0)), 0);
//    zen_redirect(zen_href_link(FILENAME_CUSTOMERS, 'cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'NONSSL'));
    } else {
        $_GET['page'] = 1;
    }
}

$subscribers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, $subscribers_query_raw, $subscribers_query_numrows);
$subscribers = $db->Execute($subscribers_query_raw);

$record_count = $subscribers->RecordCount();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
        <title><?php echo TITLE; ?></title>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
        <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
        <script language="javascript" src="includes/menu.js"></script>
        <script language="javascript" src="includes/general.js"></script>
        <script type="text/javascript">
            <!--
          function init()
            {
                cssjsmenu('navbar');
                if (document.getElementById)
                {
                    var kill = document.getElementById('hoverJS');
                    kill.disabled = true;
                }
            }
            // -->
        </script>
    </head>
    <body onload="init()">
        <!-- header //-->
        <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
        <!-- header_eof //-->


        <!-- body //-->
        <table border="0" width="100%" cellspacing="2" cellpadding="2">
            <tr>
                <!-- body_text //-->
                <td width="75%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="pageHeading"><?php echo HEADING_TITLE; ?>
                                            <br/>
                                            <?php echo $header_comment; ?>
                                        </td>
                                        <td class="pageHeading" align="right">
                                            <?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
                                            <?php
                                            echo HEADING_EMAIL . ':';
                                            echo zen_draw_form('back_in_stock', FILENAME_BACK_IN_STOCK, 'filter=subscriber', 'post', '', true);
                                            echo zen_hide_session_id();
                                            echo zen_draw_input_field('sub_email')
                                            ?>
                                            </form><br/>
                                            <?php
                                            echo HEADING_PRODUCT_ID . ':';
                                            echo zen_draw_form('back_in_stock', FILENAME_BACK_IN_STOCK, 'filter=product', 'post', '', true);
                                            echo zen_hide_session_id();
                                            echo zen_draw_input_field('pid')
                                            ?>
                                            </form><br/>
                                            <?php
                                            if ($bis_show != "all") {
                                                echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'filter=all') . '">' . HEADING_SHOW_ACTIVE_AND . '</a><br/>';
                                            }
                                            ?>
                                            <?php
                                            if ($bis_show != '') {
                                                echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK) . '">' . HEADING_SHOW_ACTIVE_ONLY . '</a><br/>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <form name="back_in_stock" action="<?php echo HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG . "cron/send_back_in_stock_notifications.php"; ?>" target="_blank" method="get">
                                                Product: <?php echo zen_draw_products_pull_down('product_id', '> <option value="0">' . HEADING_ALL_PRODUCTS . '</option'); ?>
                                                <?php echo zen_draw_hidden_field('key', BACK_IN_STOCK_CRON_KEY) ?>
                                                <?php echo zen_draw_hidden_field('bis_id', '0') ?>
                                                <?php echo TEXT_PREVIEW . ': ' . zen_draw_checkbox_field('preview', 'true', true) ?>
                                                <input type="submit" value="<?php echo TEXT_RUN_NOTIFICATIONS; ?>">
                                            </form>
                                        </td>
                                        <td><?php
                                            echo $conversion_offered;
                                            ?></td>
                                    </tr>
                                </table></td>
                        </tr>
                    </table>
                </td>
                <!-- body_text_eof //-->
            </tr>
            <tr>
                <td width="75%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr class="dataTableHeadingRow">
                            <td class="dataTableHeadingContent" align="left" valign="top">ID</td>
                            <td class="dataTableHeadingContent" align="center" valign="top">
                                <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'sort=sub_date') . '">' . HEADING_DATE_SUBSCRIBED . '</a><br/>'; ?></td>
                            <td class="dataTableHeadingContent" align="center" valign="top">
                                <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'sort=email') . '">' . HEADING_EMAIL . '</a><br/>'; ?></td>
                            <td class="dataTableHeadingContent" align="center" valign="top">Active</td>
                            <td class="dataTableHeadingContent" align="center" valign="top">
                            <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'sort=products_model') . '">' . HEADING_PRODUCT_MODEL . '</a><br/>'; ?></td>
                            <td class="dataTableHeadingContent" align="center" valign="top">
                                <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'sort=products_name') . '">' . HEADING_PRODUCT . '</a><br/>'; ?></td>
                            <td class="dataTableHeadingContent" align="center" valign="top"><?php echo HEADING_STOCK_LEVEL; ?></td>
                        </tr>
                        <?php
                        $rowi = 0;

                        while (!$subscribers->EOF) {
                            // BOF Check for products that aren't present and delete
                            if($subscribers->fields['products_name'] == ''){
                                $product_present = $db->Execute("SELECT * FROM ".TABLE_PRODUCTS." WHERE products_id='".$subscribers->fields['products_name']."'");
                                if($product_present->RecordCount() == 0){
                                    $db->Execute("DELETE FROM ".TABLE_BACK_IN_STOCK." WHERE bis_id='".$subscribers->fields['bis_id']."'");
                                    $subscribers->MoveNext();
                                    continue;
                                }
                            }
                            // EOF Check for products that aren't present and delete
                            $rowi++;
                            if ($rowi % 2 == 0) {
                                $over = 'Over';
                            } else {
                                $over = '';
                            }

                            $rowheader = 'class="dataTableRow' . $over . '"';
                            if ($rowi == 1 && $bis_selected == 0) {
                                $rowheader = 'id="defaultSelected" class="dataTableRow' . $over . 'Selected"';
                                $bis_selected = $subscribers->fields['bis_id'];
                            }
                            if ($subscribers->fields['bis_id'] == $bis_selected) {
                                $rowheader = 'id="defaultSelected" class="dataTableRow' . $over . 'Selected"';
                            }
                            ?>
                            <tr <?php echo $rowheader; ?> onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href = '<?php echo zen_href_link(FILENAME_BACK_IN_STOCK, 'bis_selected=' . $subscribers->fields['bis_id']); ?>'">
                                <td class="dataTableContent" align="left"><?php echo $subscribers->fields['bis_id']; ?></td>
                                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['sub_date']; ?></td>
                                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['email']; ?></td>
                                <td class="dataTableContent" align="center"><?php echo ($subscribers->fields['sub_active'] == 1 ? 'Y' : 'N'); ?></td>
                                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['products_model']; ?></td>
                                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['products_name']; ?></td>
                                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['products_quantity']; ?></td>
                            </tr>
                            <?php
                            $subscribers->MoveNext();
                        }
                        ?>
                        <tr>
                            <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tbody><tr>
                                            <td class="smallText" valign="top"><?php echo $subscribers_split->display_count($subscribers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                                            <td class="smallText" align="right"><?php echo $subscribers_split->display_links($subscribers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                                        </tr>
                                    </tbody></table></td>
                        </tr>
                    </table>
                </td>
                <?php
                $bis_sub_info = get_back_in_stock_sub_info($bis_selected);
                ?>
                <td width="25%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr class="infoBoxHeading">
                            <td class="infoBoxHeading"><b>ID#<?php echo $bis_selected . "  " . $bis_sub_info['email']; ?></b></td>
                        </tr>
                    </table>
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">
                        <tr>
                            <td class="infoBoxContent"><br><b><?php echo HEADING_SUBSCRIPTION_STARTED; ?>:</b> <?php echo $bis_sub_info['sub_date']; ?></td>
                        </tr>
                        <tr>
                            <td class="infoBoxContent"><br><b><?php echo HEADING_SUBSCRIPTION_ACTIVE; ?>:</b> <?php echo ($bis_sub_info['sub_active'] == 1 ? 'Y' : 'N'); ?></td>
                        </tr>
                        <tr>
                            <td class="infoBoxContent"><br><b><?php echo HEADING_PRODUCT; ?>:</b> <?php echo zen_get_products_name($bis_sub_info['product_id']); ?></td>
                        </tr>
                        <tr>
                            <td class="infoBoxContent"><br><b><?php echo HEADING_CANCEL_W_PURCHASE; ?>:</b> <?php echo ($bis_sub_info['active_til_purch'] == 1 ? 'Y' : 'N'); ?></td>
                        </tr>
                        <tr>
                            <td class="infoBoxContent"><br><b><?php echo HEADING_LAST_SENT; ?>:</b> <?php echo $bis_sub_info['last_sent']; ?></td>
                        </tr>
                        <tr>
                            <td class="infoBoxContent"><br><b><?php echo HEADING_FLAG_SPAM; ?>:</b> <?php echo ($bis_sub_info['spam'] == 1 ? 'Y' : 'N'); ?></td>
                        </tr>
                        <tr>
                            <td class="infoBoxContent"></td>
                        </tr>
                    </table>  
                </td>
            </tr>
        </table>
        <!-- body_eof //-->
        <?php echo TEXT_HINT_ADD_TO_CPANEL . ": '" . HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG . "cron/send_back_in_stock_notifications.php?key=" . BACK_IN_STOCK_CRON_KEY . "' "; ?>
        <!-- footer //-->
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
        <!-- footer_eof //-->
        <br>
    </body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');