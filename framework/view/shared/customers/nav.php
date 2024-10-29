<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="beautyplus-title--menu __A__Coupons_Mode_2">
  <div class="row beautyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status')?>" href="<?php echo BeautyPlus_Helpers::admin_page('customers', array( ));  ?>"><?php esc_html_e('Customers', 'beautyplus'); ?> (<?php echo esc_html($count); ?>)</a> </li>

      <?php do_action('beautyplus_submenu', 'customers'); ?>

      <li class="__A__Li_Search">
        <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'beautyplus'); ?></a>
      </li>
      </ul>
  </div>
</div>
