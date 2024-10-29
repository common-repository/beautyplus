<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>

<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Reports', 'beautyplus'), 'description' => '', 'buttons'=>'')); ?>

<?php echo BeautyPlus_View::run('reports/nav');  ?>

<div id="beautyplus-reports-woocommerce">
  <?php
   if (!empty($report)) {
     $url =  admin_url('admin.php?page=wc-admin&path=/analytics/' . esc_attr($report));
   } else {
     $url =  admin_url('admin.php?page=wc-reports');
   }
  ?>
  <iframe src="<?php echo esc_url($url); ?>" id="beautyplus-frame" frameborder=0></iframe>
</div>
