<?php

//Ajax Back In Stock
if(!defined(CSS_JS_LOADER_VERSION)){
    echo '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('jquery.fancybox.css',DIR_WS_TEMPLATE, $current_page_base,'css') . '/jquery.fancybox.css" />'."\n";
    echo '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('back_in_stock.css',DIR_WS_TEMPLATE, $current_page_base,'css') . '/back_in_stock.css" />'."\n";
    echo '<script type="text/javascript" src="' .  $template->get_template_dir('jquery-1.10.2.min.js',DIR_WS_TEMPLATE, $current_page_base,'jscript/jquery') . '/jquery-1.10.2.min.js"></script>'."\n";
    echo '<script type="text/javascript" src="' .  $template->get_template_dir('jquery-migrate-1.2.1.min.js',DIR_WS_TEMPLATE, $current_page_base,'jscript/jquery') . '/jquery-migrate-1.2.1.min.js"></script>'."\n";
    echo '<script type="text/javascript" src="' .  $template->get_template_dir('jquery.fancybox.js',DIR_WS_TEMPLATE, $current_page_base,'jscript/jquery') . '/jquery.fancybox.js"></script>'."\n";
    echo '<script type="text/javascript" src="' .  $template->get_template_dir('jquery_back_in_stock.js',DIR_WS_TEMPLATE, $current_page_base,'jscript/jquery') . '/jquery_back_in_stock.js"></script>'."\n";
}
// DEBUG: echo '<!-- I SEE cat: ' . $current_category_id . ' || vs cpath: ' . $cPath . ' || page: ' . $current_page . ' || template: ' . $current_template . ' || main = ' . ($this_is_home_page ? 'YES' : 'NO') . ' -->';


