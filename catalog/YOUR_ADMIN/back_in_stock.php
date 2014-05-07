<?php
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  
if($_GET['bis_selected'] != ''){
$bis_selected = $_GET['bis_selected'];
}
else{
    $bis_selected = 0;
}

$convert_get = $_GET['convert'];
if($convert_get == true){
    $conversion_offered = ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'confirm_convert=true') .'">Confirm Converison from CEON Back In Stock?</a><br/>';
}
$confirm_convert_get = $_GET['confirm_convert'];
if($confirm_convert_get == true){
    $conversion_offered = "Conversion Complete";
}
$table_exists_query = 'SHOW TABLES LIKE "' .
			TABLE_BACK_IN_STOCK_NOTIFICATION_SUBSCRIPTIONS . '";';
    $table_exists_result = $db->Execute($table_exists_query);
if (!$table_exists_result->EOF) {
        $ceon_bis_table_present = true; 
  }
if($confirm_convert_get != true && $convert_get != true && $ceon_bis_table_present == true){
    $conversion_offered = ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'convert=true') .'">Convert from CEON Back In Stock?</a><br/>';
}    

$bis_show = $_GET['filter'];
$product_id = $_POST['pid'];
$subscriber = $_POST['sub_email'];

switch($bis_show){
    case "all":
        $sql_statement = "SELECT * FROM ".TABLE_BACK_IN_STOCK;
        $header_comment = "showing all active and non active subscriptions";
        break;
    case "product":
        $sql_statement = "SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE product_id=".$product_id." AND sub_active = 1";
        $header_comment = "showing all active subscriptions to ".zen_get_products_name($product_id);
        break;
    case "subscriber":
        $sql_statement = "SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE email='".$subscriber."' AND sub_active = 1";
        $header_comment = "showing all active Subscriptions for ".$subscriber;
        break;
    default:
        $sql_statement = "SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE sub_active = 1";
        $header_comment = "showing all active subscriptions";
        break;
    
}

$sort = $_GET['sort'];
if($sort != ''){
    $order_by = " ORDER BY ".$sort." ASC";
}
else{
    $order_by = " ";
}
$subscribers = $db->Execute($sql_statement.$order_by);


$record_count = $subscribers->RecordCount();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->


<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="75%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">Back In Stock Notifications
                <br/>
            <?php echo $header_comment;?>
            </td>
            <td class="pageHeading" align="right">
                <?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
                    <?php
                    echo 'Email:'; 
                    echo zen_draw_form('back_in_stock', FILENAME_BACK_IN_STOCK, 'filter=subscriber', 'post', '', true);
                    echo zen_hide_session_id();
                    echo zen_draw_input_field('sub_email')
                    ?>
                    </form><br/>
                    <?php
                    echo 'Product ID:'; 
                    echo zen_draw_form('back_in_stock', FILENAME_BACK_IN_STOCK, 'filter=subscriber', 'post', '', true);
                    echo zen_hide_session_id();
                    echo zen_draw_input_field('pid')
                    ?>
                    </form><br/>
                    <?php
                    if($bis_show != "all"){
                    echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK, 'filter=all') .'">Show Active & Non-Active</a><br/>';
                    }
                    ?>
                    <?php
                    if($bis_show != ''){
                    echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK) .'">Show Active Only</a><br/>';    
                    }
                    ?>
            </td>
          </tr>
          <tr>
              <td>
                 <form name="back_in_stock" action="<?php echo HTTPS_CATALOG_SERVER.DIR_WS_HTTPS_CATALOG."cron/send_back_in_stock_notifications.php";?>" target="_blank" method="get">
                  Product: <?php echo zen_draw_products_pull_down('product_id','> <option value="0">All Products</option'); ?>
                  <?php echo zen_draw_hidden_field('key',BACK_IN_STOCK_CRON_KEY)?>
                  <?php echo zen_draw_hidden_field('bis_id','0')?>
                  <?php echo 'Preview: '.zen_draw_checkbox_field('preview','true',true) ?>
                  <input type="submit" value="Run Notifications">
                 </form>
              </td>
              <td><?php
              echo $conversion_offered;
              ?></td>
          </tr>
        </table></td>
      </tr>
        </table>
    </td>
<!-- body_text_eof //-->
  </tr>
  <tr>
  <td width="75%" valign="top">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
           <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left" valign="top">ID</td>
                <td class="dataTableHeadingContent" align="center" valign="top">
                    <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK,'sort=sub_date') .'">Date Subscribed</a><br/>';?></td>
                <td class="dataTableHeadingContent" align="center" valign="top">
                    <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK,'sort=email') .'">Email</a><br/>';?></td>
                <td class="dataTableHeadingContent" align="center" valign="top">Active</td>
                <td class="dataTableHeadingContent" align="center" valign="top">
                    <?php echo ' <a href="' . zen_href_link(FILENAME_BACK_IN_STOCK,'sort=product_id') .'">Product</a><br/>';?></td>
           </tr>
           <?php
           $rowi = 0;
           while(!$subscribers->EOF){
                $rowi++;
                if($rowi % 2 == 0){ $over = 'Over'; }
                else{ $over = ''; }

                $rowheader = 'class="dataTableRow'.$over.'"';
                if($rowi == 1 && $bis_selected == 0){
                    $rowheader = 'id="defaultSelected" class="dataTableRow'.$over.'Selected"';
                    $bis_selected = $subscribers->fields['bis_id'];
                }
                if($subscribers->fields['bis_id'] == $bis_selected){
                    $rowheader = 'id="defaultSelected" class="dataTableRow'.$over.'Selected"';
                }
           ?>
           <tr <?php echo $rowheader; ?> onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href='<?php echo zen_href_link(FILENAME_BACK_IN_STOCK, 'bis_selected=' . $subscribers->fields['bis_id']);?>'">
                <td class="dataTableContent" align="left"><?php echo $subscribers->fields['bis_id'];?></td>
                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['sub_date'];?></td>
                <td class="dataTableContent" align="center"><?php echo $subscribers->fields['email'];?></td>
                <td class="dataTableContent" align="center"><?php echo ($subscribers->fields['sub_active'] == 1 ? 'Y' : 'N');?></td>
                <td class="dataTableContent" align="center"><?php echo zen_get_products_name($subscribers->fields['product_id']);?></td>
           </tr>
           <?php
           $subscribers->MoveNext();
           }
           ?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tbody><tr>
                    <td class="smallText" valign="top">Displaying <?php echo $record_count; ?> Active Notification Subscriptions</td>
                    <td class="smallText" align="right"></td>
                  </tr>
                </tbody></table></td>
              </tr>
            </table>
  </td>
  <?php
  $bis_sub_info = get_back_in_stock_sub_info($bis_selected);
  ?>
  <td width="25%" valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr class="infoBoxHeading">
            <td class="infoBoxHeading"><b>ID#<?php echo $bis_selected."  ".$bis_sub_info['email']; ?></b></td>
        </tr>
    </table>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
            <td class="infoBoxContent"><br><b>Subscription Started:</b> <?php echo $bis_sub_info['sub_date']; ?></td>
        </tr>
        <tr>
            <td class="infoBoxContent"><br><b>Subscription Active:</b> <?php echo ($bis_sub_info['sub_active'] == 1 ? 'Y' : 'N'); ?></td>
        </tr>
        <tr>
            <td class="infoBoxContent"><br><b>Product:</b> <?php echo zen_get_products_name($bis_sub_info['product_id']); ?></td>
        </tr>
        <tr>
            <td class="infoBoxContent"><br><b>Canceled When Purchased:</b> <?php echo ($bis_sub_info['active_til_purch'] == 1 ? 'Y' : 'N'); ?></td>
        </tr>
        <tr>
            <td class="infoBoxContent"><br><b>Last Sent:</b> <?php echo $bis_sub_info['last_sent']; ?></td>
        </tr>
        <tr>
            <td class="infoBoxContent"><br><b>Flagged As Spam:</b> <?php echo ($bis_sub_info['spam'] == 1 ? 'Y' : 'N'); ?></td>
        </tr>
        <tr>
            <td class="infoBoxContent"></td>
        </tr>
    </table>  
  </td>
  </tr>
</table>
<!-- body_eof //-->
File to add to your cron in cpanel: <?php echo "'".HTTPS_CATALOG_SERVER.DIR_WS_HTTPS_CATALOG."cron/send_back_in_stock_notifications.php?key=".BACK_IN_STOCK_CRON_KEY."' ";?>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
