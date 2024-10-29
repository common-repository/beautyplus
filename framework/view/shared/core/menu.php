<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<?php foreach ( $_beautyplus_menu AS $beautyplus_menu_k => $beautyplus_menu ) { ?>
  <?php if ( 1 === $beautyplus_menu ['active']) { ?>
    <li id="beautyplus-<?php echo esc_attr($beautyplus_menu_k)?>" title="<?php echo esc_html(strip_tags($beautyplus_menu['title'])) ?>" <?php if (isset($beautyplus_menu['segment']) && BeautyPlus_Helpers::get("segment") === $beautyplus_menu['segment']) { echo "class='beautyplus-menu--selected'"; }?>>
      <?php if (isset($beautyplus_menu['admin_link'])) { ?>
        <a href="<?php echo BeautyPlus_Helpers::clean( $beautyplus_menu['admin_link'] ) ?>"<?php if(isset($beautyplus_menu['target'])) { echo esc_attr( " target='_blank'" );} ?>>
        <?php } else { ?>
          <a href="<?php echo admin_url( "admin.php?page=beautyplus&segment=". $beautyplus_menu['segment'] ."" )?>">
          <?php } ?>
          <?php if (false !== stripos($beautyplus_menu['icon'], 'fa-')) {?>
            <div class="dashicons-before svg"><span class='__A__Custom_Icon_Container beautyplus-custom-icon <?php echo esc_attr($beautyplus_menu['icon'])?>'></span></div>
          <?php } else if ('dashicons-admin-generic' === $beautyplus_menu['icon'] OR false === stripos($beautyplus_menu['icon'], 'dashicons') or (false !== stripos($beautyplus_menu['icon'], '//'))) {?>
            <div class="dashicons-before svg"><span class='__A__Custom_Icon_Container beautyplus-menu--empty-icon'><?php echo esc_html(substr($beautyplus_menu['title'],0,2)); ?></span></div>
          <?php } else {?>
            <div class="__A__Custom_Icon_Container dashicons-before <?php echo esc_attr($beautyplus_menu["icon"]); ?>"><?php if("" === $beautyplus_menu["icon"]) { echo "<span class='beautyplus-menu--empty-icon'>" . esc_html(substr($beautyplus_menu['title'],0,2)) ."</span>"; } ?></div>
          <?php } ?>
          <?php if (isset($beautyplus_menu['badge']) && $beautyplus_menu['badge'] > 0 ) { ?>
            <span class="badge badge-pill badge-danger __A__Menu_Badge"><?php echo absint($beautyplus_menu['badge']); ?></span>
          <?php } ?>
          <div class="beautyplus-menu--text"><?php echo wp_kses_post($beautyplus_menu['title']) ?></div></a>
          <?php if (isset($beautyplus_menu['submenu'])) { ?>
            <ul class="beautyplus-header-submenu">
              <?php foreach ($beautyplus_menu['submenu'] AS $sub) { ?>
                <li><a href="<?php echo esc_url($sub[2]) ?>"><span class="beautyplus-menu--textx"><?php echo wp_kses_post($sub[0]) ?></span></a></li>
              <?php }?>
            </ul>
          <?php } ?>
        </li>
      <?php }?>
    <?php }?>
