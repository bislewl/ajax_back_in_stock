<?php

/**
 * @copyright Copyright 2010-2015  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
if ($_GET['key'] == BACK_IN_STOCK_CRON_KEY) {
    if ($_GET['product_id'] !== '') {
        $products_id = zen_db_prepare_input($_GET['product_id']);
    } else {
        $products_id = 0;
    }
    if ($_GET['bis_id'] !== '') {
        $bis_id = zen_db_prepare_input($_GET['bis_id']);
    } else {
        $bis_id = 0;
    }
    if ($_GET['preview'] == true) {
        $preview = true;
    } else {
        $preview = false;
    }
    back_in_stock_send($products_id, $bis_id, $preview);
}

