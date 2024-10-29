<?php

/**
* Tweaks
*
* @since      1.1.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework/libs/widgets
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class Reactors__tweaks__tweaks  {

  public static function settings() {

    wp_enqueue_script("jquery-fontselect",     BeautyPlus_Public . "3rd/jquery.fontselect.js", array(), BeautyPlus_Version);


    $reactor = BeautyPlus_Reactors::reactors_list('tweaks');

    $saved = 0;

    if ($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'beautyplus_reactors' ) ) {
        exit;
      }

      /* Detail window size */

      $window_size = intval(BeautyPlus_Helpers::post('reactors-tweaks-window-size', '1090'));
      if ('px' === BeautyPlus_Helpers::post('reactors-tweaks-window-size-dimension', 'px') && $window_size < 700) {
        $window_size = 900;
      }
      if ('%' === BeautyPlus_Helpers::post('reactors-tweaks-window-size-dimension', 'px') && ($window_size < 20 || $window_size > 100)) {
        $window_size = 60;
      }
      BeautyPlus::option('reactors-tweaks-window-size', intval($window_size).BeautyPlus_Helpers::post('reactors-tweaks-window-size-dimension', 'px'), 'set');

      /* Order statuses */

      $order_statuses = array();
      foreach (array_keys(wc_get_order_statuses()) AS $key)  {
        $order_statuses[$key] = $key;
      }

      foreach ($_POST['reactors-tweaks-order-statuses'] AS $index => $key) {
        if (isset($order_statuses[$key])) {
          unset($order_statuses[$key]);
        }
      }

      BeautyPlus::option('reactors-tweaks-order-statuses', array_keys($order_statuses), 'set');

      // reactors-tweaks-adminbar-hotkey

      BeautyPlus::option('reactors-tweaks-adminbar-hotkey', BeautyPlus_Helpers::post('reactors-tweaks-adminbar-hotkey', 0), 'set');

      // Landing page

      BeautyPlus::option('reactors-tweaks-landing', strtolower(BeautyPlus_Helpers::post('reactors-tweaks-landing', 'dashboard')), 'set');

      // reactors-tweaks-settings-woocommerce

      BeautyPlus::option('reactors-tweaks-settings-woocommerce', BeautyPlus_Helpers::post('reactors-tweaks-settings-woocommerce', 0), 'set');

      // reactors-tweaks-screenoptions

      BeautyPlus::option('reactors-tweaks-screenoptions', BeautyPlus_Helpers::post('reactors-tweaks-screenoptions', 0), 'set');

      //reactors-tweaks-icon-text

      BeautyPlus::option('reactors-tweaks-icon-text', BeautyPlus_Helpers::post('reactors-tweaks-icon-text', 0), 'set');

      //reactors-tweaks-font
      $redirect = 0;

      if (BeautyPlus::option('reactors-tweaks-font') !== BeautyPlus_Helpers::post('reactors-tweaks-font', 'Open+Sans:400')) {
        $redirect = 1;
      }

      BeautyPlus::option('reactors-tweaks-font', BeautyPlus_Helpers::post('reactors-tweaks-font', 'Open+Sans:400'), 'set');

      if ('Theme+Default' === BeautyPlus_Helpers::post('reactors-tweaks-font', 'Open+Sans:400') ) {
        delete_option('beautyplus_reactors-tweaks-font');
      }

      if (1 === $redirect) {
        wp_redirect(BeautyPlus_Helpers::admin_page('reactors', array('action'=>'detail', 'id'=>'tweaks')));
      }
      $saved = 1;

    }

    echo   BeautyPlus_View::reactor('tweaks/views/settings', array('reactor' => $reactor, 'saved' => $saved));
  }

  public static function init() {
  }


  public static function deactivate() {
    // Remove options
    $options = array(
      'reactors-tweaks-window-size',
      'reactors-tweaks-window-size-dimension',
      'reactors-tweaks-order-statuses',
      'reactors-tweaks-adminbar-hotkey',
      'reactors-tweaks-landing',
      'reactors-tweaks-settings-woocommerce',
      'reactors-tweaks-icon-text',
      'reactors-tweaks-font',
      'reactors-tweaks-screenoptions'
    );

    foreach ($options AS $option) {
      delete_option('beautyplus_' . $option);
    }
  }
}