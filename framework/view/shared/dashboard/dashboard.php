<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>
<?php
if ( class_exists('\Automattic\WooCommerce\Admin\FeaturePlugin') ) { // if Woocomerce Admin active
 echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_attr__('Dashboard', 'beautyplus'), 'description' => '', 'buttons'=>'<a href="' . BeautyPlus_Helpers::admin_page('dashboard', array('action'=>'default')) . '" class="__A__Dashboard_Buttons __A__Selected">' . esc_html__('Overview', 'beautyplus').'</a> <a href="' . BeautyPlus_Helpers::admin_page('dashboard', array('action'=>'wc-admin')) . '" class="__A__Dashboard_Buttons">' . esc_html__('Charts (WC)', 'beautyplus'). '</a>'));
} else {
  echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_attr__('Dashboard', 'beautyplus'), 'description' => '', 'buttons'=>''));
}
?>

<meta http-equiv="refresh" content="1800"/>

<div class="grid __A__GP">
  <?php foreach ($map AS $widget_id => $widget) {  ?>
    <?php $widgetclass =  "Widgets__". sanitize_key($widget['type']); ?>
    <div class="__A__Widget __A__Widget_w<?php echo esc_attr($widget['w']); ?> __A__Widget_h<?php echo esc_attr($widget['h']); ?> __A__Widget_<?php echo esc_attr($widget['type']); ?>" data-w="<?php echo esc_attr($widget['w']); ?>" data-h="<?php echo esc_attr($widget['h']); ?>" data-id="<?php echo esc_attr($widget['id']); ?>" data-type="<?php echo esc_attr($widget['type']); ?>" id="__A__Widget_<?php echo esc_attr($widget['id']); ?>">
      <div class="item-content">
        <div class="__A__ControlButton"><a href="javascript:;" class="XX"><span class="dashicons dashicons-move"></span></a> <a href="javascript:;" class="__A__Widget_Settings_Button"><span class="dashicons dashicons-admin-tools"></span></a></div>
        <div class="__A__ControlSettings">
          <div class="d-flex align-items-center">

            <?php
            if (isset($settings[$widget['id']])) {
              $__settings = $_settings = $settings[$widget['id']];
            } else {
              $__settings = $_settings = array();
            }
            $_settings = $widgetclass::settings($_settings); ?>

            <?php foreach ((array)$_settings AS $setting_key => $setting) {  ?>
              <div class="__A__Widget_Settings_Div">
                <?php if (isset($setting['type']) && 'wh' === $setting['type']) {  ?>
                  <h6 class=" p-2"><?php echo esc_html($setting['title']); ?></h6>

                  <div class="row p-2">
                    <div class="col-5 d-flex align-items-center">
                      <?php esc_html_e('Width', 'beautyplus'); ?>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                      <select name="w" class="__A__Widget_Resize" data-id="<?php echo esc_attr($widget['id']); ?>" id="__A__Widget_W_<?php echo esc_attr($widget['id']); ?>">
                        <?php foreach ($setting['values'][0]['values'] AS $value) {  ?>
                          <option value="<?php echo esc_attr($value);  ?>"  <?php if ((string)$value ===  $widget['w']) { echo " selected"; }?>><?php echo esc_attr($value); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div  class="row  p-2">
                    <div class="col-5 d-flex align-items-center">
                      <?php esc_html_e('Height', 'beautyplus'); ?>
                    </div>
                    <div class="col-6 d-flex align-items-center">

                      <select name="h" class="__A__Widget_Resize" data-id="<?php echo esc_attr($widget['id']); ?>" id="__A__Widget_H_<?php echo esc_attr($widget['id']);  ?>">
                        <?php foreach ($setting['values'][1]['values'] AS $value) {  ?>
                          <option value="<?php echo esc_attr($value);  ?>" <?php if ((string)$value === $widget['h']) { echo " selected"; }?>><?php echo esc_attr($value); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                <?php } ?>

                <?php if (isset($setting['type']) && 'checkbox' === $setting['type']) {  ?>
                  <h6 class="p-2"><?php echo esc_attr($setting['title']);  ?></h6>
                  <div class="d-flex1 align-items-center">

                    <?php foreach ($setting['values'] AS $value) {  ?>
                      <div class="m-2 float-left">
                        <input type="checkbox" name="__A__Widget_Settings_<?php echo esc_attr($widget['id']). "_". esc_attr($setting_key) ?>" class="__A__Widget_Settings_<?php echo esc_attr($widget['id']). "_". esc_attr($setting_key) ?>" value="<?php echo esc_attr($value['id'])?>" <?php if ('true' === $value['selected']) echo ' checked'; ?>><?php echo esc_attr($value['title']); ?>

                      </div>
                    <?php } ?>

                  </div>
                  <script>
                  jQuery(function () {
                    "use strict";

                    jQuery(".__A__Widget_Settings_<?php echo esc_attr($widget['id']. "_". $setting_key) ?>").on( "click", function() {

                      jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url("admin-ajax.php")?>',
                        data: {
                          _wpnonce: jQuery('input[name=_wpnonce]').val(),
                          _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
                          _asnonce: BeautyPlusGlobal._asnonce,
                          action: 'beautyplus_widgets',
                          a: 'settings',
                          id: '<?php echo esc_attr($widget['id'])?>',
                          set_id: jQuery(this).val(),
                          s: jQuery(this).prop('checked')
                        },
                        cache: false,
                        headers: {
                          'cache-control': 'no-cache'
                        },
                        success: function(response) {
                          window.reload_widgets();
                        }
                      }, 'json');
                    });
                  });
                  </script>
                <?php } ?>
              </div>
            <?php } ?>

          </div>
        </div>
        <div class="__A__Widget_Content">
          <?php $widgetclass::run($widget, $__settings); ?></div>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="__A__Widget_Add __A__GP text-right"><a href="<?php echo BeautyPlus_Helpers::admin_page('dashboard', array('action'=>'widget_list')); ?>" class="trig"><?php _e('Add or remove widgets', 'beautyplus'); ?></a></div>

  <div id="beautyplus-wp-notices" class="__A__GP">
    <?php apply_filters('admin_notices', array()); ?>
  </div>

  <p>&nbsp;</p>
