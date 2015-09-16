<?php

/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
$subcriptions = false;
$action = zen_db_prepare_input($_POST['action']);
if (is_array($_POST['bis_id'])) {
    switch ($action) {
        case "stop":
            foreach (zen_db_prepare_input($_POST['bis_id']) as $sub_id) {
                $modify_subscription = array(
                    'bis_id' => $sub_id,
                    'sub_active' => 0,
                    'spam' => 1
                );
                back_in_stock_subscription($modify_subscription, "modify");
            }
            break;
        case "delete":
            back_in_stock_subscription(array('bis_id' => $bis_id), "delete");
            break;
    }
    $bis_id_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE bis_id='" . zen_db_prepare_input($_POST['bis_id'][0]) . "' AND sub_active=1");
    if ($bis_id_info->RecordCount() > 0) {
        $subcriptions = true;
        $email_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE email LIKE '" . $bis_id_info->fields['email'] . "' AND sub_active=1");
    } else {
        $subcriptions = false;
    }
} elseif ((int) $_GET['bis_id'] > 0) {
    $bis_id_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE bis_id='" . (int) $_GET['bis_id'] . "' AND sub_active=1");
    if ($bis_id_info->RecordCount() > 0) {
        $subcriptions = true;
        $email_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE email LIKE '" . $bis_id_info->fields['email'] . "' AND sub_active=1");
    } else {
        $subcriptions = false;
    }
}
$get_action = zen_db_prepare_input($_GET['action']);
if ($get_action == 'send') {
    $error = false;
    $name = zen_db_prepare_input($_POST['customer_name']);
    $email_address = zen_db_prepare_input($_POST['email']);
    $empty = isset($_POST['should_be_empty']) ? zen_db_prepare_input($_POST['should_be_empty']) : '';
    $product_id = zen_db_prepare_input($_POST['product_id']);
    //Validate the email address
    $zc_validate_email = zen_validate_email($email_address);
    if (sizeof($_SESSION['navigation']->snapshot) > 0) {
        $old_page = $_SESSION['navigation']->snapshot['page'];
    }
    if ($zc_validate_email && $empty == '' && $product_id != '') {
        $bis['email'] = $email_address;
        $bis['name'] = $name;
        $bis['product_id'] = $product_id;
        $returned_results = back_in_stock_subscription($bis, "add");
        if ($returned_results === "Subscribed") {
            $messageStack->add_session($old_page, BACK_IN_STOCK_SUCCESS, 'success');
        } else {
            $messageStack->add_session($old_page, $returned_results, 'caution');
        }
    } else {
        $error = true;
        if (empty($name)) {
            $messageStack->add_session($old_page, BACK_IN_STOCK_EMAIL_ADDRESS_ERROR, 'caution');
        }
        if ($zc_validate_email == false) {
            $messageStack->add_session($old_page, BACK_IN_STOCK_EMAIL_ADDRESS_ERROR, 'caution');
        }
        if ($product_id == '') {
            $messageStack->add_session($old_page, BACK_IN_STOCK_NO_PRODUCT_ERROR, 'caution');
        }
        $messageStack->add_session($old_page, $returned_results, 'caution');
    }
    if (sizeof($_SESSION['navigation']->snapshot) > 0) {
        $origin_href = zen_href_link($_SESSION['navigation']->snapshot['page'], zen_array_to_string($_SESSION['navigation']->snapshot['get'], array(zen_session_name())), $_SESSION['navigation']->snapshot['mode']);
        zen_redirect($origin_href);
    }
}
if ($_SESSION['customer_id']) {
    $email = $db->Execute("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id=" . $_SESSION['customer_id']);

    $email_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE email LIKE '" . $email->fields['customers_email_address'] . "' AND sub_active=1");
    if ($email_info->RecordCount() > 0) {
        $subcriptions = true;
    }
}