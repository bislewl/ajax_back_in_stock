<?php
$config_query = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='BACK_IN_STOCK_VERSION' LIMIT 1");
while(!$config_query->EOF){
    $configuration_group_id = $config_query->fields['configuration_group_id'];
    $config_query->MoveNext();
}

// Fix Spellign errors
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_title='Enable Module' "." WHERE configuration_key='BACK_IN_STOCK_ENABLE'");
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_title='Text of button' "." WHERE configuration_key='BACK_IN_STOCK_SEND_TEXT'");

$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='2.1.4' "." WHERE configuration_key='BACK_IN_STOCK_VERSION'");
