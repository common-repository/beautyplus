<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!$ajax) {  ?>
  <?php echo BeautyPlus_View::run('header-beautyplus'); ?>
  <?php
  $buttons = '<a href="' .  admin_url( 'post-new.php?post_type=shop_order&beautyplus_hide' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; ' . esc_htm__('New order', 'beautyplus') . ' &nbsp;</a>';
  echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => __('Orders', 'beautyplus'), 'description' => '', 'buttons'=>$buttons));
  ?>

  <?php echo BeautyPlus_View::run('orders/nav', array('list' => $list )); ?>

  <div id="beautyplus-orders-2" class="__A__GP __A__GP_Top __A__Filter_Closed">

    <div class="__A__Searching<?php if ('' === BeautyPlus_Helpers::get('s', '')) echo" closed"; ?>">
      <div class="__A__Searching_In">
        <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in orders...', 'beautyplus'); ?>" value="<?php echo esc_attr(BeautyPlus_Helpers::get('s'));  ?>" data-status='<?php echo esc_attr(BeautyPlus_Helpers::get('status'))?>' autofocus></span>
      </div>
    </div>

  <?php } ?>


  <div class="beautyplus-list-m2 __A__List_M2-1 __A__Panel_Orders __A__Container">

    <?php if (0 === count( $orders['all']['orders'] )) {  ?>
      <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
        <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'beautyplus'); ?></div>
      </div>
    <?php } else {  ?>


      <table class="table table-hover">
        <thead>
          <tr class="__A__Standart">
            <th class="d-none d-sm-table-cell __A__Table_Checkbox __A__Table_Col1" scope="col"><input type="checkbox" class="__A__CheckAll"></th>
            <th scope="col" class="__A__Table_Col2"><a href="<?php echo BeautyPlus_Helpers::thead_sort('id')?>"><?php esc_html_e('No', 'beautyplus'); ?></a></th>
            <th scope="col" class="__A__Table_Col3"><?php esc_html_e('Status', 'woooplus-beautyplus') ?></th>
            <th scope="col"  class="__A__Table_Col4 __A__Col_Customer"><?php esc_html_e('Customer', 'woooplus-beautyplus') ?></th>
            <th scope="col"  class="__A__Table_Col5"><a href="<?php echo BeautyPlus_Helpers::thead_sort('post_date')?>"><?php esc_html_e('Details', 'woooplus-beautyplus') ?></a></th>
            <th scope="col" class="__A__Table_Col6 __A__Col_Products"><?php esc_html_e('Products', 'woooplus-beautyplus') ?></th>
            <th scope="col"  class="__A__Table_Col7 text-right"><a href="<?php echo BeautyPlus_Helpers::thead_sort('meta__order_total')?>"><?php esc_html_e('Total', 'woooplus-beautyplus') ?></a></th>
            <?php if ('trash' === BeautyPlus_Helpers::get('status')) {  ?>
              <th scope="col"  class="text-right __A__Table_Col8"></th>
            <?php } else {  ?>
              <th scope="col" class="text-right __A__Table_Col9"></th>
            <?php }?>

          </tr>

        </thead>

        <tbody>

          <tr  class="__A__List_M2_Bulk __A__Bulk __A__Display_None">
            <td scope="col" colspan="9">
              <?php if ('trash' === BeautyPlus_Helpers::get('status')) {  ?>
                <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Restore" data-do="changestatus" data-status='restore' href="javascript:;"><?php esc_html_e('Restore orders', 'beautyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Restore" data-do="changestatus" data-status='deleteforever' href="javascript:;"><?php esc_html_e('Delete forever', 'beautyplus'); ?></a>
              <?php } else {  ?>
                <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='processing'  href="javascript:;"><?php esc_html_e('Change status to &mdash; Processing', 'beautyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='on-hold'  href="javascript:;"><?php esc_html_e('Change status to &mdash; On-Hold', 'beautyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='completed'  href="javascript:;"><?php esc_html_e('Change status to &mdash; Completed', 'beautyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do" data-do="changestatus" data-status='trash' href="javascript:;"><?php esc_html_e('Move to trash', 'beautyplus'); ?></a>
              <?php }?>

            </td>
          </tr>

          <?php foreach ($orders AS $order_group) {  ?>

            <?php foreach ( $order_group['orders'] AS $order ) {  ?>
              <tr class="align-middle __A__Status<?php echo esc_attr($order['status']) ?>" id='item_<?php echo absint($order['id']) ;?>'>
                <td class="d-none d-sm-table-cell align-middle __A__Col_Checkbox"  class="text-right align-middle"><input type="checkbox" class="__A__Checkbox" data-id='<?php echo esc_attr($order['id'])  ?>' data-state='<?php echo esc_attr($order['status']) ?>'></td>
                <td class="__A__Col_No align-middle"><?php echo esc_attr($order['id'])?></td>
                <td class="d-none d-sm-table-cell align-middle beautyplus-orders--item-badge"  data-colname="Status"><span class="badge badge-pill badge-<?php echo esc_attr($order['status']) ?>"><?php echo wc_get_order_status_name($order['status']); ?></span></td>
                <td class="__A__Col_Name align-middle">
                  <p class="beautyplus-orders--name">
                    <?php echo BeautyPlus_Helpers::clean($order['shipping']['first_name'],$order['billing']['first_name']). " ".  BeautyPlus_Helpers::clean($order['shipping']['last_name'],$order['billing']['last_name']); ?>
                  </p>
                  <p class="beautyplus-orders--address">
                    <?php echo BeautyPlus_Helpers::clean($order['shipping']['city'],$order['billing']['city']) . ', ' . WC()->countries->states[BeautyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][BeautyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])]; ?>
                  </p>

                  <span class="badge badge-pill badge-<?php echo esc_attr($order['status']) ?> d-inline-block d-sm-none __A__Order_Status_R"><?php echo esc_attr($order['status']); ?></span>

                </td>
                <td class="__A__Col_3 __A__Col_Details align-middle"  data-colname="Details"><span><?php echo date("d F", strtotime( $order['date_created'] )); ?><br />

                  <?php echo esc_html($order['payment_method_title']) ?>
                </span>
              </td>
              <td class="__A__Col_3 align-middle __A__Col_Products"  data-colname="Products">
                <?php foreach ($order['line_items'] AS $item) {
                  echo esc_url(BeautyPlus_Helpers::product_image($item['product_id'], $item['quantity'], 'height:60px;'));
                } ?>
                <div class="__A__Clear_Both">

                  <?php if ('trash' === $order['status']) {  ?>
                    <div class="d-none d-sm-block">
                      <a href="<?php echo admin_url( 'post.php?post=' . $order['id']. '&action=edit&beautyplus_hide' );?>" class="__A__Button1 __A__Ajax_Button __A__MainButton" data-do='restore' data-id="<?php echo esc_attr($order['id'])?>"><?php esc_html_e('Restore order', 'beautyplus') ?></a> &nbsp;
                      <a href="<?php echo admin_url( 'post.php?post=' . $order['id']. '&action=edit&beautyplus_hide' );?>" class="__A__Button1 __A__Ajax_Button" data-do='deleteforever' data-id="<?php echo esc_attr($order['id']) ?>"><?php esc_html_e('Delete order forever', 'beautyplus') ?></a>
                    </div>
                  <?php } ?>

                </div>
                <?php if ($order['customer_note'] && $order['status'] !== "completed") { ?>
                  <div class="__A__Clear_Both"></div><div class="__A__Order_Customer_Notice bg-warning"><?php printf(esc_html__('Note: %s', 'beautyplus'), esc_html($order['customer_note']))?></div>
                <?php } ?></td>
                <td class="__A__Col_Price align-middle text-right" data-colname='Price'>
                  <a href="<?php echo admin_url( 'post.php?post=' . intval($order['id']). '&action=edit&beautyplus_hide' );?>"><span class="beautyplus-orders--item-price"><?php echo wc_price($order['total'], array()); ?></span></a>
                  <button class="__A__Mobile_Actions __A__M21"><span class="dashicons dashicons-arrow-down-alt2"></span></button>

                </td>


                <td class="__A__Col_3 __A__Col_Actions  align-middle text-right">
                  <ul>
                    <?php if ('trash' === $order['status']) {  ?>
                      <li class="d-inline-block d-sm-none">
                        <a href="<?php echo admin_url( 'post.php?post=' . absint($order['id']). '&action=edit&beautyplus_hide' );?>" class="__A__Button1 __A__Ajax_Button __A__MainButton" data-do='restore' data-id="<?php echo absint($order['id'])?>"><?php esc_html_e('Restore order', 'beautyplus') ?></a>
                      </li>

                      <li class="d-inline-block d-sm-none">
                        <a href="<?php echo admin_url( 'post.php?post=' . absint($order['id']). '&action=edit&beautyplus_hide' );?>" class="__A__Button1 __A__Ajax_Button" data-do='deleteforever' data-id="<?php echo absint($order['id'])?>"><?php esc_html_e('Delete order forever', 'beautyplus') ?></a>
                      </li>

                    <?php } else {  ?>
                      <li>
                        <a href="<?php echo admin_url( 'post.php?post=' . absint($order['id']). '&action=edit&beautyplus_hide' );?>" class="__A__Button1 __A__MainButton"><?php esc_html_e('View', 'beautyplus') ?></a>
                      </li>
                    <?php } ?>
                  </ul>
                </td>
              </tr>
            <?php } ?>
          <?php } ?>

        </tbody>
      </table>

    <?php } ?>

    <?php if (!$ajax) {  ?>
      <?php  echo BeautyPlus_View::run( 'core/pagination', array( 'count' => $list['statuses_count'][BeautyPlus_Helpers::get('status', 'count')], 'per_page'=> BeautyPlus::option('per_page', 10), 'page' => intval ( BeautyPlus_Helpers::get( 'pg', 0 ) ) )); ?>
    <?php } ?>
  </div>
</div>
