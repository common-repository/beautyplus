<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>

<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Products', 'beautyplus'), 'description' => '', 'buttons'=> '<a href="' . admin_url( 'post-new.php?post_type=product' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; ' . esc_html__('New product', 'beautyplus'). ' &nbsp;</a>')); ?>

<?php echo BeautyPlus_View::run('products/nav'); ?>

<div id="beautyplus-products-1"  class="__A__Frame_Inline_Top">
  <div class="__A__Searching<?php if ('' === BeautyPlus_Helpers::get('s', '')) echo" closed"; ?>">
    <div class="__A__Searching_In">
      <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in products..', 'beautyplus'); ?>" value="<?php echo esc_attr(BeautyPlus_Helpers::get('s'));  ?>" autofocus></span>
    </div>
  </div>
  <div class="__A__List_M1 __A__Container __A__Frame_Inline">
    <iframe src="<?php echo esc_url_raw($iframe_url);?> " id="beautyplus-frame" frameborder=0></iframe>
  </div>
</div>
