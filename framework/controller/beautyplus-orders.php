<?php

/**
* BeautyPlus Orders
*
* Order management
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class BeautyPlus_Orders extends BeautyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		BeautyPlus::wc_engine();

		wp_enqueue_script("beautyplus-orders",  BeautyPlus_Public . "js/beautyplus-orders.js", array(), BeautyPlus_Version);

		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route() {
		switch (BeautyPlus_Helpers::get('action')) {
			default:
			self::index();
			break;
		}
	}


	/**
	* Prepare filter array for query
	*
	* @param  mixed $filter   array of filter or false
	* @return array           new filter array
	*/

	public static function filter($filter) {

		if (!$filter) {
			$filter['page']        = 1;
			$filter['limit']       = 10;
			$filter['post_status'] = array_keys( wc_get_order_statuses() );
		}

		if ($status = BeautyPlus_Helpers::get('status', null)) {
			if ('trash' === $status) {
				$filter['post_status'] = 'trash';
			} else {
				if (in_array('wc-' . $status,  array_keys( wc_get_order_statuses() ))) {
					$filter['post_status'] = "wc-". $status;
				}
				if (in_array( $status,  array_keys( wc_get_order_statuses() ))) {
					$filter['post_status'] =  $status;
				}
			}
		}


		if (BeautyPlus_Helpers::get('s', null)) {
			$filter['q'] = BeautyPlus_Helpers::get('s', '');
		}

		if (BeautyPlus_Helpers::get('go', null)) {
			$filter['mode'] = 95;
		}

		if (BeautyPlus_Helpers::get('pg', null)) {
			$filter['page'] = intval( BeautyPlus_Helpers::get( 'pg', 0 ));
		}


		if ($customer = BeautyPlus_Helpers::get('customer')) {
			$filter['meta_query'] = array(
				array(
					'key'     => '_customer_user',
					'value'   => absint( $customer ),
					'compare' => '=',
				)
			);
		}

		if (BeautyPlus_Helpers::get('orderby')) {
			if (false !== strpos(BeautyPlus_Helpers::get('orderby',''), 'meta_')) {
				$filter['orderby']  = "meta_value_num";
				$filter['meta_key'] = sanitize_sql_orderby(str_replace ( 'meta_', '', BeautyPlus_Helpers::get('orderby','')));
			} else {
				$filter['orderby'] =  sanitize_sql_orderby(BeautyPlus_Helpers::get('orderby',''));
			}

			$filter['order'] 	= 'ASC' === BeautyPlus_Helpers::get('order','ASC') ? 'ASC' : 'DESC';
		}

		return $filter;
	}

	/**
	* Main function
	*
	* @param  mixed $filter   array of filter
	*
	* @return null
	*/

	public static function index($filter = array()) {

		$filter = self::filter($filter);

		$list   = array();

		$list['statuses'] =  WC()->api->WC_API_Orders->get_order_statuses()['order_statuses'];

		foreach ($list['statuses'] AS $list_status_k => $list_status_k ) {
			$list['statuses_count'][$list_status_k] =  WC()->api->WC_API_Orders->get_orders_count( $list_status_k ) ['count'];
		}

		$list['statuses_count']["count"] = WC()->api->WC_API_Orders->get_orders_count() ['count'];
		$list['statuses_count']['trash'] = wp_count_posts('shop_order')->trash;

		$orders['orders'] =  self::get_orders( $filter['post_status'], $filter,  $filter['page'])['result'];

		switch ( $mode = ( !empty($filter['mode']) ? absint($filter['mode']) : BeautyPlus::option('mode-beautyplus-orders', 1) ) )	{

			// Standart
			case 1:
			case 98:
			$orders['orders'] = self::_group_by($orders['orders'], 'created_at');
			echo BeautyPlus_View::run('orders/list-' . $mode,  array( 'orders' => $orders['orders'], 'list' => $list,  'ajax' =>   BeautyPlus_Helpers::is_ajax()  ));
			break;

			case 2:
			echo BeautyPlus_View::run('orders/list-2',  array( 'orders' => array('all' => array('orders'=>$orders['orders'])), 'list' => $list, 'ajax' =>   BeautyPlus_Helpers::is_ajax()  ));
			break;

			case 97:
			return BeautyPlus_View::run('orders/list-2',  array( 'orders' => array('all' => array('orders'=>$orders['orders'])), 'list' => $list,  'ajax' =>   1  ));
			break;

			// Woocommerce Native
			case 99:
			if (!BeautyPlus_Admin::is_full()) {
				BeautyPlus_Helpers::frame( admin_url( 'edit.php?post_type=shop_order' ) );
			} else {
				wp_redirect(  admin_url( 'edit.php?post_type=shop_order' ) );
			}
			break;

			// Other menus
			case 95:
			echo BeautyPlus_View::run('orders/list-95', array('list'=>$list, 'iframe_url' => BeautyPlus_Helpers::get_submenu_url(BeautyPlus_Helpers::get('go')) ));
			break;
		}
	}

	/**
	* Group titles by date
	*
	* @since  1.0.0
	* @param  array    $array
	* @param  string    $key
	* @return array
	*/

	private static function _group_by($array, $key) {

		$return = array();

		foreach($array as $val) {
			$time = BeautyPlus_Helpers::grouped_time( $val['date_created'] );

			$return[$time['key']]['title']    = $time['title'];
			$return[$time['key']]['orders'][] = $val;
		}
		return $return;
	}


	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return BeautyPlus_Ajax
	*/

	public static function ajax() {
		global $woocommerce;

		BeautyPlus::wc_engine();

		BeautyPlus_Helpers::ajax_nonce(TRUE);

		$do = BeautyPlus_Helpers::post('do', 'default');

		switch ($do){

			case "filter":
			$fields = $_POST['fields'];

			$filter = array();

			foreach ($fields AS $key => $field) {

				$field['value'] = sanitize_key($field['value']);

				if ('order_id' === $field['name'] && trim($field['value']) !== '') {
					$filter['post__in'] = array(BeautyPlus_Helpers::clean($field['value']));
				}

				if ('status' === $field['name'] && trim($field['value']) !== '') {
					if (in_array($field['value'], array('pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed'))) {
						$filter['post_status'] = "wc-" . BeautyPlus_Helpers::clean($field['value']);
					}
				}

				if ('customer' === $field['name'] && trim($field['value']) !== '') {
					$filter['meta_query'] = array(
						array(
							'key'     => '_customer_user',
							'value'   => absint( BeautyPlus_Helpers::clean($field['value']) ),
							'compare' => '=',
						),
					);
				}
			}

			echo self::index($filter);
			wp_die();
			break;

			// Search
			case 'search':

			$status = BeautyPlus_Helpers::post('extra', '');

			if (in_array('wc-' . $status, array_keys( wc_get_order_statuses() ))) {
				$filter['post_status'] = "wc-" .$status;
			} else {
				$filter['post_status'] = array_keys( wc_get_order_statuses() );
			}

			$filter['search'] = BeautyPlus_Helpers::post('q', '');

			$filter['mode'] = (BeautyPlus_Helpers::post('mode') ? absint(BeautyPlus_Helpers::post('mode')) : null) ;
			$filter['page'] = 1;

			echo self::index($filter);
			wp_die();
			break;

			// Delete or restrore order
			case 'deleteforever':
				case 'restore':

				$id = absint(BeautyPlus_Helpers::post('id', 0));

				$order = new WC_Order($id);

				if (!$order) {
					BeautyPlus_Ajax::error(esc_html__('Order is not exists', 'beautyplus'));
					wp_die();
				}

				if ('deleteforever' === $do) {
					$change= wp_delete_post( $id, true );
				} else {
					$change= wp_untrash_post( $id );

				}

				if (!$change) {
					BeautyPlus_Ajax::error(esc_html__('Order can not be restore', 'beautyplus'));
				} else {
					BeautyPlus_Ajax::success('OK', array('id'=>$id, 'message'=>esc_html__('Order has been restored', 'beautyplus')));
				}

				break;

				// Change status of order
				case 'changestatus':

				$result = array();
				$status = BeautyPlus_Helpers::post('status') ;
				$ids    = wp_parse_id_list(BeautyPlus_Helpers::post('id', array())) ;

				if (!is_array( $ids ))	{
					wp_die ( -1 );
				}

				if (!in_array("wc-".$status, array_keys(wc_get_order_statuses())) && !in_array($status, array_keys(wc_get_order_statuses())) && !in_array($status, array('all', 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed', 'trash', 'restore', 'deleteforever'))) {
					wp_die ( -2 );
				}

				$ids = array_map('absint', $ids);

				foreach ($ids AS $id) {

					$change = false;
					$order = new WC_Order(absint($id));

					if ($order) {

						if ('trash' === $status) {
							$change= wp_trash_post( $id );
						} else if ('deleteforever' === $status) {
							$change= wp_delete_post( $id, true );
						} else if ('restore' === $status) {
							$change= wp_untrash_post( $id );
						} else {
							$change = $order->update_status($status);
							do_action( 'woocommerce_update_order', $order->get_id() );
							do_action( 'woocommerce_order_status_changed', $order->get_id(), $order->get_status(), $status, $order );

						}

						wc_delete_shop_order_transients( absint($id) );

						BeautyPlus_Events::save_post_shop_order($id);

					}

					if ($change) {
						$result['success'][] = $id;
					} else {
						$result['errors'][] = $id;
					}
				}

				return BeautyPlus_Ajax::success('Order status has been changed', $result);

				break;


			}
		}

		/**
		* Get list of products
		*
		* @since  1.0.0
		* @param  string    $type
		* @param  array     $filter
		* @return array
		*/

		private static function get_orders( $type, $filter = array(), $page = 0 ) {

			$count = 0;
			$orders = array();

			if (!empty(BeautyPlus_Helpers::get('s'))) {
				$filter['search'] = BeautyPlus_Helpers::get('s');
			}

			if (!empty($filter['search']) && (2 < strlen($filter['search']) OR 0 ===  strlen($filter['search']))) {

				$results = wc_order_search($filter['search']);

				if (0 < count($results)) {

					$results = array_reverse($results);

					$results = array_slice($results, 0, 100); // Limit to 100 items

					foreach ( $results as $order_id ) {
						$order = wc_get_order($order_id);
						if ($order) {

							$billing_formatted = $order->get_formatted_billing_address();
							$shipping_formatted = $order->get_formatted_shipping_address();

							$order = $order->get_data();

							$order['billing_formatted'] = $billing_formatted;
							$order['shipping_formatted'] = $shipping_formatted;

							$orders[] = $order;
						}
					}
				}

				$count = count($results);

			} else {
				$query_args = array(
					'post_type'      => 'shop_order',
					'post_status'    => array_keys(wc_get_order_statuses()),
					'posts_per_page' => 10,
					'paged'          => $page,
					'orderby'        => 'date',
					'order'          => 'DESC'
				);

				if (isset($filter['post_status']))	{
					$query_args['post_status'] = $type;
				} else {
					$query_args['orderby'] = 'date';
					$query_args['order']   = 'DESC';
				}


				if (isset($filter['post__in']))	{
					$query_args['post__in'] = $filter['post__in'];
				}

				$query_args = array_merge($query_args, $filter);

				$query = new WP_Query( $query_args );

				if ( $query->have_posts() )	{
					foreach ( $query->posts as $order_id ) {

						$order = wc_get_order($order_id);

						if (is_wp_error($order)) {
							continue;
						}

						$billing_formatted = $order->get_formatted_billing_address();
						$shipping_formatted = $order->get_formatted_shipping_address();

						$order = $order->get_data();

						$order['billing_formatted'] = $billing_formatted;
						$order['shipping_formatted'] = $shipping_formatted;

						$next_statuses = array();
						switch ($order['status']) {

							case "cancelled":
							$next_statuses[] = 'trash';
							break;

							case "refunded":
							$next_statuses[] = 'trash';
							break;

							default:
							foreach (array_keys( wc_get_order_statuses() ) AS $status) {
								if (!in_array($status, BeautyPlus::option('reactors-tweaks-order-statuses', array('wc-failed','wc-cancelled','wc-refunded')))) {
									$next_statuses[] = $status;
								}
							}
							$next_statuses[] = 'trash';
							break;
						}


						$order['next_statuses'] = $next_statuses;

						$orders[] = $order;
					}
				}

				$count = $query->found_posts;
			}

			return array(
				'count'  => $count,
				'result' => $orders
			);
		}

	}

	?>