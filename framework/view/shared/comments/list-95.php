<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>
<?php
$stars_avg = absint($stars[0]->average);
$stars_avg_f = sprintf("%.1f", (float)$stars[0]->average);
$stars_div = '
<div class="__A__Stars __A__StarsBig">
<span class="__A__StarsUp">'. str_repeat('★ ', $stars_avg) .'</span>
<span class="__A__StarsDown">'. str_repeat('★ ', 5-$stars_avg) .'</span>
</div>
<div class="__A__StarsInfo">'. sprintf(esc_html__('You have %1$s average in %2$s reviews', "beautyplus"), $stars_avg_f, $stars[0]->cnt)."</div>";
?>

<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Comments', 'beautyplus'), 'description' => '', 'buttons'=>$stars_div )); ?>
<?php echo BeautyPlus_View::run('comments/nav', array('count' => $counts)); ?>

<div id="beautyplus-comments-1"  class="__A__Frame_Inline_Top">
  <div class="__A__Searching<?php if ('' === BeautyPlus_Helpers::get('s', '')) { echo" closed"; } ?>">
    <div class="__A__Searching_In">
      <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in comments..', 'beautyplus'); ?>" value="<?php echo esc_attr(BeautyPlus_Helpers::get('s'));  ?>" autofocus></span>
    </div>
  </div>
  <div class="__A__List_M1 __A__Container __A__Frame_Inline">
    <iframe src="<?php echo esc_url_raw($iframe_url); ?> " id="beautyplus-frame" frameborder=0></iframe>
  </div>
</div>
