<?php

// moved language defines to configuration
// added preview functionality
// modified to have product drop down versus entry box

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_SUCCESS', 'Subscription Box - Success Message', 'You have been Subscribed', 'This is the message that should appear when they have successfully subscribed.');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_NAME_ERROR', 'Subscription Box - Name Error Message', 'Your name does look right', 'This is the message that should appear if they forgot to enter their name.');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_EMAIL_ADDRESS_ERROR', 'Subscription Box - Email Error Message', 'Please check your email address', 'This is the message that should appear when their email does not look correct.');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_NO_PRODUCT_ERROR', 'Subscription Box - Product Missing Error Message', 'Please select a product', 'This is the message that should appear when they did not select a product.');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_ALREADY_SUB', 'Subscription Box - Already Subscribed Error Message', 'You are already subscribed', 'This is the message that should appear when they already have an active subscription for this product.');");
