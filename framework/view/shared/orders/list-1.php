<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!$ajax) {  ?>

  <?php echo BeautyPlus_View::run('header-beautyplus'); ?>
  <?php $buttons = '<a href="' . admin_url( 'post-new.php?post_type=shop_order&beautyplus_hide' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; '. esc_attr__('New order', 'beautyplus').' &nbsp;</a>';
  echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Orders', 'beautyplus'), 'description' => '', 'buttons'=>$buttons)); ?>

  <?php echo BeautyPlus_View::run('orders/nav', array('list' => $list )); ?>

  <div id="beautyplus-orders-1">

    <div class="__A__Searching<?php if ('' === BeautyPlus_Helpers::get('s', '')) echo" closed"; ?>">
      <div class="__A__Searching_In">
        <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in orders..', 'beautyplus'); ?>" value="<?php echo esc_attr(BeautyPlus_Helpers::get('s'));  ?>"></span>
      </div>
    </div>

    <div class=" __A__GP __A__List_M1 __A__Container">
    <?php } ?>

    <?php if (0 === count( $orders )) {  ?>
      <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
        <div><span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'beautyplus'); ?></div>
      </div>
    <?php } else {  ?>

      <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="__A__List_M1_Bulk __A__Bulk __A__Display_None">
          <?php if ('trash' === BeautyPlus_Helpers::get('status')) {  ?>
            <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Restore" data-do="changestatus" data-status='restore' href="javascript:;"><?php esc_html_e('Restore orders', 'beautyplus'); ?></a>
            <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Restore" data-do="changestatus" data-status='deleteforever' href="javascript:;"><?php esc_html_e('Delete forever', 'beautyplus'); ?></a>
          <?php } else {  ?>
            <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='processing'  href="javascript:;"><?php esc_html_e('Change status to &mdash; Processing', 'beautyplus'); ?></a>
            <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='completed'  href="javascript:;"><?php esc_html_e('Change status to &mdash; Completed', 'beautyplus'); ?></a>
            <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='trash' href="javascript:;"><?php esc_html_e('Move to trash', 'beautyplus'); ?></a>
          <?php }?>
          <a class="__A__Select_All float-right" data-state='select' href="javascript:;"><?php esc_html_e('Select All', 'beautyplus'); ?></a>

        </div>

        <?php  foreach ($orders AS $order_group) {  ?>
          <h6><?php echo esc_html($order_group['title']) ?></h6>
          <div class="__A__Orders_Container">
            <?php foreach ($order_group['orders'] AS $order) {  ?>
              <div class="btnA __A__Item collapsed"  data-toggle="collapse" data-target="#order_<?php echo esc_attr($order['id'])?>" aria-expanded="false" aria-controls="order_<?php echo esc_attr($order['id'])?>"  id="item_<?php echo esc_attr($order['id'])?>">
                <div class="liste  row d-flex align-items-center">
                  <div class="__A__Checkbox_Hidden">
                    <input type="checkbox" class="__A__Checkbox __A__StopPropagation"  data-id='<?php echo esc_attr($order['id'])  ?>' data-state='<?php echo esc_attr($order['status'])?>'>
                  </div>
                  <div class="text-center d-none d-sm-inline __A__Order_No" data-colname="<?php esc_html_e('Order No: ', 'beautyplus'); ?>"><span class="__A__Order_No __A__Strong"><?php echo esc_attr($order['id'])?></span></div>
                  <div class="col col-sm-2 col-md-1 beautyplus-orders--item-badge text-center __A__Col_3">

                    <span class="siparisdurumu text-<?php echo esc_attr($order['status']);?>"><span class="bg-custom bg-<?php echo esc_attr($order['status']);?>" aria-hidden="true"></span><br><?php echo wc_get_order_status_name($order['status']); ?></span>

                    <span class="badge badge-pill __A__Display_None"><?php echo esc_attr($order['status']); ?></span></div>
                    <div class="__A__Col_Name col-7 col-sm-2"><p class="beautyplus-orders--name">
                      <?php echo BeautyPlus_Helpers::clean($order['shipping']['first_name'],$order['billing']['first_name']). " ".  BeautyPlus_Helpers::clean($order['shipping']['last_name'],$order['billing']['last_name']); ?>
                    </p>
                    <p class="beautyplus-orders--address">
                      <?php if (isset($order['shipping']['country']))  {
                        if (isset(WC()->countries->states[BeautyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][BeautyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])])) {
                          echo WC()->countries->states[BeautyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][BeautyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])];
                        } else {
                          echo BeautyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state']);
                        }
                        echo esc_html(', ' . BeautyPlus_Helpers::clean($order['shipping']['city'],$order['billing']['city']));
                      } ?>
                    </p>
                  </div>
                  <div class="col col-sm-2 __A__Col_3"  data-colname='<?php esc_html_e('Details', 'beautyplus'); ?>'>
                    <span class="__A__Order_No  d-inline d-lg-none"><?php esc_html_e('Order No', 'beautyplus'); ?>: <?php echo esc_attr($order['id'])?><br /><br /></span>
                    <span><?php echo wc_format_datetime($order['date_created'], 'd M,');; ?></span>
                    <span><?php echo wc_format_datetime($order['date_created'], 'H:i'); ?><br /></span>
                    <?php echo esc_html($order['payment_method_title']) ?>
                  </div>
                  <div class="col col-sm-4 d-md-none d-lg-block __A__Order_Products __A__Col_3" data-colname='<?php esc_html_e('Products', 'beautyplus'); ?>'>
                    <?php foreach ($order['line_items'] AS $item) {  ?>
                      <?php echo BeautyPlus_Helpers::product_image($item['product_id'],  $item['quantity'], 'width: 55px;'); ?>
                    <?php } ?>
                    <?php if ($order['customer_note'] && esc_attr($order['status']) !== "completed") { ?>
                      <div class="__A__Clear_Both"></div><div class="__A__Order_Customer_Notice bg-warning"><?php printf(esc_html__('Note: %s', 'beautyplus'), esc_html($order['customer_note']))?></div>
                    <?php } ?>
                  </div>
                  <div class="col __A__Col_Price __A__Col_3X text-right" data-colname='Price'>
                    <span class="beautyplus-orders--item-price"><?php echo wc_price($order['total'],array('currency'=>$order['currency'], 'price_format' => get_woocommerce_price_format())); ?></span>
                    <br>
                    <span class="badge badge-pill badge-<?php echo esc_attr($order['status']) ?> d-inline-block d-sm-none __A__Order_Status_R"><?php echo esc_attr($order['status']); ?></span>

                  </div>


                  <div class="col col-sm-1  __A__Actions text-center d-none">
                    <span class="dashicons dashicons-arrow-down-alt2 bthidden1" aria-hidden="true"></span>
                    <span class="dashicons dashicons-no-alt bthidden" aria-hidden="true"></span>

                  </div>

                </div>
                <div class="collapse col-xs-12 col-sm-12 col-md-12 __A__Order_Details" id="order_<?php echo esc_attr($order['id'])?>">
                  <div class="row __A__Order_Items">
                    <div class="col-md-4 col-sm-6">
                      <?php  foreach ($order['line_items'] AS $item) { ?>
                        <div class="row __A__Order_Item">
                          <div class="col-3 col-sm-3 col-md-2"><img src="<?php echo get_the_post_thumbnail_url($item['product_id']); ?>" class="__A__Product_Image" ></div>
                          <div class="col-9 col-sm-9 col-md-10">
                            <h4><?php echo esc_html($item['name'])?></h4>
                            <?php  if ($item['variation_id'] > 0) {  ?>
                              <div class="__A__Order_Details_Variation">
                                <?php
                                $formatted_meta_data = $item->get_formatted_meta_data();
                                foreach ($formatted_meta_data AS $meta) {
                                  echo '<strong>' . esc_html($meta->display_key). '</strong>: <span class="badgex badge-pillx badge-blackx"> ' . esc_html ( $meta->display_value ) . '</span> &nbsp; &nbsp; ';
                                }
                                ?>
                              </div>
                            <?php } ?>
                            <div class="fiyat">
                              <?php echo wc_price(($item['subtotal']/$item['qty']), array('currency' => $order['currency'])); ?> x   <span class="badge badge-pill badge-danger"><?php echo esc_html($item['qty']); ?></span> =   <?php echo wc_price($item['subtotal'], array('currency' => $order['currency'])); ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>

                      <?php foreach ($order['coupon_lines'] AS $item) {  ?>
                        <div class="row __A__Order_Item">
                          <div class="col-3 col-sm-3 col-md-2"><div class="__A__Order_Item_Group" ><span class="dashicons dashicons-carrot"></span></div></div>
                          <div class="col-9 col-sm-9 col-md-10">
                            <h4 class="text-uppercase"><?php echo esc_html($item['code'])?></h4>

                            <div class="fiyat">
                              - <?php echo wc_price($item['discount'], array('currency' => $order['currency'])); ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>

                      <?php foreach ($order['shipping_lines'] AS $item) {  ?>
                        <div class="row __A__Order_Item">
                          <div class="col-3 col-sm-3 col-md-2"><div  class="__A__Order_Item_Group"  ><span class="dashicons dashicons-migrate"></span></div></div>
                          <div class="col-9 col-sm-9 col-md-10">
                            <h4><?php echo esc_html($item['name'])?></h4>

                            <div class="fiyat">
                              <?php echo wc_price($item['total'], array('currency' => $order['currency'])); ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>

                      <?php foreach ($order['tax_lines'] AS $item) {  ?>

                        <div class="row __A__Order_Item">
                          <div class="col-3 col-sm-3 col-md-2"><div  class="__A__Order_Item_Group"  >%</div></div>
                          <div class="col-9 col-sm-9 col-md-10">
                            <h4><?php echo esc_html($item['label'])?></h4>

                            <div class="fiyat">
                              <?php echo wc_price($item['tax_total']+$item['shipping_tax_total']+$item['discount_tax'], array('currency' => $order['currency'])); ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>

                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-md-4 col-sm-5 __A__StopPropagation __A__Order_Address">

                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12"><h4><?php esc_html_e('Billing Address', 'beautyplus'); ?></h4></div>
                        <div class="col-xs-12 col-sm-12 col-md-12"><?php echo wp_kses_post($order['billing_formatted'])?></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('E-Mail', 'beautyplus'); ?></strong></div>
                        <div class="col-xs-12 col-sm-12 col-md-12"><a href="mailto:<?php echo sanitize_email($order['billing']['email'])?>"><?php echo sanitize_email($order['billing']['email'])?></a></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('Telephone', 'beautyplus'); ?></strong></div>
                        <div class="col-xs-12 col-sm-12 col-md-12"><a href="tel:<?php echo esc_attr($order['billing']['phone'])?>"><?php echo esc_html($order['billing']['phone'])?></a></div>
                      </div>

                      <div class="row">&nbsp;</div>

                      <?php if ('' !== trim( $order['shipping']['address_1']) OR '' !== trim( $order['shipping']['address_2'])) {  ?>
                        <div class="row __A__StopPropagation">
                          <div class="col-xs-12 col-sm-12 col-md-12"><h4><?php esc_html_e('Shipping Address', 'beautyplus'); ?></h4></div>
                          <div class="col-xs-12 col-sm-12 col-md-12"><?php echo wp_kses_post($order['shipping_formatted'])?></div>
                          <?php if (isset($order['shipping']['email'])) {  ?>
                            <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('E-Mail', 'beautyplus'); ?></strong></div>
                            <div class="col-xs-12 col-sm-12 col-md-12"><a href="mailto:<?php echo sanitize_email($order['shipping']['email'])?>"><?php echo sanitize_email($order['shipping']['email'])?></a></div>
                          <?php } ?>
                          <?php if (isset($order['shipping']['phone'])) {  ?>
                            <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('Telephone', 'beautyplus'); ?></strong></div>
                            <div class="col-xs-12 col-sm-12 col-md-12"><a href="tel:<?php echo esc_attr($order['shipping']['phone'])?>"><?php echo esc_html($order['shipping']['phone'])?></a></div>
                          <?php } ?>
                        </div>
                      <?php } ?>

                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-md-2 col-sm-12 __A__Order_Address __A__Order_Actions">
                      <?php  if (!empty($order['next_statuses']) && 'trash' !== $order['status']) {  ?>
                        <div class="row">
                          <h4><?php esc_html_e('Change to...', 'beautyplus'); ?></h4>
                          <?php $order_statuses = wc_get_order_statuses(); $order_statuses['trash'] = esc_html__('Delete', 'beautyplus'); ?>
                          <?php foreach ($order['next_statuses'] AS $next_status) { ?>
                            <a href="javascript:;" data-status="<?php echo esc_attr($next_status)?>" data-do='changestatus' data-id='<?php echo esc_attr($order['id'])?>' class="__A__Ajax_Button __A__StopPropagation __A__Order_Change_Statuses"><span  class="text-<?php echo str_replace('wc-','',esc_attr($next_status))?>">⬤</span><?php echo esc_html($order_statuses[$next_status])?></a>
                          <?php } ?>
                          <br />
                        </div>
                      <?php } ?>
                      <div class="row">
                        <?php  if ('trash' === $order['status']) {  ?>
                          <a href="javascript:;" data-status="restore" data-do='changestatus' data-id='<?php echo esc_attr($order['id'])?>' class="__A__Ajax_Button __A__StopPropagation"><?php esc_html_e('Restore order', 'beautyplus'); ?></a>
                          <a href="javascript:;" data-status="deleteforever" data-do='changestatus' data-id='<?php echo esc_attr($order['id'])?>' class="__A__Ajax_Button __A__StopPropagation"><?php esc_html_e('Delete forever', 'beautyplus'); ?></a>
                        <?php } else {  ?>
                          &nbsp;
                          <br />
                          <a href="<?php echo admin_url( 'post.php?post=' . esc_attr($order['id']). '&action=edit&beautyplus_hide' );?>" class=" __A__Ajax_Btn_SP trig" data-hash="<?php echo esc_attr($order['id'])?>"><?php esc_html_e('View order details', 'beautyplus'); ?></a>
                          <br />
                          <br />
                          <?php if (0 !== $order['customer_id']) { ?>
                            <a href="<?php echo BeautyPlus_Helpers::secure_url('customers', $order['customer_id'], array('action' => 'view', 'id' => $order['customer_id'])); ?>" class=" __A__Ajax_Btn_SP trig"><?php esc_html_e('View customer', 'beautyplus'); ?></a>
                          <?php } ?>
                          <a href="mailto:<?php echo sanitize_email($order['billing']['email'])?>"><?php esc_html_e('E-mail to customer', 'beautyplus'); ?></a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
    <?php if (!$ajax) {  ?>
      <?php   echo BeautyPlus_View::run( 'core/pagination', array( 'count' => $list['statuses_count'][BeautyPlus_Helpers::get('status', 'count')], 'per_page'=> BeautyPlus::option('per_page', 10), 'page' => intval ( BeautyPlus_Helpers::get( 'pg', 0 ) ) )); ?>
    </div>
  </div>
<?php } ?>
