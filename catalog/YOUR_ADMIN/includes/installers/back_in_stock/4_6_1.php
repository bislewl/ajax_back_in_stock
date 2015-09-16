<?php

/* 
 * 
 * @package ajax_back_in_stock
 * @copyright Copyright 2003-2015 ZenCart.Codes a Pro-Webs Company
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @filename 4_6_1.php
 * @file created 2015-09-16 11:05:34 AM
 * 
 */

// Added support for those browsers that can't process ajax/jQuery
// With a Post to the Back in stock page and a redirect to the previous page

// Modified JS to Support for FF running Windows 10
// Added a prevent default as it was processing the form BEFORE the Ajax call causing the back in stock page to load
