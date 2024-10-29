<?php

/**
* BeautyPlus Dashboard
*
* Dashboard
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BeautyPlus_Dashboard extends BeautyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		wp_enqueue_script("hammer",    BeautyPlus_Public . "3rd/hammer.js", array(), BeautyPlus_Version);
		wp_enqueue_script("muuri",     BeautyPlus_Public . "3rd/muuri.js", array('hammer'), BeautyPlus_Version);
		wp_enqueue_script("gauge",     BeautyPlus_Public . "3rd/gauge/gauge.js", array(), BeautyPlus_Version);
		wp_enqueue_script("chart",     BeautyPlus_Public . "3rd/chart.js", array(), BeautyPlus_Version);
		wp_enqueue_script("beautyplus-dashboard", BeautyPlus_Public . "js/beautyplus-dashboard.js", array('muuri'), BeautyPlus_Version);


		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route()	{

		$mode = BeautyPlus::option('dashboard-type', 'default', 'get', true);

		switch (BeautyPlus_Helpers::get('action', $mode)) {
			case 'widget_list':
			self::widget_list();
			break;

			case 'wc-admin':
			BeautyPlus::option('dashboard-type', 'wc-admin', 'set', true);
			echo BeautyPlus_View::run('dashboard/dashboard-wc-admin', array());
			break;

			case 'default':
			default:
			BeautyPlus::option('dashboard-type', '0', 'set', true);
			self::index();
			break;
		}
	}

	/**
	* Main function
	*
	* @return void
	*/

	public static function index()	{

		$widgets  = array();

		$map      = BeautyPlus::option('dashboard_widgets', array());
		$settings = BeautyPlus::option('dashboard_widgets_settings', array());

		echo BeautyPlus_View::run('dashboard/dashboard',  array( 'map' => $map, 'settings' => $settings ) );

	}

	/**
	* Widget listsadded to dashboard
	*
	* @return void
	*/

	public static function widget_list() {

		$available_widgets = BeautyPlus_Widgets::available_widgets();

		$installed         = array();
		$others            = array();

		$map               = BeautyPlus::option('dashboard_widgets', array());

		foreach ($map AS $_map)	{
			if (isset($available_widgets[$_map['type']])) {

				// $class= "Widgets__" . sanitize_key($_map['type']);

				$installed[] = array(
					'id'          => $_map['id'],
					'type'        => $_map['type'],
					'title'       => $available_widgets[$_map['type']]['title'],
					'description' => $available_widgets[$_map['type']]['description'],
					'multiple'    => $available_widgets[$_map['type']]['multiple'],
				);
			}
		}

		wp_enqueue_script("beautyplus-dashboard",  BeautyPlus_Public . "js/beautyplus-dashboard.js", array(), BeautyPlus_Version);

		echo BeautyPlus_View::run('dashboard/widget-list',  array( 'installed' => $installed, 'all' => $available_widgets ) );

	}

	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return BeautyPlus_Ajax
	*/

	public static function ajax() {

		$do = BeautyPlus_Helpers::post('do');
		$id = sanitize_key(BeautyPlus_Helpers::post('id'));

		switch ($do)	{

			/* Adding new widget */
			case 'add-widget':

			$available_widgets = BeautyPlus_Widgets::available_widgets();

			if (isset($available_widgets[$id])) {

				$uniq = uniqid();

				$map  = BeautyPlus::option('dashboard_widgets', array());

				if ($available_widgets[$id]['multiple'] === FALSE && array_search($id, array_column($map, 'type')) !== FALSE) {
					BeautyPlus_Ajax::error('This widget not allowed mutliple instance');
				}

				$map[$uniq]['id']   = $uniq;
				$map[$uniq]['type'] = sanitize_key($available_widgets[$id]['id']);
				$map[$uniq]['w']    = $available_widgets[$id]['w'];
				$map[$uniq]['h']    = $available_widgets[$id]['h'];


				BeautyPlus::option("dashboard_widgets", $map, 'set');
				BeautyPlus_Ajax::success('OK');

			}

			break;

			/* Deletes widget from dashboard */
			case 'delete-widget':

			$map = BeautyPlus::option('dashboard_widgets', array());

			if (isset($map[$id])) {
				unset($map[$id]);
				BeautyPlus::option("dashboard_widgets", $map, 'set');
				BeautyPlus_Ajax::success('OK');
			}
			break;
		}
	}
}

?>