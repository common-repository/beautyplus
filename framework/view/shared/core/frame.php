<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>
<?php if (BeautyPlus_Helpers::get('in') ) {
  echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => ''));
} ?>

<?php if (
  FALSE !== stripos($page, 'wc-settings') ||
  FALSE !== stripos($page, 'wc-reports') ||
  FALSE !== stripos($page, 'wc-status') ||
  FALSE !== stripos($page, 'wc-addons') ||
  FALSE !== stripos($page, 'post_type=product') ||
  FALSE !== stripos($page, 'page=product_attributes') ||
  FALSE !== stripos($page, 'post_type=shop_order') ||
  FALSE !== stripos($page, 'post_type=shop_coupon')
  ) { // compatibility ?>
<div id="inbrowser--loading" class="inbrowser--loading h100 d-flex align-items-center align-middle h95">
  <div class="lds-ellipsis lds-ellipsis-black"><div></div><div></div><div></div></div>
</div>
<?php } ?>

<iframe src="<?php echo BeautyPlus_Helpers::clean ( $page, 'about::blank' ); ?>" id="beautyplus-frame" class="beautyplus-frame<?php if (BeautyPlus_Helpers::get('in') ) { echo esc_attr( " beautyplus-frame-in" ); } if (BeautyPlus_Helpers::get('go') ) { echo esc_attr( " beautyplus-frame-go" ); }  ?>" frameborder=0></iframe>
  </div>
