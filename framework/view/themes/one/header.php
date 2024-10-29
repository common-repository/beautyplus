<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="__A__Container_Fluid">

  <div id="beautyplus-theme"  class="__A__Segment_<?php echo esc_attr( strtolower( BeautyPlus_Helpers::get( 'segment', 'dashboard' ) ) ); ?>">

    <?php echo BeautyPlus_Helpers::is_desktop_app(); ?>

    <a id="trig2" href="javascript:;">
      <div class="d-flex align-items-center">
        <svg version="1.1" id="__A__Mobile_Menu_Icon" x="0px" y="0px" viewBox="0 0 384.97 384.97" xml:space="preserve"> <g> <g id="Menu_1_"> <path d="M12.03,120.303h360.909c6.641,0,12.03-5.39,12.03-12.03c0-6.641-5.39-12.03-12.03-12.03H12.03 c-6.641,0-12.03,5.39-12.03,12.03C0,114.913,5.39,120.303,12.03,120.303z"/> <path d="M372.939,180.455H12.03c-6.641,0-12.03,5.39-12.03,12.03s5.39,12.03,12.03,12.03h360.909c6.641,0,12.03-5.39,12.03-12.03 S379.58,180.455,372.939,180.455z"/> <path d="M372.939,264.667H132.333c-6.641,0-12.03,5.39-12.03,12.03c0,6.641,5.39,12.03,12.03,12.03h240.606 c6.641,0,12.03-5.39,12.03-12.03C384.97,270.056,379.58,264.667,372.939,264.667z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>
    </div>
    </a>

    <div id="beautyplus-header">

      <nav class="__A__MainMenu vertical overflow-hidden">
        <ul class="beautyplus-menu __A__MainMenuV <?php if ("1" === BeautyPlus::option('reactors-tweaks-icon-text', "0")) { echo esc_attr( " __A__With_Text" ); } ?>">
          <?php echo BeautyPlus_Admin::get_menu();  ?>
          <li class="more d-none"> <span><span class="dashicons dashicons-menu"></span></span>
            <ul id="overflow">
            </ul>
          </li>
        </ul>
      </nav>
    </ul>

    <?php
    $beautyplus_img_src = wp_get_attachment_image_src( BeautyPlus::option('feature-logo'), 'full' );
    if (!is_array($beautyplus_img_src)) {
      $beautyplus_img_src = array('');
    }
    ?>
    <nav class="__A__MainMenu vertical __A__My fixed-bottom">
      <ul class="beautyplus-menu">
        <li class="text-center">
          <img src="<?php echo esc_url($beautyplus_img_src[0]); ?>" class="__A__Main_Logo">
          <ul>
            <li>
              <a href="<?php echo BeautyPlus_Helpers::admin_page('settings');?>"><?php esc_html_e('Settings', 'beautyplus'); ?></a>
            </li>
            <li>
              <a href="<?php echo esc_url_raw(get_bloginfo('url'));?>" target="blank"><?php esc_html_e('View Store', 'beautyplus'); ?></a>
            </li>
            <li>
              <a href="<?php echo wp_logout_url(); ?>"><?php esc_html_e('Logout', 'beautyplus'); ?></a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>

  </div>

  <div class="beautyplus-header-top __A__For_Search">
    <div class="beautyplus-search">
      <div class="dashicons-before dashicons-search"></div>
    </div>
  </div>

  <div class="beautyplus-header-top-container">
    <div>
      <div class="__A__GP row">
        <div class="col-8">
          <div class="beautyplus-search" >
            <input type="text" class="beautyplus-search-box beautyplus-search-input" data-close-on-empty="1" placeholder="<?php esc_html_e('Search for orders, customers, products...', 'beautyplus')?>">
          </div>
        </div>
        <div class="col-4 text-right __A__Top_Right">
          <div class="beautyplus--notification float-right">
            <?php $notification_count = BeautyPlus_Events::notification_count(); ?>
            <span>&mdash;&mdash; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;  </span><a href="javascript:;" class="__A__DisplayNotifications badge badge-pill badge-silent<?php if (0 < $notification_count) { ?> badge-danger<?php } ?>">
              <?php echo esc_html($notification_count) ?>
            </a>
          </span>
        </div>
        <div id="odometer" class="odometer __A__Top_Widget __A__Top_Widget_Today_Sales float-right"><?php echo number_format(floatval(get_transient('today_sales')), 0);?></div>
        <div class="__A__Top_Widget">
          <?php echo get_woocommerce_currency_symbol(); ?> &nbsp;
        </div>
      </div>
    </div>
  </div>
</div>

<div class="__A__Site_Name"><div><?php echo get_bloginfo('name'); ?></div></div>
