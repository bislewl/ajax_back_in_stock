<?php
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
//
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_SUBSCRIBE_BACK_IN_STOCK');

// simulate the contact us page
$_GET['main_page'] = $current_page_base = 'back_in_stock';
$language_page_directory = DIR_WS_LANGUAGES . $_SESSION['language'] . '/'; 
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$error = false;
$name = zen_db_prepare_input($_POST['contactname']);
$email_address = zen_db_prepare_input($_POST['email']);
$enquiry = zen_db_prepare_input(strip_tags($_POST['enquiry']));
$antiSpam = isset($_POST['should_be_empty']) ? zen_db_prepare_input($_POST['should_be_empty']) : '';
$zco_notifier->notify('NOTIFY_CONTACT_US_CAPTCHA_CHECK');
$product_id = zen_db_prepare_input($_POST['product_id']);

$zc_validate_email = zen_validate_email($email_address);

if($zc_validate_email && $antiSpam == '' && $product_id != ''){
 /*
  // Prepare extra-info details
  $extra_info = email_collect_extra_info($name, $email_address, $customer_name, $customer_email);
  // Prepare Text-only portion of message
  $text_message = OFFICE_FROM . "\t" . $name . "\n" .
  OFFICE_EMAIL . "\t" . $email_address . "\n\n" .
  '------------------------------------------------------' . "\n\n" .
  strip_tags($_POST['enquiry']) .  "\n\n" .
  '------------------------------------------------------' . "\n\n" .
  $extra_info['TEXT'];
  // Prepare HTML-portion of message
  $html_msg['EMAIL_MESSAGE_HTML'] = strip_tags($_POST['enquiry']);
  $html_msg['CONTACT_US_OFFICE_FROM'] = OFFICE_FROM . ' ' . $name . '<br />' . OFFICE_EMAIL . '(' . $email_address . ')';
  $html_msg['EXTRA_INFO'] = $extra_info['HTML'];
  // Send message
  if($setemailarr == 1 && is_array($emailarraysend)){
  
for ($i = 0; $i < count($emailarraysend); $i++) {
$emailtosendarr='';
$emailtosendarr = explode("||", $emailarraysend[$i]);
  zen_mail($emailtosendarr[0], $emailtosendarr[1], EMAIL_SUBJECT, $text_message, $name, $email_address, $html_msg,'contact_us');
 
  }
  
  }else{
  zen_mail($send_to_name, $send_to_email, EMAIL_SUBJECT, $text_message, $name, $email_address, $html_msg,'contact_us');
 
  }
 */
  
  $bis['email'] = $email_address;
  $bis['name'] = $name;
  $bis['product_id'] = $product_id;
  $returned_results = back_in_stock_subscription($bis, "add");
  if($returned_results === "Subscribed"){
      echo '<p class="messageStackSuccess">' . BACK_IN_STOCK_SUCCESS . '</p>';
  }
  else{
      echo '<p class="messageStackError">' . $returned_results . '</p>';
  }
} else {
  $error = true;
  if (empty($name)) {
    echo '<p class="messageStackError">' . BACK_IN_STOCK_NAME_ERROR . '</p>';
  }
  if ($zc_validate_email == false) {
    echo '<p class="messageStackError">' . BACK_IN_STOCK_EMAIL_ADDRESS_ERROR . '</p>';
  }
  if ($product_id == '') {
    echo '<p class="messageStackError">' . BACK_IN_STOCK_NO_PRODUCT_ERROR . '</p>';
  }
  echo '<p class="messageStackError">' . $returned_results . '</p>';
}

// This should be the last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_SUBSCRIBE_BACK_IN_STOCK');
//
require_once('includes/application_bottom.php');
?>
