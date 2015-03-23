<?php

// Moved loading of jquery into module's template file, versus in the header to avoid "render blocking JS/CSS" that decreases google page speed
// Removed jQuery migrate as it isn't needed
// Changed jquery version to 1.11.1 as this is used in ZC 154
// Added Script to check if jquery is present to avoid duplicate loading
// Modifed DIR_FS_CATALOG.'includes/modules/pages/index/jscript_back_in_stock.php' to self delete since older versions will have this file present
// Modifed DIR_FS_CATALOG.'includes/modules/pages/product_info/jscript_back_in_stock.php' to self delete since older versions will have this file present

// Getting rid of the files here too!
if(file_exists(DIR_FS_CATALOG.'includes/modules/pages/index/jscript_back_in_stock.php')){
         unlink(DIR_FS_CATALOG.'includes/modules/pages/index/jscript_back_in_stock.php');
     }
     