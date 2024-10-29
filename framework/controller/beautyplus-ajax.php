<?php

/**
* BeautyPlus Ajax
*
* Ajax router
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class BeautyPlus_Ajax extends BeautyPlus {

  /**
  * Main function
  *
  * @since  1.0.0
  */

  public static function run() {

    $segment = BeautyPlus_Helpers::post('segment', false);

    if (!$segment) {
      $segment = BeautyPlus_Helpers::get('segment', false);
    }

    check_admin_referer( 'beautyplus-segment--' . $segment, '_asnonce' );

    if ( ! wp_verify_nonce( $_REQUEST['_asnonce'],  'beautyplus-segment--' . $segment ) ) {
      wp_die( esc_html__('Failed on security check', 'beautyplus') );
    }

    switch ($segment) {
      case 'search':
      BeautyPlus_Events::search();
      break;

      case 'lists':
      BeautyPlus_Events::lists();
      break;

      case 'orders':
      BeautyPlus_Orders::ajax();
      break;

      case 'customers':
      BeautyPlus_Customers::ajax();
      break;

      case 'coupons':
      BeautyPlus_Coupons::ajax();
      break;

      case 'products':
      BeautyPlus_Products::ajax();
      break;

      case 'comments':
      BeautyPlus_Comments::ajax();
      break;

      case 'settings':
      BeautyPlus_Settings::ajax();
      break;

      case 'reports':
      BeautyPlus_Reports::ajax();
      break;

      case 'dashboard':
      BeautyPlus_Dashboard::ajax();
      break;

      case 'notifications':
      BeautyPlus_Events::notifications();
      break;
      }
    }

    /**
    * Print error message on failure
    *
    * @since  1.0.0
    * @param  string    $error
    */

    public static function error($error) {
      echo json_encode(array('status'=>0, 'error'=> esc_html($error)));
      wp_die();
    }


    /**
    * Print success message
    *
    * @since  1.0.0
    * @param  string    $message
    * @param  array     $details
    */

    public static function success($message = '', $details = array(), $raw = false){
      echo json_encode(array_merge(array('status'=>1, 'message'=>$message), $details));
      wp_die();
    }
  }