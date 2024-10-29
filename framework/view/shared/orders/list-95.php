<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>

<?php $buttons = '<a href="' . admin_url( 'post-new.php?post_type=shop_order&beautyplus_hide' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; '. esc_attr__('New order', 'beautyplus').' &nbsp;</a>';
echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Orders', 'beautyplus'), 'description' => '', 'buttons'=>$buttons)); ?>

<?php echo BeautyPlus_View::run('orders/nav', array('list' => $list )); ?>

<div id="beautyplus-orders-1" class="__A__Frame_Inline_Top">
  <div class="__A__Searching<?php if ('' === BeautyPlus_Helpers::get('s', '')) echo" closed"; ?>">
    <div class="__A__Searching_In">
      <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in orders..', 'beautyplus'); ?>" value="<?php echo esc_attr(BeautyPlus_Helpers::get('s'));  ?>" autofocus></span>
    </div>
  </div>
  <div class="__A__List_M1 __A__Container __A__Frame_Inline">
    <iframe src="<?php echo esc_url_raw($iframe_url); ?> " id="beautyplus-frame" frameborder=0></iframe>
  </div>
</div>
