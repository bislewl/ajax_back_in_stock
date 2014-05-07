<div id="backInStockPage">
    <h2>Back In Stock Notifications</h2><br/>
    <?php
    if($subcriptions){
    ?>
    <div class="currentNotificationsHead">
        <b><?php echo $email_info->fields['email']?></b><br/><br/>
        <div class="currentNotifications">
            <?php 
            echo zen_draw_form('back_in_stock', zen_href_link(FILENAME_BACK_IN_STOCK, '', ($_SERVER['HTTPS'] == 'on' ? 'SSL' : 'NONSSL')));
            echo zen_draw_hidden_field('action',"stop");
            ?>
            <table>
                <th style="text-align: center;">Unsubscribe</th>
                <th style="text-align: left;">Products</th>
                <th style="text-align: left;">Date Subscribed</th>
            <?php
        while(!$email_info->EOF){
             echo '<tr>';
             echo '<td style="text-align: center;">'.zen_draw_checkbox_field('bis_id[]',$email_info->fields['bis_id']).'</td>';
             echo '<td style="text-align: left;">'.strip_tags(zen_get_products_name($email_info->fields['product_id'])).'</td>';
             echo '<td style="text-align: left;">'.$email_info->fields['sub_date'].'</td>';
             echo '</tr>';
            $email_info->MoveNext();
        }
        ?>
        </table>
            <button type="submit" value="unsubscribe" style="float:left;">Unsubscribe</button>
            </form> 
            <?php
    }
    else{
        echo 'No Active Notifications';
    }
        ?>
        </div>
    </div>
</div>