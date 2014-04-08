<?php

if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', '');
}
//Tables
        define('TABLE_BACK_IN_STOCK',DB_PREFIX .'back_in_stock');


        // CEON's old table, needed for conversion if that is desired
        if(!defined(TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS)){
        define('TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS', DB_PREFIX .
            'back_in_stock_notification_subscriptions');
        }

//Files
        define('FILENAME_CHANGE_BACK_IN_STOCK', 'change_back_in_stock');
        define('FILENAME_BACK_IN_STOCK', 'back_in_stock');
        define('FILENAME_BACK_IN_STOCK_FANCYBOX', 'back_in_stock_fancybox');
