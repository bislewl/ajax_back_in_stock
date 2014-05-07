<?php

// Added sort in admin ability
// Added conent to emails

$config_query = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='BACK_IN_STOCK_VERSION' LIMIT 1");
while(!$config_query->EOF){
    $configuration_group_id = $config_query->fields['configuration_group_id'];
    $config_query->MoveNext();
}
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='2.1.3' "." WHERE configuration_key='BACK_IN_STOCK_VERSION'");


