<?php
/**
 * @copyright Copyright 2010-2014  ZenCart.codes Owned & Operated by PRO-Webs, Inc. 
 * @copyright Copyright 2003-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
?>
<div id="backInStockPage">
    <h2><?php echo BACK_IN_STOCK_TITLE ?></h2><br/>
    <?php
    if ($subcriptions) {
        echo BACK_IN_STOCK_DESC;
        ?>
        <div class="currentNotificationsHead"></br>
            <?php echo BACK_IN_STOCK_CURRENT, "<b>", $email_info->fields['email'],"</b>"?></br></br>
            <div class="currentNotifications">
                <?php
                echo zen_draw_form('back_in_stock', zen_href_link(FILENAME_BACK_IN_STOCK, '', ($_SERVER['HTTPS'] == 'on' ? 'SSL' : 'NONSSL')));
                echo zen_draw_hidden_field('action', "stop");
                ?>
                <table>
                    <tr>
                    <th style="text-align: center;width: 20%;"><?php echo BACK_IN_STOCK_FIELD1 ?></th>
                    <th style="text-align: left; width: 50%;"><?php echo BACK_IN_STOCK_FIELD2 ?></th>
                    <th style="text-align: left; width: 30%;"><?php echo BACK_IN_STOCK_FIELD3 ?></th>
                    </tr>
                    <?php
                    while (!$email_info->EOF) {
                        echo '<tr>';
                        echo '<td style="text-align: center;">' . zen_draw_checkbox_field('bis_id[]', $email_info->fields['bis_id']) . '</td>';
                        echo '<td style="text-align: left;">' . strip_tags(zen_get_products_name($email_info->fields['product_id'])) . '</td>';
                        echo '<td style="text-align: left;">' . $email_info->fields['sub_date'] . '</td>';
                        echo '</tr>';
                        $email_info->MoveNext();
                    }
                    ?>
                </table>
                <button type="submit" value="unsubscribe" style="float:left;"><?php echo BACK_IN_STOCK_BUTTON ?></button>
                <br/>
                </form> 
                <?php
            } else {
                echo BACK_IN_STOCK_NONE;
            }
            ?>
        </div>
    </div>