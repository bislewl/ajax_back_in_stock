<?php
/*
 * Back in Stock Notification
 * Inspired by the CEON Back In Stock Module. Please continue to keep Conor and his Family in our prayers 
 * 
 */
//Converts Ceon's table into ours
function back_in_stock_convert(){
    
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
                $result = "Failed Already Subscribed";
                break;
            }
            $db->Execute("INSERT INTO ".TABLE_BACK_IN_STOCK." (email, product_id, sub_date, sub_active) VALUES
                     ('".$email."', ".$product_id.", NOW(), 1 )");
            $result = "Subscribed";
            break;
        case "modify":
            break;
        case "delete":
            break;
    }
    return $result;
}

function back_in_stock_send ($products_id = 0,$bis_id = 0){
    
}