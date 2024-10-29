<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="beautyplus-title--menu __A__Coupons_Mode_2 __A__Scroll<?php if (BeautyPlus_Helpers::get('orderby')) { echo " mb-0"; }?>">
  <div class="row beautyplus-gp __A__GP">
    <ul>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('action')?>" href="<?php echo BeautyPlus_Helpers::admin_page('products', array( ));  ?>"><?php esc_html_e('Products', 'beautyplus'); ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('action', 'categories')?>" href="<?php echo BeautyPlus_Helpers::admin_page('products', array( 'action' => 'categories' ));  ?>"><?php esc_html_e('Categories', 'beautyplus'); ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('action', 'attributes')?>" href="<?php echo BeautyPlus_Helpers::admin_page('products', array( 'action' => 'attributes' ));  ?>"><?php esc_html_e('Attributes', 'beautyplus'); ?></a></li>

      <?php do_action('beautyplus_submenu', 'products'); ?>

      <?php if ('' === BeautyPlus_Helpers::get('action')): ?>
        <li class="__A__Li_Search">
          <?php if('-1' !== BeautyPlus_Helpers::get('category') && '-2' !== BeautyPlus_Helpers::get('category')) { ?>
          <div class="btn-group">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="dashicons dashicons-sort"></span></a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="<?php echo BeautyPlus_Helpers::thead_sort('title'); ?>"><?php esc_html_e('Name', 'beautyplus'); ?></a>
              <a class="dropdown-item" href="<?php echo BeautyPlus_Helpers::thead_sort('meta__price'); ?>"><?php esc_html_e('Price', 'beautyplus'); ?></a>
              <a class="dropdown-item" href="<?php echo BeautyPlus_Helpers::thead_sort('meta__sku'); ?>"><?php esc_html_e('SKU', 'beautyplus'); ?></a>
              <a class="dropdown-item" href="<?php echo BeautyPlus_Helpers::thead_sort('date'); ?>"><?php esc_html_e('Date', 'beautyplus'); ?></a>
            </div>
          </div>
        <?php } ?>
          <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'beautyplus'); ?></a>
        </li>
      <?php endif; ?>
    </ul>
  </div>

</div>
<?php if (BeautyPlus_Helpers::get('orderby')) { ?>
<div class="beautyplus-title--menu __A__Coupons_Mode_2 __A__Scroll __A__OrderBy">
  <div class="row beautyplus-gp __A__GP">
    <ul>
      <li>ORDER BY</li>
      <li>&nbsp;&nbsp;&nbsp;&nbsp;  &mdash;</li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('orderby', 'title')?>" href="<?php echo BeautyPlus_Helpers::thead_sort('title'); ?>"><?php esc_html_e('Name', 'beautyplus'); ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('orderby', 'meta__price')?>" href="<?php echo BeautyPlus_Helpers::thead_sort('meta__price'); ?>"><?php esc_html_e('Price', 'beautyplus'); ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('orderby', 'meta__sku')?>" href="<?php echo BeautyPlus_Helpers::thead_sort('meta__sku'); ?>"><?php esc_html_e('SKU', 'beautyplus'); ?></a></li>
      <li><a class="__A__Button1<?php BeautyPlus_Helpers::selected('orderby', 'date')?>" href="<?php echo BeautyPlus_Helpers::thead_sort('date'); ?>"><?php esc_html_e('Date', 'beautyplus'); ?></a></li>
    </ul>
  </div>
</div>
<?php } ?>
