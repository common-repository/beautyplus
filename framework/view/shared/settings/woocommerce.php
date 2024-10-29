<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>
<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Settings', 'beautyplus'), 'description' => '', 'buttons'=>'')); ?>
<?php echo BeautyPlus_View::run('settings/nav'); ?>

<div id="beautyplus-settings-woocommerce">
  <iframe src="<?php echo admin_url('admin.php?page=wc-settings'); ?> " id="beautyplus-frame" frameborder=0></iframe>
</div>
