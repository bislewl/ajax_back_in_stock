<?php
/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
//let's add this in case someone someday wants to add an observer.
$zco_notifier->notify('NOTIFY_HEADER_START_SUBSCRIBE_BACK_IN_STOCK');

$language_page_directory = DIR_WS_LANGUAGES . $_SESSION['language'] . '/';
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$error = false;
$name = zen_db_prepare_input($_POST['customer_name']);
$email_address = zen_db_prepare_input($_POST['email']);
$empty = isset($_POST['should_be_empty']) ? zen_db_prepare_input($_POST['should_be_empty']) : '';
$product_id = zen_db_prepare_input($_POST['product_id']);
//Validate the email address
$zc_validate_email = zen_validate_email($email_address);

if ($zc_validate_email && $empty == '' && $product_id != '') {
    $bis['email'] = $email_address;
    $bis['name'] = $name;
    $bis['product_id'] = $product_id;
    $returned_results = back_in_stock_subscription($bis, "add");
    if ($returned_results === "Subscribed") {
        echo '<p class="messageStackSuccess">' . BACK_IN_STOCK_SUCCESS . '</p>';
    } else {
        echo '<p class="messageStackError">' . $returned_results . '</p>';
    }
} else {
    $error = true;
    if (empty($name)) {
        echo '<p class="messageStackError">' . BACK_IN_STOCK_NAME_ERROR . '</p>';
    }
    if ($zc_validate_email == false) {
        echo '<p class="messageStackError">' . BACK_IN_STOCK_EMAIL_ADDRESS_ERROR . '</p>';
    }
    if ($product_id == '') {
        echo '<p class="messageStackError">' . BACK_IN_STOCK_NO_PRODUCT_ERROR . '</p>';
    }
    echo '<p class="messageStackError">' . $returned_results . '</p>';
}
//let's add this in case someone someday wants to add an observer.
$zco_notifier->notify('NOTIFY_HEADER_END_SUBSCRIBE_BACK_IN_STOCK');
//end of the file
require_once('includes/application_bottom.php');
