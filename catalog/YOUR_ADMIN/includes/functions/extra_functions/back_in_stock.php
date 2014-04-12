<?php
function get_back_in_stock_sub_info($bis_id){
    global $db;
    $query = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE bis_id=".$bis_id);
    $return = array();
    $return['email'] = $query->fields['email'];
    $return['product_id'] = $query->fields['product_id'];
    $return['sub_date'] = $query->fields['sub_date'];
    $return['sub_active'] = $query->fields['sub_active'];
    $return['active_til_purch'] = $query->fields['active_til_purch'];
    $return['last_sent'] = $query->fields['last_sent'];
    $return['spam'] = $query->fields['spam'];
    return $return;
}
include_once DIR_FS_CATALOG.'includes/functions/extra_functions/back_in_stock.php';