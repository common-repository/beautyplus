<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>
<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => $title, 'description' => '', 'buttons'=>'')); ?>
<?php $nav ?>
<div id="beautyplus-frame-inline">
  <iframe src="<?php echo esc_url_raw($iframe_url); ?> " id="beautyplus-frame" frameborder=0></iframe>
</div>
