<?php

/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
$autoLoadConfig[90][] = array('autoType' => 'class',
    'loadFile' => 'observers/class.back_in_stock_after_purchase.php');
$autoLoadConfig[90][] = array('autoType' => 'classInstantiate',
    'className' => 'bisAfterPurchase',
    'objectName' => 'bisAfterPurchase');
