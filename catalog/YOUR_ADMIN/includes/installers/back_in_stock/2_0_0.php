<?php
// create table for back in stock
$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_BACK_IN_STOCK . " (
	`bis_id` int(11) NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL default '0',
        `variant` int(11) NOT NULL default '0',
	`sub_date` int(11) NOT NULL default '0',
        `purch_date` int(11) NOT NULL default '0',
        `name` varchar(96) NOT NULL,
        `email` varchar(96) NOT NULL,
	`active_til_purch` int(11) NOT NULL DEFAULT '1',
        `sub_active` int(11) NOT NULL DEFAULT '1',
        `spam` int(11) NOT NULL DEFAULT '0',
	`last_sent` int(11) NOT NULL default '0',
	PRIMARY KEY ( `bis_id` ));"
);

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_ENABLE', 'Enabel Module', 'false', 'Should this module be used', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_LINK', 'Back In Stock Link', 'Email me when back in stock', 'this is the words that someone would click on. HINT - You could also put a img tag here too!');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_POPUP_HEADING', 'Heading for Popup', 'Sorry we ran out!', 'This heading is in the popup');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_POPUP_SUBHEADING', 'Sub Heading for Popup', 'Fill out this form and we will let you know when it comes back in stock', 'This Sub heading is in the popup');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_SEND_TEXT', 'Text of buton', 'Notify Me!', 'This is the text on the button');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_SHOW_PRODUCT_INFO', 'Show Product Name and Image', 'true', 'Show product name and image on the fancy box on the product_info page', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_ACTIVE_TIL_PURCH', 'Notifications are Active Till They Purchase', 'false', 'This is useful if you want to sent your customers multipule reminders, until they Buy or Die', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int)$configuration_group_id . ", 'BACK_IN_STOCK_DAYS_WAITING', 'Days of Lag', '5', 'This is the number of days until you should send them again, this is an almost must if leaving Active Till Purch Active, you can also set zero to be notified everytime the cron is run but this will more then likely make you customer want to unsubscribre');");

if(version_compare(PROJECT_VERSION_MAJOR.".".PROJECT_VERSION_MINOR, "1.5.0") >= 0) { 
  // continue Zen Cart 1.5.0
    // add to configuration menus
  if (function_exists('zen_page_key_exists') && function_exists('zen_register_admin_page') && !zen_page_key_exists('configBackInStock')) {
    zen_register_admin_page('configBackInStock',
                            'BOX_BACK_IN_STOCK', 
                            'FILENAME_CONFIGURATION',
                            'gID='.(int)$configuration_group_id, 
                            'configuration', 
                            'Y',
                            999);
      
    $messageStack->add('Enabled Back In Stock Configuration menu.', 'success');
  }
  // add to tools menu
  if (function_exists('zen_page_key_exists') && function_exists('zen_register_admin_page') && !zen_page_key_exists('toolsBackInStock')) {
    zen_register_admin_page('toolsBackInStock',
                            'BOX_BACK_IN_STOCK_TOOLS', 
                            'FILENAME_BACK_IN_STOCK',
                            '', 
                            'tools', 
                            'Y',
                            999);
      
    $messageStack->add('Enabled Back In Stock Tools menu.', 'success');
  }      
}

