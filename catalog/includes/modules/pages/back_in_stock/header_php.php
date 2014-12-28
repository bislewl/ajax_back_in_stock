<?php

/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
$action = $_POST['action'];
if (is_array($_POST['bis_id'])) {
    switch ($action) {
        case "stop":
            foreach ($_POST['bis_id'] as $sub_id) {
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
    $bis_id_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE bis_id=" . $_POST['bis_id'][0]);
    if ($bis_id_info->RecordCount() > 0) {
        $subcriptions = true;
        $email_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE email LIKE '" . $bis_id_info->fields['email'] . "' AND sub_active=1");
    } else {
        $subcriptions = false;
    }
}
if ($_SESSION['customer_id']) {
    $email = $db->Execute("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id=" . $_SESSION['customer_id']);

    $email_info = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE email LIKE '" . $email->fields['customers_email_address'] . "' AND sub_active=1");
    if ($email_info->RecordCount() > 0) {
        $subcriptions = true;
    }
}
