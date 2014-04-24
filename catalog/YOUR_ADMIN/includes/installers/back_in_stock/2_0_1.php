<?php
$config_query = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='BACK_IN_STOCK_VERSION' LIMIT 1");
while(!$config_query->EOF){
    $configuration_group_id = $config_query->fields['configuration_group_id'];
    $config_query->MoveNext();
}
//add admin CC email functionality
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_ADMIN_EMAIL', 'Admin Email Address', '".STORE_OWNER_EMAIL_ADDRESS."', 'This is the addess you want the copy of the admin emails to go to.');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_SEND_ADMIN_EMAIL', 'Send Admin Copy of Emails', 'false', 'Select tris if you want the email address on this screen to receive a copy', 'zen_cfg_select_option(array(\'true\', \'false\'),');");

//Fix incorrect table defines
$db->Execute("ALTER TABLE " . TABLE_BACK_IN_STOCK . " CHANGE `sub_date` `sub_date` DATETIME NULL DEFAULT NULL;");
$db->Execute("ALTER TABLE " . TABLE_BACK_IN_STOCK . " CHANGE `last_sent` `last_sent` DATETIME NULL DEFAULT NULL;");

//Add maximum emails per batch
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_MAX_EMAILS_PER_BATCH', 'Maximim Emails per Batch', '0', 'This is the maximum emails sent per batch sent out, you will need to rerun until all notifications are performed');");

//cron security key
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_CRON_KEY', 'Security Key', '8675309', 'This is the key required to run the back in stock email. Note: This is here to prevent incorrect use of the module' );");
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='2.0.1' "." WHERE configuration_key='BACK_IN_STOCK_VERSION'");