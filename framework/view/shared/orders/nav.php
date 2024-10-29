<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="beautyplus-title--menu __A__Coupons_Mode_2 __A__Scroll">
  <div class="row beautyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status')?>" href="<?php echo BeautyPlus_Helpers::admin_page('orders', array());  ?>"><?php esc_html_e('All Orders', 'beautyplus'); ?> <?php echo esc_html($list['statuses_count']['count']) ?></a></li>
      <?php  foreach ( $list['statuses'] AS $status_k => $status) {
        if ($list['statuses_count'][$status_k] > 0) { ?>
        <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', $status_k)?>" href="<?php echo BeautyPlus_Helpers::admin_page('orders', array('status' => $status_k));  ?>"><?php echo esc_attr($status) ?> <span class="__A__Count"><?php echo esc_html($list['statuses_count'][$status_k]) ?></span></a></li>
      <?php }
    }  ?>

    <?php if (0<$list['statuses_count']['trash']) { ?>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('status', 'trash')?>" href="<?php echo BeautyPlus_Helpers::admin_page('orders', array('status' => 'trash'));  ?>"><?php esc_html_e('Trash', 'beautyplus'); ?>  <span class="__A__Count"><?php echo esc_html($list['statuses_count']['trash']) ?></span></a></li>
    <?php }  ?>

    <?php do_action('beautyplus_submenu', 'orders'); ?>

    <li class="__A__Li_Search">
      <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'beautyplus'); ?></a>
    </li>
  </ul>
</div>
</div>
