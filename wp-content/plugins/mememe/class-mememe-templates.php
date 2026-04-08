<?php
/**
 * MeMeMe Templates
 *
 * @since  1.0.0
 *
 * @category  WordPress_Plugin
 * @package   MeMeMe
 * @author    Nicola Franchini
 */

/**
 * Options Templates class
 */
class Mememe_Templates {
	/**
	 * Refers to a single instance of this class.
	 *
	 * @var $instance
	 */
	private static $instance = null;

	/**
	 * Option templates per_page
	 *
	 * @var string
	 */
	protected $per_page = 100;

	/**
	 * Option name
	 *
	 * @var string
	 */
	protected $option = 'mememe_options_templates';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  Venomaps_Options A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initializes the class
	 */
	private function __construct() {
	}

	/**
	 * Initiate hooks
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'register_page_options' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'admin_notices', array( $this, 'update_notice' ) );
		add_action( 'cmb2_admin_init', array( $this, 'migrate_templates' ) );
	}

	/**
	 * Remove old settings and update new ones
	 */
	public function migrate_templates() {

		$old_templates = mememe_admin()->get_option( 'mememe_option_templates', false );

		if ( $old_templates && is_array( $old_templates ) ) {
			$converted_old = array_keys( array_filter( $old_templates ) );
			$new_templates = get_option( 'mememe_options_templates', array() );
			$merged = array_unique( array_merge( $converted_old, $new_templates ) );

			update_option( $this->option, $merged );

			$general_settings = get_option( 'mememe_options', false );

			if ( isset( $general_settings['mememe_option_templates'] ) ) {
				unset( $general_settings['mememe_option_templates'] );
				update_option( 'mememe_options', $general_settings );
			}
		}
	}

	/**
	 * Update notice
	 */
	public function update_notice() {
		settings_errors();
	}

	/**
	 * Validate all fields.
	 *
	 * @param string $hook page hook.
	 */
	public function load_scripts( $hook ) {
		// if ( 'mememe_page_mememe_templates' != $hook ) {
		$gethook = get_plugin_page_hook('mememe_templates', 'mememe_options');

		if ( $gethook != $hook ) {
			return;
		}
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
		wp_enqueue_script( 'mememe-admin-templates', plugin_dir_url( __FILE__ ) . 'js/media-template.js', array( 'jquery' ), MEMEME_PLUGIN_VERSION, true );
	}

	/**
	 * Add the options page under Setting Menu.
	 */
	public function add_page() {
		$page_title = __( 'MeMeMe Templates', 'mememe' );
		$menu_title = __( 'Templates', 'mememe' );
		add_submenu_page( 'mememe_options', $page_title, $menu_title, 'manage_options', 'mememe_templates', array( $this, 'display_page' ) );
	}

	/**
	 * Display the options page.
	 */
	public function display_page() {
		?>
		<div class="wrap">
			<h2><?php esc_attr_e( 'MeMeMe Templates', 'mememe' ); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( __FILE__ );
				do_settings_sections( __FILE__ );
				submit_button();
				?>
			</form>
		</div> <!-- /wrap -->
		<?php
	}

	/**
	 * Register admin page options.
	 */
	public function register_page_options() {
		add_settings_section( 'mememe_templates_section', '', array( $this, 'display_section' ), __FILE__ ); // id, title, display cb, page.
		register_setting( __FILE__, $this->option, array( $this, 'validate_options' ) ); // option group, option name, sanitize cb.
	}

	/**
	 * Validate all fields.
	 *
	 * @param array $values posted fields.
	 */
	public function validate_options( $values ) {

		$valid_fields = array();

		$array_values = ! is_array( $values ) ? array_filter( explode( ',', $values ) ) : $values;

		foreach ( $array_values as $value ) {
			$valid_fields[] = esc_attr( $value );
		}
		return apply_filters( 'validate_options', $valid_fields, $values );
	}

	/**
	 * Display Section
	 */
	public function display_section() {

		$get_templates = get_option( $this->option, array() );

		$available_templates = array();
		$available_ids = array();

		// Check if still exist all selected templates.
		foreach ( $get_templates as $key => $id ) {
			$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
			if ( $thumb ) {
				if ( isset( $thumb[0] ) ) {
					$available_templates[] = array(
						'id' => $id,
						'thumb' => $thumb[0],
					);
					$available_ids[] = $id;
				}
			}
		}

		$templates = array_reverse( $available_templates, true );

		$total_pages = 0;
		$this_url = admin_url( 'admin.php?page=mememe_templates' );

		$slice = false;

		$get_current_page = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );

		$current_page = $get_current_page ? $get_current_page : 1;
		$per_page = $this->per_page;

		if ( is_array( $templates ) ) {

			$total_rows = count( $templates );
			$total_pages = ceil( $total_rows / $per_page );

			$current_page = ( $total_rows > 0 ) ? min( $total_pages, $current_page ) : 1;

			$prev_link = $current_page > 1 ? '<a class="prev-page button" href="' . esc_url( add_query_arg( 'paged', ( $current_page - 1 ), $this_url ) ) . '">‹</a> ' : '<span class="button disabled">‹</span> ';
			$next_link = $current_page < $total_pages ? '<a class="next-page button" href="' . esc_url( add_query_arg( 'paged', ( $current_page + 1 ), $this_url ) ) . '">›</a>' : '<span class="button disabled">›</span>';

			$start = $current_page * $per_page - $per_page;

			$slice = array_slice( $templates, $start, $per_page, true );
		}

		$pagination = '<div class="tablenav-pages">';
		$pagination .= '<div class="mememe-template-counter-container"><span class="mememe-template-counter">' . count( $templates ) . '</span> ' . __( 'items', 'mememe' ) . '</div>';

		if ( $total_pages > 1 ) {
			$pagination .= $prev_link;

			for ( $page = 1; $page <= $total_pages; $page++ ) {
				$pagelink = $page != $current_page ? add_query_arg( 'paged', $page, $this_url ) : 'javascript:void(0)';
				$current_link = $page != $current_page ? '<a class="next-page button" href="' . esc_url( $pagelink ) . '">' . esc_attr( $page ) . '</a> ' : '<span class="button disabled">' . esc_attr( $page ) . '</span> ';
				$pagination .= $current_link;
			}
			$pagination .= $next_link;
		}
		$pagination .= '</div>';

		$output = '<div class="tablenav">';
		$output .= '<a href="#" class="mememe_upload_template button">' . __( 'Upload templates', 'mememe' ) . '</a>';
		$output .= '<input type="hidden" class="mememe_all_templates" name="' . $this->option . '" value="' . implode( ',', $available_ids ) . '" readonly>';
		$output .= $pagination;
		$output .= '</div>';

		$output .= '<div class="mememe-templates-container" data-page="' . $current_page . '" data-perpage="' . $per_page . '">';

		if ( is_array( $slice ) ) {
			foreach ( $slice as $template ) {
				$id = $template['id'];
				$thumb = $template['thumb'];
				$output .= '<div class="mememe-template-wrap" data-id="' . $id . '"><div class="mememe-thumb-wrap mememe_upload_template"><img src="' . $thumb . '"></div><a href="#" class="mememe_remove_template"><svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zm-4.146-3.146a.5.5 0 00-.708-.708L8 7.293 4.854 4.146a.5.5 0 10-.708.708L7.293 8l-3.147 3.146a.5.5 0 00.708.708L8 8.707l3.146 3.147a.5.5 0 00.708-.708L8.707 8l3.147-3.146z" clip-rule="evenodd"/></svg></a></div>';
			}
		}

		$output .= '</div>';

		$output .= '<div class="tablenav mememe-template-pagination">';
		if ( count( $templates ) > 0 ) {
			$output .= '<a href="#" class="mememe_upload_template button">' . __( 'Upload templates', 'mememe' ) . '</a>';
		}
		$output .= $pagination;

		$output .= '</div>';

		echo $output; // XSS ok.
	}

} // end class

// Call options.
Mememe_Templates::get_instance();
