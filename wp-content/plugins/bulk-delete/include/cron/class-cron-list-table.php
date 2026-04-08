<?php
/**
 * Table to show cron list.
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Cron
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

class Cron_List_Table extends WP_List_Table { //phpcs:ignore
	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	public function __construct() {
		parent::__construct( array(
				'singular' => 'cron_list', //Singular label
				'plural'   => 'cron_lists', //plural label, also this well be one of the table css class
				'ajax'     => false, //We won't support Ajax for this table
			) );
	}

	/**
	 * Add extra markup in the toolbars before or after the list.
	 *
	 * @param string $which Whether the markup should appear after (bottom) or before (top) the list
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			//The code that goes before the table is here
			echo '<p>';
			esc_html_e( 'This is the list of jobs that are currently scheduled for auto deleting posts in Bulk Delete Plugin.', 'bulk-delete' );
			echo ' <strong>';
			esc_html_e( 'Note: ', 'bulk-delete' );
			echo '</strong>';
			esc_html_e( 'Scheduling is available only in PRO version.', 'bulk-delete' );
			echo '</p>';
		}
	}

	/**
	 * Define the columns that are going to be used in the table.
	 *
	 * @return array Array of columns to use with the table
	 */
	public function get_columns() {
		return array(
			'col_cron_due'      => __( 'Next Due', 'bulk-delete' ),
			'col_cron_schedule' => __( 'Schedule', 'bulk-delete' ),
			'col_cron_type'     => __( 'Type', 'bulk-delete' ),
			'col_cron_options'  => __( 'Options', 'bulk-delete' ),
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on.
	 *
	 * @return array Array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		return array(
			'col_cron_type' => array( 'cron_type', true ),
		);
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements.
	 */
	public function prepare_items() {
		$cron_items = BD_Util::get_cron_schedules();
		$totalitems = count( $cron_items );

		//How many to display per page?
		$perpage = 50;

		//How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			) );

		//The pagination links are automatically built according to those parameters

		/* — Register the Columns — */
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $cron_items;
	}

	/**
	 * Display cron due date column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_col_cron_due( $item ) {
		//Build row actions
        $page = sanitize_text_field(wp_unslash($_REQUEST['page'] ?? '')); //phpcs:ignore
		$actions = array(
			'delete'    => sprintf( '<a href="?page=%s&bd_action=%s&cron_id=%s&%s=%s">%s</a>',
				$page,
				'delete_cron',
				$item['id'],
				'bd-delete_cron-nonce',
				wp_create_nonce( 'bd-delete_cron' ),
				__( 'Delete', 'bulk-delete' )
			),
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(%2$s)</span>%3$s',
			/*$1%s*/ $item['due'],
			/*$2%s*/ ( $item['timestamp'] + get_option( 'gmt_offset' ) * 60 * 60 ),
			/*$3%s*/ $this->row_actions( $actions )
		);
	}

	/**
	 * Display cron schedule column.
	 *
	 * @param array $item
	 */
	public function column_col_cron_schedule( $item ) {
		echo esc_html($item['schedule']);
	}

	/**
	 * Display cron type column.
	 *
	 * @param array $item
	 */
	public function column_col_cron_type( $item ) {
		echo esc_html($item['type']);
	}

	/**
	 * Display cron options column.
	 *
	 * @param array $item
	 */
	public function column_col_cron_options( $item ) {
		// TODO: Make it pretty
		print_r( $item['args'] ); //phpcs:ignore
	}

	public function no_items() {
		esc_html_e( 'You have not scheduled any bulk delete jobs.', 'bulk-delete' );
	}
}
