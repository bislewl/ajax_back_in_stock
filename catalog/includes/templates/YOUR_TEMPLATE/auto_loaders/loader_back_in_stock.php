<?php
/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (BACK_IN_STOCK_ENABLE == "true") {
    $loaders[] = array('conditions' => array('pages' => array('*')),
        'jscript_files' => array(
            'jquery/jquery-1.10.2.min.js' => 1,
            'jquery/jquery-migrate-1.2.1.min.js' => 2,
            'jquery/jquery.fancybox.js' => 3,
            'jquery/jquery_back_in_stock.js' => 4
        ),
        'css_files' => array(
            'jquery.fancybox.css' => 1,
            'back_in_stock.css' => 2
        )
    );
}