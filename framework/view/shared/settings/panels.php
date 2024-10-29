<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-beautyplus'); ?>
<?php echo BeautyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Settings', 'beautyplus'), 'description' => '', 'buttons'=>'')); ?>
<?php echo BeautyPlus_View::run('settings/nav'); ?>

<div id="beautyplus-settings-general" class="beautyplus-settings __A__GP">
  <ol class="beautyplus-settings-panels">
    <?php foreach ($menu AS $m_k=>$m) { ?>
      <li id="beautyplus-menu-<?php echo esc_attr($m_k); ?>">
        <a name="<?php echo esc_attr($m_k);  ?>"></a>

        <div class="__A__Item">
          <div class="row">
            <div class="col-lg-2 __A__Title">
              <div class="__A__Item_Icon <?php
              if (false !== stripos($m["icon"], 'dashicons')) {
                echo esc_attr( "dashicons-before" );
              }
              echo esc_attr($m["icon"]); ?>"></div>
              <?php if (isset($m['title'])) {
                echo esc_html( $m['title'] );
              }  ?>

            </div>
            <div class="col-lg-10 __A__Description">
              <div class="row">
                <div class="d-flex align-items-center mt-0 ml-3">
                  <label class="switch">
                    <input  data-id="<?php echo esc_attr($m_k);  ?>" data-for="administrator" type="checkbox" value="1" class="beautyplus-panel-item-onoff success" <?php if (1 === $m["roles"]['administrator']) echo " checked"; ?> />
                    <span class="__A__slider"></span>
                  </label>
                  <?php esc_html_e('Admins', 'beautyplus'); ?></a>
                  <div class="mr-5"></div>
                  <label class="switch">
                    <input  data-id="<?php echo esc_attr($m_k);  ?>" data-for="shop_manager" type="checkbox" value="1" class="beautyplus-panel-item-onoff success" <?php if (1 === $m["roles"]['shop_manager']) echo " checked"; ?> />
                    <span class="__A__slider"></span>
                  </label>

                  <?php esc_html_e('Shop Managers', 'beautyplus'); ?></a>
                </div>
              </div>

              <div class="__A__Description">

                <button class="__A__Settings_Change_Icon" data-id="<?php echo esc_attr($m_k);  ?>" data-iconset="fontawesome5"><?php esc_html_e('Change icon', 'beautyplus'); ?></button>

                <a href="javascript:;" class="__A__Products_Hand" title="<?php esc_attr_e('Move', 'beautyplus'); ?>"><span>≡</span></a>
              </div>

              <?php switch ($m_k) {
                case 'beautyplus-orders': ?>
                <div class="__A__Options">
                  <span class="text-mute __A__Settings_On_H">&mdash;&mdash;&mdash;</span>
                  <br>
                  <br>
                  <?php esc_html_e('How will it look?', 'beautyplus'); ?>
                  <div class="mt-3">
                    <?php
                    $modes = array( 'Standard'=> array('1',''), 'Woocommerce Native'=> array('99',""));
                    $current_mode = BeautyPlus::option('mode-' . esc_attr( $m_k ));
                    foreach ($modes AS $mode=>$value) {
                      ?>
                      <div class="radio">
                        <label>
                          <input class="radio-button beautyplus-modes" name="beautyplus_mode_<?php echo esc_attr($m_k);  ?>" data-panel="<?php echo esc_attr($m_k); ?>" type="radio"  value="<?php echo esc_attr($value[0]); ?>" <?php if ($value[0] === $current_mode) echo esc_attr( ' checked' ); ?>/>
                          <strong><?php echo esc_html($mode);?></strong> &nbsp;&nbsp; <?php echo esc_html($value[1]); ?>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <?php break; ?>

                <?php case 'beautyplus-products': ?>
                <div class="__A__Options">
                  <span class="text-mute __A__Settings_On_H">&mdash;&mdash;&mdash;</span>
                  <br>
                  <br>
                  <?php esc_html_e('How will it look?', 'beautyplus'); ?>
                  <div class="mt-3">
                    <?php
                    $modes = array( 'Standard'=>array('1',''), 'Woocommerce Native'=>array('99', ''));
                    $current_mode = BeautyPlus::option('mode-' . esc_attr( $m_k ));
                    foreach ($modes AS $mode=>$value) {
                      ?>
                      <div class="radio">
                        <label>
                          <input class="radio-button beautyplus-modes" name="beautyplus_mode_<?php echo esc_attr($m_k);  ?>" data-panel="<?php echo esc_attr($m_k); ?>" type="radio"  value="<?php echo esc_attr($value[0]); ?>" <?php if ($value[0] === $current_mode) echo esc_attr( ' checked' ); ?>/>
                          <strong><?php echo esc_html($mode);?></strong> &nbsp;&nbsp; <?php echo esc_html($value[1]); ?>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <?php break; ?>

                <?php case 'beautyplus-customers': ?>
                <div class="__A__Options">
                  <span class="text-mute __A__Settings_On_H">&mdash;&mdash;&mdash;</span>
                  <br>
                  <br>
                  <?php esc_html_e('How will it look?', 'beautyplus'); ?>
                  <div class="mt-3">
                    <?php
                    $modes = array( 'Standard'=>array('1',''),  'Woocommerce Native'=>array('99',""));
                    $current_mode = BeautyPlus::option('mode-' . esc_attr( $m_k ));
                    foreach ($modes AS $mode=>$value) {
                      ?>
                      <div class="radio">
                        <label>
                          <input class="radio-button beautyplus-modes" name="beautyplus_mode_<?php echo esc_attr($m_k);  ?>" data-panel="<?php echo esc_attr($m_k); ?>" type="radio"  value="<?php echo esc_attr($value[0]); ?>" <?php if ($value[0] === $current_mode) echo esc_attr( ' checked' ); ?>/>
                          <strong><?php echo esc_html($mode);?></strong> &nbsp;&nbsp; <?php echo esc_html($value[1]); ?>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <?php break; ?>
                <?php case 'beautyplus-comments': ?>
                <div class="__A__Options">
                  <span class="text-mute __A__Settings_On_H">&mdash;&mdash;&mdash;</span>
                  <br>
                  <br>
                  <?php esc_html_e('How will it look?', 'beautyplus'); ?>
                  <div class="mt-3">
                    <?php
                    $modes = array( 'Standard'=>array('1',''),  'Woocommerce Native'=>array('99',""));
                    $current_mode = BeautyPlus::option('mode-' . esc_attr( $m_k ));
                    foreach ($modes AS $mode=>$value) {
                      ?>
                      <div class="radio">
                        <label>
                          <input class="radio-button beautyplus-modes" name="beautyplus_mode_<?php echo esc_attr($m_k);  ?>" data-panel="<?php echo esc_attr($m_k); ?>" type="radio"  value="<?php echo esc_attr($value[0]); ?>" <?php if ($value[0] === $current_mode) echo esc_attr( ' checked' ); ?>/>
                          <strong><?php echo esc_html($mode);?></strong> &nbsp;&nbsp; <?php echo esc_html($value[1]); ?>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                </div>

                <?php break; ?>

                <? case 'beautyplus-coupons': ?>
                <div class="__A__Options">
                  <span class="text-mute __A__Settings_On_H">&mdash;&mdash;&mdash;</span>
                  <br>
                  <br>
                  <?php esc_html_e('How will it look?', 'beautyplus'); ?>
                  <div class="mt-3">
                    <?php
                    $modes = array( 'Standard'=>array('1',''),  'Woocommerce Native'=>array('99',""));
                    $current_mode = BeautyPlus::option('mode-' . esc_attr( $m_k ));
                    foreach ($modes AS $mode=>$value) {
                      ?>
                      <div class="radio">
                        <label>
                          <input class="radio-button beautyplus-modes" name="beautyplus_mode_<?php echo esc_attr($m_k);  ?>" data-panel="<?php echo esc_attr($m_k); ?>" type="radio"  value="<?php echo esc_attr($value[0]); ?>" <?php if ($value[0] === $current_mode) echo esc_attr( ' checked' ); ?>/>
                          <strong><?php echo esc_html($mode);?></strong> &nbsp;&nbsp; <?php echo esc_html($value[1]); ?>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
              <?php if (isset($m['parent'])) { ?>
                <br />
                <?php printf(esc_html__('This is submenu of BeautyPlus -- %s', 'beautyplus'), strtoupper(str_replace('beautyplus-', '', $m['parent']))); ?><br />
              <?php } ?>
              <?php if ("0" === $m_k{0}) { ?>
                <br />
                <u><a href="javascript:;" data-id="<?php echo esc_attr( $m_k); ?>" class="beautyplus-panel-item--delete text-danger text-underline"><?php esc_html_e('Remove', 'beautyplus'); ?></a></u>
              <?php } ?>
            </div>
          </div>
        </div>
      </li>
    <?php }?>
  </ol>
  <div class="__A__Item">
    <div class="row">
      <div class="col-lg-2 __A__Title">
        <div class="__A__Item_Icon dashicons-before dashicons-plus-alt"></div>
        <?php esc_html_e('New Menu', 'beautyplus'); ?>
      </div>
      <div class="col-lg-8 __A__Description">
        <div class="row">
          <div class="col-12 col-md-3 mb-3">
            <select name="beautyplus-panel-item--new-parent" id="beautyplus-panel-item--new-parent" class="form-control">
              <option value="0">
                -- <?php esc_html_e('In Admin Panel', 'beautyplus'); ?> --
              </option>
              <option value="00">
                -- <?php esc_html_e('In New Tab', 'beautyplus'); ?> --
              </option>
              <?php foreach ($menu AS $menu_key=>$menu_value) { ?>
                <?php if (!isset($menu_value['admin_link']) && $menu_key !== 'beautyplus-dashboard'  && $menu_key !== 'beautyplus-reports'  && $menu_key !== 'beautyplus-reactors') {?>
                  <option value='<?php echo esc_attr($menu_key); ?>'>
                    <?php echo esc_html($menu_value['title']); ?>
                  </option>
                <?php } ?>
              <?php } ?>
            </select>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <input id="beautyplus-panel-item--new-title" class="form-control"  placeholder="<?php esc_html_e('Title', 'beautyplus'); ?>"/>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <input id="beautyplus-panel-item--new-url" class="form-control"  placeholder="<?php esc_html_e('URL', 'beautyplus'); ?>"/>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <button class="__A__Button1  badge-black" id="beautyplus-panel-item--new-save"><?php esc_html_e('Save', 'beautyplus'); ?></button>
          </div>
        </div>
        <div class="__A__Description"><?php esc_html_e('You can add a new link to your panel. If you want to add this link to an another menu, then select a parent panel from "---" section', 'beautyplus'); ?></div>
      </div>
    </div>
  </div>

  <div class="__A__Item">
    <div class="row">
      <div class="col-lg-2 __A__Title">
      </div>
      <div class="col-lg-8 __A__Description">
        <div class="row">
          <div class="col-12">
            <a href="javascript:;" id="beautyplus-reset-menu"><?php esc_html_e('Reset menu', 'beautyplus'); ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
