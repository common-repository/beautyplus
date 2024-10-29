<?php

/**
* BeautyPlus Core
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
*/


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class BeautyPlus {

  public static $theme      = 'one';

  /**
  * Construct of BeautyPlus
  *
  * @since    1.0.0
  */
  public function __construct() {
    //	Nothing to do
  }

  /**
  * Starts everyting
  *
  * @since  1.0.0
  */

  public function start() {

    /* BeautyPlus Admin is loading */
    if (is_admin())  {
      (new BeautyPlus_Admin());
    }

    add_action( 'beautyplus_cron_daily', 'BeautyPlus_Reports::cron_daily');

    //
    add_action( 'save_post_shop_order',                 'BeautyPlus_Events::save_post_shop_order',  20, 1  );
    add_action( 'woocommerce_update_order',             'BeautyPlus_Events::save_post_shop_order',  10, 1  );
    add_action( 'woocommerce_checkout_order_processed', 'BeautyPlus_Events::new_order',  20, 1  );
    add_action( 'woocommerce_thankyou',                 'BeautyPlus_Events::save_post_shop_order',  10, 1  );
    add_action( 'comment_post',                         'BeautyPlus_Events::comment_post', 10, 2 );
    add_action( 'woocommerce_add_to_cart',              'BeautyPlus_Live::woocommerce_add_to_cart', 10, 6);
    add_action( 'woocommerce_remove_cart_item',         'BeautyPlus_Live::woocommerce_remove_cart_item', 10, 2);
    add_action( 'woocommerce_checkout_order_review',    'BeautyPlus_Live::woocommerce_checkout_order_review', 10, 2 );
    ////

    add_filter('post_updated_messages', 'BeautyPlus_Events::save_post_shop_order');

    // Starting Reactors
    $reactors = BeautyPlus::option('reactors_list', array());

    if (is_array($reactors) && count($reactors) > 0) {
      foreach ($reactors AS $reactor => $reactor_details) {
        $class = "Reactors__" . sanitize_key($reactor) . "__" . sanitize_key($reactor);
        if (class_exists($class)) {
          $class::init();
        }
      }
    }

    // Enable BeautyPlus Pulse for tracking
    if ( "1" === BeautyPlus::option('feature-pulse', "0")) {
      add_action('wp_ajax_nopriv_beautyplus_pulse', 'BeautyPlus_Live::pulse');
      add_action('wp_ajax_beautyplus_pulse', 'BeautyPlus_Live::pulse');
      self::enable_live_pulse();
    }
  }


  /**
  * Track pages in front-end
  *
  * @since  1.0.0
  */

  public static function enable_live_pulse()  {
    add_action("wp_footer", function() {

      wp_enqueue_script('beautyplus',  BeautyPlus_Public . 'js/beautyplus.js');

      $JSvars = array(
        'ajax_url'                => admin_url('admin-ajax.php')
      );


      $JSvars["beautyplus_p"] = "pulse";

      if (BeautyPlus_Helpers::get('s')) {

         $JSvars["beautyplus_t"] = "s";
         $JSvars["beautyplus_i"] = BeautyPlus_Helpers::get('s', '');
       } else if (is_front_page()) {

         $JSvars["beautyplus_t"] = "h";
         $JSvars["beautyplus_i"] = 0;

       } else if (is_product()) {
         global $product;

         $JSvars["beautyplus_t"] = "p";
         $JSvars["beautyplus_i"] = $product->id;

       } else if (is_product_category()) {
         global $wp_query;

         $cat = $wp_query->get_queried_object();

         $JSvars["beautyplus_t"] = "c";
         $JSvars["beautyplus_i"] = $cat->term_id;

       } else {

         global $wp_query;

         if (isset($wp_query->queried_object) && is_object($wp_query->queried_object) && isset($wp_query->queried_object->has_archive)) {
           $page = get_page_by_path($wp_query->queried_object->has_archive);
           if ($page) {
             $JSvars["beautyplus_t"] = "o";
             $JSvars["beautyplus_i"] = $page->ID;
           }
         } else if (isset($wp_query->post->ID) && $wp_query->post->ID > 0) {
           $JSvars["beautyplus_t"] = "o";
           $JSvars["beautyplus_i"] = intval($wp_query->post->ID);
         }
       }

      wp_localize_script('beautyplus', 'BeautyPlus_vars', $JSvars);
    });
  }

  /**
  * Options for BeautyPlus
  *
  * @since  1.0.0
  */

  public static function option($key, $default = '', $action = 'get', $user = false) {

    if ($user) {
      $current_user    = wp_get_current_user();
      $key .= '__' . intval( $current_user->ID ) . '__';
    }

    if ('get' === $action) {
      $value = get_option( 'beautyplus_' . $key, $default );
      return $value;
    }	else
    {
      update_option( 'beautyplus_' . $key, $default );
    }
  }

  /**
  * Starts Woocommece API
  *
  * @since  1.0.0
  */

  public static function wc_engine()  {
    global $wpdb;

    WC()->api->includes();
    WC()->api->register_resources( new WC_API_Server( '/' ) );

  }


}

?>