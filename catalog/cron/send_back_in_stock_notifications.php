<?php
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
if($_GET['key'] == BACK_IN_STOCK_CRON_KEY){
    if($_GET['products_id'] !== ''){
        $products_id = $_GET['products_id'];
    }
    else{
        $products_id = 0;
    }
    if($_GET['bis_id'] !== ''){
        $bis_id = $_GET['bis_id'];
    }
    else{
        $bis_id = 0;
    }
    back_in_stock_send($products_id,$bis_id);
}

