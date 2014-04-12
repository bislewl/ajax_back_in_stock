<div id="backInStockPage">
    <h2>Back In Stock Notifications</h2><br/>
    <?php
    if($subcriptions){
    ?>
    <div class="currentNotificationsHead">
        <b><?php echo $email_info->fields['email']?></b><br/><br/>
        <div class="currentNotifications">
        <?php
        while(!$email_info->EOF){
            ?>
        
        <?php echo zen_get_products_name($email_info->fields['product_id'])."\n";
              echo zen_draw_form('back_in_stock', zen_href_link(FILENAME_BACK_IN_STOCK, 'bis_id'.$email_info->fields['bis_id'], ($_SERVER['HTTPS'] == 'on' ? 'SSL' : 'NONSSL')));
              echo zen_draw_hidden_field('action',"stop");?>
              <button type="submit" value="unsubscribe">Unsubscribe</button>
        </form> 
        <br/>
            <?php
            $email_info->MoveNext();
        }
    }
    else{
        echo 'No Active Notifications';
    }
        ?>
        </div>
    </div>
</div>