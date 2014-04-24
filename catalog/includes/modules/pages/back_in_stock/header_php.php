<?php
$bis_id = $_GET[bis_id];
if(is_numeric($bis_id)){
    switch ($_POST['action']) {
        case "stop":
        $modify_subscription = array(
            'bis_id' => $bis_id,
            'sub_active' => 0,
            'spam' => 1
        );
        back_in_stock_subscription($modify_subscription, "modify");
            break;
        case "delete":
            back_in_stock_subscription(array('bis_id' => $bis_id),"delete");
            break;
        
    }
            $bis_id_info = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE bis_id=".$bis_id);
            if($bis_id_info->RecordCount() > 0){
                $subcriptions = true;
            $email_info = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE email LIKE '".$bis_id_info->fields['email']."'");
            }
            else{
                $subcriptions = false;
                
            }
}
else if($_SESSION['customer_id']){
            $email = $db->Execute("SELECT customers_email_address FROM ".TABLE_CUSTOMERS." WHERE customers_id=".$_SESSION['customer_id']);
            
            $email_info = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE email LIKE '".$email->fields['customers_email_address']."'");
            if($email_info->RecordCount() > 0){
            $subcriptions = true;
            }
}
