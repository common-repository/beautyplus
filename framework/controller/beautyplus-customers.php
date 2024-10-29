
<?php

/**
* BeautyPlus Customers
*
* Customer management
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class BeautyPlus_Customers extends BeautyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {
		BeautyPlus::wc_engine();

		wp_enqueue_script("beautyplus-customers",  BeautyPlus_Public . "js/beautyplus-customers.js");

		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route() {

		switch (BeautyPlus_Helpers::get('action'))
		{
			case 'view':
			self::detail();
			break;

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

	public static function filter($filter = false) {

		if (!$filter) {
			$filter['offset'] = 0;
			$filter['paged']  = 1;
			$filter['q']      = '';
		}

		$filter['number']  = 20;
		$filter['orderby'] = "registered";
		$filter['order']   = "DESC";

		if (BeautyPlus_Helpers::get('go', null)) {
			$filter['mode'] = 95;
		}

		if ('' !== BeautyPlus_Helpers::get('s', '')) {
			$filter['q'] = BeautyPlus_Helpers::get('s', '');
		}

		if (BeautyPlus_Helpers::get('pg', null)) {
			$filter['offset'] = (intval( BeautyPlus_Helpers::get( 'pg', 1 )) - 1) *  $filter['number'];
		}

		if (BeautyPlus_Helpers::get('orderby'))	{
			if (false !== strpos(BeautyPlus_Helpers::get('orderby',''), 'meta_'))	{
				$filter['orderby'] = $filter['meta_key'] = sanitize_sql_orderby(str_replace ( 'meta_', '', BeautyPlus_Helpers::get('orderby','')));

				if ('meta__money_spent' === BeautyPlus_Helpers::get('orderby','')) {
					$filter['orderby'] = 'meta_value_num';
				}
			} else {
				$filter['orderby'] =  sanitize_sql_orderby(BeautyPlus_Helpers::get('orderby',''));
			}

			$filter['order'] 	= 'ASC' === BeautyPlus_Helpers::get('order','ASC') ? 'DESC' : 'ASC';
		}

		return $filter;
	}

	/**
	* Main function
	*
	* @param  mixed $filter   array of filter
	* @return BeautyPlus_View
	*/

	public static function index($filter = false) {

		$filter = self::filter($filter);

		switch ( $mode = ( !empty($filter['mode']) ? absint($filter['mode']) : BeautyPlus::option('mode-beautyplus-customers', 1) ) ) {

			// Woocommerce Native
			case 99:
			if (!BeautyPlus_Admin::is_full()) {
				BeautyPlus_Helpers::frame( admin_url( 'users.php' ) );
			} else {
				wp_redirect( admin_url( 'users.php' ) );
			}
			break;

			// Other menus
			case 95:
			$customers_count =  WC()->api->WC_API_Customers->get_customers_count();
			echo BeautyPlus_View::run('customers/list-95', array('count' => intval($customers_count['count']), 'iframe_url' => BeautyPlus_Helpers::get_submenu_url(BeautyPlus_Helpers::get('go')) ));
			break;

			// Standart & Search
			case 1:
			case 2:
			case 98:

			global $wpdb;
			$customers = self::get_customers($filter);
			$customers_count =  WC()->api->WC_API_Customers->get_customers_count();

			echo BeautyPlus_View::run('customers/list-'. $mode,  array( 'count' => intval($customers_count['count']), 'per_page' => $filter['number'], 'customers' => $customers, 'mode' => $mode, 'ajax' =>   BeautyPlus_Helpers::is_ajax() ));

			break;
		}
	}

	/**
	* Get user details
	*
	* @since  1.0.0
	* @return void
	*/

	public static function detail() {

		$id = intval ( BeautyPlus_Helpers::get( 'id', 0 ) );

		if (0 === $id) {
			wp_die( -1 );
		}

		$customer =  WC()->api->WC_API_Customers->get_customer( $id );

		if (is_wp_error($customer)) {
			wp_die ( -2 );
		}

		$customer_id = absint($customer['customer']['id']);

		$orders      = WC()->api->WC_API_Orders->get_orders( null, array('customer' => $customer_id ));
		$refunded    = WC()->api->WC_API_Orders->get_orders( null, array('status'=> 'refunded', 'customer' => $customer_id ));
		$meta        = get_user_meta($customer_id) ;

		$orders = BeautyPlus_Orders::index(array(
			'post_status'    => array_keys(wc_get_order_statuses()),
			'mode'           => 97,
			'page'           => 1,
			'posts_per_page' => 99999,
			'meta_query'     => array(
				array(
					'key'     => '_customer_user',
					'value'   => $customer_id ,
					'compare' => '=',
				)
			)
		));

		echo BeautyPlus_View::run('customers/detail',  array( 'customer' => $customer[ 'customer' ], 'orders' => $orders, 'meta' => $meta));

	}

	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return BeautyPlus_Ajax
	*/

	public static function ajax() {

		$do          = BeautyPlus_Helpers::post('do') ;
		$customer_id = absint(BeautyPlus_Helpers::post('id', 0)) ;

		switch ($do) {

			// Search
			case 'search':

			BeautyPlus::wc_engine();

			$filter['mode']    = (BeautyPlus_Helpers::post('mode') ? absint(BeautyPlus_Helpers::post('mode')) : null) ;
			$filter['search1'] = esc_sql(sanitize_text_field(BeautyPlus_Helpers::post('q', '')));

			$filter['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'first_name',
					'value'   => $filter['search1'] ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'last_name',
					'value'   => $filter['search1'] ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_email',
					'value'   => $filter['search1']  ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_phone',
					'value'   => $filter['search1']  ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_city',
					'value'   => $filter['search1']  ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'shipping_email',
					'value'   => $filter['search1']  ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'shipping_phone',
					'value'   => $filter['search1']  ,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'shipping_city',
					'value'   => $filter['search1']  ,
					'compare' => 'LIKE'
				)
			);

			echo self::index($filter);
			wp_die();
			break;


			// Update user details
			case 'update':

			BeautyPlus::wc_engine();

			$customer =  WC()->api->WC_API_Customers->get_customer( $customer_id );

			if (is_wp_error( $customer )) {
				BeautyPlus_Ajax::error($customer->get_error_message());
				wp_die ();
			}

			$customer_id = absint($customer['customer']['id']);

			$data['first_name']                    = esc_sql(BeautyPlus_Helpers::post('billing_first_name'));
			$data['last_name']                     = esc_sql(BeautyPlus_Helpers::post('billing_last_name'));
			$data['billing_address']['first_name'] = esc_sql(BeautyPlus_Helpers::post('billing_first_name'));
			$data['billing_address']['last_name']  = esc_sql(BeautyPlus_Helpers::post('billing_last_name'));
			$data['billing_address']['company']    = esc_sql(BeautyPlus_Helpers::post('billing_company'));
			$data['billing_address']['address_1']  = esc_sql(BeautyPlus_Helpers::post('billing_address_1'));
			$data['billing_address']['address_2']  = esc_sql(BeautyPlus_Helpers::post('billing_address_2'));
			$data['billing_address']['city']       = esc_sql(BeautyPlus_Helpers::post('billing_city'));
			$data['billing_address']['state']      = esc_sql(BeautyPlus_Helpers::post('billing_state'));
			$data['billing_address']['postcode']   = esc_sql(BeautyPlus_Helpers::post('billing_postcode'));
			$data['billing_address']['country']    = esc_sql(BeautyPlus_Helpers::post('billing_country'));
			if ('' !== trim(BeautyPlus_Helpers::post('billing_email'))) {
				$data['billing_address']['email'] 	 = esc_sql(sanitize_email(BeautyPlus_Helpers::post('billing_email')));
				$data['email']                       = esc_sql(sanitize_email(BeautyPlus_Helpers::post('billing_email')));
			}
			$data['billing_address']['phone']      = esc_sql(BeautyPlus_Helpers::post('billing_phone'));

			$customer =  WC()->api->WC_API_Customers->edit_customer(  $customer_id, array('customer' => $data) );

			if (is_wp_error( $customer )){
				BeautyPlus_Ajax::error($customer->get_error_message());
				wp_die ();
			}

			BeautyPlus_Ajax::success('OK');

			break;

			// Retrive states by country
			case 'states':

			BeautyPlus::wc_engine();

			$country = BeautyPlus_Helpers::post('country');
			$states  = WC()->countries->get_states( $country );
			$return  = "";


			BeautyPlus_Ajax::success(  woocommerce_form_field('billing_state', array(
				'type'        => 'state',
				'country'     => $country,
				'class'       => array( '' ),
				'label'       => '',
				'placeholder' => esc_html__('Select a state', 'beautyplus'),
				'return'      => TRUE
			)
		));

		break;

		// Retrive user details
		case 'details':

		BeautyPlus::wc_engine();

		$customer =  WC()->api->WC_API_Customers->get_customer( $customer_id );

		if (is_wp_error( $customer )) {
			BeautyPlus_Ajax::error($customer->get_error_message());
			wp_die ();
		}

		$customer_id = absint($customer['customer']['id']);

		$output = '<h6>'.esc_html__('Last Orders', 'beautyplus').'</h6>';

		//$query_orders =  WC()->api->WC_API_Customers->get_customer_orders( $customer_id );

		$orders = BeautyPlus_Orders::index(
			array('post_status' => array_keys(wc_get_order_statuses()),
			'mode'              => 97,
			'page'              => 1,
			'posts_per_page'    => 99999,
			'meta_query'        => array(
				array(
					'key'     => '_customer_user',
					'value'   => absint( $customer_id ),
					'compare' => '=',
				))));

				$output .= $orders . '';

				$output .=  '';

				wp_die( $output );

				break;
			}

		}

		/**
		* Get list of customers
		*
		* @since  1.0.0
		* @param  array    $filter
		* @return array
		*/

		public static function get_customers($filter) {

			$result = array();

			$_query = new WP_User_Query( $filter );
			$query = $_query->get_results();

			if ( !empty($query) )
			{
				foreach ( $query as $user ) {

					$customer      = new WC_Customer( $user->ID );

					if (is_wp_error($customer)) {
						continue;
					}

					$last_order    = $customer->get_last_order();

					$customer_data = array(
						'id'              => $customer->get_id(),
						//      'created_at'       => $this->server->format_datetime( $customer->get_date_created() ? $customer->get_date_created()->getTimestamp() : 0 ), // API gives UTC times.
						//      'last_update'      => $this->server->format_datetime( $customer->get_date_modified() ? $customer->get_date_modified()->getTimestamp() : 0 ), // API gives UTC times.
						'email'           => $customer->get_email(),
						'first_name'      => $customer->get_first_name(),
						'last_name'       => $customer->get_last_name(),
						'username'        => $customer->get_username(),
						'role'            => $customer->get_role(),
						'last_order_id'   => is_object( $last_order ) ? $last_order->get_id() : null,
						'last_order_date' => is_object( $last_order ) ? ( $last_order->get_date_created() ? $last_order->get_date_created() : 0 ) : 0,
						'orders_count'    => $customer->get_order_count(),
						'total_spent'     => wc_format_decimal( $customer->get_total_spent(), 2 ),
						'avatar_url'      => $customer->get_avatar_url(),
						'billing_address' => array(
							'first_name' => $customer->get_billing_first_name(),
							'last_name'  => $customer->get_billing_last_name(),
							'company'    => $customer->get_billing_company(),
							'address_1'  => $customer->get_billing_address_1(),
							'address_2'  => $customer->get_billing_address_2(),
							'city'       => $customer->get_billing_city(),
							'state'      => $customer->get_billing_state(),
							'postcode'   => $customer->get_billing_postcode(),
							'country'    => $customer->get_billing_country(),
							'email'      => $customer->get_billing_email(),
							'phone'      => $customer->get_billing_phone(),
						),
						'shipping_address' => array(
							'first_name' => $customer->get_shipping_first_name(),
							'last_name'  => $customer->get_shipping_last_name(),
							'company'    => $customer->get_shipping_company(),
							'address_1'  => $customer->get_shipping_address_1(),
							'address_2'  => $customer->get_shipping_address_2(),
							'city'       => $customer->get_shipping_city(),
							'state'      => $customer->get_shipping_state(),
							'postcode'   => $customer->get_shipping_postcode(),
							'country'    => $customer->get_shipping_country(),
						),
					);

					$result[] = $customer_data;
				}
			}
			return $result;
		}
	}

	?>