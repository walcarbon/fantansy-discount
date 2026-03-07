<?php
/**
 * Optimized List Table for the analytics vendors.
 *
 * This version uses a direct SQL query to handle filtering, sorting, and pagination
 * at the database level, avoiding memory exhaustion issues with large datasets.
 *
 * @package    Includes
 * @subpackage Includes/Admin
 * @extends    \WP_List_Table
 * @abstract
 */

namespace Includes\Admin\List_Tables;

// Ensure WP_List_Table is available.
if ( ! class_exists( '\WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class VCPL_Admin_List_Table_Analytics_Vendor extends \WP_List_Table {

    /**
     * Stores the calculated total profit for the filtered results.
     * @var float
     */
    private $total_profit = 0.0;

    /**
     * Stores the calculated total amount for the filtered results.
     * @var float
     */
    private $total_amount = 0.0;


    public function __construct() {
        parent::__construct(
            array(
                'singular' => __( 'vendor profit', 'vcpl-text-domain' ), // Reemplaza 'VCPL_TEXT_DOMAIN' con tu text domain real
                'plural'   => __( 'vendor profits', 'vcpl-text-domain' ),
                'ajax'     => false,
            )
        );
    }

    /**
     * Display a message when no items are found.
     */
    public function no_items(): void {
        _e( 'No profit data found for the selected filters.', 'vcpl-text-domain' );
    }

    /**
     * Prepares the list of items for display.
     * This is the core method where the data query is executed.
     */
    public function prepare_items(): void {
        global $wpdb;

        // DEBUG: Inicio de la preparación de items.
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'VCPL_DEBUG: prepare_items() - Iniciando preparación de la tabla.' );
        }

        // Definir las columnas
        $this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns() );

        // --- 1. OBTENER PARÁMETROS DE FILTRADO Y ORDENACIÓN ---
        $per_page    = 100;
        $paged       = $this->get_pagenum();
        $offset      = ( $paged - 1 ) * $per_page;
        $order_by    = vcpl_get_var( 'orderby', 'ID' );
        $order       = vcpl_get_var( 'order', 'DESC' );
        $search_term = vcpl_get_var( 's', '' );
        $date_before = vcpl_get_var( 'filter_date_before', '' );
        $date_after  = vcpl_get_var( 'filter_date_after', '' );
        $vendor_id   = vcpl_get_var( 'filter_vendor', '' );
        $status      = vcpl_get_var( 'filter_status', '' );

        // --- 2. CONSTRUIR LA CONSULTA SQL BASE ---
        // Usamos alias para mayor claridad y unimos las tablas necesarias.
        $sql_select = "
            SELECT
                orders.ID,
                orders.post_status,
                orders.post_date,
                meta_customer.meta_value AS customer_id,
                meta_vendor.meta_value AS vendor_id,
                customers.user_login AS customer_login,
                vendors.user_login AS vendor_login,
                (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = orders.ID AND meta_key = '_order_total') as order_total
        ";
        $sql_from = "
            FROM {$wpdb->posts} AS orders
            LEFT JOIN {$wpdb->postmeta} AS meta_customer ON orders.ID = meta_customer.post_id AND meta_customer.meta_key = '_customer_user'
            LEFT JOIN {$wpdb->users} AS customers ON meta_customer.meta_value = customers.ID
            LEFT JOIN {$wpdb->usermeta} AS meta_vendor ON customers.ID = meta_vendor.user_id AND meta_vendor.meta_key = 'vendor'
            LEFT JOIN {$wpdb->users} AS vendors ON meta_vendor.meta_value = vendors.ID
        ";
        // FIX: Excluir permanentemente los estados 'auto-draft', 'draft' y 'trash'.
        $sql_where = " WHERE orders.post_type = 'shop_order' AND orders.post_status NOT IN ('auto-draft', 'draft', 'trash')";
        
        $params = [];

        // --- 3. AÑADIR CONDICIONES 'WHERE' BASADAS EN LOS FILTROS ---
        if ( ! empty( $status ) ) {
            $sql_where .= " AND orders.post_status = %s";
            $params[] = $status;
        }
        if ( ! empty( $vendor_id ) ) {
            $sql_where .= " AND meta_vendor.meta_value = %d";
            $params[] = $vendor_id;
        }
        if ( ! empty( $date_before ) && ! empty( $date_after ) ) {
            $sql_where .= " AND orders.post_date BETWEEN %s AND %s";
            $params[] = $date_before . ' 00:00:00';
            $params[] = $date_after . ' 23:59:59';
        }
        if ( ! empty( $search_term ) ) {
            if ( is_numeric( $search_term ) ) {
                $sql_where .= " AND orders.ID = %d";
                $params[] = intval( $search_term );
            } else {
                $sql_where .= " AND customers.user_login LIKE %s";
                $params[] = '%' . $wpdb->esc_like( $search_term ) . '%';
            }
        }

        // --- 4. OBTENER EL CONTEO TOTAL DE ITEMS PARA LA PAGINACIÓN ---
        $sql_count = "SELECT COUNT(orders.ID) " . $sql_from . $sql_where;
        // FIX: Solucionado el Notice de wpdb::prepare. Se usa prepare solo si hay parámetros.
        $query_count = empty($params) ? $sql_count : $wpdb->prepare($sql_count, $params);
        $total_items = $wpdb->get_var($query_count);

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'VCPL_DEBUG: Total items query: ' . $query_count );
            error_log( 'VCPL_DEBUG: Total items found: ' . $total_items );
        }
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ) );
        
        // --- 5. OBTENER TOTALES GLOBALES (PROFIT Y AMOUNT) ---
        $sql_totals = $sql_select . $sql_from . $sql_where;
        // FIX: Solucionado el Notice de wpdb::prepare. Se usa prepare solo si hay parámetros.
        $query_totals = empty($params) ? $sql_totals : $wpdb->prepare($sql_totals, $params);
        $all_filtered_results = $wpdb->get_results($query_totals);

        foreach($all_filtered_results as $item) {
             $order = wc_get_order($item->ID);
             if ($order) {
                 $this->total_amount += $order->get_total();
                 $vendor = $item->vendor_id ? get_userdata($item->vendor_id) : false;
                 
                 // FIX: Solucionado el Fatal Error. Se comprueba que $vendor sea un objeto antes de pasarlo a la función.
                 if ($vendor instanceof \WP_User) {
                    $this->total_profit += vcpl_get_my_commission_amount($vendor, $order);
                 }
             }
        }
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'VCPL_DEBUG: Total amount calculated: ' . $this->total_amount );
            error_log( 'VCPL_DEBUG: Total profit calculated: ' . $this->total_profit );
        }

        // --- 6. OBTENER LOS ITEMS PARA LA PÁGINA ACTUAL ---
        $allowed_orderby = ['ID', 'order_total', 'post_date']; // Columnas seguras para ordenar
        $order_by_safe = in_array($order_by, $allowed_orderby) ? $order_by : 'ID';
        $order_safe = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql_paged = $sql_select . $sql_from . $sql_where;
        $sql_paged .= " ORDER BY {$order_by_safe} {$order_safe}";
        $sql_paged .= " LIMIT %d OFFSET %d";
        $paged_params = array_merge($params, [$per_page, $offset]); // Se crea un nuevo array para la consulta paginada

        $this->items = $wpdb->get_results( $wpdb->prepare( $sql_paged, $paged_params ) );

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'VCPL_DEBUG: Paged items query: ' . $wpdb->prepare( $sql_paged, $paged_params ) );
            error_log( 'VCPL_DEBUG: Items found for current page: ' . count($this->items) );
        }
    }

    /**
	 * Display extra navigation elements (filters).
	 * @param string $which Position ('top' or 'bottom').
	 */
	public function extra_tablenav( $which ): void {
		if ( $which === 'top' ) {
            // Este código es mayormente estético y se mantiene igual.
			?>
		<div class="alignleft actions bulkactions">
			<label for="filter_date_before" class="filter_date_label"><?php echo __( 'from', 'vcpl-text-domain' ); ?></label>
			<input type="date" name="filter_date_before" id="filter_date_before" value="<?php echo vcpl_get_var( 'filter_date_before', '' ); ?>">
			<label for="filter_date_after" class="filter_date_label"><?php echo __( 'to', 'vcpl-text-domain' ); ?></label>
			<input type="date" name="filter_date_after" id="filter_date_after" value="<?php echo vcpl_get_var( 'filter_date_after', '' ); ?>">
		</div>
		<div class="alignleft actions bulkactions">
			<select name="filter_vendor" class="js-select2-multiple" id="vcpl_filter_vendor">
                <option value=""><?php _e( 'Select a vendor', 'vcpl-text-domain' ); ?></option>
                <?php
                // Llama a la nueva función optimizada para obtener todos los vendedores
                if ( function_exists( 'vcpl_get_all_vendors' ) ) {
                    $all_vendors    = vcpl_get_all_vendors();
                    $current_vendor = vcpl_get_var( 'filter_vendor', '' );

                    if ( ! empty( $all_vendors ) ) {
                        foreach ( $all_vendors as $vendor ) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr( $vendor->ID ),
                                selected( $current_vendor, $vendor->ID, false ),
                                esc_html( $vendor->display_name )
                            );
                        }
                    }
                }
                ?>
			</select>
		</div>
		<div class="alignleft actions bulkactions">
			<select name="filter_status">
				<?php
				echo array_reduce(
					array_keys( wc_get_order_statuses() ),
					fn ( $options, $key ) => $options . sprintf( '<option value="%s" ' . selected( vcpl_get_var( 'filter_status', '' ), $key, false ) . '>%s</option>', $key, wc_get_order_statuses()[ $key ] ),
					'<option value="">' . __( 'Select Status', 'vcpl-text-domain' ) . '</option>'
				);
				?>
			</select>
		</div>
			<?php submit_button( 'Filter', 'action', 'filter_action', false ); ?>
			<?php
		}
	}

    /**
	 * Handles the output for a single column.
	 * @param object $item The current item's data from the database query.
	 * @param string $column_name The name of the column.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
        $order = wc_get_order( $item->ID );
        if ( ! $order ) {
            return __( 'Invalid Order', 'vcpl-text-domain' );
        }
        
        $vendor = $item->vendor_id ? get_userdata($item->vendor_id) : false;

		switch ( $column_name ) {
			case 'vendor':
				return $item->vendor_login ?: __( 'no vendor', 'vcpl-text-domain' );
			case 'customer':
				return $item->customer_login ?: __( 'n/a', 'vcpl-text-domain' );
			case 'order':
				return sprintf(
                    '<a href="%s">#%s</a> (%s)',
                    esc_url( $order->get_edit_order_url() ),
                    $order->get_order_number(),
                    esc_html( wc_get_order_status_name( $order->get_status() ) )
                );
			case 'order_total':
				return $order->get_formatted_order_total();
			case 'commission':
                // FIX: Solucionado el Fatal Error. Se comprueba que $vendor sea un objeto.
                if ($vendor instanceof \WP_User) {
				    return vcpl_get_commission_percent_by_date( $order->get_date_created()->format( 'Y-m-d H:i:s' ), $vendor ) . '%';
                }
                return '0%';
			case 'profit':
                // FIX: Solucionado el Fatal Error. Se comprueba que $vendor sea un objeto.
                if ($vendor instanceof \WP_User) {
				    return wc_price( vcpl_get_my_commission_amount( $vendor, $order ) );
                }
                return wc_price(0);
            default:
                return print_r( $item, true ); // Para depuración de otras columnas
		}
	}

    /**
	 * Defines sortable columns.
	 * @return array
	 */
	public function get_sortable_columns(): array {
        // Mapeamos nuestras columnas a las columnas de la base de datos para ordenar.
		return array(
            'order'       => array( 'ID', false ),
            'order_total' => array( 'order_total', false ),
        );
	}

    /**
	 * Defines the columns that are going to be used in the table
	 * @return array
	 */
	public function get_columns(): array {
		return array(
			'vendor'      => __( 'Vendor', 'vcpl-text-domain' ),
			'customer'    => __( 'Customer', 'vcpl-text-domain' ),
			'order'       => __( 'Order', 'vcpl-text-domain' ),
			'order_total' => __( 'Order Total', 'vcpl-text-domain' ),
			'commission'  => __( 'Commission (%)', 'vcpl-text-domain' ),
			'profit'      => __( 'Profit', 'vcpl-text-domain' ),
		);
	}

    /**
	 * Defines hidden columns.
	 * @return array
	 */
	public function get_hidden_columns(): array {
		return array();
	}

    /**
	 * Displays the table rows. Overridden to add a custom total row.
	 */
	public function display_rows() {
		// Muestra las filas de la página actual
		parent::display_rows();

		// Muestra la fila de totales calculada en prepare_items
        $columns = $this->get_columns();
        $column_count = count($columns);
		?>
        <tr class="wp-table-total" style="font-weight: bold;">
            <td colspan="<?php echo $column_count - 2; ?>">
                <?php _e( 'Totals for all filtered results:', 'vcpl-text-domain' ); ?>
            </td>
            <td>
                <?php echo wc_price( $this->total_amount ); ?>
            </td>
            <td>
                <?php echo wc_price( $this->total_profit ); ?>
            </td>
        </tr>
        <?php
	}
    
    // Las funciones display_tablenav y search_box se mantienen sin cambios de tu código original.
    protected function display_tablenav( $which ) {
		?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">
		<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
		?>

		<br class="clear" />
	</div>
		<?php
	}

	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
	<p class="search-box">
		<label class="screen-reader-text" for="<?php echo $input_id; ?>"><?php echo $text; ?>:</label>
		<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" />
		<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
	</p>
		<?php
	}
}

