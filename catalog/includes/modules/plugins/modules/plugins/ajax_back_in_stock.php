<?php

/*
 * 
 * @package ajax_back_in_stock
 * @copyright Copyright 2003-2015 ZenCart.Codes a Pro-Webs Company
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @filename ajax_back_in_stock.php
 * @file created 2015-07-28 1:43:21 PM
 * 
 * TODO's
 * modules.php
 * incorp zenplugin check
 * add config values function
 * 
 */

class ajaxBackInStock extends base {

    var $code, $title, $description, $enabled;

// class constructor
    function __construct() {
        global $order;

        $this->code = 'back_in_stock';
        $this->title = MODULE_PAYMENT_AJAX_BACK_IN_STOCK_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_AJAX_BACK_IN_STOCK_TEXT_DESCRIPTION;
        $this->plugin_version = '4.5.0';
        $this->plugin_id = 1944;
        $this->plugin_admin_pages = 'configBackInStock;toolsBackInStock';
        $this->enabled = ((BACK_IN_STOCK_ENABLE == 'True') ? true : false);
    }

// class methods
    function update_status() {
        return false;
    }

    function javascript_validation() {
        return false;
    }

    function selection() {
        return false;
    }

    function pre_confirmation_check() {
        return false;
    }

    function confirmation() {
        return false;
    }

    function process_button() {
        return false;
    }

    function before_process() {
        return false;
    }

    function after_process() {
        return false;
    }

    function get_error() {
        return false;
    }

    function check() {
        global $db;
        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'BACK_IN_STOCK_ENABLE'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }
    
    function plugin_version(){
        return $this->plugin_version;
    }

    function plugin_id(){
        return $this->plugin_id;
    }
    
    function install() {
        global $db, $messageStack, $sniffer;
        // BOF v2.0.0
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

        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_LINK', 'Back In Stock Link', 'Email me when back in stock', 'this is the words that someone would click on. HINT - You could also put a img tag here too!');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_POPUP_HEADING', 'Heading for Popup', 'Sorry we ran out!', 'This heading is in the popup');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_POPUP_SUBHEADING', 'Sub Heading for Popup', 'Fill out this form and we will let you know when it comes back in stock', 'This Sub heading is in the popup');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_SEND_TEXT', 'Text of buton', 'Notify Me!', 'This is the text on the button');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES ('6', 'BACK_IN_STOCK_SHOW_PRODUCT_INFO', 'Show Product Name and Image', 'true', 'Show product name and image on the fancy box on the product_info page', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES ('6', 'BACK_IN_STOCK_ACTIVE_TIL_PURCH', 'Notifications are Active Till They Purchase', 'false', 'This is useful if you want to sent your customers multipule reminders, until they Buy or Die', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_DAYS_WAITING', 'Days of Lag', '5', 'This is the number of days until you should send them again, this is an almost must if leaving Active Till Purch Active, you can also set zero to be notified everytime the cron is run but this will more then likely make you customer want to unsubscribre');");

        if (version_compare(PROJECT_VERSION_MAJOR . "." . PROJECT_VERSION_MINOR, "1.5.0") >= 0) {
            // continue Zen Cart 1.5.0
            // add to configuration menus
            if (function_exists('zen_page_key_exists') && function_exists('zen_register_admin_page') && !zen_page_key_exists('configBackInStock')) {
                zen_register_admin_page('configBackInStock', 'BOX_BACK_IN_STOCK', 'FILENAME_CONFIGURATION', 'gID=' . (int) $configuration_group_id, 'configuration', 'Y', 999);
            }
            $messageStack->add('Enabled Back In Stock Configuration menu.', 'success');
            // add to tools menu
            if (function_exists('zen_page_key_exists') && function_exists('zen_register_admin_page') && !zen_page_key_exists('toolsBackInStock')) {
                zen_register_admin_page('toolsBackInStock', 'BOX_BACK_IN_STOCK_TOOLS', 'FILENAME_BACK_IN_STOCK', '', 'tools', 'Y', 999);
            }
            $messageStack->add('Enabled Back In Stock Tools menu.', 'success');
        }

        // BOF v2.0.1
        //add admin CC email functionality
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_ADMIN_EMAIL', 'Admin Email Address', '" . STORE_OWNER_EMAIL_ADDRESS . "', 'This is the addess you want the copy of the admin emails to go to.');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES ('6', 'BACK_IN_STOCK_SEND_ADMIN_EMAIL', 'Send Admin Copy of Emails', 'false', 'Select this if you want the email address on this screen to receive a copy', 'zen_cfg_select_option(array(\'true\', \'false\'),');");

        //Fix incorrect table defines
        if ($sniffer->field_exists(TABLE_BACK_IN_STOCK, 'sub_date')) $db->Execute("ALTER TABLE " . TABLE_BACK_IN_STOCK . " CHANGE `sub_date` `sub_date` DATETIME NULL DEFAULT NULL;");
        if ($sniffer->field_exists(TABLE_BACK_IN_STOCK, 'last_sent'))$db->Execute("ALTER TABLE " . TABLE_BACK_IN_STOCK . " CHANGE `last_sent` `last_sent` DATETIME NULL DEFAULT NULL;");

        //Add maximum emails per batch
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_MAX_EMAILS_PER_BATCH', 'Maximum Emails per Batch', '0', 'This is the maximum emails sent per batch sent out, you will need to rerun until all notifications are performed');");

        //cron security key
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_CRON_KEY', 'Security Key', '8675309', 'This is the key required to run the back in stock email. Note: This is here to prevent incorrect use of the module' );");

        //BOF v2.1.0
        // moved language defines to configuration
        // added preview functionality
        // modified to have product drop down versus entry box

        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_SUCCESS', 'Subscription Box - Success Message', 'You have been Subscribed', 'This is the message that should appear when they have successfully subscribed.');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_NAME_ERROR', 'Subscription Box - Name Error Message', 'Your name does look right', 'This is the message that should appear if they forgot to enter their name.');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_EMAIL_ADDRESS_ERROR', 'Subscription Box - Email Error Message', 'Please check your email address', 'This is the message that should appear when their email does not look correct.');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_NO_PRODUCT_ERROR', 'Subscription Box - Product Missing Error Message', 'Please select a product', 'This is the message that should appear when they did not select a product.');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_ALREADY_SUB', 'Subscription Box - Already Subscribed Error Message', 'You are already subscribed', 'This is the message that should appear when they already have an active subscription for this product.');");

        //BOF v2.1.1
        // Adding functionality to use CSS/Button
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_POPUP_NOTIFY_IMG', 'Subscription Box - Notify Submit Button', '', 'If you want to use a button from your template from the notify me button enter the name here');");

        //BOF v2.1.2
        // Show description in email
        // Increased image size on email
        // link in my account or otherwise page
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES ('6', 'BACK_IN_STOCK_DESC_IN_EMAIL', 'Email - Description', 'true', 'If you want to show the description in the email', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description, set_function) VALUES ('6', 'BACK_IN_STOCK_EMAIL_SUBSCRIBE', 'Send Email Confirming Subscription', 'true', 'Select this option if you want the customer to get notifed they have joined the Back In Stock Notifications', 'zen_cfg_select_option(array(\'true\', \'false\'),');");
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES ('6', 'BACK_IN_STOCK_MY_ACCOUNT_LINK', 'My Account - Link Text', 'Change my Product Back In Stock Notifications', 'The text that will appear for the link on my account page');");

        //BOF v2.1.4
        // Fix Spellign errors
        $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_title='Text of button' " . " WHERE configuration_key='BACK_IN_STOCK_SEND_TEXT'");
    }

    function remove() {
        global $db;
        $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
        $admin_pages_array = explode(';', $this->plugin_admin_pages);
        foreach ($admin_pages_array as $admin_page) {
            $db->Execute("DELETE FROM admin_pages WHERE page_key = '" . $admin_page . "' LIMIT 1;");
        }
        $db->Execute("DROP TABLE ".TABLE_BACK_IN_STOCK);
    }

    function keys() {
        return array('BACK_IN_STOCK_LINK',
            'BACK_IN_STOCK_POPUP_HEADING',
            'BACK_IN_STOCK_POPUP_SUBHEADING',
            'BACK_IN_STOCK_SEND_TEXT',
            'BACK_IN_STOCK_SHOW_PRODUCT_INFO',
            'BACK_IN_STOCK_ACTIVE_TIL_PURCH',
            'BACK_IN_STOCK_DAYS_WAITING',
            'BACK_IN_STOCK_ADMIN_EMAIL',
            'BACK_IN_STOCK_SEND_ADMIN_EMAIL',
            'BACK_IN_STOCK_MAX_EMAILS_PER_BATCH',
            'BACK_IN_STOCK_CRON_KEY',
            'BACK_IN_STOCK_SUCCESS',
            'BACK_IN_STOCK_NAME_ERROR',
            'BACK_IN_STOCK_EMAIL_ADDRESS_ERROR',
            'BACK_IN_STOCK_NO_PRODUCT_ERROR',
            'BACK_IN_STOCK_ALREADY_SUB',
            'BACK_IN_STOCK_POPUP_NOTIFY_IMG',
            'BACK_IN_STOCK_DESC_IN_EMAIL',
            'BACK_IN_STOCK_EMAIL_SUBSCRIBE',
            'BACK_IN_STOCK_MY_ACCOUNT_LINK',);
    }
}
