<?php
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
if($_GET['key'] == BACK_IN_STOCK_CRON_KEY){
    if($_GET['product_id'] !== ''){
        $products_id = $_GET['product_id'];
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
    if($_GET['preview'] == true){
        $preview = true;
    }
    else{
        $preview = false;
    }
    back_in_stock_send($products_id,$bis_id,$preview);
}

