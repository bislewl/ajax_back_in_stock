<?php

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

$module_constant = 'BACK_IN_STOCK_VERSION'; // This should be a UNIQUE name followed by _VERSION for convention
$module_installer_directory = DIR_FS_ADMIN . 'includes/installers/back_in_stock'; // This is the directory your installer is in, usually this is lower case
$module_name = "Ajax Back In Stock"; // This should be a plain English or Other in a user friendly way
$zencart_com_plugin_id = 1944; // from zencart.com plugins - Leave Zero not to check
//Just change the stuff above... Nothing down here should need to change


$configuration_group_id = '';
if (defined($module_constant)) {
    $current_version = constant($module_constant);
} else {
    $current_version = "0.0.0";
    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('" . $module_name . "', 'Set " . $module_name . " Options', '1', '1');");
    $configuration_group_id = $db->Insert_ID();

    $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = " . $configuration_group_id . " WHERE configuration_group_id = " . $configuration_group_id . ";");

    $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES
                    ('Version', '" . $module_constant . "', '0.0.0', 'Version installed:', " . $configuration_group_id . ", 0, NOW(), NOW(), NULL, NULL);");
}
if ($configuration_group_id == '') {
    $config = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key= '" . $module_constant . "'");
    $configuration_group_id = $config->fields['configuration_group_id'];
}

$installers = scandir($module_installer_directory, 1);

$newest_version = $installers[0];
$newest_version = substr($newest_version, 0, -4);

sort($installers);
if (version_compare($newest_version, $current_version) > 0) {
    foreach ($installers as $installer) {
        if (version_compare($newest_version, substr($installer, 0, -4)) >= 0 && version_compare($current_version, substr($installer, 0, -4)) < 0) {
            include($module_installer_directory . '/' . $installer);
            $current_version = str_replace("_", ".", substr($installer, 0, -4));
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . $current_version . "' WHERE configuration_key = '" . $module_constant . "' LIMIT 1;");
            $messageStack->add("Installed " . $module_name . " v" . $current_version, 'success');
        }
    }
}

// Version Checking 
if ($zencart_com_plugin_id != 0) {
    $new_version_details = plugin_version_check_for_updates($zencart_com_plugin_id, $current_version);
    if ($_GET['gID'] == $configuration_group_id) {
        $messageStack->add("Version " . $new_version_details['latest_plugin_version'] . " of " . $new_version_details['title'] . ' is available at <a href="' . $new_version_details['link'] . '" target="_blank">[Details]</a>', 'caution');
    }
}

if (!function_exists('plugin_version_check_for_updates')) {

    function plugin_version_check_for_updates($fileid = 0, $version_string_to_check = '') {
        if ($fileid == 0) {
            return FALSE;
        }
        $new_version_available = FALSE;
        $lookup_index = 0;
        $url = 'http://www.zen-cart.com/downloads.php?do=versioncheck' . '&id=' . (int) $fileid;
        $data = json_decode(file_get_contents($url), true);
        // compare versions
        if (version_compare($data[$lookup_index]['latest_plugin_version'], $version_string_to_check) > 0) {
            $new_version_available = TRUE;
        }
        // check whether present ZC version is compatible with the latest available plugin version
        if (!in_array('v' . PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR, $data[$lookup_index]['zcversions'])) {
            $new_version_available = FALSE;
        }
        if ($version_string_to_check == true) {
            return $data[$lookup_index];
        } else {
            return FALSE;
        }
    }

}

 
