<?php

// Show description in email
// Increased image size on email
// link in my account or otherwise page

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_DESC_IN_EMAIL', 'Email - Description', 'true', 'If you want to show the description in the email', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_EMAIL_SUBSCRIBE', 'Send Email Confirming Subscription', 'true', 'Select this option if you want the customer to get notifed they have joined the Back In Stock Notifications', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_MY_ACCOUNT_LINK', 'My Account - Link Text', 'Change my Product Back In Stock Notifications', 'The text that will appear for the link on my account page');");
