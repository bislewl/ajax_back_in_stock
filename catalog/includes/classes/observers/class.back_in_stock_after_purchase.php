<?php
class bisAfterPurchase extends base 
{
	function bisAfterPurchase()
	{
		global $zco_notifier;
		$zco_notifier->attach($this, array('NOTIFY_ORDER_INVOICE_CONTENT_READY_TO_SEND'));
	}
	
	function update(&$class, $eventID, $paramsArray) {
    global $db;
    // check if account is subscribed
    $customers = $db->Execute("SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id = " . (int)$_SESSION['customer_id'] . " LIMIT 1;");
    $subscriptions = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE email='".$customers->fields['customers_email_address']."'");
    if ($subscriptions->RecordCount() > 0) {
        $products_subscribed = array();
        //Create array of subscribed products
        while($subscriptions->EOF){
            $products_subscribed[] = $subscriptions->fields['product_id'];
            $subscriptions->MoveNext();
        }
            //get products in cart
            $products = $_SESSION['cart']->get_products();
		for ($i=0, $n=sizeof($products); $i<$n; $i++) {
                    if(in_array($products[$i]['id'], $products_subscribed)){
                        $bis_prod_subscription = $db->Execute("SELECT * FROM ".TABLE_BACK_IN_STOCK." WHERE email='".$customers->fields['customers_email_address']."' AND product_id=".(int)$products[$i]['id']);
                        $bis_id = $bis_prod_subscription->fields['nis_id'];
                        back_in_stock_subscription(array('sub_active' => 0, 'bis_id => '.(int)$bis_id.', purch_date => NOW()'),'modify');
                    }
                }
    }
  }
}
// eof

