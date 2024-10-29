<?php

/**
* BeautyPlus Uninstall
*
* Fired during BeautyPlus uninstall
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove options
$options = array(
	'menu',
	'mode-beautyplus-orders',
	'mode-beautyplus-products',
	'mode-beautyplus-customers',
	'mode-beautyplus-coupons',
	'mode-beautyplus-comments',
	'feature-full',
	'feature-auto',
	'feature-use-administrator',
	'feature-use-shop_manager',
	'feature-logo',
	'feature-pulse',
	'feature-own_themes',
	'feature-sounds_notification',
	'feature-sounds_product',
	'feature-sounds_checkout',
	'goals-daily',
	'goals-weekly',
	'goals-monthly',
	'goals-yearly',
	'refresh',
	'dashboard_widgets',
	'dashboard_widgets_settings',
	'reactors_list',
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
	delete_option('beautyyplus_' . $option);
}

// Remove tables
global $wpdb;

$tableArray = array(
	$wpdb->prefix . "beautyyplus_daily",
	$wpdb->prefix . "beautyyplus_events",
	$wpdb->prefix . "beautyyplus_requests",
);

foreach ($tableArray as $tablename) {
	$wpdb->query("DROP TABLE IF EXISTS $tablename");
}
