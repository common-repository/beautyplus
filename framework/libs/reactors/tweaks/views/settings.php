<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>

<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html($reactor['title']), 'description' => '', 'buttons'=>'')); ?>

<?php echo BeautyPlus_View::reactor('tweaks/views/nav', array('id'=> $reactor['id']) ); ?>

<div id="beautyplus-settings-general" class="beautyplus-settings __A__Reactors_Settings __A__GP">
  <?php if (1 === $saved) { ?>
    <div class="alert alert-success" role="alert">
      <span class="dashicons dashicons-smiley"></span>&nbsp;&nbsp;<?php esc_html_e('Settings are saved', 'beautyplus'); ?>
    </div>
  <?php } ?>
  <form action="" method="POST">

    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-3 __A__Title">
          <?php esc_html_e('Detail window width', 'beautyplus'); ?>
        </div>
        <div class="col-lg-9 __A__Description">
          <div class="col-lg-3 input-group __A__Settings_NCT">
            <input name="reactors-tweaks-window-size" class="__A__Settings_Input form-control"  placeholder="<?php esc_attr_e('1090', 'beautyplus'); ?>" value='<?php echo esc_attr(intval(BeautyPlus::option('reactors-tweaks-window-size', '1090px'))); ?>'/>
            <select name="reactors-tweaks-window-size-dimension" class="__A__Settings_Select form-control">
              <option<?php if (stripos(BeautyPlus::option('reactors-tweaks-window-size', '1090px'), 'px') > 0) {echo " selected";}?>>px</option>
                <option<?php if (stripos(BeautyPlus::option('reactors-tweaks-window-size', '1090px'), '%') > 0) {echo " selected";}?>>%</option></select>
                </div>
                <br>
                <?php esc_html_e('Adjust the width of the detail window that opens from the right. It can be px or % value. (Example: 1090px or 90%)', 'beautyplus'); ?>
              </div>
            </div>
          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-3 __A__Title">
                <?php esc_html_e('Order statuses', 'beautyplus'); ?>
              </div>
              <div class="col-lg-9 __A__Description">
                <div class="row">
                  <?php foreach ( wc_get_order_statuses() AS $key=>$value) {  ?>
                    <div class="col-lg-4 __A__Settings_NCT">
                      <div class="form-check">
                        <input type="checkbox" value="<?php echo esc_attr($key);?>" name="reactors-tweaks-order-statuses[]" <?php if (!in_array($key, BeautyPlus::option('reactors-tweaks-order-statuses', array('wc-failed','wc-cancelled','wc-refunded')))) { echo " checked";}?>>
                          <?php echo esc_html($value); ?>
                        </div>
                      </div>

                    <?php } ?>
                  </div>
                  <br>
                  <?php esc_html_e('Select which statuses will be on the right when an order detail is clicked on the Orders page', 'beautyplus'); ?>
                </div>
              </div>
            </div>

            <div class="__A__Item">
              <div class="row">
                <div class="col-lg-3 __A__Title">
                  <?php esc_html_e('Homepage', 'beautyplus'); ?>
                </div>
                <div class="col-lg-9 __A__Description">
                  <div class="row">
                    <div class="col-sm-12 __A__Settings_NCT">
                      <div class="form-check">

                        <select name="reactors-tweaks-landing" class="__A__Settings_Select form-control">
                          <option<?php if (BeautyPlus::option('reactors-tweaks-landing', 'dashboard') === 'dashboard') { echo esc_attr( " selected" ); } ?>><?php esc_html_e('Dashboard', 'beautyplus'); ?></option>
                            <option<?php if (BeautyPlus::option('reactors-tweaks-landing', 'dashboard') === 'orders') { echo esc_attr( " selected" ); } ?>><?php esc_html_e('Orders', 'beautyplus'); ?></option>
                              <option<?php if (BeautyPlus::option('reactors-tweaks-landing', 'dashboard') === 'products') { echo esc_attr( " selected" ); } ?>><?php esc_html_e('Products', 'beautyplus'); ?></option>
                                <option<?php if (BeautyPlus::option('reactors-tweaks-landing', 'dashboard') === 'reports') { echo esc_attr( " selected" ); } ?>><?php esc_html_e('Reports', 'beautyplus'); ?></option>
                                  <option<?php if (BeautyPlus::option('reactors-tweaks-landing', 'dashboard') === 'customers') { echo esc_attr( " selected" ); } ?>><?php esc_html_e('Customers', 'beautyplus'); ?></option>
                                    <option<?php if (BeautyPlus::option('reactors-tweaks-landing', 'dashboard') === 'coupons') { echo esc_attr( " selected" ); } ?>><?php esc_html_e('Coupons', 'beautyplus'); ?></option>
                                    </select>
                                    <br>
                                    <?php esc_html_e('What will be the landing page', 'beautyplus'); ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="__A__Item">
                          <div class="row">
                            <div class="col-lg-3 __A__Title">
                              <?php esc_html_e('Appearance', 'beautyplus'); ?>
                            </div>
                            <div class="col-lg-9 __A__Description">
                              <div class="row">
                                <div class="col-sm-12 __A__Settings_NCT">

                                  <div class="form-check">
                                    <input type="checkbox" value="1" name="reactors-tweaks-settings-woocommerce" <?php if ("1" === BeautyPlus::option('reactors-tweaks-settings-woocommerce', "0")) { echo esc_attr( " checked" ); } ?>>
                                      <?php esc_html_e('Show "WooCommerce Settings" tab in Settings for Shop Managers', 'beautyplus'); ?>
                                    </div>

                                    <?php if (in_array(BeautyPlus::$theme, array('one', 'one-shadow'))) {?>
                                      <div class="form-check">
                                        <input type="checkbox" value="1" name="reactors-tweaks-icon-text" <?php if ("1" === BeautyPlus::option('reactors-tweaks-icon-text', "0")) { echo esc_attr( " checked" ); } ?>>
                                          <?php esc_html_e('Show menu item titles at bottom of icons', 'beautyplus'); ?>
                                        </div>
                                      <?php } ?>

                                      <div class="form-check">
                                        <input type="checkbox" value="1" name="reactors-tweaks-screenoptions" <?php if ("1" === BeautyPlus::option('reactors-tweaks-screenoptions', "0")) { echo esc_attr( " checked" ); } ?>>
                                          <?php esc_html_e('Show Screen Options for non-Beauty pages', 'beautyplus'); ?>
                                        </div>

                                      <div class="form-check">
                                        <input type="checkbox" value="1" name="reactors-tweaks-adminbar-hotkey" <?php if ("1" === BeautyPlus::option('reactors-tweaks-adminbar-hotkey', "1")) { echo esc_attr( " checked" ); } ?>>
                                          <?php esc_html_e('Show WP Adminbar when press A key from keyboard', 'beautyplus'); ?>
                                        </div>

                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>


                              <div class="__A__Item">
                                <div class="row">
                                  <div class="col-lg-3 __A__Title">
                                    <?php esc_html_e('Font', 'beautyplus'); ?>
                                  </div>
                                  <div class="col-lg-9 __A__Description">
                                    <div class="col-lg-8 input-group __A__Settings_NCT">
                                      <input id="reactors-tweaks-font" name="reactors-tweaks-font" class="__A__Settings_Input form-control"  placeholder="<?php esc_attr_e('Font', 'beautyplus'); ?>" value='<?php echo esc_attr(BeautyPlus::option('reactors-tweaks-font', 'Theme+Default')) ?>'/>
                                    </div>
                                    <br>
                                    <?php esc_html_e('Change your BeautyPlus font.', 'beautyplus'); ?>
                                    <a href="//fonts.google.com/" target="_blank"><?php esc_html_e('Go to Google Fonts >', 'beautyplus'); ?></a>
                                  </div>
                                </div>
                                <script>
                                jQuery(document).ready(function() {
                                  "use strict";

                                  jQuery('#reactors-tweaks-font')
                                  .fontselect({
                                    systemFonts: ['Theme+Default']
                                  });
                                });
                                </script>
                              </div>


                              <div class="mt-4 text-center">
                                <?php wp_nonce_field( 'beautyplus_reactors' ); ?>
                                <button name="submit" class="btn btn-sm __A__Button1" type="submit"><?php esc_html_e('Save', 'beautyplus'); ?></button>
                              </div>
                            </form>
                          </div>