<?php

/**
* BeautyPlus Admin
*
* Actions/Filters and initial functions
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
*/


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class BeautyPlus_Admin {

  public static $api = array();
  public static $menu_hash = array();

  /**
  * Construct
  *
  * @since    1.0.0
  */

  public function __construct() {

    // Actions
    add_action( 'admin_init',                         'BeautyPlus_Admin::init' );
    add_action( 'admin_head',                         'BeautyPlus_Admin::admin_head', 10);
    add_action( 'in_admin_header',                    'BeautyPlus_Admin::in_admin_header', 10);
    add_action( 'admin_menu',                         'BeautyPlus_Admin::admin_menu' );
    add_action( 'admin_enqueue_scripts',              'BeautyPlus_Admin::styles' );
    add_action( 'beautyplus_submenu',                 'BeautyPlus_Helpers::submenu', 10, 1);
    add_action( 'wp_ajax_beautyplus_ajax',            'BeautyPlus_Ajax::run');
    add_action( 'wp_ajax_beautyplus_settings',        'BeautyPlus_Settings::ajax');
    add_action( 'wp_ajax_beautyplus_widgets',         'BeautyPlus_Widgets::ajax');
    add_action( 'wp_ajax_beautyplus_settings_panels', 'BeautyPlus_Settings::ajax_panels_active');
    add_action( 'woocommerce_api_pagination_headers', 'BeautyPlus_Helpers::api_pagination', 10, 2);

    // Filters
    add_filter( 'admin_title',                        'BeautyPlus_Admin::admin_title', 10, 1);
    add_filter( 'admin_body_class',                   'BeautyPlus_Admin::admin_body_class',10, 1 );
    add_filter( 'comment_edit_redirect',              'BeautyPlus_Admin::filter_comment_edit_redirect', 10, 2 );


  }

  /**
  * initial
  *
  * @since 1.0.0
  */

  public static function init() {

    global $pagenow;

    /* Check if WooCommerce is activated. */

    if (!class_exists('WooCommerce')) {
      return;
    }


    if ( "1" === BeautyPlus::option('feature-own_themes')) {
      $current_user    = wp_get_current_user();
      BeautyPlus::$theme = BeautyPlus::option('theme-' . intval( $current_user->ID ));

      if ( !BeautyPlus::$theme ) {
        BeautyPlus::$theme = BeautyPlus::option('theme', 'one');
      }

    } else {
      BeautyPlus::$theme = BeautyPlus::option('theme', 'one');
    }

    // i18n
    $mo_file = BeautyPlus_Framework. 'languages/' . get_locale() . '.mo';

    if (!file_exists($mo_file)) {
      $mo_file = BeautyPlus_Framework. 'languages/beautyplus-' . get_locale() . '.mo';
    }

    if (!file_exists($mo_file)) {
      $mo_file = WP_LANG_DIR. '/plugins/beautyplus-' . get_locale() . '.mo';
    }

    load_textdomain( 'beautyplus', $mo_file );

    if ($pagenow === 'index.php'
    && !is_network_admin()
    && 0 === count($_GET)
    && (
      (self::is_admin(null) && ("1" === BeautyPlus::option( 'feature-use-administrator', "0" )))
      || (self::is_admin(null,'shop_manager') && ("1" === BeautyPlus::option( 'feature-use-shop_manager', "0" )) )
      || ("1" === BeautyPlus::option( 'feature-auto', "0" ))
      )
      && (self::is_admin($user) || is_admin($user, 'shop_manager'))
      )
      {
        wp_redirect( admin_url ( 'admin.php?page=beautyplus&segment=' . BeautyPlus::option('reactors-tweaks-landing', 'dashboard') ) );
      }

    }

    /**
    * Title of admin panel
    *
    * @since  1.0.0
    */

    public static function admin_title() {
      return BeautyPlus_Events::get_title(0);
    }

    /**
    * admin_body_class filter
    *
    * @since  1.0.0
    */

    public static function admin_body_class( $classes ) {

      if (self::is_beautyplus()) {
        $classes .= 'beautyplus-engine';
      }

      if (self::is_full()) {
        $classes .= ' beautyplus-full';
      } else {
        $classes .= ' beautyplus-half';

      }

      return "$classes beautyplus-admin-" . esc_attr(BeautyPlus_Helpers::get('segment', 'dashboard')) .  " beautyplus-action-" . esc_attr(BeautyPlus_Helpers::get('action', 'default'));
    }

    /**
    * Build and show admin menu
    *
    * @since  1.0.0
    */
    public static function get_menu( $args = array() ) {

      global $submenu;

      $beautyplus_menu = BeautyPlus::option('menu', array());

      if (0 === count ($beautyplus_menu) OR !is_array($beautyplus_menu)) {

        BeautyPlus::option( 'menu', array (
          'beautyplus-dashboard' => array( 'title' => esc_html__('Dashboard', 'beautyplus'), 'segment' => 'dashboard', 'icon' => 'dashicons-admin-site', 'order' => -9, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'beautyplus-orders'    => array( 'title' => esc_html__('Orders', 'beautyplus'), 'segment' => 'orders', 'icon' => 'dashicons-cart', 'order' => -8, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'beautyplus-products'  => array( 'title' => esc_html__('Products', 'beautyplus'), 'segment' => 'products', 'icon' => 'dashicons-screenoptions', 'order' => -7, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'beautyplus-customers' => array( 'title' => esc_html__('Customers', 'beautyplus'), 'segment' => 'customers', 'icon' => 'dashicons-admin-users', 'order' => -6, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'beautyplus-reports'   => array( 'title' => esc_html__('Reports', 'beautyplus'), 'segment' => 'reports', 'icon' => 'dashicons-chart-pie', 'order' => -5, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'beautyplus-coupons'   => array( 'title' => esc_html__('Coupons', 'beautyplus'), 'segment' => 'coupons', 'icon' => 'dashicons-carrot', 'order' => -4, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
          'beautyplus-comments'  => array( 'title' => esc_html__('Comments', 'beautyplus'), 'segment' => 'comments', 'icon' => 'dashicons-admin-comments', 'order' => -3, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1)),
        ), 'set');

        $beautyplus_menu = BeautyPlus::option('menu', array());
      }

      $output = array();

      if (isset( $args['settings'] )) {
        $beautyplus_menu['beautyplus-settings'] =  array( 'title' => esc_html__('Settings', 'beautyplus'), 'segment' => 'settings', 'icon' => 'dashicons-admin-generic', 'order' => 999, 'active' => 1, 'roles'=>array('administrator' =>1, 'shop_manager'=>1));
      }

      $next_index = 100;

      if (self::is_admin(null, 'administrator')) {
        $user_role = 'administrator';
      } elseif (self::is_admin(null, 'shop_manager')) {
        $user_role = 'shop_manager';
      } else {
        $user_role = 'other';
      }


      // Add all menus to BeautyPlus Menu
      foreach ($GLOBALS[ 'menu' ] as $key => $value) {
        if (isset($value[5]) && $value[5] !== "toplevel_page_beautyplus") {
          $all_menu[$value[5]] = array(
            'title'=> $value[0],
            'admin_link' => $value[2],
            'active'=>null,
            'other' => '1'
          );

          if (isset($beautyplus_menu[$value[5]]['roles'][$user_role])) {
            $all_menu[$value[5]]['active'] = $beautyplus_menu[$value[5]]['roles'][$user_role];
            $all_menu[$value[5]]['roles'] = $beautyplus_menu[$value[5]]['roles'];
          }

          if (!isset($beautyplus_menu[$value[5]]['icon'])) {
            $all_menu[$value[5]]['icon'] = $value[6];
          } else {
            $all_menu[$value[5]]['icon'] = $beautyplus_menu[$value[5]]['icon'];
          }

          ++$next_index;

          if (!isset($beautyplus_menu[$value[5]]['order'])) {
            $all_menu[$value[5]]['order'] = $next_index;
          } else {
            $all_menu[$value[5]]['order'] = $beautyplus_menu[$value[5]]['order'];
          }
        }
      }

      $menu = array_merge($beautyplus_menu, $all_menu);

      foreach ($menu AS $_m_k => $_m)  {
        if (isset($_m['parent'])) {
          continue;
        }

        if (!isset($_m['roles'])) {
          $_m['roles'] = array('administrator' =>1, 'shop_manager'=>1);
        }

        if (!isset($_m['active'])) {
          $_m['active'] = 1;
        }

        if ('administrator' === $user_role && 1 === $_m['roles']['administrator']) {
          $_m['active'] = 1;
        } else if ('shop_manager' === $user_role && 1 === $_m['roles']['shop_manager']) {
          $_m['active'] = 1;
        } else {
          $_m['active'] = 0;
        }

        if (!isset($all_menu[$_m_k]) AND false === stripos($_m_k, 'beautyplus-') AND false === stripos($_m_k, '0-')) {
          $_m['active'] = 0;
        }

        if (isset($_m['admin_link']) && isset($submenu[$_m['admin_link']])) {
          foreach ($submenu[$_m['admin_link']] AS $sublink_key => $sublink) {
            if (false === stripos($sublink[2], '.')) {
              $sublink[2] = 'admin.php?page='. $sublink[2];
            }

            if ( !empty( get_plugin_page_hook($sublink[2], $_m['admin_link'])) OR ('index.php' !== $sublink[2]  && file_exists( WP_PLUGIN_DIR . "/".$sublink[2] ) )) {
              $sublink[2] = admin_url ( 'admin.php?page=' . sanitize_text_field($sublink[2]) );
            }

            if (false !== stripos($sublink[2], 'customize.php')) {
              $sublink[2] = 'customize.php';
            }

            if (false !== stripos($sublink[2], 'index.php')) {
              $sublink[2] = 'index.php?dashboard=yes';
            }

            self::$menu_hash[md5($sublink[2])] = array($sublink[2], strip_tags($_m['title']).' - '.  $sublink[0]);

            if (!self::is_full()) {
              $sublink[2] = BeautyPlus_Helpers::secure_url('frame', md5($sublink[2]), array('go' => md5($sublink[2]) ));
            }
            $_m["submenu"][$sublink_key]  = $sublink;

          }
        }

        if (isset($_m['admin_link'])) {
          self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);

          if ('index.php' === $_m['admin_link']) {
            $_m['admin_link'] = 'index.php?dashboard=yes';
          }
          if (isset($_m['other']) && false !== stripos($_m['admin_link'], '.')) {

            if ( !empty( get_plugin_page_hook($_m['admin_link'],"admin.php")) OR ( 'index.php' !== $_m['admin_link']  && file_exists( WP_PLUGIN_DIR . "/".$_m['admin_link'] ) )) {
              $_m['admin_link'] = admin_url ( 'admin.php?page=' . sanitize_text_field($_m['admin_link']) );
            } else {
              $_m['admin_link'] = $_m['admin_link'];
            }
          } else {
            //  if (isset($submenu[$_m['admin_link']])) {
            //    $_m['admin_link'] = 'javascript:;';
            //  } else {
            if (isset($_m['target']) && '_blank' === $_m['target']) {
              $_m['admin_link'] = $_m['admin_link'];
            } else if (false === stripos($_m['admin_link'], '.')) {
              self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);
              $_m['admin_link'] = admin_url ( 'admin.php?page=' . sanitize_key($_m['admin_link']) );
            } else {
              self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);
              $_m['admin_link'] = BeautyPlus_Helpers::secure_url('frame', md5($_m['admin_link']), array('go' => md5($_m['admin_link']) ));
            }
            //  }
          }

          if (!self::is_full() && isset($_m['other'])) {
            self::$menu_hash[md5($_m['admin_link'])] = array($_m['admin_link'], $_m['title']);
            $_m['admin_link'] = BeautyPlus_Helpers::secure_url('frame', md5($_m['admin_link']), array('go' => md5($_m['admin_link']) ));
          }
        }

        $output[$_m_k] = $_m;

      }

      array_multisort(array_map(function($element) {
        return $element['order'];
      }, $output), SORT_ASC, $output);

      /* Check if WooCommerce is activated. */
      if (class_exists('WooCommerce')) {
        /* Badges */
        $output['beautyplus-orders']['badge'] = wc_orders_count('on-hold') + wc_orders_count('processing') + wc_orders_count('pending');
      }

      $output['beautyplus-comments']['badge'] = intval(wp_count_comments()->moderated);



      if ( isset($args['hash'])) {
        return;
      }

      return BeautyPlus_View::run('core/menu', array('_beautyplus_menu' => $output));

    }


    /**
    * Register admin menu to Wordpress
    *
    * @since  1.0.0
    */

    public static function admin_menu() {
      add_menu_page( 'BeautyPlus', 'BeautyPlus', 'manage_woocommerce', 'beautyplus', 'BeautyPlus_Admin::admin_page', 'dashicons-plus-alt' );
    }

    /**
    * Router for BeautyPlus sub panels
    *
    * @since  1.0.0
    */

    public static function admin_page() {

      switch (BeautyPlus_Helpers::get('segment', BeautyPlus::option('reactors-tweaks-landing', 'dashboard'))) {

        case "coupons":
        BeautyPlus_Coupons::run();
        break;

        case "orders":
        BeautyPlus_Orders::run();
        break;

        case "products":
        BeautyPlus_Products::run();
        break;

        case "customers":
        BeautyPlus_Customers::run();
        break;

        case "comments":
        BeautyPlus_Comments::run();
        break;

        case "reports":
        BeautyPlus_Reports::run();
        break;

        case "settings":
        BeautyPlus_Settings::run();
        break;


        case "frame":

        if ($url = BeautyPlus_Helpers::get('in')) {

          if ( ! wp_verify_nonce( BeautyPlus_Helpers::get('_asnonce'),  'beautyplus-segment--notifications' ) ) {
            wp_die( esc_html__('Failed on security check', 'beautyplus') );
          }

          $url = esc_url_raw( urldecode($url) );

          if (strpos($url, admin_url()) !== false && strpos($url, admin_url()) === 0) {
            BeautyPlus_Helpers::frame( BeautyPlus_Helpers::get('in') );
          } else {
            esc_html_e("Restricted Area.", 'beautyplus');
            wp_die();
          }

        }  else {

          $go = sanitize_key(BeautyPlus_Helpers::get('go'));

          self::get_menu( array('hash'=> true) );

          if ( !isset(self::$menu_hash[$go]) ) {
            esc_html_e("Restricted Area.", 'beautyplus');
            wp_die();
          }

          BeautyPlus_Helpers::frame( self::$menu_hash[$go][0] );
        }

        break;

        default:
        BeautyPlus_Dashboard::run();
        break;
      }

    }

    /**
    * Styles and scripts enqueue
    *
    * @since  1.0.0
    */

    public static function styles() {

      if ((self::is_full()) OR (!self::is_full() && self::is_beautyplus())) {

        // Styles
        if (self::is_beautyplus()) {
          wp_enqueue_style("bootstrap", BeautyPlus_Public . "3rd/bootstrap/4.3.1/css/bootstrap.min.css");

        } else {
          wp_enqueue_style("beautyplus-font",           "//fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese");
          wp_enqueue_style("bootstrap-lite", BeautyPlus_Public . "css/bootstrap-lite.css", null, BeautyPlus_Version);
        }

        wp_enqueue_style("fontawesome", BeautyPlus_Public . "3rd/fontawesome/css/all.min.css");


        wp_enqueue_style("beautyplus-shared",    BeautyPlus_Public . "css/shared.css", null, BeautyPlus_Version);

        // Themes
        if ('one-shadow' === BeautyPlus::$theme) {
          wp_enqueue_style("beautyplus-theme-required",     BeautyPlus_Public . "css/theme-one.css", null, BeautyPlus_Version);
        }

        wp_enqueue_style("beautyplus-theme",     BeautyPlus_Public . "css/theme-". esc_attr( BeautyPlus::$theme ) .".css", null, BeautyPlus_Version);


        $colors = BeautyPlus::option('colors', BeautyPlus_Settings::colors()['ffffff'], 'get', true);
        $colors_css = ":root{";
          foreach ($colors AS $k=>$v) {
            $colors_css .= esc_html("--$k: $v;");
          }
          $colors_css .= "}";

          if ("1" === BeautyPlus::option('reactors-tweaks-screenoptions', "0")) {
            $colors_css .= '#screen-meta-links {display: block !important;} #screen-meta {margin-top: -9px;}#screen-meta-links .show-settings {border-top: 1px solid #ccd0d4; border-radius:inherit;}';
          }

          wp_add_inline_style('beautyplus-theme', $colors_css);

          if (file_exists( BeautyPlus_Dir . "/public/css/custom.css")) {
            wp_enqueue_style("beautyplus-custom",    BeautyPlus_Public . "css/custom.css", null, BeautyPlus_Version);
          }

          // Fonts

          $fonts = array(
            'one'     => "//fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese",
            'one-shadow'     => "//fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese",
            'console' => "//fonts.googleapis.com/css?family=Source+Sans+Pro:400,600&display=swap&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese"
          );

          if (BeautyPlus::option('reactors-tweaks-font')) {
            $font = esc_attr(BeautyPlus::option('reactors-tweaks-font'));
            wp_enqueue_style("beautyplus-font2",  "//fonts.googleapis.com/css?family=" . $font . ",700,800&display=swap&subset=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin-ext,vietnamese");
            wp_add_inline_style('beautyplus-font2', '#beautyplus-theme,#notifications,#__A__Ajax_Notification,#beautyplus-search-1--overlay {font-family: "'.str_replace(array(':400', '+'), array('', ' '), $font).'" !important; }');
          } else {
            wp_enqueue_style("beautyplus-font",  $fonts[BeautyPlus::$theme]);
          }

          // Scripts
          wp_enqueue_script("beautyplus-3rd",       BeautyPlus_Public . "js/beautyplus-3rd.js", array('jquery','jquery-ui-sortable'), BeautyPlus_Version, TRUE);

          if (self::is_beautyplus()) {
            wp_enqueue_script( "bootstrap.bundle", BeautyPlus_Public . "3rd/bootstrap/4.3.1/js/bootstrap.bundle.min.js", array("jquery",'jquery-ui-sortable'), BeautyPlus_Version, TRUE);
          }

          wp_enqueue_script("beautyplus-admin",     BeautyPlus_Public . "js/beautyplus-admin.js", array(), BeautyPlus_Version, TRUE);

          // Enqueue WP Media scripts for file uploads
          if ('settings' === BeautyPlus_Helpers::get('segment')) {
            //  wp_register_style('wp-admin');
            wp_enqueue_media();
          }

          // Ajax & i18n for BeautyPlus
          $JSvars['ajax_url']                        = admin_url('admin-ajax.php');
          $JSvars['_admin_url']                      = admin_url();
          $JSvars['_asnonce']                        = wp_create_nonce( 'beautyplus-segment--' . BeautyPlus_Helpers::get('segment', false));
          $JSvars['_asnonce_notifications']          = wp_create_nonce( 'beautyplus-segment--notifications');
          $JSvars['_asnonce_search']                 = wp_create_nonce( 'beautyplus-segment--search');
          $JSvars['refresh']                         = absint(BeautyPlus::option('feature-refresh', 10))*1000;
          $JSvars['i18n']                            = array('wait'=> esc_html__('Please wait', 'beautyplus'), 'done'=> esc_html__('Done', 'beautyplus'));
          $JSvars['reactors_tweaks_window_size']     = BeautyPlus::option('reactors-tweaks-window-size', '1090px');
          $JSvars['reactors_tweaks_adminbar_hotkey'] = intval(BeautyPlus::option('reactors-tweaks-adminbar-hotkey', 1));

          wp_localize_script('beautyplus-admin', 'BeautyPlusGlobal', $JSvars);

        }



      }

      public static function is_full() {

        if ("1" === BeautyPlus::option( 'feature-full', "0" )) {
          BeautyPlus::option( 'feature-use-administrator', "1", 'set' );
          BeautyPlus::option( 'feature-use-shop_manager', "1", 'set' );
          delete_option('beautyplus_feature-full');
        }

        if (is_network_admin()) {
          return false;
        }

        if (self::is_admin(null, 'administrator') && ("1" === BeautyPlus::option( 'feature-use-administrator', "0" ))) {
          return true;
        } elseif (self::is_admin(null, 'shop_manager') && ("1" === BeautyPlus::option( 'feature-use-shop_manager', "0" ))) {
          return true;
        } else {
          return false;
        }

        return false;
      }

      public static function in_admin_header() {

        if ((self::is_full()) OR (!self::is_full() && self::is_beautyplus())) {
          echo BeautyPlus_View::run('header');
        }
      }

      /**
      * Is user in BeautyPlus page?
      *
      * @since  1.0.0
      */

      public static function is_beautyplus() {
        return (isset($_GET["page"]) AND $_GET["page"]==='beautyplus')?true:false;
      }


      /**
      * Check roles of user
      *
      * @since  1.0.0
      */

      public static function is_admin( $user, $role = 'administrator' ) {
        if ( ! is_object( $user ) ) {
          $user = wp_get_current_user();

        } else {
          //  $user = get_userdata( $user );
        }

        if ( ! $user || ! $user->exists() ) {
          return false;
        }

        return in_array( $role, $user->roles, true );
      }

      /**
      * Auto-start BeautyPlus
      *
      * @since  1.0.0
      */

      public static function admin_default_page($redirect_to, $request, $user ) {

        if( !isset( $user->user_login ) ) {
          return $redirect_to;
        }

        // Auto start
        if ("1" === BeautyPlus::option( 'feature-full', "0" ) && (self::is_admin($user) OR is_admin($user, 'shop_manager')) ) {
          return admin_url ( 'admin.php?page=beautyplus' );
        } else {
          return $redirect_to;
        }
      }


      /**
      * Filter for redirection after comment update
      *
      * @since  1.0.0
      */

      public static function filter_comment_edit_redirect($location, $comment_id) {

        if (0 < stripos($_POST['referredby'], 'beautyplus')) {
          return admin_url ( "edit-comments.php");
        } else {
          return $location;
        }
      }

      /**
      * Hides WP elements in BeautyPlus panel
      *
      * @since  1.0.0
      */

      public static function admin_head() {

        // When does a POST action, we refresh BeautyPlus page
        if ($_POST) {
          echo '<script>"use strict"; window.parent.refreshOnClose=1;</script>';
        }

        if ((self::is_full()) OR (!self::is_full() && self::is_beautyplus())) {
          echo BeautyPlus_View::run('footer');
        }

        // Hides some WP styles when it is loaded from BeautyPlus iframe
        echo '<script>
        "use strict";
        var BeautyPlus_Window = 1; // Necessary global scope with unique prefix
        if (self!==top && window.parent.BeautyPlus_Window != null && window.parent.BeautyPlus_Window != undefined) {
          document.write("<style> \
          body{background: transparent} \
          html.wp-toolbar {padding-top: 0 !important;} \
          #wpbody { width: 100% !important; padding-left:0px !important; padding-top: 0px !important; } \
          .update-nag,#beautyplus-header, .beautyplus-header-top, .beautyplus-header-top-container, #trig2, .__A__Site_Name {display:none !important;} \
          #adminmenuback,#adminmenuwrap,#screen-meta-links,#wpadminbar,#woocommerce-embedded-root,.woocommerce-layout__header{display: none !important;} \
          body:not(.beautyplus-engine) #wpbody-content { margin-right: 0px; margin-left: 0px; padding-top: 0px; width:100%; } \
          body:not(.beautyplus-engine).rtl #wpbody {margin-right: 0px !important} \
          .woocommerce-embed-page .wrap { padding-top:0px !important } \
          @media (max-width: 782px) { \
            #wpbody { padding-top: 0px; } \
            body:not(.beautyplus-engine) #wpbody { padding-top: 0px !important; } \
            .woocommerce-table__table {width:88vw !important} \
            .woocommerce table.form-table .select2-container, .woocommerce table.form-table input[type=text], .select2-container{width:80vw !important; max-width:80vw !important;min-width:100px !important} \
            .woocommerce_order_items_wrapper {width:85vw !important; max-width:90vw !important} \
            .woocommerce-layout__primary { \
              margin-top: 0px; \
            } \
          } \
          @media (min-width: 782px) { \
            #footer, #wpcontent {margin-left : 0 !important;padding-left: 0 !important;} \
            .rtl #footer, .rtl #wpcontent {margin-right : 0 !important;padding-left: 0 !important;} \
            .rtl #wpcontent { margin-right: 0px; } \
            .woocommerce-embed-page .wrap {padding:00px 0px 0px 0px; width:90%} \
            .woocommerce-layout__primary { margin-left: 0; margin-top: 50px; } \
            .wrap { margin:0 auto !important; width: 90%; padding-top:10px } \
            body.auto-fold .edit-post-layout__content, .edit-post-header {margin-left:0px !important; left: 0 !important;} \
            .woocommerce-layout__primary{margin-top: 20px !important; padding-top:0px !important;} \
            .update-nag a {color: #353535 !important;} \
          } \
          \
          ::-webkit-scrollbar {width: 8px;height: 8px; background-color: rgb(245, 245, 245); }\
          ::-webkit-scrollbar:hover { background-color: rgba(0, 0, 0, 0.09); }\
          ::-webkit-scrollbar-thumb { background : rgb(230, 230, 230);-webkit-border-radius: 100px; } \
          ::-webkit-scrollbar-thumb:active { background : rgba(0,0,0,0.61); -webkit-border-radius: 100px; } \
          </style>");
          jQuery(document).ready(function(jQuery){if (jQuery(".inbrowser--loading", window.parent.document).length>0) { jQuery(".inbrowser--loading", window.parent.document).removeClass("d-flex").addClass("hidden").css("display", "none !important"); } jQuery(".button,.submitdelete").on("click",function() {window.parent.refreshOnClose=1;console.log("refreshOnClose")})});
        }</script>';

      }
    }