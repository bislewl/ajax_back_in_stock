<?php

// Adding functionality to use CSS/Button

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_group_id, configuration_key, configuration_title, configuration_value, configuration_description) VALUES (" . (int) $configuration_group_id . ", 'BACK_IN_STOCK_POPUP_NOTIFY_IMG', 'Subscription Box - Notify Submit Button', '', 'If you want to use a button from your template from the notify me button enter the name here');");
