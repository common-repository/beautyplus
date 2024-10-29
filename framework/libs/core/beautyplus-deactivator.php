<?php

/**
* BeautyPlus Activator
*
* Fired during plugin deactivation
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class BeautyPlus_Deactivator {

  /**
  * Deactivate
  *
  * @since    1.0.0
  */

  public static function deactivate() {

    // Remove scheduled actions
    wp_clear_scheduled_hook('beautyplus_cron_daily');

  }

}