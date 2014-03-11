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

$loaders[] = array('conditions' => array('pages' => array('*')),
	'jscript_files' => array(
		'jquery/jquery-1.10.2.min.js' => 1,
		'jquery/jquery-migrate-1.2.1.min.js' => 2,
		'jquery/jquery.fancybox.js' => 3
		
	),
	'css_files' => array(	
		'auto_loaders/jquery.fancybox.css' => 1
	)
);
