<?php
/*
Plugin Name: BeautyPlus
Plugin URI:  https://rajthemes.com/
Description: BeautyPlus is a beautiful admin panel for WooCommerce & WordPress. It gives you morden UI for WordPress & WooCommerce.
Author:      rajthemes
Author URI:  https://rajthemes.com/
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Version:     1.0
Text Domain: beautyplus
Domain Path: /lang/
*/

// If this file is called directly, abort.
defined( 'ABSPATH' ) or wp_die();

/**
* Current plugin version and paths.
*/
	if (!defined( 'BeautyPlus_Version' )) {
		define( 'BeautyPlus_Version',   '1.1.8' );
	}

	if ( !defined( 'BeautyPlus_Public' ) ) {
		define( 'BeautyPlus_Public', plugin_dir_url( __FILE__ ) . 'public/' );
	}

	if ( !defined( 'BeautyPlus_Framework' ) ) {
		define( 'BeautyPlus_Framework', plugin_dir_path( __FILE__ ) . 'framework/' );
	}

	if ( !defined( 'BeautyPlus_Dir' ) ) {
		define( 'BeautyPlus_Dir', plugin_dir_path( __FILE__ ) );
	}

	/**
	* Activate BeautyPlus
	*
	* @since  1.0
	* @return void
	*/

	function activate_beautyplus() {
		require_once plugin_dir_path( __FILE__ ) . 'framework/libs/core/beautyplus-activator.php';
		BeautyPlus_Activator::activate();
	}

	/**
	* Deactivate BeautyPlus
	*
	* @since  1.0
	* @return void
	*/
	function deactivate_beautyplus() {
		require_once plugin_dir_path( __FILE__ ) . 'framework/libs/core/beautyplus-deactivator.php';
		BeautyPlus_Deactivator::deactivate();
	}

	/**
	* Activation/Deactivation hooks
	*/
	register_activation_hook( __FILE__, 'activate_beautyplus' );
	register_deactivation_hook( __FILE__, 'deactivate_beautyplus' );


	/**
	* Safe Mode for BeautyPlus
	*/
	add_action('init', 'beautyplus_init');
	function beautyplus_init() {


		if ( (isset($_GET['page']) && 'beautyplus' === $_GET['page']) && (isset($_GET['segment']) && 'safe-mode' === $_GET['segment']) ) {

			if ((isset($_GET['page']) && 'beautyplus' === $_GET['page'] && 'on' === $_GET['action']) ) {

				if (wp_verify_nonce($_REQUEST['_wpnonce'], 'beautyplus-safe-mode')) {
					$plugins = array(
						'beauty-plus/beauty-plus.php'
					);

					require_once(ABSPATH . 'wp-admin/includes/plugin.php');

					deactivate_plugins($plugins);

					wp_die(sprintf(
						__('<h1>BeautyPlus is deactivated</h1><br><br><a href="%s">Return to Wordpress Admin</a>', 'beautyplus'), admin_url('plugins.php')
					));
				}
			}

			$nonce = wp_create_nonce('beautyplus-safe-mode');

			wp_die(sprintf(
				__('<h1>BeautyPlus Safe Mode</h1><br>If BeautyPlus is not working properly and causes errors on your site, you can temporarily deactivate BeautyPlus.<br><br><a href="%s">Deactivate BeautyPlus now</a>', 'beautyplus'), admin_url('admin.php?page=beautyplus&segment=safe-mode&action=on&_wpnonce='.$nonce)
			));
		}
	}

	/**
	* SPL Autoload
	*
	* @since    1.0.0
	*/
	spl_autoload_register(function ($class) {

		if( class_exists( $class )) return;

		$file = str_replace("_","-", strtolower($class)) . '.php';
		$file = sanitize_file_name($file);
		$file = BeautyPlus_Framework. 'controller/' . $file;

		if (file_exists($file)) {
			require $file;
		} else {

			$file = str_replace(array( "__", "_"), array("/", "-"), strtolower($class)) . '.php';
			$file = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $file));
			$file = BeautyPlus_Framework.  'libs/' . $file;
			if (file_exists($file)) {
				require $file;
			}
		}
	}
);

/**
* Let's start.
*
* @since    1.0.0
*/
add_action( 'plugins_loaded', 'beautyplus_plugins_loaded' );

function beautyplus_plugins_loaded() {
	if (class_exists('WooCommerce', false)) {
		require BeautyPlus_Framework. 'controller/beautyplus.php';

		(new BeautyPlus())->start();
	}
}