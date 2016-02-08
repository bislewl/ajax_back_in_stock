<?php
/**
 * @copyright Copyright 2010-2015  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (BACK_IN_STOCK_ENABLE == "true") {
    $loaders[] = array('conditions' => array('pages' => array('*')),
        'jscript_files' => array(
            '//code.jquery.com/jquery-1.12.0.min.js' => 1,
            'jquery/jquery.fancybox.js' => 2,
            'jquery/jquery_back_in_stock.js' => 3
        ),
        'css_files' => array(
            'jquery.fancybox.css' => 1,
            'back_in_stock.css' => 2
        )
    );
}