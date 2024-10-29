<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="beautyplus-title--menu __A__Coupons_Mode_2">
  <div class="row beautyplus-gp">

    <ul>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status')?>" href="<?php echo BeautyPlus_Helpers::admin_page('comments', array( ));  ?>"><?php esc_html_e('All', 'beautyplus'); ?> <?php echo esc_html(absint($count->total_comments))  ?></a> </li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', '-1')?>" href="<?php echo BeautyPlus_Helpers::admin_page('comments', array( 'status' => '-1' ));  ?>"><?php esc_html_e('Pending', 'beautyplus'); ?> <?php echo esc_html(absint( $count->moderated)) ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', '1')?>" href="<?php echo BeautyPlus_Helpers::admin_page('comments', array( 'status' => '1' ));  ?>"><?php esc_html_e('Approved', 'beautyplus'); ?> <?php echo esc_html(absint( $count->approved ))?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', 'spam')?>" href="<?php echo BeautyPlus_Helpers::admin_page('comments', array( 'status' => 'spam' ));  ?>"><?php esc_html_e('Spam', 'beautyplus'); ?> <?php echo esc_html(absint( $count->spam)) ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', 'trash')?>" href="<?php echo BeautyPlus_Helpers::admin_page('comments', array( 'status' => 'trash' ));  ?>"><?php esc_html_e('Deleted', 'beautyplus'); ?> <?php echo esc_html(absint( $count->trash)) ?></a></li>

      <?php do_action('beautyplus_submenu', 'comments'); ?>

      <li class="__A__Li_Search">
        <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'beautyplus'); ?></a>
      </li>
    </ul>
  </div>
</div>
