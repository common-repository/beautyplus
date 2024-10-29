<?php

/**
* BeautyPlus Orders
*
* Store reports
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BeautyPlus_Reports extends BeautyPlus {

	public static $store = array();
	public static $zero = '0';

	/**
	* Starts everything
	*
	* @return null
	*/

	public static function run() {

		BeautyPlus::wc_engine();

		wp_enqueue_script("funnel-graph",  BeautyPlus_Public . "3rd/funnel-graph/js/funnel-graph.js");
		wp_enqueue_script("chart",     BeautyPlus_Public . "3rd/chart.js", array(), BeautyPlus_Version);

		self::route();
	}


	/**
	* Router for sub pages
	*
	* @return null
	*/

	private static function route() {

		switch (BeautyPlus_Helpers::get('action')) {

			case 'woocommerce':
			echo BeautyPlus_View::run('reports/woocommerce',  array('report'=>BeautyPlus_Helpers::get('report', '')));
			break;

			case 'import':
			self::import();
			break;

			default:
			self::index();
			break;
		}
	}

	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return BeautyPlus_Ajax
	*/

	public static function ajax() {

		$do        = BeautyPlus_Helpers::post('do') ;

		switch ($do)
		{
			case 'import':
			self::import();
			break;
		}
	}


	/**
	* Main function
	*
	* @param  mixed $filter   array of filter
	*
	* @return BeautyPlus_View
	*/

	public static function index() {

		if (BeautyPlus_Helpers::get('graph')) {
			BeautyPlus::option('reports-graph', (string)intval(BeautyPlus_Helpers::get('graph', "2")), 'set');
		}

		// Delete transients
		// wc_delete_shop_order_transients();

		$data                 = array();
		$range                = BeautyPlus_Helpers::get('range', 'daily');
		$data['results']      = self::beautyplus_data(array('range'=>$range));

		switch ($range) {

			case 'yearly':

			$data['quick'][0] = array('title'=>esc_html__("This Year's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('now', 'Y')]['sales']));
			if (isset($data['results'][BeautyPlus_Helpers::strtotime('last year', 'Y')]['sales'])) {
				$data['quick'][1] = array('title'=>esc_html__("Last Year's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('last year', 'Y')]['sales']));
			}
			$data['quick'][2] = array('title'=>esc_html__("Average Sales", 'beautyplus'), 'text'=> wc_price(end($data['results'])['average_sales']));

			$result_key = BeautyPlus_Helpers::strtotime('now', 'Y');

			break;


			case 'monthly':

			$data['quick'] = array(
				0 => array('title'=>esc_html__("This Month's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('now', 'Ym')]['sales'])),
				1 => array('title'=>esc_html__("Last Month's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('last month', 'Ym')]['sales'])),
				2 => array('title'=>esc_html__("Average Sales", 'beautyplus'), 'text'=> wc_price(end($data['results'])['average_sales'])),
			);

			$result_key = BeautyPlus_Helpers::strtotime('now', 'Ym');

			break;


			case 'weekly':

			$data['quick'] = array(
				0 => array('title'=>esc_html__("This Week's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('now', 'YW')]['sales'])),
				1 => array('title'=>esc_html__("Last Week's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('last week', 'YW')]['sales'])),
				2 => array('title'=>esc_html__("Average Sales", 'beautyplus'), 'text'=> wc_price(end($data['results'])['average_sales'])),
			);

			$result_key = BeautyPlus_Helpers::strtotime('now', 'YW');

			break;

			case 'daily':


			$data['quick'][0] = array('title'=>esc_html__("Today's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('now', 'Ymd')]['sales']));
			if (isset($data['results'][BeautyPlus_Helpers::strtotime('yesterday', 'Ymd')]['sales'])) {
				$data['quick'][1] = array('title'=>esc_html__("Yesterday's Sales", 'beautyplus'), 'text'=> wc_price($data['results'][BeautyPlus_Helpers::strtotime('yesterday', 'Ymd')]['sales']));
			}
			$data['quick'][2] = array('title'=>esc_html__("Average Sales", 'beautyplus'), 'text'=> wc_price(end($data['results'])['average_sales']));

			$result_key = BeautyPlus_Helpers::strtotime('now', 'Ymd');

		}

		$funnel_order         = intval($data['results'][$result_key]['orders']);
		$funnel_visitors      = intval($data['results'][$result_key]['visitors']);
		$funnel_product_pages = intval($data['results'][$result_key]['product_pages']);
		$funnel_carts         = intval($data['results'][$result_key]['carts']);
		$funnel_checkout      = intval($data['results'][$result_key]['checkout']);

		if (0 === $funnel_visitors) {
			$funnel_visitors = '0.0001'; // Prevent graph error
		}

		$data['funnel']       = array($funnel_visitors, $funnel_product_pages, $funnel_carts, $funnel_checkout, $funnel_order);


		echo BeautyPlus_View::run('reports/overview',  array( 'data'=>$data ));
	}

	/**
	* Get reports data from beautyplus_daily table
	*
	* @since  1.0.0
	* @param  array     $args [description]
	*/

	public static function beautyplus_data($args = array()) {
		global $wpdb;

		switch ($args['range']) {

			case 'yearly':

			$_first_order_date = $wpdb->get_results(
				$wpdb->prepare("
				SELECT {$wpdb->prefix}posts.*
				FROM {$wpdb->prefix}posts
				WHERE post_type = %s ORDER BY post_date ASC LIMIT 1",
				'shop_order'
			), ARRAY_A );

			if (isset($_first_order_date[0])) {
				$start_date = $day_start  = BeautyPlus_Helpers::strtotime($_first_order_date[0]['post_date'], 'Y');
			} else {
				$start_date =  $day_start  = BeautyPlus_Helpers::strtotime('first day of january  00:00:00', 'Y');
			}


			$type       = 'Y';
			$label      = 'y';
			$day_end    = BeautyPlus_Helpers::strtotime('last day of december', 'Y');
			$goal       = BeautyPlus::option('feature-goals-yearly',0);


			break;

			case 'monthly':

			$type       = 'M';
			$label      = 'm';
			$start_date = BeautyPlus_Helpers::strtotime('first day of january');
			$day_start  = BeautyPlus_Helpers::strtotime('first day of january', 'Ym');
			$day_end    = BeautyPlus_Helpers::strtotime('now', 'Ym');
			$goal       = BeautyPlus::option('feature-goals-monthly',0);


			break;

			case 'weekly':

			$type       = 'W';
			$label      = 'W';
			$start_date = BeautyPlus_Helpers::strtotime('first day of january');

			$day_start  = BeautyPlus_Helpers::strtotime('first day of january', 'YW');
			$day_end    = BeautyPlus_Helpers::strtotime('now', 'YW');
			$goal       = BeautyPlus::option('feature-goals-weekly',0);

			break;

			case 'daily':

			$type       = 'D';
			$label      = 'd l';
			$start_date = BeautyPlus_Helpers::strtotime('first day of this month');
			$day_start  = BeautyPlus_Helpers::strtotime('first day of this month', 'Ymd');
			$day_end    = BeautyPlus_Helpers::strtotime('now', 'Ymd');
			$goal       = BeautyPlus::option('feature-goals-daily',0);

			break;
		}

		$_result = $wpdb->get_results(
			$wpdb->prepare("
			SELECT {$wpdb->prefix}beautyplus_daily.*
			FROM {$wpdb->prefix}beautyplus_daily
			WHERE type = %s AND day >= %s ORDER BY day ASC",
			$type, $start_date
		), ARRAY_A
	);

	foreach ($_result AS $r) {
		$result[$r['day']]          = $r;
		$result[$r['day']]['day']   = $r['day'];
		$result[$r['day']]['label'] = strtoupper(date_i18n($label,  strtotime($r['day'])));
	}


	$average_sales = 0;
	$prev          = 0;
	$graph         = 2;
	$i             = 0;


	if ("1" === BeautyPlus::option('reports-graph', "2")) {
		$graph = "1";
	}

	for ($x = $day_start; $x <= $day_end; ++$x) {

		++$i;

		if ('weekly' === $args['range']) {
			$day2 = date_i18n("d M", strtotime(date('Y')."-01-00 + ".(($i)*7)." days ")-(24*60*60));
		} else 	if ('daily' === $args['range']) {

			if ("1" === $graph) {
				$day2 =	date_i18n('d',  strtotime($x));
			} else {
				$day2 =	date_i18n('d D',  strtotime($x));
			}

		} else 	if ('monthly' === $args['range']) {
			$day2 =	date_i18n('F',  strtotime($x."01"));
		} else 	if ('yearly' === $args['range']) {
			$day2 =	date_i18n('Y',  strtotime($x."-01-01"));
		}



		if (!isset($result[$x])) {
			$results[$x] = array('day' => $x,
			'average_sales'            => ($average_sales/$i),
			'goal'                     => $goal,
			'customers'                => 0,
			'carts'                    => 0,
			'checkout'                 => 0,
			'product_pages'            => 0,
			'orders'                   => 0,
			'net_sales'                => 0,
			'total_refunds'            => 0,
			'total_shipping'           => 0,
			'total_tax'                => 0,
			'total_discount'           => 0,
			'sales'                    => static::$zero,
			'visitors'                 => static::$zero,
			'label'                    => strtoupper($day2),
			'prev'                     => 0
		);
	} else {
		$average_sales               += $result[$x]['sales'];
		$result[$x]['label']          = strtoupper($day2);
		$result[$x]['average_sales']  = $average_sales/$i;
		$result[$x]['prev']           = $prev;
		$results[$x]                  = $result[$x];
	}
	if (isset($result[$x]['sales'])) {
		$prev = $result[$x]['sales'];
	}

}
$results = self::beautyplus_data_today($args['range'], $results);

return $results;
}

/**
* Get live reports which are not saved to database yet
*
* @since  1.0.0
*/

public static function beautyplus_data_today($range, $results) {

	global $wpdb;

	if ('daily' === $range) {

		$key       = date('Ymd', current_time('timestamp'));
		$day_start = BeautyPlus_Helpers::strtotime('now', 'Y-m-d 00:00:00');
		$day_end   = BeautyPlus_Helpers::strtotime('now', 'Y-m-d H:i:s');

	}  else if ('weekly' === $range) {

		$key                    = date('YW', current_time('timestamp'));
		$results[$key]['label'] = strtoupper(BeautyPlus_Helpers::strtotime('now', 'd M'));
		$day_start              = BeautyPlus_Helpers::strtotime('monday this week', 'Y-m-d 00:00:00');
		$day_end                = BeautyPlus_Helpers::strtotime('now', 'Y-m-d H:i:s');

	}else if ('monthly' === $range) {

		$key       = date('Ym', current_time('timestamp'));
		$day_start = BeautyPlus_Helpers::strtotime('first day of this month', 'Y-m-d 00:00:00');
		$day_end   = BeautyPlus_Helpers::strtotime('now', 'Y-m-d H:i:s');

	}else if ('yearly' === $range) {

		$key       = date('Y', current_time('timestamp'));
		$day_start = BeautyPlus_Helpers::strtotime('first day of january', 'Y-m-d 00:00:00');
		$day_end   = BeautyPlus_Helpers::strtotime('last day of december', 'Y-m-d 00:00:00');

	}


	$_visitors = $wpdb->get_results(
		$wpdb->prepare("
		SELECT type, count(distinct session_id) as count
		FROM {$wpdb->prefix}beautyplus_requests
		WHERE date >= %s AND date <= %s
		GROUP By type",
		$day_start, $day_end
	),
	ARRAY_A
);

$_visitors_all = $wpdb->get_var(
	$wpdb->prepare("
	SELECT  count(distinct session_id) as count
	FROM {$wpdb->prefix}beautyplus_requests
	WHERE date >= %s AND date <= %s",
	$day_start, $day_end
	)
);

$results[$key]['visitors'] = $_visitors_all;

foreach ($_visitors AS $value) {

	if ("1" === $value['type']) {
		if (!isset($results[$key]['product_pages'])) {
			$results[$key]['product_pages'] = 0;
		}

		$results[$key]['product_pages'] += $value['count'];

	}

	if ("4" === $value['type']) {
		if (!isset($results[$key]['carts'])) {
			$results[$key]['carts'] = 0;
		}

		$results[$key]['carts'] += $value['count'];
	}

	if ("6" === $value['type']) {
		if (!isset($results[$key]['checkout'])) {
			$results[$key]['checkout'] = 0;
		}
		$results[$key]['checkout'] += $value['count'];
	}

}

$_sales =  WC()->api->WC_API_Reports->get_sales_report(null, array('date_min' => date('Y-m-d', strtotime($day_start)), 'date_max' =>  date('Y-m-d',strtotime($day_end))));

if ($_sales) {

	$results[$key]['sales']          += $_sales['sales']['total_sales'];
	$results[$key]['orders']         += $_sales['sales']['total_orders'];
	$results[$key]['customers']      += $_sales['sales']['total_customers'];
	$results[$key]['total_discount'] += $_sales['sales']['total_discount'];
	$results[$key]['total_tax']      += $_sales['sales']['total_tax'];
	$results[$key]['total_shipping'] += $_sales['sales']['total_shipping'];
	$results[$key]['total_refunds']  += $_sales['sales']['total_refunds'];
	$results[$key]['net_sales']      += $_sales['sales']['net_sales'];
}

return $results;
}


/**
* Daily cron for insert stats to beautyplus_daily table
*
* @since  1.0.0
*/

public static function cron_daily($args = array()) {
	global $wpdb;

	BeautyPlus::wc_engine();

	include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
	include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

	foreach (array('D', 'W', 'M') AS $type) {

		$report = new WC_Report_Sales_By_Date();

		$_GET['start_date'] = null;
		$_GET['end_date'] = null;

		if ('D' === $type){
			$_GET['start_date'] = BeautyPlus_Helpers::strtotime('yesterday', 'Y-m-d');
			$_GET['end_date']   = BeautyPlus_Helpers::strtotime('yesterday', 'Y-m-d');
		}

		if ('W' === $type && "1" === BeautyPlus_Helpers::strtotime('now', 'N')) {
			$_GET['start_date'] = BeautyPlus_Helpers::strtotime('now - 7 days', 'Y-m-d');
			$_GET['end_date']   = BeautyPlus_Helpers::strtotime('yesterday', 'Y-m-d');
		}

		if ('M' === $type && "1" === BeautyPlus_Helpers::strtotime('now','j')) {
			$_GET['start_date'] = BeautyPlus_Helpers::strtotime('first day of last month', 'Y-m-d');
			$_GET['end_date']   = BeautyPlus_Helpers::strtotime('first day of this month', 'Y-m-d');
		}

		if (!isset($_GET['start_date'])) {
			continue;
		}

		$report_data     = $report->calculate_current_range( 'custom' );

		if (is_wp_error($report_data)) {
			return;
		}

		switch ($type) {
			case 'D':

			$day             = BeautyPlus_Helpers::strtotime('yesterday', 'Ymd');
			$report_data     = $report->get_report_data();
			$result['goal']  = floatval(BeautyPlus::option('feature-goals-daily', 0));
			$strtotime_start = BeautyPlus_Helpers::strtotime('yesterday', "Y-m-d 00:00:00");
			$strtotime_end   = BeautyPlus_Helpers::strtotime('today', "Y-m-d 00:00:00");
			break;

			case 'W':

			$day             = BeautyPlus_Helpers::strtotime('monday last week', 'YW');
			$report_data     = $report->get_report_data();
			$result['goal']  = floatval(BeautyPlus::option('feature-goals-weekly', 0));
			$strtotime_start = BeautyPlus_Helpers::strtotime('monday last week', "Y-m-d 00:00:00");
			$strtotime_end   = BeautyPlus_Helpers::strtotime('monday this week', 'Y-m-d 00:00:00');

			break;

			case 'M':

			$day             = BeautyPlus_Helpers::strtotime('first day of last month', 'Ym');
			$report_data     = $report->get_report_data();
			$result['goal']  = floatval(BeautyPlus::option('feature-goals-monthly', 0));
			$strtotime_start = BeautyPlus_Helpers::strtotime('first day of last month', "Y-m-d 00:00:00");
			$strtotime_end   = BeautyPlus_Helpers::strtotime('first day of this month', "Y-m-d 00:00:00");

			break;

		}

		$result['sales']          = floatval($report_data->total_sales);
		$result['orders']         = intval  ($report_data->total_orders);
		$result['customers']      = intval  ($report_data->total_customers);
		$result['net_sales']      = floatval($report_data->net_sales);
		$result['total_discount'] = floatval($report_data->total_coupons);
		$result['total_tax']      = floatval($report_data->total_tax);
		$result['total_shipping'] = floatval($report_data->total_shipping);
		$result['total_refunds']  = floatval($report_data->total_refunds);


		// Visitors
		$visitors = $wpdb->get_var(
			$wpdb->prepare("
			SELECT count(distinct session_id) as counts
			FROM {$wpdb->prefix}beautyplus_requests
			WHERE date >= %s AND date <= %s",
			$strtotime_start,$strtotime_end
			)
		);


		$result['visitors'] = intval($visitors);

		$insert = $wpdb->insert( $wpdb->prefix."beautyplus_daily",
		array(
			'type'           => $type,
			'day'            => $day,
			'visitors'       => $result['visitors'],
			'sales'          => $result['sales'],
			'orders'         => $result['orders'],
			'customers'      => intval($result['customers']),
			'goal'           => $result['goal'],
			'net_sales'      => $result['net_sales'],
			'total_discount' => $result['total_discount'],
			'total_tax'      => $result['total_tax'],
			'total_shipping' => $result['total_shipping'],
			'total_refunds'  => $result['total_refunds'],
			'updated_at'     => current_time('mysql')
		),
		array('%s', '%s','%f', '%d', '%d', '%f', '%f', '%f', '%f', '%f', '%f', '%s')
	);
}
}

/**
* Import old reports to beautyplus_daily table
*
* @since  1.1.0
*/

public static function import($args = array()) {
	global $wpdb;

	BeautyPlus::wc_engine();

	if ('import-date' === BeautyPlus_Helpers::post('sub')) {

		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$first = BeautyPlus_Helpers::strtotime('today', 'Y-m-01');
		$date = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y-m-01');

		if (strtotime($date) < strtotime($first)) {
			BeautyPlus_Ajax::success(array('type' => 'import-week', 'date' => BeautyPlus_Helpers::strtotime("today", "Y-m-d"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("$date - 1 month", "Y-m-01").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#2 imported successfully</div>'));
			return;
		}

		for ($i = 1; $i<date('d'); ++$i) {
			$i = sprintf("%02d", $i);
			$report = new WC_Report_Sales_By_Date();

			$_GET['start_date'] = BeautyPlus_Helpers::strtotime($date, "Y-m-$i");
			$_GET['end_date']   = BeautyPlus_Helpers::strtotime($date, "Y-m-$i");

			$report_data     = $report->calculate_current_range( 'custom' );

			if (is_wp_error($report_data)) {
				return;
			}

			$report_data     = $report->get_report_data();

			if (date("Ym$i") <> date("Ymd")) {
				self::import_db('D', date("Ym$i"), $report_data);
			}

		}

		BeautyPlus_Ajax::success(array('type' => 'import-date', 'date' => BeautyPlus_Helpers::strtotime("$date - 1 month", "Y-m-01"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("$date - 1 month", "Y-m-01").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#1 imported successfully</div>'));


	} else if ('import-week' === BeautyPlus_Helpers::post('sub')) {
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$first = BeautyPlus_Helpers::strtotime('today', 'Y-01-01');
		$date = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y-m-d');

		$week = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'W');
		$year = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y');

		if (strtotime($date) < strtotime($first)) {
			BeautyPlus_Ajax::success(array('type' => 'import-month', 'date' => BeautyPlus_Helpers::strtotime('today', "Y-m-d"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("today", "Y-m-d").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#' . BeautyPlus_Helpers::strtotime($date, "m"). ' imported successfully</div>'));
			return;
		}

		$week_start =  date('Y-m-d', strtotime("$year-W$week-1"));
		$week_end =  date('Y-m-d', strtotime("$year-W$week-7"));

		$report = new WC_Report_Sales_By_Date();

		$_GET['start_date'] = $week_start;
		$_GET['end_date']   = $week_end;

		$report_data     = $report->calculate_current_range( 'custom' );

		if (is_wp_error($report_data)) {
			return;
		}

		$report_data     = $report->get_report_data();

		if ("$year$week" <> date("YW")) {
			self::import_db('W',  $year.''.$week, $report_data);
		}

		BeautyPlus_Ajax::success(array('type' => 'import-week', 'date' => BeautyPlus_Helpers::strtotime("$week_start - 3 day", "Y-m-d"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("$week_start - 3 day", "Y-m-d").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#' . BeautyPlus_Helpers::strtotime($date, "W"). ' imported successfully</div>'));
	} else if ('import-month' === BeautyPlus_Helpers::post('sub')) {

		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$first = BeautyPlus_Helpers::strtotime('today', 'Y-01-01');
		$date = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y-m-d');

		$lastday = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 't');
		$month = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'm');
		$year = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y');

		if (strtotime($date) < strtotime($first)) {
			BeautyPlus_Ajax::success(array('type' => 'import-year', 'date' => BeautyPlus_Helpers::strtotime($date, "Y-m-d"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("today", "Y-m-d").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#' . BeautyPlus_Helpers::strtotime($date, "m"). ' imported successfully</div>'));
			return;
		}

		$month_start =  date('Y-m-d', strtotime("$year-$month-01"));
		$month_end =  date('Y-m-d', strtotime("$year-$month-$lastday"));

		$report = new WC_Report_Sales_By_Date();

		$_GET['start_date'] = $month_start;
		$_GET['end_date']   = $month_end;

		$report_data     = $report->calculate_current_range( 'custom' );

		if (is_wp_error($report_data)) {
			return;
		}

		$report_data     = $report->get_report_data();

		if ("$year$month" <> date("Ym")) {
			self::import_db('M',  $year.''.$month, $report_data);
		}

		BeautyPlus_Ajax::success(array('type' => 'import-month', 'date' => BeautyPlus_Helpers::strtotime("$month_start - 10 day", "Y-m-d"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("$month_start - 10 day", "Y-m-d").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#' . BeautyPlus_Helpers::strtotime($date, "m"). ' imported successfully</div>'));
	} else if ('import-year' === BeautyPlus_Helpers::post('sub')) {

		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
		include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$first = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('first'), 'Y-m-01');
		$date = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y-m-d');

		$lastday = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 't');
		$month = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'm');
		$year = BeautyPlus_Helpers::strtotime(BeautyPlus_Helpers::post('range'), 'Y');

		if (strtotime($date) < strtotime($first)) {
			BeautyPlus_Ajax::success(array('type' => 'import-ok', 'date' => "-1", 'det'=> '<div class="__A__x __A__-1"><span class="dashicons dashicons-yes-alt text-success"></span><br>Completed!</div>'));
			return;
		}

		$month_start =  date('Y-m-d', strtotime("$year-01-01"));
		$month_end =  date('Y-m-d', strtotime("$year-12-$lastday"));

		$report = new WC_Report_Sales_By_Date();

		$_GET['start_date'] = $month_start;
		$_GET['end_date']   = $month_end;

		$report_data     = $report->calculate_current_range( 'custom' );

		if (is_wp_error($report_data)) {
			return;
		}

		$report_data     = $report->get_report_data();

		if ($year <> date("Y")) {
			self::import_db('Y',  $year, $report_data);
		}

		BeautyPlus_Ajax::success(array('type' => 'import-year', 'date' => BeautyPlus_Helpers::strtotime("$date - 1 year", "Y-m-d"), 'det'=> '<div class="__A__x __A__'.BeautyPlus_Helpers::strtotime("$date - 1 year", "Y-m-d").'"><span class="dashicons dashicons-yes-alt text-success"></span><br>#' . BeautyPlus_Helpers::strtotime($date, "m"). ' imported successfully</div>'));
	}
	else {
		$_first_order_date = $wpdb->get_results(
			$wpdb->prepare("
			SELECT {$wpdb->prefix}posts.*
			FROM {$wpdb->prefix}posts
			WHERE post_type = %s ORDER BY post_date ASC LIMIT 1",
			'shop_order'
		), ARRAY_A );

		if (isset($_first_order_date[0])) {
			$first_order_date = BeautyPlus_Helpers::strtotime($_first_order_date[0]['post_date'], 'Y-m-d');
		}

		echo BeautyPlus_View::run('reports/import',  array('first_order_date' => $first_order_date));
	}
}

/**
* Rebuild beautyplus_daily table
*
* @since  1.1.0
*/

public static function import_db($type, $day, $report_data) {
	global $wpdb;

	$result = array();

	$result['sales']          = floatval($report_data->total_sales);
	$result['orders']         = intval  ($report_data->total_orders);
	$result['customers']      = intval  ($report_data->total_customers);
	$result['net_sales']      = floatval($report_data->net_sales);
	$result['total_discount'] = floatval($report_data->total_coupons);
	$result['total_tax']      = floatval($report_data->total_tax);
	$result['total_shipping'] = floatval($report_data->total_shipping);
	$result['total_refunds']  = floatval($report_data->total_refunds);


	switch ($type) {
		case 'D':
		$goal = BeautyPlus::option('feature-goals-daily',0);
		break;
		case 'W':
		$goal = BeautyPlus::option('feature-goals-weekly',0);
		break;
		case 'M':
		$goal = BeautyPlus::option('feature-goals-monthly',0);
		break;
		case 'Y':
		$goal = BeautyPlus::option('feature-goals-yearly',0);
		break;
	}

	$data = array(
		'type'           => $type,
		'day'            => $day,
		'sales'          => $result['sales'],
		'orders'         => $result['orders'],
		'customers'      => intval($result['customers']),
		'goal'           => $goal,
		'net_sales'      => $result['net_sales'],
		'total_discount' => $result['total_discount'],
		'total_tax'      => $result['total_tax'],
		'total_shipping' => $result['total_shipping'],
		'total_refunds'  => $result['total_refunds'],
		'updated_at'     => current_time('mysql')
	);

	$check = $wpdb->get_var(
		$wpdb->prepare(" SELECT report_id FROM {$wpdb->prefix}beautyplus_daily WHERE type = %s AND day = %s",
		$type, $day) );


		if ($check && intval($check)>0) {
			$wpdb->update( $wpdb->prefix."beautyplus_daily",
			$data,
			array('report_id'=>$check) );
		} else {

			$wpdb->insert( $wpdb->prefix."beautyplus_daily",
			$data,
			array('%s', '%s','%f', '%d', '%d', '%f', '%f', '%f', '%f', '%f', '%f', '%s') );
		}
	}
}

?>