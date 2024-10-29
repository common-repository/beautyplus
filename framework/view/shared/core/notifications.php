<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<?php if (0 < count($notifications)) {  ?>
  <div class="container">
    <div class="notification-area">
      <ul class="notification-bar">
        <?php foreach ($notifications AS $message) {  ?>
          <li class="__A__Notifications_Type_<?php echo esc_attr($message['type']); ?> __A__Notifications_Status_<?php echo (isset($message['status'])?esc_attr($message['status']):0)?>">
            <div>
              <age><?php printf( esc_html__( '%s ago', 'beautyplus' ), human_time_diff( strtotime($message["time"]), current_time( 'timestamp' ) ) ); ?></age>
              <header><?php echo wp_kses_post($message["title"]); ?></header>

              <?php if ("2" === $message["type"]) {  // Orders ?>
                <?php if (isset($message['details'])) {  ?>
                  <div class="__A__Details container">
                    <div class="row">
                      <div class="col-6 __A__Content __A__Notifications_OrderTotal">
                        <h6><?php echo esc_html($message['details']['customer']); ?></h6>
                        <?php echo esc_html($message['details']['city']); ?>
                        <br>
                        <?php echo esc_html($message['details']['payment_method_title']); ?>

                      </div>
                      <div class="col-5 text-right __A__Notifications_OrderTotal">
                        <h4><?php  echo wp_kses_post($message['details']['total']); ?></h4>
                        <span class="text-uppercase badge badge-pill badge-secondary badge-<?php echo esc_html($message['details']['status']); ?>"><?php echo wc_get_order_status_name($message['details']['status']); ?></span>
                      </div>

                    </div>
                    <div class="row __A__Action">

                      <ul>
                        <li class="text-right">
                          <a href="<?php echo admin_url( 'post.php?post=' . intval($message['details']['order_id']). '&action=edit&beautyplus_hide' );?>" class="trig __A__Close_Before_Trig"><?php _e('View Order', 'beautyplus'); ?></a>
                        </li>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>

                <?php if ("4" === $message["type"]) {  // Comments ?>
                  <?php if (isset($message['details'])) {  ?>
                    <div class="__A__Details container">
                      <div class="row">
                        <div class="col-1">
                          <img src="<?php echo get_the_post_thumbnail_url(intval($message['details']['post_id'])); ?>" class="__A__Product_Image __A__Product_Image_Not" >

                        </div>
                        <div class="col-10 __A__Content">
                          <?php $stars = intval($message['details']['star']); ?>
                          <div class="__A__Stars">
                            <span class="__A__StarsUp"><?php echo str_repeat('★ ', $stars); ?></span>
                            <span class="__A__StarsDown"><?php echo str_repeat('★ ', 5-$stars); ?></span>
                          </div>
                          <?php  echo esc_html($message['details']['comment_content'])?>
                          <br><br>
                        </div>
                      </div>
                      <div class="row __A__Action">
                        <ul>
                          <li  class="text-right">
                            <a href="<?php echo admin_url('comment.php?action=editcomment&c=' .intval($message['details']['comment_id'])); ?>" class="trig"><?php esc_html_e('View Comment', 'beautyplus'); ?></a>
                          </li>

                        </ul>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>


                <?php if ("11" === $message["type"]) {  // Coupons ?>
                  <?php if (isset($message['details'])) {  ?>
                    <div class="__A__Details container">
                      <div class="row">

                        <div class="col-12 __A__Content">
                          <?php printf( wp_kses_post( esc_html__( 'Coupon', 'beautyplus' ). '<span class="badge badge-pill badge-black text-uppercase">%s</span> '.esc_html__( 'usage limit (%s) has been reached', 'beautyplus' ) ),  esc_attr($message['details']['coupon_code']),  intval($message['details']['usage'])); ?> <br />&nbsp;<br />
                        </div>
                      </div>
                      <div class="row __A__Action">
                        <ul>
                          <li  class="text-right">
                            <a href="<?php echo admin_url('post.php?post=' . intval($message['details']['coupon_id']). '&action=edit&beautyplus_hide') ?>" class="trig"><?php _e('View Coupon', 'beautyplus'); ?></a>
                          </li>

                        </ul>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>

                <?php if ("12" === $message["type"]) {  ?>
                  <div class="__A__Details container">
                    <div class="row">
                      <div class="col-12 __A__Details_Info_Text">
                        <?php echo wp_kses_post( $message['details']['message'] ); ?>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </li>
          <?php }?>

        </ul>
      </div>
    </div>

  <?php } else { ?>
    <div class="container">
      <div class="notification-area">
        <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
          <div><br><?php esc_html_e('No notification', 'beautyplus'); ?></div>
        </div>
      </div>
    </div>
  <?php } ?>
