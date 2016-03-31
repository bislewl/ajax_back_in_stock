<?php

/**
 * @copyright Copyright 2010-2015  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
/*
 * Back in Stock Notification
 * Forked / Inspired by the CEON Back In Stock Module. 
 * Please continue to keep Conor and his Family in our prayers 
 * 
 */
//sort multi-deminsional array
function aasort(&$array, $key) {
    $sorter = array();
    $ret = array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii] = $va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii] = $array[$ii];
    }
    $array = $ret;
}

//Converts Ceon's table
function back_in_stock_convert() {
    global $db;
    $table_exists_query = 'SHOW TABLES LIKE "' .
            TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';
    $table_exists_result = $db->Execute($table_exists_query);
    if (!$table_exists_result->EOF) {
        $ceons_subscribers = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS);
        while (!$ceons_subscribers->EOF) {
            $array = array();
            $array['product_id'] = $ceons_subscribers->fields['product_id'];
            $array['sub_date'] = $ceons_subscribers->fields['date_subscribed'];
            $array['sub_active'] = 1;
            if ($ceons_subscribers->fields['customer_id'] != '') {
                $customer_info = $db->Execute("SELECT customers_email_address, customers_lastname, customers_firstname FROM " . TABLE_CUSTOMERS . " WHERE customers_id=" . $ceons_subscribers->fields['customer_id']);
                $array['name'] = $customer_info->fields['customers_firstname'] . " " . $customer_info->fields['customers_lastname'];
                $array['email'] = $customer_info->fields['customers_email_address'];
            } else {
                $array['name'] = $ceons_subscribers->fields['name'];
                $array['email'] = $ceons_subscribers->fields['email_address'];
            }
            back_in_stock_subscription($array, "bulk");
            $ceons_subscribers->MoveNext();
        }
    }
    $db->Execute("RENAME TABLE " . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . " TO " . TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS_OLD);
}

function back_in_stock_status($email, $product = 0) {
    global $db;
    if ($product != 0) {
        $product_query = " AND product_id =" . $product;
    } else {
        $product_query = '';
    }
    $active = $db->Execute("SELECT * FROM " . TABLE_BACK_IN_STOCK . " WHERE email='" . $email . "' AND sub_active=1 " . $product_query);
    return $active->RecordCount();
}

function back_in_stock_subscription($array, $change_type = "add") {
    global $db;
    $result = "Failed";
    $email = $array['email'];
    $name = $array['name'];
    $product_id = $array['product_id'];
    $current_status = back_in_stock_status($email, $product_id);
    switch ($change_type) {
        case "bulk":
        case "add":
            if ($current_status == 1) {
                $result = BACK_IN_STOCK_ALREADY_SUB;
                break;
            }
            $db->Execute("INSERT INTO " . TABLE_BACK_IN_STOCK . " (email, product_id, sub_date, sub_active, name, active_til_purch) VALUES
                     ('" . $email . "', " . $product_id . ", NOW(), 1, " . '"' . addslashes($name) . '"' . ", " . BACK_IN_STOCK_ACTIVE_TIL_PURCH . " )");
            $bis_id = $db->Insert_ID();
            $result = "Subscribed";
            //send email
            if (BACK_IN_STOCK_EMAIL_SUBSCRIBE && $change_type != "bulk") {
                $customers_name = $name;
                $customers_email = $email;
                $html_message = array();
                $html_message['CUSTOMERS_NAME'] = $customers_name;
                $html_message['PRODUCT_NAME'] = str_replace("<br/>", " ", zen_get_products_name($product_id));
                //$html_message['PRODUCT_NAME'] = strip_tags(zen_get_products_name($product_id));
                $html_message['SPAM_LINK'] = zen_href_link(FILENAME_BACK_IN_STOCK,'bis_id='.$bis_id,'SSL');
                $html_message['TOP_MESSAGE'] = BACK_IN_STOCK_MAIL_TOP . $html_message['PRODUCT_NAME'] . "\n" . "\n" . BACK_IN_STOCK_MAIL_MAIN;
                if (BACK_IN_STOCK_DESC_IN_EMAIL == 1) {
                    $html_message['PRODUCT_DESCRIPTION'] = zen_get_products_description($product_id);
                } else {
                    $html_message['PRODUCT_DESCRIPTION'] = " ";
                }
                $html_message['PRODUCT_IMAGE'] = zen_get_products_image($product_id, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
                $html_message['PRODUCT_LINK'] = zen_href_link('product_info', 'products_id=' . $product_id);
                $html_message['BOTTOM_MESSAGE'] = BACK_IN_STOCK_MAIL_BOTTOM;
                $email_text = BACK_IN_STOCK_MAIL_GREETING . $customers_name . ',' . "\n" . "\n"
                        . $html_message['TOP_MESSAGE'] . "\n" . "\n"
                        . $html_message['PRODUCT_NAME'] . "\n"
                        . $html_message['PRODUCT_DESCRIPTION'] . "\n"
                        . $html_message['PRODUCT_LINK'] . "\n" . "\n"
                        . $html_message['BOTTOM_MESSAGE'] . "\n" . "\n"
                        . BACK_IN_STOCK_MAIL_CANCEL . "\n" . $html_message['SPAM_LINK'] . "\n";
                zen_mail($customers_name, $customers_email, $html_message['PRODUCT_NAME'] . BACK_IN_STOCK_MAIL_STATUS . STORE_NAME, $email_text, STORE_NAME, EMAIL_FROM, $html_message, 'back_in_stock_notification');
                if (BACK_IN_STOCK_SEND_ADMIN_EMAIL == 'true'  && BACK_IN_STOCK_ADMIN_EMAIL != '') {
                    zen_mail('', BACK_IN_STOCK_ADMIN_EMAIL, $html_message['PRODUCT_NAME'] . BACK_IN_STOCK_MAIL_STATUS . STORE_NAME, $email_text, STORE_NAME, EMAIL_FROM, $html_message, 'back_in_stock_notification');
                }
            }
            break;
        case "modify":
            if ($array['bis_id'] == '') {
                break;
            }
            $i = 0;
            $update = "";
            foreach ($array as $key => $value) {
                if ($key != "bis_id") {
                    $i++;
                    if ($i > 1) {
                        $update .= ", ";
                    }
                    if($key != "last_sent"){
                        $sql_value = "'".$value."'";
                    }
                    else{
                        $sql_value = $value;
                    }
                    $update .= " " . $key . "=" . $sql_value . " ";
                } else {
                    $where = " WHERE bis_id=" . (int) $value;
                }
            }
            $db->Execute("UPDATE " . TABLE_BACK_IN_STOCK . " SET " . $update . $where);
            break;
        case "delete":
            if ($array['bis_id'] != '') {
                $db->Execute("DELETE FROM " . TABLE_BACK_IN_STOCK . " WHERE bis_id=" . $array['bis_id']);
            }
            break;
    }
    return $result;
}

function back_in_stock_send($product_id = 0, $bis_id = 0, $preview = true) {
    global $db;
    cleanse_back_in_stock_subscriptions();
    if ($product_id != 0) {
        $addtl_where = ' AND product_id=' . $product_id;
    } else {
        $addtl_where = '';
    }
    if ($bis_id != 0) {
        $addtl_where .= ' AND bis_id=' . $bis_id;
    }
    // Find all Items in notifications
    $bis_emails = array();
    $now = time();
    $bis_products = $db->Execute("SELECT p.products_id, pd.products_name, b.product_id, b.last_sent, b.email, b.name, b.bis_id, b.active_til_purch, b.sub_active FROM "
            .TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_BACK_IN_STOCK." b WHERE p.products_id = pd.products_id AND p.products_id = b.product_id AND p.products_quantity > 0 AND sub_active=1 ". $addtl_where);
    while(!$bis_products->EOF){
        echo 'Back in stock: ' . $bis_products->fields['products_name'] . "\n" . "<br/>";
        $your_date = strtotime($bis_products->fields['last_sent']);
            $datediff = $now - $your_date;
            $days_since = floor($datediff / (60 * 60 * 24));
            if (BACK_IN_STOCK_DAYS_WAITING > $days_since && BACK_IN_STOCK_DAYS_WAITING != '0') {
                $bis_products->MoveNext();
            }
            $bis_emails[] = array(
                'email' => $bis_products->fields['email'],
                'name' => stripslashes($bis_products->fields['name']),
                'product_id' => $bis_products->fields['product_id'],
                'bis_id' => $bis_products->fields['bis_id'],
                'active_til_purch' => $bis_products->fields['active_til_purch']
            );
        $bis_products->MoveNext();
    }
        $counted = 0;
    if (!$preview) {
        foreach ($bis_emails as $emails) {
            if ($emails['email'] == '')
                continue;
            if ($counted >= (int) BACK_IN_STOCK_MAX_EMAILS_PER_BATCH && BACK_IN_STOCK_MAX_EMAILS_PER_BATCH != '0') {
                break;
            }
            $customers_name = stripslashes($emails['name']);
            $customers_email = $emails['email'];
            $html_message = array();
            $html_message['CUSTOMERS_NAME'] = $customers_name;
            $html_message['PRODUCT_NAME'] = strip_tags(zen_get_products_name($emails['product_id']));
            $html_message['SPAM_LINK'] = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG . 'index.php?main_page=back_in_stock&bis_id=' . $emails['bis_id'];
            $html_message['TOP_MESSAGE'] = BACK_IN_STOCK_MAIL_TOP . $html_message['PRODUCT_NAME'] . "\n" . "\n" . BACK_IN_STOCK_MAIL_AVAILABLE;
            if (BACK_IN_STOCK_DESC_IN_EMAIL == 1) {
                $html_message['PRODUCT_DESCRIPTION'] = zen_get_products_description($emails['product_id']);
            } else {
                $html_message['PRODUCT_DESCRIPTION'] = " ";
            }
            $html_message['PRODUCT_IMAGE'] = zen_get_products_image($emails['product_id'], LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
            $html_message['PRODUCT_LINK'] = zen_href_link('product_info', 'products_id=' . $emails['product_id']);
            $html_message['BOTTOM_MESSAGE'] = BACK_IN_STOCK_MAIL_BOTTOM;
            $email_text = BACK_IN_STOCK_MAIL_GREETING . $customers_name . ',' . "\n" . "\n"
                    . $html_message['TOP_MESSAGE'] . "\n" . "\n"
                    . $html_message['PRODUCT_NAME'] . "\n"
                    . $html_message['PRODUCT_DESCRIPTION'] . "\n"
                    . $html_message['PRODUCT_LINK'] . "\n" . "\n"
                    . $html_message['BOTTOM_MESSAGE'] . "\n" . "\n"
                    . BACK_IN_STOCK_MAIL_CANCEL . "\n" . $html_message['SPAM_LINK'] . "\n";
            $counted++;
            zen_mail($customers_name, $customers_email, $html_message['PRODUCT_NAME'] . BACK_IN_STOCK_MAIL_BACK . STORE_NAME, $email_text, STORE_NAME, EMAIL_FROM, $html_message, 'back_in_stock_notification');
            if (BACK_IN_STOCK_SEND_ADMIN_EMAIL == 'true') {
                zen_mail('', BACK_IN_STOCK_ADMIN_EMAIL, $html_message['PRODUCT_NAME'] . BACK_IN_STOCK_MAIL_BACK . STORE_NAME, $email_text, STORE_NAME, EMAIL_FROM, $html_message, 'back_in_stock_notification');
            }
            echo BACK_IN_STOCK_MAIL_SENT . $customers_email . "\n" . "<br/>";
            $modify_subscription = array(
                'bis_id' => $emails['bis_id'],
                'sub_active' => $emails['active_til_purch'],
                'last_sent' => "NOW()",
            );
            back_in_stock_subscription($modify_subscription, "modify");
        }
        ?>
        <br/>
        Processed <?php echo $counted; ?> Notifications
        <?php
        if ($counted == (int) BACK_IN_STOCK_MAX_EMAILS_PER_BATCH) {
            echo BACK_IN_STOCK_MAIL_MANY;
        }
    }
    if ($preview) {
        ?>
        <br/>Preview:</br>
        <table>
            <tr>
                <th>Customers Name</th>
                <th>Customers Email</th>
                <th>Product</th>
            </tr>
            <?php
            foreach ($bis_emails as $emails) {
                if ($counted >= (int) BACK_IN_STOCK_MAX_EMAILS_PER_BATCH && BACK_IN_STOCK_MAX_EMAILS_PER_BATCH != '0') {
                    break;
                }
                $counted++;
                ?>
                <tr>
                    <td><?php echo stripslashes($emails['name']); ?></td>
                    <td><?php echo $emails['email']; ?></td>
                    <td><?php echo zen_get_products_name($emails['product_id']); ?></td>
                </tr>
                <?php
                
            }
            ?>    
        </table>
<br/>
                You can Process <?php echo $counted; ?> Notifications by clicking:
                <?php
                echo '<a href="'.zen_href_link("cron/send_back_in_stock_notifications.php", zen_get_all_get_params(array('preview')),'NONSSL',true,true,true).'">'.'HERE'.'</a>';
                echo "\n".'<br/>';
                if ($counted == (int) BACK_IN_STOCK_MAX_EMAILS_PER_BATCH) {
                    echo BACK_IN_STOCK_MAIL_MANY;
                }
    }
}

function cleanse_back_in_stock_subscriptions(){
global $db;
$db->Execute("DELETE FROM ".TABLE_BACK_IN_STOCK." WHERE product_id NOT IN (SELECT p.products_id FROM ".TABLE_PRODUCTS." p)");
}

