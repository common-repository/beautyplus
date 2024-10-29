<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>

<iframe src="<?php echo BeautyPlus_Helpers::clean ( $page, 'about::blank' ); ?>" id="beautyplus-frame" frameborder=0></iframe>
