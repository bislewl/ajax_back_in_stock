<?php

// Adding functionality to use CSS/Button

$config_query = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='BACK_IN_STOCK_VERSION' LIMIT 1");
while(!$config_query->EOF){
    $configuration_group_id = $config_query->fields['configuration_group_id'];
    $config_query->MoveNext();
}

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_POPUP_NOTIFY_IMG', 'Subscription Box - Notify Submit Button', '', 'If you want to use a button from your template from the notify me button enter the name here');");

$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='2.1.1' "." WHERE configuration_key='BACK_IN_STOCK_VERSION'");


