<?php

// Fix Spellign errors
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_title='Enable Module' " . " WHERE configuration_key='BACK_IN_STOCK_ENABLE'");
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_title='Text of button' " . " WHERE configuration_key='BACK_IN_STOCK_SEND_TEXT'");
