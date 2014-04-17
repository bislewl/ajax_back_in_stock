<?php
  if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
  }   
   

 if(defined('BACK_IN_STOCK_VERSION')) { $current_version =  BACK_IN_STOCK_VERSION; } else { $current_version = "0.0.0"; }
 
 $module_installer_directory =  DIR_FS_ADMIN.'includes/installers/back_in_stock';
 $current_version = BACK_IN_STOCK_VERSION;
 $module_name = "Back In Stock";
 
 $installers = scandir($module_installer_directory, 1);

 $newest_version = $installers[0];
 $newest_version = str_replace(".php", "", $newest_version);
 $newest_version = str_replace("_", ".", $newest_version);
 
 sort($installers);
 if($newest_version != $current_version){
     foreach ($installers as $installer) {
         if(version_compare($newest_version, substr($installer,0,-4) ) >= 0){
         include($module_installer_directory.'/'.$installer);
         $pretty_version = str_replace("_", ".", substr($installer,0,-4));
         $messageStack->add("Installed ".$module_name." V".$pretty_version, 'success');
         }
     }     
 }

 

