<?php
/*
 * Back in Stock Notification
 * Forked / Inspired by the CEON Back In Stock Module. 
 * Please continue to keep Conor and his Family in our prayers 
 * 
 */
//sort multi-deminsional array
function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}


//Converts Ceon's table
function back_in_stock_convert(){
    global $db;
    $table_exists_query = 'SHOW TABLES LIKE "' .
			TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';
    $table_exists_result = $db->Execute($table_exists_query);
    if (!$table_exists_result->EOF) {
        $ceons_subscribers = $db-Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS);
        while(!$ceons_subscribers->EOF){
            $array = array();
            $array['product_id'] = $ceons_subscribers->fields['product_id'];
            $array['sub_date'] = $ceons_subscribers->fields['date_subscribed'];
            $array['sub_active'] = 1;
            if($ceons_subscribers->fields['customer_id'] != ''){
               $customer_info = $db->Execute("SELECT customers_email_address, customers_lastname, customers_firstname FROM ".TABLE_CUSTOMERS." WHERE customers_id=".$ceons_subscribers->fields['customer_id']); 
               $array['name'] = $customer_info->fields['customers_firstname']." ".$customer_info->fields['customers_lastname'];
               $array['email'] = $customer_info->fields['customers_email_address'];
            }
            else{
                $array['name'] = $ceons_subscribers->fields['name'];
                $array['email'] = $ceons_subscribers->fields['email_address'];
            }
            back_in_stock_subscription($array);
            $ceons_subscribers->MoveNext();
        }
    }
    $db->Execute("DROP TABLE ".TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS);
}

function back_in_stock_status ($email, $product = 0){
    global $db;
            if($product != 0){
                $product_query = " AND product_id =".$product;
            }
            else{
                $product_query = '';
            }
    $active = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE email='".$email."' AND sub_active=1 ".$product_query);
    return $active->RecordCount();
}

function back_in_stock_subscription($array, $change_type = "add"){
    global $db;
    $result = "Failed";
    $email = $array['email'];
    $name = $array['name'];
    $product_id = $array['product_id'];
    $current_status = back_in_stock_status($email, $product_id);
    switch ($change_type){
        case "add":
            if($current_status == 1){
                $result = BACK_IN_STOCK_ALREADY_SUB;
                break;
            }
            $db->Execute("INSERT INTO ".TABLE_BACK_IN_STOCK." (email, product_id, sub_date, sub_active, name, active_til_purch) VALUES
                     ('".$email."', ".$product_id.", NOW(), 1, '".$name."', ".BACK_IN_STOCK_ACTIVE_TIL_PURCH." )");
            $bis_id = $db->Insert_ID();
            $result = "Subscribed";
            //send email
            if(BACK_IN_STOCK_EMAIL_SUBSCRIBE){
                $customers_name = $name;
                $customers_email = $email;
                $html_message = array();
                $html_message['CUSTOMERS_NAME'] = $customers_name;
                $html_message['PRODUCT_NAME'] = strip_tags(zen_get_products_name($product_id));
                $html_message['SPAM_LINK'] = HTTPS_SERVER.DIR_WS_HTTPS_CATALOG.'index.php?main_page=back_in_stock&bis_id='.$bis_id;
                $html_message['TOP_MESSAGE'] = 'Thank You for your interest in the '.$html_message['PRODUCT_NAME'];
                if(BACK_IN_STOCK_DESC_IN_EMAIL == 1){
                $html_message['PRODUCT_DESCRIPTION'] = zen_get_products_description($product_id);
                }
                else{
                    $html_message['PRODUCT_DESCRIPTION'] = " ";
                }
                $html_message['PRODUCT_IMAGE'] = zen_get_products_image($product_id, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
                $html_message['PRODUCT_LINK'] = zen_href_link('product_info','products_id='.$product_id);
                $html_message['BOTTOM_MESSAGE'] = 'Please reply to this email with any questions';
                $email_text = 'Dear '.$customers_name.','. "\n"
                                .$html_message['TOP_MESSAGE']."\n"."\n"
                                .$html_message['PRODUCT_NAME']."\n"
                                .$html_message['PRODUCT_DESCRIPTION']."\n"
                                .$html_message['PRODUCT_LINK']."\n"."\n"
                                .$html_message['BOTTOM_MESSAGE']."\n"."\n"
                                .'To unsubscribe click here '.$html_message['SPAM_LINK']."\n";
                zen_mail($customers_name, $customers_email,$html_message['PRODUCT_NAME'].' Subscription is active '.STORE_NAME,$email_text,STORE_NAME, EMAIL_FROM,$html_message,'back_in_stock_notification');
                if(BACK_IN_STOCK_SEND_ADMIN_EMAIL == true){
                zen_mail('', BACK_IN_STOCK_ADMIN_EMAIL,$html_message['PRODUCT_NAME'].' Subscription is active '.STORE_NAME,$email_text,STORE_NAME, EMAIL_FROM,$html_message,'back_in_stock_notification');
                }
            }
            break;
        case "modify":
            if($array['bis_id'] == ''){
                break;
            }
            $i = 0;
            $update = '';
            foreach ($array as $key => $value) {
                if($key != "bis_id"){
                    $i++;
                    if($i > 1){
                        $update .= ", ";
                    }
                    $update .= " ".$key."=".(int)$value;
                }
                else{
                    $where = " WHERE bis_id=".(int)$value;
                }
            }
            $db->Execute("UPDATE ".TABLE_BACK_IN_STOCK." SET ".$update.$where);
            break;
        case "delete":
            if($array['bis_id'] != ''){
            $db->Execute("DELETE FROM ".TABLE_BACK_IN_STOCK." WHERE bis_id=".$array['bis_id']);
            }
            break;
    }
    return $result;
}

function back_in_stock_send ($product_id = 0,$bis_id = 0, $preview = true){
    global $db;
    if($product_id != 0){
        $addtl_where = ' AND product_id='.$product_id;
    }
    else{
        $addtl_where = '';
    }
    if($bis_id != 0){
        $addtl_where .= ' AND bis_id='.$bis_id;
    }
    // Find all Items in notifications
    $bis_emails[] = array();
    $bis_products = $db->Execute("SELECT DISTINCT product_id FROM ".TABLE_BACK_IN_STOCK." WHERE sub_active=1 ".$addtl_where);
    while(!$bis_products->EOF){
        if(zen_get_products_stock($bis_products->fields['product_id']) == 0){
            $bis_products->MoveNext();
        }
        echo 'Back in stock: '.zen_get_products_name($bis_products->fields['product_id'])."\n"."<br/>";
        $bis_notifications = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE sub_active=1 AND product_id=".$bis_products->fields['product_id']);
        while(!$bis_notifications->EOF){
                $now = time(); 
                $your_date = strtotime($bis_notifications->fields['last_sent']);
                $datediff = $now - $your_date;
                $days_since = floor($datediff/(60*60*24));
                if(BACK_IN_STOCK_DAYS_WAITING > $days_since){
                    $bis_notifications->MoveNext();
                }
                $bis_emails[] = array(
                                        'email' =>  $bis_notifications->fields['email'],
                                        'name' => $bis_notifications->fields['name'],
                                        'product_id' => $bis_notifications->fields['product_id'],
                                        'bis_id' => $bis_notifications->fields['bis_id'],
                                        'active_til_purch' => $bis_notifications->fields['active_til_purch']
                                      );

                $bis_notifications->MoveNext();
        }
        $bis_products->MoveNext();
    }
    if(!$preview){
    $counted = 0;
    foreach ($bis_emails as $emails) {
        if($emails['email'] == '')            continue;
        $counted++;
        if($counted >= (int)BACK_IN_STOCK_MAX_EMAILS_PER_BATCH){
            break;
        }
        $customers_name = $emails['name'];
        $customers_email = $emails['email'];
        $html_message = array();
        $html_message['CUSTOMERS_NAME'] = $customers_name;
        $html_message['PRODUCT_NAME'] = strip_tags(zen_get_products_name($emails['product_id']));
        $html_message['SPAM_LINK'] = HTTPS_SERVER.DIR_WS_HTTPS_CATALOG.'index.php?main_page=back_in_stock&bis_id='.$emails['bis_id'];
        $html_message['TOP_MESSAGE'] = 'Thank You for your interest in the '.$html_message['PRODUCT_NAME'];
        if(BACK_IN_STOCK_DESC_IN_EMAIL == 1){
        $html_message['PRODUCT_DESCRIPTION'] = zen_get_products_description($emails['product_id']);
        }
        else{
            $html_message['PRODUCT_DESCRIPTION'] = " ";
        }
        $html_message['PRODUCT_IMAGE'] = zen_get_products_image($emails['product_id'], LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
        $html_message['PRODUCT_LINK'] = zen_href_link('product_info','products_id='.$emails['product_id']);
        $html_message['BOTTOM_MESSAGE'] = 'Please reply to this email with any questions';
        $email_text = 'Dear '.$customers_name.','. "\n"
                        .$html_message['TOP_MESSAGE']."\n"."\n"
                        .$html_message['PRODUCT_NAME']."\n"
                        .$html_message['PRODUCT_DESCRIPTION']."\n"
                        .$html_message['PRODUCT_LINK']."\n"."\n"
                        .$html_message['BOTTOM_MESSAGE']."\n"."\n"
                        .'To unsubscribe click here '.$html_message['SPAM_LINK']."\n";
        zen_mail($customers_name, $customers_email,$html_message['PRODUCT_NAME'].' is Back In Stock at '.STORE_NAME,$email_text,STORE_NAME, EMAIL_FROM,$html_message,'back_in_stock_notification');
        if(BACK_IN_STOCK_SEND_ADMIN_EMAIL == true){
        $counted++;   
        zen_mail('', BACK_IN_STOCK_ADMIN_EMAIL,$html_message['PRODUCT_NAME'].' is Back In Stock at '.STORE_NAME,$email_text,STORE_NAME, EMAIL_FROM,$html_message,'back_in_stock_notification');
        }
        echo "Sent Email to: ".$customers_email."\n"."<br/>";
        $modify_subscription = array(
            'bis_id' => $emails['bis_id'],
            'sub_active' => $emails['active_til_purch'],
            'last_sent' => 'now()'
        );
        back_in_stock_subscription($modify_subscription, "modify");
    }
    }
    if($preview){
       ?>
<br/>Preview:</br>
<table>
    <tr>
        <th>Customers Name</th>
        <th>Customers Email</th>
        <th>Product</th>
    </tr>
    <?php
    $counted = 0;
    foreach ($bis_emails as $emails) {
        $counted++;
        if(BACK_IN_STOCK_SEND_ADMIN_EMAIL == true){
        $counted++;
        }
        if($counted >= (int)BACK_IN_STOCK_MAX_EMAILS_PER_BATCH){
            break;
        }
        ?>
    <tr>
        <td><?php echo $emails['name']; ?></td>
        <td><?php echo $emails['email']; ?></td>
        <td><?php echo zen_get_products_name($emails['product_id']); ?></td>
    </tr>
    <?php
    }
    ?>    
</table>

       <?php
    }
    ?>
<br/>
Processed <?php echo $counted;?> Notifications
<?php
if($counted == (int)BACK_IN_STOCK_MAX_EMAILS_PER_BATCH) {echo 'Please Run Again';}
?>
<?php
}