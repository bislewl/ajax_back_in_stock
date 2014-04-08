<?php
/**
 * @package Pages
 * @copyright Copyright 2008-2010 RubikIntegration.com
 * @author yellow1912
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

/**
 * NOTE: You can use php files for both javascript and css.
 *
 * 
 */
if(BACK_IN_STOCK_ENABLE == "true"){
$loaders[] = array('conditions' => array('pages' => array('*')),
	'jscript_files' => array(
		'jquery/jquery-1.10.2.min.js' => 1,
		'jquery/jquery-migrate-1.2.1.min.js' => 2,
		'jquery/jquery.fancybox.js' => 3,
                'jquery/jquery_back_in_stock.js' => 4
		
	),
	'css_files' => array(	
                'jquery.fancybox.css' => 1,
		'back_in_stock.css' => 1
	)
);
}