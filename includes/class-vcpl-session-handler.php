<?php

/**
 * The profile class.
 * This class is used to manage the profile of the user.
 *
 * @package    Includes
 * @subpackage Includes/Profile
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Session_Handler {


	const CUSTOMER_ROLES = array( 'cbs', 'cbv', 'cw' );

	private $current_user_id;

	public function __construct() {
		$this->current_user_id = wp_get_current_user()->ID;
	}

	/**
	 * Set role of the user to customer by shop.
	 *
	 * @param  int $customer_id The id of the customer.
	 */
	public function set_customer_by_shop_role( $customer_id ) {

		$user = new \WP_User( $customer_id );
		$user->set_role( 'cbs' );
	}

	// Redirect shop manager to dashboard.
	public function redirect_shop_manager_to_dashboard() {

		$user = wp_get_current_user();
		if ( is_page( 'my-account' ) && is_user_logged_in() ) {
			if ( in_array( 'shop_manager', $user->roles ) ) {
				wp_redirect( home_url( '/frontend-manager' ) );
			}
		}

		if ( is_page( 'frontend-manager' ) && is_user_logged_in() ) {
			if ( ! in_array( 'shop_manager', $user->roles ) ) {
				wp_redirect( home_url( '/' ) );
			}
		}
	}

	// Redirect user not customer to home.
	public function redirect_user_not_customer_to_home() {

		if ( is_page( 'my-account' ) && is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$roles = array_merge( self::CUSTOMER_ROLES, array( 'shop_manager', 'vendor', 'administrator' ) );

			if ( empty( array_intersect( $roles, $user->roles ) ) ) {
				wp_redirect( home_url( '/' ) );
				exit;
			}
		}
	}

	/**
	 * Add custom menu items to my account page.
	 *
	 * @param  array $items The menu items.
	 * @return array        The menu items.
	 */
	public function new_menu_items( $items ) {

		if ( is_page( 'my-account' ) ) {
			$user = wp_get_current_user();

			if ( ! in_array( 'vendor', $user->roles ) ) {
				return $items;
			}
			unset( $items['dashboard'] );
			unset( $items['downloads'] );
			unset( $items['orders'] );
			unset( $items['edit-address'] );

			return array_merge(
				array(
					'dashboard'     => __( 'Dashboard', VCPL_TEXT_DOMAIN ),
					'customers'     => __( 'Customers', VCPL_TEXT_DOMAIN ),
					'orders_vendor' => __( 'Orders', VCPL_TEXT_DOMAIN ),
					'analytics'     => __( 'Analytics', VCPL_TEXT_DOMAIN ),
				),
				$items
			);
		}
	}

	// Add custom endpoints to my account page.
	public function add_endpoints() {

		add_rewrite_endpoint( 'customers', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'orders_vendor', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'analytics', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Add custom query vars.
	 *
	 * @param  array $vars The query vars.
	 * @return array       The query vars.
	 */
	public function add_query_vars( $vars ) {

		$vars[] = 'customers';
		$vars[] = 'orders_vendor';
		$vars[] = 'dashboard';
		$vars[] = 'analytics';

		return $vars;
	}

	public function button_table_menu() {
		?>
<div class="vcpl-table-menu">
	<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'add_user' ) ) ); ?>"
	class="button button-primary"><?php esc_html_e( 'Add Customer', VCPL_TEXT_DOMAIN ); ?></a>
</div>
		<?php
	}

	// Restrict my account pages.
	public function restrict_pages() {

		if ( is_page( 'my-account' ) ) {

			$user = wp_get_current_user();
			if ( ! in_array( 'vendor', $user->roles ) ) {
				return;
			}

			if ( is_wc_endpoint_url( 'downloads' ) || is_wc_endpoint_url( 'orders' ) || is_wc_endpoint_url( 'edit-address' ) ) {
				wp_redirect( home_url( '/my-account' ) );
				exit;
			}
		}
	}

	// Add content to custom endpoints.
	public function add_content_endpoint() {

		global $wp_query;

		$endpoints = array(
			'customers',
			'orders_vendor',
			'analytics',
		);

		foreach ( array_keys( $wp_query->query_vars ) as $endpoint ) {
			if ( in_array( $endpoint, $endpoints ) ) {
				$endpoint = str_replace( '_', '-', $endpoint );
				require_once plugin_dir_path( __DIR__ ) . "templates/myaccount/{$endpoint}.php";
				break;
			}
		}
	}

	/**
	 * Change the title of the endpoint.
	 *
	 * @param  string $title The title of the endpoint.
	 * @return string        The title of the endpoint.
	 */
	public function endpoint_title( $title ) {

		global $wp_query;

		$endpoints = array(
			'customers'     => __( 'Manage Customers', 'woocommerce' ),
			'orders_vendor' => __( 'Orders', 'woocommerce' ),
			'analytics'     => __( 'Analytics', 'woocommerce' ),
		);

		foreach ( $endpoints as $endpoint => $endpoint_title ) {
			if ( isset( $wp_query->query_vars[ $endpoint ] ) && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				$title = $endpoint_title;
				remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
				break;
			}
		}

		return $title;
	}

	/**
	 * Add custom dashboard template.
	 *
	 * @param  string $template       The template.
	 * @param  string $template_name  The name of the template.
	 * @param  string $template_path  The path of the template.
	 */
	public function custom_dashboard_template( $template, $template_name, $template_path ) {

		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = untrailingslashit( plugin_dir_path( __DIR__ ) ) . '/templates/';

		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = wp_get_current_user()->roles[0] === 'vendor' ? $plugin_path . $template_name : $_template;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		return $template;
	}

	/**
	 * Get Form fields for new customer.
	 *
	 * @return array fields
	 */
	private static function get_form_fields_new_customer(): array {
		return apply_filters(
			'vcpl_session_new_customer_form_fields',
			array(
				'user_login' => array(
					'label'    => esc_html__( 'Username', VCPL_TEXT_DOMAIN ),
					'type'     => 'text',
					'required' => true,
				),
				'user_email' => array(
					'label'    => esc_html__( 'Email', VCPL_TEXT_DOMAIN ),
					'type'     => 'email',
					'required' => true,
				),
				'first_name' => array(
					'label'    => esc_html__( 'First Name', VCPL_TEXT_DOMAIN ),
					'type'     => 'text',
					'required' => true,
				),
				'last_name'  => array(
					'label'    => esc_html__( 'Last Name', VCPL_TEXT_DOMAIN ),
					'type'     => 'text',
					'required' => true,
				),
				'tax_id_ein' => array(
					'label'             => __( 'Tax ID / EIN', VCPL_TEXT_DOMAIN ),
					'type'              => 'text',
					'required'          => true,
					'custom_attributes' => array( 'autocomplete' => 'off' ),
				),
				'user_pass'  => array(
					'label'             => esc_html__( 'Password', VCPL_TEXT_DOMAIN ),
					'id'                => 'password',
					'type'              => 'password',
					'required'          => true,
					'custom_attributes' => array( 'autocomplete' => 'off' ),
					'input_class'       => array( 'woocommerce-Input woocommerce-Input--text input-text' ),
					'class'             => array( 'woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide ' ),
				),
			)
		);
	}

	/**
	 * Get table customers columns headers.
	 *
	 * @return array columns
	 */
	private static function get_table_customers_columns_headers(): array {

		return array(
			'user_login'      => esc_html__( 'Username', VCPL_TEXT_DOMAIN ),
			'state'     => esc_html__( 'State', VCPL_TEXT_DOMAIN ),
			'city'      => esc_html__( 'City', VCPL_TEXT_DOMAIN ),
			'address_1' => esc_html__( 'Address', VCPL_TEXT_DOMAIN ),
			'user_email'      => esc_html__( 'Email', VCPL_TEXT_DOMAIN ),
			'actions'         => esc_html__( 'Actions', VCPL_TEXT_DOMAIN ),
		);
	}

	/**
	 * Get table customers rows.
	 *
	 * @return array rows
	 */
	private static function get_table_customers_rows(): array {

		$users = vcpl_get_my_customers( wp_get_current_user()->ID ) ?: array();
		$rows  = array();

		foreach ( $users as $user ) {
			$rows[] = array(
				'user_login'      => $user->user_login,
				'user_email'      => $user->user_email,
				'state'     => get_user_meta( $user->ID, 'shipping_state', true ),
			    'city'      => get_user_meta( $user->ID, 'shipping_city', true ),
			    'address_1' => get_user_meta( $user->ID, 'shipping_address_1', true ),
				'actions'         => '<a href="' . add_query_arg(
					array(
						'action'  => 'edit_user',
						'user_id' => $user->ID,
					)
				) . '">' . esc_html__( 'Edit', VCPL_TEXT_DOMAIN ) . '</a>'
							. ' <a href="' . add_query_arg(
								array(
									'action'  => 'delete_user',
									'user_id' => $user->ID,
								)
							) . '">' . esc_html__( 'Delete', VCPL_TEXT_DOMAIN ) . '</a> ',
			);
		}

		return $rows;
	}

	/**
	 * Get the cards for the dashboard.
	 *
	 * @return array The cards.
	 */
	public function cards_dashboard_vendor(): array {

		return array(
			array(
				'title'   => esc_html__( 'Total sales amount', VCPL_TEXT_DOMAIN ),
				'insight' => number_format( vcpl_get_my_customers_total_purchases( $this->current_user_id ), 2 ) . ' ' . get_woocommerce_currency_symbol(),
				'desc'    => esc_html__( 'All customers total sales completed', VCPL_TEXT_DOMAIN ),
			),
			array(
				'title'   => esc_html( 'Total sales amount today', VCPL_TEXT_DOMAIN ),
				'insight' => number_format( vcpl_get_my_customers_sales_today( $this->current_user_id ), 2 ) . ' ' . get_woocommerce_currency_symbol(),
				'desc'    => esc_html__( 'All customers total sales today', VCPL_TEXT_DOMAIN ),
			),
			array(
				'title'   => esc_html__( 'Profit this week', VCPL_TEXT_DOMAIN ),
				'insight' => $this->get_weekly_profit() . ' ' . get_woocommerce_currency_symbol(),
				'desc'    => esc_html__( 'Profit for this month, Orders completed', VCPL_TEXT_DOMAIN ),
			),
			array(
				'title'   => esc_html__( 'Nro orders today', VCPL_TEXT_DOMAIN ),
				'insight' => vcpl_get_number_my_customers_sales_today( $this->current_user_id ),
				'desc'    => esc_html__( 'Number of orders all customers', VCPL_TEXT_DOMAIN ),
			),
			array(
				'title'   => esc_html__( 'Nro sales', VCPL_TEXT_DOMAIN ),
				'insight' => vcpl_get_number_my_customers_sales( $this->current_user_id ),
				'desc'    => esc_html__( 'Number of orders all customers completed', VCPL_TEXT_DOMAIN ),
			),
			array(
				'title'   => esc_html__( 'Nro Customers', VCPL_TEXT_DOMAIN ),
				'insight' => count( vcpl_get_my_customers( $this->current_user_id ) ?: array() ),
				'desc'    => esc_html__( 'Number of customers', VCPL_TEXT_DOMAIN ),
			),
		);
	}

	/**
	 * Items for the analitycs table in the dashboard.
	 *
	 * @return array The items.
	 */
	public function analysis_table_items(): array {
		return array(
			array(
				'title' => esc_html__( 'Profile Features', VCPL_TEXT_DOMAIN ),
				'items' => $this->get_profile_features_analysis_table_items(),
			),
			array(
				'title' => esc_html__( 'Last sales completed', VCPL_TEXT_DOMAIN ),
				'items' => $this->get_last_sales_completed(),
			),
			array(
				'title' => esc_html__( 'Last orders', VCPL_TEXT_DOMAIN ),
				'items' => $this->get_last_orders(),
			),
			array(
				'title' => esc_html__( 'Top customers', VCPL_TEXT_DOMAIN ),
				'items' => $this->get_top_customers(),
			),
		);
	}

	/**
	 * Get profile features analysis table items.
	 *
	 * @return array The items.
	 */
	private function get_profile_features_analysis_table_items(): array {
		return array(
			array( 'Commission per sale', vcpl_get_current_feature_vendor( $this->current_user_id, 'commission' ) . ' %' ),
			array( 'Weekly profit', $this->get_weekly_profit() . ' ' . get_woocommerce_currency_symbol() ),
			array( 'Salary', vcpl_get_current_feature_vendor( $this->current_user_id, 'salary' ) . ' ' . get_woocommerce_currency_symbol() ),
			array( 'Number Customers', count( vcpl_get_my_customers( $this->current_user_id ) ?: array() ) ),
		);
	}

	private function get_weekly_profit(): float {
		$weekly_profit = 0;
		foreach ( $this->get_orders( 4, 'completed', date( 'Y-m-d', strtotime( 'monday this week' ) ) . '...' . date( 'Y-m-d', strtotime( 'sunday this week' ) ) ) as $order ) {
			$weekly_profit += vcpl_get_my_commission_amount( get_userdata( $this->current_user_id ), $order );
		}
		return $weekly_profit;
	}

	/**
	 * Get the last 4 sales completed.
	 *
	 * @return array The sales.
	 */
	private function get_last_sales_completed(): array {
		return $this->format_analysis_table_items(
			'sale',
			$this->get_orders( 4, 'completed' ),
			fn ( $sales ) => ! empty( $sales )
				? array_reduce(
					$sales,
					fn ( $sales, $order ) =>
						array_merge(
							$sales,
							array(
								array(
									sprintf(
										'#%s %s - %s item%s',
										$order->get_id(),
										get_userdata( $order->get_customer_id() )->user_login,
										$order->get_item_count(),
										$order->get_item_count() > 1 ? 's' : ''
									),
									$order->get_total() . ' ' . get_woocommerce_currency_symbol(),
								),
							)
						),
					array()
				)
				: array()
		);
	}

	/**
	 * Get the last orders
	 *
	 * @return array The orders.
	 */
	private function get_last_orders(): array {
		return $this->format_analysis_table_items(
			'order',
			$this->get_orders( 4 ),
			fn ( $orders ) => ! empty( $orders )
				? array_reduce(
					$orders,
					fn ( $orders, $order ) =>
						array_merge(
							$orders,
							array(
								array(
									sprintf(
										'order #%s (%s)',
										$order->get_id(),
										$order->get_status()
									),
									$order->get_total() . ' ' . get_woocommerce_currency_symbol(),
								),
							)
						),
					array()
				)
				: array()
		);
	}

	/**
	 * Get the top customers.
	 *
	 * @return array The customers.
	 */
	private function get_top_customers(): array {
		return $this->format_analysis_table_items(
			'customer',
			vcpl_get_my_top_customers( $this->current_user_id, 4 ),
			fn ( $topcustomers ) => ! empty( $topcustomers )
			? array_map(
				fn ( $customer_id, $count_orders ) =>
				array(
					get_userdata( $customer_id )->user_login,
					$count_orders . __( ' <span>order' . ( (int) $count_orders > 1 ? 's' : '' ) . '</span>', VCPL_TEXT_DOMAIN ),
				),
				array_keys( $topcustomers ),
				$topcustomers
			)
			: array()
		);
	}

	/**
	 * format analysis table items.
	 *
	 * @param  string $name     The name of the item.
	 * @param  array  $items    The items.
	 * @param  callable $callback The callback.
	 * @return array            The items.
	 */
	private function format_analysis_table_items( string $name, array $items, callable $callback ) {
		$items = $callback( $items );
		return count( $items ) > 3 ? $items : array_pad( $items, 4, array( "Not {$name} yet", '' ) );
	}

	/**
	 * Get last orders.
	 *
	 * @param  int $limit  The limit of orders.
	 * @param  string|array $status The status of the orders.
	 * @return array        The orders.
	 */
	private function get_orders( int $limit, string|array $status = null, string $date_created = '' ): array {
		$args = array(
			'limit'        => $limit,
			'orderby'      => 'date',
			'order'        => 'DESC',
			'customer'     => get_user_meta( $this->current_user_id, 'customers', true ) ?: false,
			'date_created' => $date_created,
		);

		if ( ! empty( $status ) ) {
			$args = array_merge( $args, array( 'status' => $status ) );
		}

		$orders = wc_get_orders( $args );

		return $orders;
	}

	/**
	 * Get table orders columns headers.
	 *
	 * @return array columns
	 */
	public static function get_table_orders_columns_headers(): array {
		return array(
			'order'    => esc_html__( 'Order', VCPL_TEXT_DOMAIN ),
			'date'     => esc_html__( 'Date Created', VCPL_TEXT_DOMAIN ),
			'customer' => esc_html__( 'Customer', VCPL_TEXT_DOMAIN ),
			'shipping_address' => esc_html__( 'Shipping Address', VCPL_TEXT_DOMAIN ),
			/*'status'   => esc_html__( 'Status', VCPL_TEXT_DOMAIN ),*/
			'pdf'      => esc_html__( 'PDF', VCPL_TEXT_DOMAIN ),
			'total'    => esc_html__( 'Total', VCPL_TEXT_DOMAIN ),
		);
	}

	/**
	 * Get table orders rows.
	 *
	 * @return array rows
	 */
	public static function get_table_orders_rows(): array {
		
		$date_before = vcpl_get_var( 'filter_date_before', '' );
		$date_after  = vcpl_get_var( 'filter_date_after', '' );
		$rows        = array();
		$args        = array(
			'limit'        => -1,
			'customer'     => get_user_meta( wp_get_current_user()->ID, 'customers', true ) ?: false,
			'date_created' => $date_before && $date_after ? $date_before . '...' . $date_after : '',
		);

		if ( ! empty( vcpl_get_var( 'filter_status' ) ) ) {
			$args['status'] = str_replace( 'wc-', '', vcpl_get_var( 'filter_status', '' ) );
		}

		$orders = wc_get_orders( $args );
		
		$rows = array();
		foreach ( $orders as $order ) {
			$rows[]   = array(
					'order'       => "#{$order->get_order_number()} ({$order->get_status()})",
					'date'     => "<span class='vcprofitlos-oreder-date'>{$order->get_date_created()->date( 'm-d-Y' )}</span>",
					'customer' => get_userdata( $order->get_customer_id() )->user_login,
					'shipping_address' => get_user_meta( $order->get_customer_id(), 'shipping_state', true ) . ', ' . get_user_meta( $order->get_customer_id(), 'shipping_city', true ) .' ' . get_user_meta( $order->get_customer_id(), 'shipping_address_1', true ),
					/*'status'   => $order->get_status(),*/
					'pdf'  => '<a href="' . do_shortcode('[wcpdf_document_link order_id="' . $order->get_id() . '" ]') . '"><i class="fa-solid fa-download"></i></a>',
					'total'    => $order->get_total() . get_woocommerce_currency_symbol(),
				);
		}

		return $rows;
	}


	// Add custom fields to the form.
	public function customer_fields() {
		if ( ! is_user_logged_in() || ! in_array( 'vendor', wp_get_current_user()->roles ) ) {
			return;
		}
		
		$customers = vcpl_get_my_customers( $this->current_user_id );
		$options   = array();
		
		if ( !is_array( $customers ) || empty( $customers ) ) {
			return;
		}
		
		foreach( $customers as $customer ) {
			$options[ $customer->ID ] = $customer->user_login;
		}

		printf( 
			'<div class="woocommerce-billing-fields__field-wrapper"><h3>%s</h3><p>%s</p>%s</div>',
			esc_html__( 'Customer assign order', VCPL_TEXT_DOMAIN ),
			esc_html__( 'Select the customer to assign the order.', VCPL_TEXT_DOMAIN ),
			woocommerce_form_field(
				'customer_order',
				array(
					'type'              => 'select',
					'label'             => esc_html__( 'Select a customer', VCPL_TEXT_DOMAIN ),
					'class'             => 'input-text',
					'options'           => $options,
					'required'          => true,
					'custom_attributes' => array(
						'required'     => 'required',
						'autocomplete' => 'off',
					),
				),
				WC()->checkout->get_value( 'customer_order' ) ?? 0
			)
		);
	}

	// Validate customer assign order.
	public function validate_customer_assign_order() {

		if ( ! is_user_logged_in() || ! in_array( 'vendor', wp_get_current_user()->roles ?: array() ) ) {
			return;
		}

		if ( empty( $_POST['customer_order'] ?? strval( false ) ) ) {
			wc_add_notice( '<strong>' . __( 'Customer Order' ) . '</strong>' . __( ' is required field, please select a customer to assign order', VCPL_TEXT_DOMAIN ), 'error' );
		}
	}

	/**
	 * change headline checkout
	 *
	 * @param  string $translated_text The translated text.
	 * @param  string $text            The text.
	 * @param  string $domain          The domain.
	 * @return string new text
	 */
	public function change_headline_checkout( string $translated_text, string $text, string $domain ): string {
		if ( ! is_user_logged_in() || ! in_array( 'vendor', wp_get_current_user()->roles ?: array() ) ) {
			return $translated_text;
		}

		if ( 'Customer information' === $text && 'astra-addon' === $domain ) {
			$translated_text = 'Vendor information';
		}
		return $translated_text;
	}

	/**
	 * Change checkout customer id.
	 *
	 * @param  int $customer_id The id of the customer.
	 * @return int new customer id
	 */

	public function change_checkout_customer_id( int $customer_id ): int {
		if ( isset( $_POST['customer_order'] ) ) {
			$customer_id_to_asign = intval( $_POST['customer_order'] );

			if ( $customer_id_to_asign > 0 ) {
				return $customer_id_to_asign;
			}
		}

		return $customer_id;
	}

	/**
	 * Update customer billing shipping.
	 *
	 * @param  int $order_id The id of the order.
	 * @return void
	 */
	public function update_customer_billing_shipping( $order_id ): void {
		if ( ! empty( $_POST['customer_order'] ) ) {
			$customer = new \WC_Customer( $_POST['customer_order'] );

			foreach ( $_POST ?: array() as $key => $value ) {
				if ( method_exists( $customer, $key ) ) {
					$customer->$key( $value );
				}
			}

			$customer->save();
		}
	}

	// redirect after pruchase
	public function woo_custom_redirect_after_purchase() {
		global $wp;

		if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
			$order_id = absint( $wp->query_vars['order-received'] );
			$order    = wc_get_order( $order_id );
			$user     = get_userdata( $order->get_customer_id() );

			wc_add_notice( "Order's <strong>{$user->user_login}</strong> has been successfull." );
			wp_redirect( wc_get_endpoint_url( 'orders_vendor', strval( false ), wc_get_page_permalink( 'myaccount' ) ) );
			exit;
		}
	}

	/**
	 * Get table analytics columns headers.
	 *
	 * @return array columns
	 */
	private static function get_table_analytics_columns_headers(): array {
		return array(
			'customer'    => esc_html__( 'Customer', VCPL_TEXT_DOMAIN ),
			'order'       => esc_html__( 'Order', VCPL_TEXT_DOMAIN ),
			// 'items'          => esc_html__( 'Items', VCPL_TEXT_DOMAIN ),
			'order_total' => esc_html__( 'Order total', VCPL_TEXT_DOMAIN ),
			'commission'  => esc_html__( 'Commission (%)', VCPL_TEXT_DOMAIN ),
			'profit'      => esc_html__( 'Profit', VCPL_TEXT_DOMAIN ),
		);
	}

	/**
	* Get table analytics rows.
	*
	* @return array rows
	*/
	private static function get_table_analytics_rows(): array {
		$date_before = vcpl_get_var( 'filter_date_before', '' );
		$date_after  = vcpl_get_var( 'filter_date_after', '' );
		$rows        = array();
		$args        = array(
			'limit'        => -1,
			'customer'     => get_user_meta( wp_get_current_user()->ID, 'customers', true ) ?: false,
			'date_created' => $date_before && $date_after ? $date_before . '...' . $date_after : '',
		);

		if ( ! empty( vcpl_get_var( 'filter_status' ) ) ) {
			$args['status'] = str_replace( 'wc-', '', vcpl_get_var( 'filter_status', '' ) );
		}

		$orders = wc_get_orders( $args );

		foreach ( $orders as $order ) {
			$customer = get_userdata( $order->get_customer_id() );
			$date     = ( $order->get_date_created() )->format( 'Y-m-d H:i:s' );
			$rows[]   = array(
				'customer'    => $customer->user_login,
				'order'       => "#{$order->get_order_number()} ({$order->get_status()})",
				'order_total' => $order->get_total() . get_woocommerce_currency_symbol(),
				'commission'  => vcpl_get_commission_percent_by_date( $date, wp_get_current_user() ) . '%',
				'profit'      => vcpl_get_my_commission_amount( wp_get_current_user(), $order ) . get_woocommerce_currency_symbol(),
			);
		}

		return array(
			'rows'         => $rows,
			'total_amount' => array_reduce( $orders, fn ( $total, $order ) => $total + $order->get_total(), 0 ),
			'total_profit' => array_reduce( $orders, fn ( $total, $order ) => $total + vcpl_get_my_commission_amount( wp_get_current_user(), $order ), 0 ),
		);
	}
}
