<?php
  if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
  }   
 
 $module_installer_directory =  DIR_FS_ADMIN.'includes/installers/back_in_stock';
 $module_name = "Back In Stock"; 

 if(defined('BACK_IN_STOCK_VERSION')) { $current_version =  BACK_IN_STOCK_VERSION; } else { $current_version = "0.0.0"; }
  
 $installers = scandir($module_installer_directory, 1);
 
 $newest_version = $installers[0];
 $newest_version = substr($newest_version,0,-4);
 
 sort($installers);
 if(version_compare($newest_version, $current_version) > 0){
     foreach ($installers as $installer) {
         if(version_compare($newest_version, substr($installer,0,-4) ) >= 0 && version_compare($current_version, substr($installer,0,-4) ) < 0 ){
         include($module_installer_directory.'/'.$installer);
         $current_version = str_replace("_", ".", substr($installer,0,-4));
         $messageStack->add("Installed ".$module_name." V ".$current_version, 'success');
         }
     }     
 }

 

