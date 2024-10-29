<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="beautyplus-title--menu __A__Coupons_Mode_2">
  <div class="row beautyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status')?>" href="<?php echo BeautyPlus_Helpers::admin_page('coupons', array( ));  ?>"><?php _e('Active', 'beautyplus'); ?> <?php echo intval($counts->publish) ?></a> </li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', 'private')?>" href="<?php echo BeautyPlus_Helpers::admin_page('coupons', array( 'status' => 'private' )); ?>"><?php _e('Inactive', 'beautyplus'); ?> <?php echo intval($counts->private) ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', 'trash')?>" href="<?php echo BeautyPlus_Helpers::admin_page('coupons', array( 'status' => 'trash' )); ?>"><?php _e('Archived', 'beautyplus'); ?> <?php echo intval($counts->trash) ?></a></li>

      <?php do_action('beautyplus_submenu', 'coupons'); ?>

      <li class="__A__Li_Search">
        <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'beautyplus'); ?></a>
      </li>
    </ul>
  </div>
</div>
