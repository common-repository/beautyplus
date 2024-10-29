<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="beautyplus-title--menu __A__Coupons_Mode_2 __A__Scroll">
  <div class="row beautyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('panel')?>" href="<?php echo BeautyPlus_Helpers::admin_page('settings', array( ));  ?>"><?php esc_html_e('General', 'beautyplus'); ?></a> </li>
      <?php if (BeautyPlus_Admin::is_admin(null)) { ?><li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('panel', 'panels'); ?>" href="<?php echo BeautyPlus_Helpers::admin_page('settings', array( 'panel' => 'panels' )); ?>"><?php esc_html_e('Menu', 'beautyplus'); ?></a></li><?php } ?>
      <?php if (BeautyPlus_Admin::is_admin(null) || (!BeautyPlus_Admin::is_admin(null) && '1' === BeautyPlus::option('reactors-tweaks-settings-woocommerce',0))) { ?><li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('panel', 'woocommerce'); ?>" href="<?php echo BeautyPlus_Helpers::admin_page('settings', array( 'panel' => 'woocommerce' )); ?>"><?php esc_html_e('WooCommerce', 'beautyplus'); ?></a></li><?php } ?>
    </ul>
  </div>
</div>
