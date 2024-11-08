<?php

/**
* BeautyPlus Activator
*
* Activation procedure
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BeautyPlus_Activator {

	/**
	* Main function
	*
	* @since  1.0.0
	*/

	public static function activate() {

		/* Check WooCommerce */

		if (!class_exists('WooCommerce', false)){
			wp_die( __( 'WooCommerce must be enabled before activate the BeautyPlus.', 'beautyplus' ) );
		}

		// Create or updated tables about BeautyPlus

		self::db_update();

		// Cron jobs

		if (! wp_next_scheduled ( 'beautyplus_cron_daily' )) {
			wp_schedule_event(strtotime( 'tomorrow 00:00:01' ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ), 'daily', 'beautyplus_cron_daily');
		}

		// Setting dashboard widgets

		$map = array(
			1 => array('id'=>'1', 'type'=>'overview', 'w'=>10, 'h'=>1),
			2 => array('id'=>'2', 'type'=>'onlineusers', 'w'=>2, 'h'=>3),
			3 => array('id'=>'3', 'type'=>'hourly', 'w'=>8, 'h'=>3),
			4 => array('id'=>'4', 'type'=>'lastactivity', 'w'=>7, 'h'=>5),
			5 => array('id'=>'5', 'type'=>'productviews', 'w'=>3, 'h'=>5),
		);

		BeautyPlus::option('dashboard_widgets', $map, 'set');

		$widget_options = array(
			1 => array(
				'pending_orders' => 'true',
				'today_total_visitors' => 'true',
				'today_orders' => 'true',
				'today_revenue' => 'true',
				'week_revenue' => 'true',
				'month_revenue' => 'true'
			),
			4 => array(
				'range' => 'online'
			)
		);

		BeautyPlus::option('dashboard_widgets_settings', $widget_options, 'set');


		// Settings options

		$options = array('mode-beautyplus-orders' => 1,
		'mode-beautyplus-products' => 1,
		'mode-beautyplus-customers' => 1,
		'mode-beautyplus-coupons' => 1,
		'mode-beautyplus-comments' => 1,
		'feature-own_themes' => 1,
		'feature-pulse' => 1
	);

	foreach ($options AS $option=>$value) {
		BeautyPlus::option( $option, $value, 'set');
	}

	// Add first message to events

	BeautyPlus_Events::add(
		array(
			'user'   => 0,
			'id'     => 0,
			'type'   => 12,
			'extra'  => serialize(array(
				'title'   => __('Welcome to BeautyPlus', 'beautyplus'),
				'message' => sprintf( wp_kses_post( __( 'If you need assistance, please visit ', 'beautyplus' ). '<a target="_blank" href="//rajthemes.com/blog/woocommerce-admin-beautyplus-overview/">'.__( 'documentation page', 'beautyplus' ) .'</a>.' ),
				BeautyPlus_Version)
			))
		)
	);


}

/**
* DB update
*
* @since  1.0.0
*/

public static function db_update() {
	global $wpdb;

	// Create the database tables
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . 'beautyplus_events';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		event_id int(11) NOT NULL AUTO_INCREMENT,
		user int(11) NOT NULL DEFAULT '0',
		type tinyint(4) NOT NULL DEFAULT '0',
		id int(11) NOT NULL,
		extra text NOT NULL,
		time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (event_id),
		KEY id  (id)
	) $charset_collate";

	dbDelta( $sql );


	$table_name = $wpdb->prefix . 'beautyplus_requests';

	$sql = "CREATE TABLE $table_name (
		request_id int(11) NOT NULL AUTO_INCREMENT,
		session_id varchar(32) DEFAULT NULL,
		year tinyint(2) DEFAULT NULL,
		month tinyint(2) DEFAULT NULL,
		week tinyint(2) DEFAULT NULL,
		day tinyint(2) DEFAULT NULL,
		date timestamp NULL DEFAULT NULL,
		time int(11) NOT NULL,
		visitor varchar(33) NOT NULL,
		type tinyint(4) NOT NULL DEFAULT '0',
		id int(11) DEFAULT NULL,
		extra text,
		ref varchar(254) NOT NULL,
		ip varchar(32) NULL,
		PRIMARY KEY  (request_id),
		KEY type (type),
		KEY month (month),
		KEY week (week)
	) $charset_collate";

	dbDelta( $sql );

	$table_name = $wpdb->prefix . 'beautyplus_daily';

	$sql = "CREATE TABLE $table_name (
		report_id int(11) NOT NULL AUTO_INCREMENT,
		type char(1) DEFAULT NULL,
		day varchar(10) DEFAULT NULL,
		visitors smallint(6) DEFAULT '0',
		sales float DEFAULT '0',
		orders mediumint(11) DEFAULT '0',
		customers mediumint(9) DEFAULT '0',
		carts mediumint(9) DEFAULT '0',
		product_pages int(11) DEFAULT '0',
		checkout mediumint(9) DEFAULT '0',
		goal float DEFAULT '0',
		updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		net_sales float DEFAULT '0',
		total_discount float DEFAULT '0',
		total_tax float DEFAULT '0',
		total_shipping float DEFAULT '0',
		total_refunds float DEFAULT '0',
		PRIMARY KEY  (report_id)
	) $charset_collate";

	dbDelta( $sql );

	add_option( 'beautyplus_db_version', '0.4' );
}
}