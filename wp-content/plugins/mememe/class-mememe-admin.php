<?php
/**
 * Settings panel
 *
 * @since  1.0.0
 *
 * @category  WordPress_Plugin
 * @package   MeMeMe
 * @author    Nicola Franchini
 */

if ( ! class_exists( 'MeMeMe_Admin', false ) ) {

	/**
	 * MeMeme Theme Options
	 */
	class MeMeMe_Admin {
		/**
		 * Holds the name of the shortcode tag
		 *
		 * @var string
		 */
		public $shortcode_tag = 'mememe_shortcode';
		/**
		 * Option key, and option page slug
		 *
		 * @var string
		 */
		protected $key = 'mememe_options';
		/**
		 * Options page metabox id
		 *
		 * @var string
		 */
		protected $metabox_id = 'mememe_option_metabox';
		/**
		 * Options Page hook
		 *
		 * @var string
		 */
		protected $options_page = 'mememe_options';
		/**
		 * Holds an instance of the object
		 *
		 * @var MeMeMe_Admin
		 */
		protected static $instance = null;
		/**
		 * Returns the running object
		 *
		 * @return MeMeMe_Admin
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
				self::$instance->hooks();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		protected function __construct() {
			$this->title = __( 'MeMeMe Settings', 'mememe' );
			$this->menu_title = __( 'MeMeMe', 'mememe' );
		}

		/**
		 * Initiate hooks
		 */
		public function hooks() {
			add_action( 'admin_init', array( $this, 'init' ) );
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
			add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mememe_enqueue' ) );
			add_action( 'cmb2_render_mememe_text_number', array( $this, 'render_text_number' ), 10, 5 );
			add_action( 'cmb2_render_mememe_thumb_size', array( $this, 'render_thumb_size' ), 10, 5 );
			add_action( 'wp_ajax_mememe_get_google_font_lib', array( $this, 'get_google_font_lib' ) );
			add_action( 'admin_head', array( $this, 'init_shortcode_button' ) );
			add_action( 'update_option_mememe_options', array( $this, 'check_license' ), 10, 2 );
			add_filter( 'manage_mememe_posts_columns', array( $this, 'add_img_column' ) );
			add_filter( 'manage_mememe_posts_custom_column', array( $this, 'manage_img_column' ), 10, 2 );
			add_action( 'before_delete_post', array( $this, 'remove_attachment_with_post' ), 10 );
		}

		/**
		 * Register setting to WP
		 */
		public function init() {
			register_setting( $this->key, $this->key );
		}

		/**
		 * Check purchase code
		 *
		 * @param  arr $old_value The old value.
		 * @param  arr $new_value The new changed value.
		 * @return void
		 */
		public function check_license( $old_value, $new_value ) {
			$oldkey = isset( $old_value['mememe_option_license_key'] ) ? $old_value['mememe_option_license_key'] : false;
			$newkey = isset( $new_value['mememe_option_license_key'] ) ? $new_value['mememe_option_license_key'] : false;

			// Notify about updates at first option save.
			if ( ! $old_value && ! $newkey ) {
				add_settings_error( $this->key . '-notices', '', __( 'Enter a Licence to activate the automatic updates.', 'mememe' ), 'updated' );
			}
			// Check license if changes.
			if ( $oldkey !== $newkey ) {
				if ( $newkey ) {

					$plugin_meta = mememe_plugin()->update_checker->requestUpdate();
					if ( $plugin_meta ) {
						// Set wrong code option if fails.
						if ( ! $plugin_meta->download_url ) {
							add_settings_error( $this->key . '-notices', '', __( 'Invalid Licence, automatic updates are disabled.', 'mememe' ), 'error' );
							$new_value['mememe_option_wrong_code'] = 1;
						} else {
							$new_value['mememe_option_wrong_code'] = 0;
							add_settings_error( $this->key . '-notices', '', __( 'Valid Licence, automatic updates are active.', 'mememe' ), 'updated' );
						}
						update_option( 'mememe_options', $new_value );
					}
				}
			}
		}

		/**
		 * Setup the new imagecolumn
		 *
		 * @param arr $columns The columns.
		 */
		public function add_img_column( $columns ) {
			$newcols = array_merge( $columns, array( 'mememe-thumb' => '<span class="dashicons dashicons-format-image"></span><span class="screen-reader-text">' . __( 'Thumbnail', 'mememe' ) . '</span></span>' ) );
			return $newcols;
		}

		/**
		 * Place the image preview
		 *
		 * @param str $column_name The column to edit.
		 * @param int $post_id The post to edit.
		 */
		public function manage_img_column( $column_name, $post_id ) {
			if ( 'mememe-thumb' === $column_name ) {
				echo get_the_post_thumbnail( $post_id, array( 50, 50 ) );
			}
			return $column_name;
		}

		/**
		 * Delete thumb associated
		 *
		 * @param int $post_id The post id.
		 */
		public function remove_attachment_with_post( $post_id ) {

			// We check if the global post type isn't ours and just return.
			global $post_type;
			if ( 'mememe' !== $post_type ) {
				return;
			}
			if ( has_post_thumbnail( $post_id ) ) {
				$attachment_id = get_post_thumbnail_id( $post_id );
				wp_delete_attachment( $attachment_id, true );
			}
		}

		/**
		 * Add menu options page
		 */
		public function add_options_page() {

			$this->options_page = add_menu_page(
				$this->title,
				$this->menu_title,
				'manage_options',
				$this->key,
				array( $this, 'admin_page_display' ),
				'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10,15.4a5,5,0,0,0,1.11-.13v-.44l.46-.16.25-.09c.4-.13.42-.16.45-.22s0-.1-.16-.48l0-.08a4,4,0,0,1-2.07.6,4,4,0,0,1-4-4H5A5,5,0,0,0,10,15.4Z"/><path d="M14.41,19.51h-.06a1.57,1.57,0,0,0-.43.18l-.23.11-.43.21-1.47-1.47.21-.44a1.63,1.63,0,0,1,.11-.23.33.33,0,0,1,0-.06H2.2V2.2H17.7v10l.21-.1.23-.12.44-.2L20,13.16V0H0V20H14.64v0C14.5,19.56,14.47,19.54,14.41,19.51Z"/><path d="M19.56,17.38c0,.06,0,.1.16.48l.12.23.16.34V17.16C19.62,17.29,19.59,17.31,19.56,17.38Z"/><path d="M19.56,14.36c0,.06.06.09.44.22V13.3l-.16.34-.12.24C19.54,14.26,19.54,14.29,19.56,14.36Z"/><path d="M18.14,19.79l-.23-.12a1.83,1.83,0,0,0-.43-.17h-.05c-.07,0-.09,0-.23.46v0H20V18.57L18.58,20Z"/><path d="M14.2,7.3a1.3,1.3,0,1,0-1.3,1.3A1.32,1.32,0,0,0,14.2,7.3Z"/><path d="M7.1,8.6A1.3,1.3,0,1,0,5.8,7.3,1.32,1.32,0,0,0,7.1,8.6Z"/><path d="M14.76,11.75l.17-.69A4.76,4.76,0,0,0,15,10.4H14a3.92,3.92,0,0,1-.31,1.54h0l.24.12a1.77,1.77,0,0,0,.42.18,1.46,1.46,0,0,0,.19-.16"/><path d="M18.93,14.62h0c-.18-.44,0-.73.29-1.27l-.78-.79c-.54.26-.84.48-1.28.29h0c-.43-.18-.49-.54-.69-1.1H15.36c-.2.56-.25.92-.69,1.1h0c-.44.19-.73,0-1.27-.29l-.79.79c.26.54.48.83.3,1.27s-.55.49-1.11.69v1.11c.56.2.92.26,1.11.69s0,.75-.3,1.27l.79.79c.53-.26.83-.47,1.27-.29h0c.44.18.49.54.69,1.1h1.11c.2-.56.26-.92.7-1.1h0c.43-.18.72,0,1.27.29l.78-.79c-.26-.53-.47-.83-.29-1.27s.54-.49,1.1-.69V15.31C19.47,15.11,19.11,15.05,18.93,14.62Zm-3,2.62a1.38,1.38,0,1,1,1.37-1.37A1.38,1.38,0,0,1,15.92,17.24Z"/></svg>' )
			);

			$this->options_page = add_submenu_page(
				$this->key,
				$this->title,
				__( 'Settings', 'mememe' ),
				'manage_options',
				$this->key,
				array( $this, 'admin_page_display' )
			);

			// Include CMB CSS in the head to avoid FOUC.
			add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		}

		/**
		 * Admin page markup.
		 */
		public function admin_page_display() {
			?>
			<div class="wrap cmb2-options-page <?php echo esc_html( $this->key ); ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
			</div>
			<?php
		}

		/**
		 * Register input type text_number
		 *
		 * @param  object $field             The CMB2_Field type object.
		 * @param  str    $escaped_value     The saved (and escaped) value.
		 * @param  int    $object_id         The current post ID.
		 * @param  str    $object_type       The current object type.
		 * @param  object $field_type_object The CMB2_Types object.
		 * @return void
		 */
		public function render_text_number( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			echo $field_type_object->input( // XSS ok.
				array(
					'class' => 'cmb2-text-small',
					'type' => 'number',
				)
			);
		}

		/**
		 * Register input type text_number
		 *
		 * @param  object $field             The CMB2_Field type object.
		 * @param  str    $escaped_value     The saved (and escaped) value.
		 * @param  int    $object_id         The current post ID.
		 * @param  str    $object_type       The current object type.
		 * @param  object $field_type        The CMB2_Types object.
		 * @return void
		 */
		public function render_thumb_size( $field, $escaped_value, $object_id, $object_type, $field_type ) {

			// Make sure we specify each part of the value we need.
			$value = wp_parse_args(
				$escaped_value,
				array(
					'thumb_w' => 300,
					'thumb_h' => 225,
				)
			);
			?>
			<div class="alignleft">
				<label for="<?php echo esc_attr( $field_type->_id( '_thumb_w' ) ); ?>">
					<?php echo esc_html__( 'Width', 'mememe' ); ?>
				</label>
				<?php
				echo $field_type->input( // XSS ok.
					array(
						'name'  => esc_attr( $field_type->_name( '[thumb_w]' ) ),
						'id'    => esc_attr( $field_type->_id( '_thumb_w' ) ),
						'class' => 'small-text',
						'value' => esc_attr( $escaped_value['thumb_w'] ),
						'type'  => 'number',
						'desc'  => '',
					)
				);
				?>
			</div>
			<div class="alignleft">
				<label for="<?php echo esc_attr( $field_type->_id( '_thumb_h' ) ); ?>'">
					<?php echo esc_html__( 'Height', 'mememe' ); ?>
				 </label>
				<?php
				echo $field_type->input( // XSS ok.
					array(
						'name'  => esc_attr( $field_type->_name( '[thumb_h]' ) ),
						'id'    => esc_attr( $field_type->_id( '_thumb_h' ) ),
						'class' => 'small-text',
						'value' => esc_attr( $escaped_value['thumb_h'] ),
						'type'  => 'number',
						'desc'  => '',
					)
				);
				?>
			</div>
			<?php
			echo $field_type->_desc( true ); // XSS ok.
		}

		/**
		 * Handles escaping for the initial text box width
		 *
		 * @param  mixed      $value      The unescaped value from the database.
		 * @param  array      $field_args Array of field arguments.
		 * @param  CMB2_Field $field      The field object.
		 *
		 * @return mixed                  Escaped value to be displayed.
		 */
		public function escape_range( $value, $field_args, $field ) {

			if ( ! is_numeric( $value ) || ! isset( $value ) ) {
				$escaped_value = $field_args['default'];
			} else {
				$escaped_value = absint( $value );
			}

			if ( is_numeric( $escaped_value ) && $escaped_value > $field_args['attributes']['max'] ) {
				$escaped_value = $field_args['attributes']['max'];
			}

			if ( is_numeric( $escaped_value ) && $escaped_value < $field_args['attributes']['min'] ) {
				$escaped_value = $field_args['attributes']['min'];
			}
			return $escaped_value;
		}

		/**
		 * Add the options metabox to the array of metaboxes
		 */
		public function add_options_page_metabox() {

			$prefix = 'mememe_option_';

			// Hook in our save notices.
			add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

			$cmb = new_cmb2_box(
				array(
					'id'         => $this->metabox_id,
					'hookup'     => false,
					'cmb_styles' => false,
					'show_on'    => array(
						// These are important, don't remove.
						'key'   => 'options-page',
						'value' => array( $this->key ),
					),
				)
			);

			/*
			* Tools
			*/
			$cmb->add_field(
				array(
					'name' => __( 'Generator', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'generator_title',
					'classes' => 'tabme',
				)
			);

			/*
			* Tools
			*/
			$cmb->add_field(
				array(
					'name' => __( 'Tools', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'tools_title',
				)
			);

			/*
			* General
			*/
			$cmb->add_field(
				array(
					'name' => __( 'Default mode', 'mememe' ),
					'id'   => $prefix . 'mode',
					'type'    => 'radio_inline',
					'options' => array(
						'text' => __( 'Text box', 'mememe' ),
						'hand'   => __( 'Free draw', 'mememe' ),
					),
					'default' => 'text',
				)
			);

			/*
			* Inline tools
			*/
			$cmb->add_field(
				array(
					'name' => __( 'Show inline tools', 'mememe' ),
					'id'   => $prefix . 'show_tools',
					'type'    => 'checkbox',
					'default' => 0,
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Select Categories', 'mememe' ),
					'id'   => $prefix . 'select_category',
					'type'    => 'select',
					'options' => array(
						'none' => __( 'None', 'mememe' ),
						'taxonomy_select'   => __( 'Dropdown select', 'mememe' ),
						'taxonomy_multicheck_inline'   => __( 'Multiple checkbox', 'mememe' ),
					),
					'default' => 'none',
				)
			);

			// Meme categories.
			$meme_cats = get_categories(
				array(
					'taxonomy' => 'mememe_category',
					'hide_empty' => false,
				)
			);
			$list_cats = array( '' => '--' );

			foreach ( $meme_cats as $key => $cat ) {
				$list_cats[ $cat->slug ] = $cat->name;
			}

			$cmb->add_field(
				array(
					'name' => __( 'Default Category', 'mememe' ),
					'id'   => $prefix . 'default_category',
					'type'    => 'select',
					'options' => $list_cats,
					'default' => '',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Uploader', 'mememe' ),
					'desc' => __( 'Allow Guests to upload new images', 'mememe' ),
					'id'   => $prefix . 'uploader',
					'type' => 'checkbox',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Buttons background', 'mememe' ),
					'id'      => $prefix . 'btnbg',
					'type'    => 'colorpicker',
					'default' => '#D7DADA',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Buttons color', 'mememe' ),
					'id'      => $prefix . 'btncolor',
					'type'    => 'colorpicker',
					'default' => '#333333',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Spacer', 'mememe' ),
					'desc' => __( 'Add a spacer above or below the image', 'mememe' ),
					'id'   => $prefix . 'spacer',
					'type' => 'checkbox',
				)
			);

			/*
			 * Canvas
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Canvas', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'canvas_title',
				)
			);
			$cmb->add_field(
				array(
					'name'    => __( 'Background', 'mememe' ),
					'desc'    => __( 'Default canvas background color', 'mememe' ),
					'id'      => $prefix . 'bgcolor',
					'type'    => 'colorpicker',
					'default' => '#EBEEEE',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Color', 'mememe' ),
					'desc'    => __( 'Default color for text and draw', 'mememe' ),
					'id'      => $prefix . 'color',
					'type'    => 'colorpicker',
					'default' => '#F2FFFF',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Outline', 'mememe' ),
					'desc'    => __( 'Default color for text shadow and outline', 'mememe' ),
					'id'      => $prefix . 'outcolor',
					'type'    => 'colorpicker',
					'default' => '#000000',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Random template', 'mememe' ),
					'desc' => __( 'Load a random template as default', 'mememe' ),
					'id'   => $prefix . 'random',
					'type' => 'checkbox',
				)
			);

			/*
			 * Title
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Title', 'mememe' ),
					'desc' => __( 'If the Title field of the generator is empty, the plugin will use the values from the text boxes (if any), or the Default Title', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'default_title_title',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Default Title', 'mememe' ),
					'id'   => $prefix . 'meme_title',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => __( 'Meme', 'mememe' ),
					),
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Hide title field', 'mememe' ),
					// 'desc' => __( 'The plugin will use the text from the text boxes (if any), or the Default Title', 'mememe' ),
					'id'   => $prefix . 'hide_title',
					'type'    => 'checkbox',
					'default' => 0,
				)
			);

			/*
			 * Text Boxes
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Text boxes', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'text_boxes_title',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Position', 'mememe' ),
					'desc' => __( 'Default position for the first text box', 'mememe' ),
					'id'   => $prefix . 'text_box_position',
					'type'    => 'select',
					'options' => array(
						'top-left'      => __( 'Top Left', 'mememe' ),
						'top-center'    => __( 'Top Center', 'mememe' ),
						'top-right'     => __( 'Top Right', 'mememe' ),
						'center-left'   => __( 'Center Left', 'mememe' ),
						'center-center' => __( 'Center Center', 'mememe' ),
						'center-right'  => __( 'Center Right', 'mememe' ),
						'bottom-left'   => __( 'Bottom Left', 'mememe' ),
						'bottom-center' => __( 'Bottom Center', 'mememe' ),
						'bottom-right'  => __( 'Bottom Right', 'mememe' ),
					),
					'default' => 'top-center',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Initial Textboxes', 'mememe' ),
					'desc' => __( 'Initial number of text boxes', 'mememe' ),
					'id'   => $prefix . 'text_box_num',
					'type'    => 'select',
					'options' => array(
						1 => 1,
						2 => 2,
					),
					'default' => 1,
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Initial width ( % )', 'mememe' ),
					'desc' => __( 'Relative to the canvas. min: 10, max: 100', 'mememe' ),
					'id'   => $prefix . 'text_box_width',
					'type' => 'text',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => 10,
						'max' => 100,
					),
					'sanitization_cb' => 'absint',
					'escape_cb'   => array( $this, 'escape_range' ),
					'default' => 70,
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Outline / Shadow', 'mememe' ),
					'desc' => __( 'Default Text outline / shadow. min: 0, max: 9', 'mememe' ),
					'id'   => $prefix . 'outline',
					'type'    => 'text',
					'attributes' => array(
						'type' => 'number',
						'min' => 0,
						'max' => 9,
					),
					'sanitization_cb' => 'absint',
					'escape_cb'   => array( $this, 'escape_range' ),
					'default' => 2,
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Text Placeholder', 'mememe' ),
					'id'   => $prefix . 'placeholder',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => __( 'Click here to enter text', 'mememe' ),
					),
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Initial Text', 'mememe' ),
					'id'   => $prefix . 'textinit',
					'type' => 'text',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Text direction', 'mememe' ),
					'id'   => $prefix . 'text_direction',
					'type'    => 'select',
					'options' => array(
						'LTR' => __( 'Left To Right', 'mememe' ),
						'RTL' => __( 'Right To Left', 'mememe' ),
					),
					'default' => 'LTR',
				)
			);

			/*
			* Free Draw
			*/
			$cmb->add_field(
				array(
					'name' => __( 'Free drawing', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'free_drawing_title',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Stroke', 'mememe' ),
					'desc' => __( 'Default Stroke width', 'mememe' ),
					'id'   => $prefix . 'stroke',
					'type'    => 'select',
					'options' => array(
						2 => '2px',
						4 => '4px',
						6 => '6px',
						8 => '8px',
						12 => '12px',
						16 => '16px',
					),
					'default' => 4,
				)
			);

			/*
			 * Final Meme
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Generated Memes', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'final_meme_title',
					'classes' => 'tabme',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Post slug', 'mememe' ),
					'id'   => $prefix . 'slug',
					'desc' => __( 'Remember to update your <a href="options-permalink.php">Permalinks</a> if you change this value', 'mememe' ),
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => __( 'mememe', 'mememe' ),
					),
					'default' => 'mememe',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Category slug', 'mememe' ),
					'id'   => $prefix . 'category_slug',
					'desc' => __( 'Post category slug, remember to update your <a href="options-permalink.php">Permalinks</a> if you change this value', 'mememe' ),
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => __( 'category', 'mememe' ),
					),
					'default' => 'category',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Tag slug', 'mememe' ),
					'id'   => $prefix . 'tag_slug',
					'desc' => __( 'Template tag slug, remember to update your <a href="options-permalink.php">Permalinks</a> if you change this value', 'mememe' ),
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => __( 'tag', 'mememe' ),
					),
					'default' => 'tag',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Status', 'mememe' ),
					'id'   => $prefix . 'status',
					'desc' => __( 'Default status for memes generated by anonymous users', 'mememe' ),
					'type'    => 'select',
					'options' => array(
						'pending' => __( 'Pending', 'mememe' ),
						'publish' => __( 'Published', 'mememe' ),
					),
					'default' => 'pending',
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Hide submission message', 'mememe' ),
					'desc' => __( 'Hide message for pending memes after submission', 'mememe' ),
					'id'   => $prefix . 'hide_submission_message',
					'type'    => 'checkbox',
					'default' => 0,
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Image width', 'mememe' ),
					'id'   => $prefix . 'final_size',
					'type' => 'mememe_text_number',
					'default' => 600,
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Thumbnail size', 'mememe' ),
					// 'desc' => __( 'Thumbnail size for listed memes (in pixels)', 'mememe' ),
					'id'   => $prefix . 'thumb_size',
					'type' => 'mememe_thumb_size',
					'default' => array(
						'thumb_w' => 300,
						'thumb_h' => 225,
					),
				)
			);

			/*
			 * Watermark
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Watermark', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'watermark_title',
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Text', 'mememe' ),
					// 'desc' => __( 'Leave blank to disable', 'mememe' ),
					'id'   => $prefix . 'watermark',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => get_bloginfo( 'name' ),
					),
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Image', 'mememe' ),
					// 'desc' => __( 'Leave blank to disable', 'mememe' ),
					'id'   => $prefix . 'watermark_image',
					'type' => 'file',
					'options' => array(
						'url' => false, // Hide the text input for the url.
					),
					'query_args' => array(
						'type' => 'image', // Make library only display images.
					),
					'preview_size' => array( 100, 100 ),
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Position', 'mememe' ),
					'id'   => $prefix . 'watermark_position',
					'type'    => 'select',
					'options' => array(
						'top-left'     => __( 'Top Left', 'mememe' ),
						'top-center'    => __( 'Top Center', 'mememe' ),
						'top-right'    => __( 'Top Right', 'mememe' ),
						'center-left'     => __( 'Center Left', 'mememe' ),
						'center-right'    => __( 'Center Right', 'mememe' ),
						'bottom-left'  => __( 'Bottom Left', 'mememe' ),
						'bottom-center'    => __( 'Bottom Center', 'mememe' ),
						'bottom-right' => __( 'Bottom Right', 'mememe' ),
					),
					'default' => 'bottom-right',
				)
			);

			/*
			 * Socials
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Socials', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'socials_title',
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Description', 'mememe' ),
					'desc' => __( 'This description will be used for shared memes, defaults to site description', 'mememe' ),
					'id'   => $prefix . 'social_description',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => get_bloginfo( 'description' ),
					),
				)
			);
			$cmb->add_field(
				array(
					'name' => 'Social Share',
					'id'   => $prefix . 'social_share',
					'type'    => 'multicheck_inline',
					'options' => array(
						'facebook' => 'FaceBook',
						'twitter' => 'Twitter',
						'linkedin' => 'LinkedIn',
						'pinterest' => 'Pinterest',
						'reddit' => 'Reddit',
						'tumblr' => 'Tumblr',
						'buffer' => 'Buffer',
						'whatsapp' => 'WhatsApp',
						// 'evernote' => 'Evernote',
					),
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Permalink', 'mememe' ),
					'desc' => __( 'Post URL', 'mememe' ),
					'id'   => $prefix . 'social_link',
					'type'    => 'checkbox',
					'default' => 0,
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Query string key', 'mememe' ),
					'desc' => __( 'Optional query string will be added to sharing links', 'mememe' ),
					'id'   => $prefix . 'qs_key',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => 'key',
					),
				)
			);
			$cmb->add_field(
				array(
					'name' => __( 'Query string value', 'mememe' ),
					'desc' => __( 'e.g. www.example.ext/mememe/?key=value ', 'mememe' ),
					'id'   => $prefix . 'qs_value',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => 'value',
					),
				)
			);

			/*
			 * Rating
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Rating', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'meme_rating_title',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Enable meme rating', 'mememe' ),
					'id'   => $prefix . 'meme_rating',
					'type'    => 'checkbox',
					'default' => 0,
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Buttons background', 'mememe' ),
					'id'      => $prefix . 'rating_btnbg',
					'type'    => 'colorpicker',
					'default' => '#D7DADA',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Buttons color', 'mememe' ),
					'id'      => $prefix . 'rating_btncolor',
					'type'    => 'colorpicker',
					'default' => '#333333',
				)
			);

			/*
			 * Report abuse
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Report abuse', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'report_title',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Send report to', 'mememe' ),
					'desc' => __( 'Fill this field to let the users report inappropriate memes', 'mememe' ),
					'id'   => $prefix . 'report',
					'type' => 'text_email',
					'attributes'  => array(
						'placeholder' => get_option( 'admin_email' ),
					),
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'E-mail subject', 'mememe' ),
					'default' => 'Report meme abuse',
					'id'   => $prefix . 'report_mail_title',
					'type' => 'text',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'E-mail text', 'mememe' ),
					'default' => 'I think this meme should be removed',
					'id'   => $prefix . 'report_mail_text',
					'type' => 'textarea',
				)
			);

			/*
			 * Style
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Galleries', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'galleries_title',
					'classes' => 'tabme',
				)
			);

			/*
			 * Skins
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Style', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'skins_title',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Light', 'mememe' ),
					'desc'    => __( 'Default background color for gallery cards', 'mememe' ),
					'id'      => $prefix . 'bg_light',
					'type'    => 'colorpicker',
					'default' => '#FFFFFF',
				)
			);

			$cmb->add_field(
				array(
					'name'    => __( 'Dark', 'mememe' ),
					'desc'    => __( 'Check the Dark style option for Meme or Template galleries, or set "mmm-dark" inside the field "CSS Class" if you are using the Classic Editor', 'mememe' ),
					'id'      => $prefix . 'bg_dark',
					'type'    => 'colorpicker',
					'default' => '#2E2E2E',
				)
			);

			/*
			 * Re-caption
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Re-caption', 'mememe' ),
					'desc' => __( 'Link template gallery and generated memes to the generator', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'recaption_title',
				)
			);

			$cmb->add_field(
				array(
					'name'             => __( 'Destination', 'mememe' ),
					'desc'             => __( 'Set the ID of a destination page or post hosting the generator. Click the magnifing lens to search for content.', 'mememe' ),
					'id'               => $prefix . 'recaption_dest',
					'type'        => 'post_search_text', // This field type.
					'post_type'   => array( 'post', 'page' ),
					'select_type' => 'radio', // Default is 'checkbox'.
					'select_behavior' => 'replace', // Default is 'add'.
				)
			);

			/*
			 * Fonts
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Fonts', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'fonts_title',
					'classes' => 'tabme',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Custom Fonts', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'custom_fonts_title',
				)
			);
			$custom_font_group = $cmb->add_field(
				array(
					'id'          => $prefix . 'custom_font',
					'type'        => 'group',
					'options'     => array(
						'group_title'   => __( 'Custom Font {#}', 'mememe' ), // {#} gets replaced by row number.
						'add_button'    => __( 'Add Another Font', 'mememe' ),
						'remove_button' => __( 'Remove Font', 'mememe' ),
						'sortable'      => true, // Beta.
						'closed'     => true,
					),
				)
			);
			$cmb->add_group_field(
				$custom_font_group,
				array(
					'name' => __( 'Font family', 'mememe' ),
					// Translators: Link to Google Fonts library.
					'desc' => __( 'Specify a font family name available from an external stylesheet, loaded by the theme or any other plugin', 'mememe' ),
					'id'   => 'custom_fonts',
					'type' => 'text',
					'sanitization_cb' => array( $this, 'sanitize_custom_font' ),
				)
			);

			$cmb->add_group_field(
				$custom_font_group,
				array(
					'name' => __( 'Font style', 'mememe' ),
					// Translators: Link to Google Fonts library.
					'id'   => 'custom_fonts_style',
					'type'    => 'select',
					'options' => array(
						'normal' => __( 'Normal', 'mememe' ),
						'italic' => __( 'Italic', 'mememe' ),
					),
					'default' => 'normal',
				)
			);

			$cmb->add_group_field(
				$custom_font_group,
				array(
					'name' => __( 'Font weight', 'mememe' ),
					'id'   => 'custom_fonts_weight',
					'type'    => 'select',

					'options' => array(
						'100' => '100',
						'200' => '200',
						'300' => '300',
						'400' => '400', // (default, also recognized as 'normal')
						'500' => '500',
						'600' => '600',
						'700' => '700', // (also recognized as 'bold')
						'800' => '800',
						'900' => '900',
					),
					'default' => '400',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Google Fonts', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'google_fonts_title',
					// 'classes' => 'tabme',
				)
			);

			$google_link   = '<a target="_blank" href="https://fonts.google.com/">Google Fonts library</a>';
			$google_dev_link   = '<a target="_blank" href="https://developers.google.com/fonts/docs/developer_api#APIKey">API Key</a>';

			$font_group = $cmb->add_field(
				array(
					'id'          => $prefix . 'font',
					'type'        => 'group',
					'options'     => array(
						'group_title'   => __( 'Google Font {#}', 'mememe' ), // {#} gets replaced by row number.
						'add_button'    => __( 'Add Another Font', 'mememe' ),
						'remove_button' => __( 'Remove Font', 'mememe' ),
						'sortable'      => true, // Beta.
						'closed'     => true,
					),
				)
			);

			$cmb->add_group_field(
				$font_group,
				array(
					'name' => __( 'Search font', 'mememe' ),
					// Translators: Link to Google Fonts library.
					'desc' => sprintf( __( 'Select a font from the %s and start typing here its name.', 'mememe' ), $google_link ),
					'id'   => 'google_fonts',
					'type'    => 'text',
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Google API Key', 'mememe' ),
					// Translators: Link to Google Developers api key documentation.
					'desc' => sprintf( __( 'Set here your %s to keep the font library up to date.', 'mememe' ), $google_dev_link ),
					'id'   => $prefix . 'google_api',
					'type' => 'text',
					'sanitization_cb' => array( $this, 'license_key_callback' ),
				)
			);

			/*
			 * License
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Licence', 'mememe' ),
					'type' => 'title',
					'id'   => $prefix . 'license_title',
					'classes' => 'tabme',
				)
			);

			/*
			 * Purchase code
			 */
			$cmb->add_field(
				array(
					'name' => __( 'Purchase code', 'mememe' ),
					// Translators: envato purchase code.
					'desc' => sprintf( esc_html__( 'Enter here your %s to enable the automatic updates', 'mememe' ), '<a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">Envato Purchase Code</a>' ),
					'id' => $prefix . 'license_key',
					'type' => 'text',
					'sanitization_cb' => array( $this, 'license_key_callback' ),
				)
			);
		}

		/**
		 * Sanitize custom font values.
		 *
		 * @param  mixed      $value      The unsanitized value from the form.
		 * @param  array      $field_args Array of field arguments.
		 * @param  CMB2_Field $field      The field object.
		 * @return mixed                  Sanitized value to be stored.
		 */
		public function sanitize_custom_font( $value, $field_args, $field ) {
			$text = trim( filter_var( str_replace( '"', "'", $value ), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES ) );
			return $text;
		}

		/**
		 * Sanitize and remove white spaces around.
		 *
		 * @param  mixed      $value      The unsanitized value from the form.
		 * @param  array      $field_args Array of field arguments.
		 * @param  CMB2_Field $field      The field object.
		 * @return mixed                  Sanitized value to be stored.
		 */
		public function license_key_callback( $value, $field_args, $field ) {
			$code = trim( filter_var( $value, FILTER_SANITIZE_STRING ) );
			return $code;
		}

		/**
		 * Register settings notices for display
		 *
		 * @param int   $object_id Option key.
		 * @param array $updated   Array of updated fields.
		 * @return void
		 */
		public function settings_notices( $object_id, $updated ) {
			if ( $object_id !== $this->key || empty( $updated ) ) {
				return;
			}
			add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'mememe' ), 'updated' );
			settings_errors( $this->key . '-notices' );
		}

		/**
		 * Public getter method for retrieving protected/private variables
		 *
		 * @param str $field Field to retrieve.
		 * @return mixed Field value.
		 * @throws Exception Invalid property.
		 */
		public function __get( $field ) {
			// Allowed fields to retrieve.
			if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
				return $this->{$field};
			}
			throw new Exception( 'Invalid property: ' . $field );
		}

		/**
		 * Wrapper function around cmb2_get_option
		 *
		 * @param str   $key     Options array key.
		 * @param mixed $default Optional default value.
		 * @return mixed         Option value
		 */
		public function get_option( $key = '', $default = false ) {
			if ( function_exists( 'cmb2_get_option' ) ) {
				// Use cmb2_get_option as it passes through some key filters.
				return cmb2_get_option( mememe_admin()->key, $key, $default );
			}
			// Fallback to get_option if CMB2 is not loaded yet.
			$opts = get_option( mememe_admin()->key, $default );
			$val = $default;
			if ( 'all' == $key ) {
				$val = $opts;
			} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
				$val = $opts[ $key ];
			}
			return $val;
		}

		/**
		 * Get the Google font list
		 * called via ajax from admin.js
		 */
		public function get_google_font_lib() {
			$thisapi = $this->get_option( 'mememe_option_google_api', false );
			echo wp_json_encode( $this->mememe_get_google_font( $thisapi ) );
			wp_die(); // Ajax call must die to avoid trailing 0 in your response.
		}

		/**
		 * Save google fonts list
		 *
		 * @param str $key    Google API key key.
		 * @param int $interval Optional default value.
		 * @return array        Fonts list
		 */
		public function mememe_get_google_font( $key = false, $interval = 2419200 ) {

			if ( ! defined( 'FONT_CACHE_INTERVAL' ) ) {
				define( 'FONT_CACHE_INTERVAL', $interval ); // Checking once a week for new Fonts.
			}

			delete_option( 'mememe_plugin_version' );

			// Get cached fields.
			$db_cache_field = 'mememe_googlefont_cache';
			$db_cache_field_last_updated = 'mememe_googlefont_cache_last';

			$api_key = strlen( $key ) > 0 ? $key : false;
			$current_fonts = get_option( $db_cache_field ); // Get current fonts.
			$last = get_option( $db_cache_field_last_updated ); // Get the date for last update.
			$now = time(); // get current timestamp.

			// Check if is the first run, or we must update the db records.
			if ( ( ( $now - $last ) > FONT_CACHE_INTERVAL && $api_key ) || ! $last || '' == $current_fonts || ! $current_fonts ) {

				// No records on db, load the json provided with the plugin.
				if ( ! $last || '' == $current_fonts || ! $current_fonts ) {
					// $curlout = file_get_contents( plugin_dir_url( __FILE__ ) . 'js/googlefonts.json' );
					$curlout = file_get_contents( dirname( __FILE__ ) . '/js/googlefonts.json' );
				}

				// Check every month if api key is set, the updated library from google.
				if ( ( $now - $last ) > FONT_CACHE_INTERVAL && $api_key ) {
					$api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $api_key;
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, $api_url );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					$curlout = curl_exec( $ch );
					curl_close( $ch );
				}

				// Parse the result.
				$font_array = json_decode( $curlout, true );
				$google_font_array = array();

				// Generate the array to store the fonts.
				if ( isset( $font_array['items'] ) ) {
					foreach ( $font_array['items'] as $index => $value ) {
						$_family = str_replace( ' ', '+', $value['family'] );

						foreach ( $value['variants'] as $variant ) {
							$google_font_array[ $_family . '-' . $variant ]['variant'] = $variant;
							$google_font_array[ $_family . '-' . $variant ]['category'] = $value['category'];
							$google_font_array[ $_family . '-' . $variant ]['family'] = $value['family'] . ':' . $variant;
						}
					}
				}

				update_option( $db_cache_field, $google_font_array );
				update_option( $db_cache_field_last_updated, time() );

				// Get the google font array from options DB.
				$google_font_array = get_option( $db_cache_field );

				if ( empty( $google_font_array ) ) {
					if ( $api_key ) {
						$error = __( 'invalid API key', 'mememe' );
					} else {
						$error = '/js/googlefonts.json ' . __( 'not found', 'mememe' );
					}
					$google_font_array['error'] = $error;
				}
			} else {
				// Get the google font array from options DB.
				if ( '' !== $current_fonts ) {
					$google_font_array = $current_fonts;
				}
			}
			return $google_font_array;
		}

		/**
		 * Check if the current page is a edit/new post/page
		 *
		 * @return boolean
		 */
		public function is_edit_page() {
			global $pagenow;
			$screen = get_current_screen();
			$post_type = $screen->post_type;
			// Make sure we are on the backend.
			if ( ! is_admin() || 'mememe' == $post_type ) {
				return false;
			}

			if ( in_array( $post_type, array( 'post', 'page' ) ) ) {
				// Check for either new or edit.
				return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
			}
			return false;
		}

		/**
		 * Shortcode button to tinymce.
		 *
		 * @return void
		 */
		public function init_shortcode_button() {

			if ( ! $this->is_edit_page() ) {
				return;
			}

			// Check if WYSIWYG is enabled.
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
				add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );

				// Get categories list.
				$terms = get_terms( 'mememe_category' );

				if ( ! is_wp_error( $terms ) ) {
					$categories = array();

					foreach ( $terms as $term ) {
						$categories[ $term->slug ] = $term->name;
					}

					$image_sizes = mememe_plugin()->available_thumbs_size();
					$thumb_sizes = array();
					foreach ( $image_sizes as $thumb => $sizes ) {
						$crop = $sizes['crop'] ? __( 'Proportional', 'mememe' ) : '';
						$thumb_sizes[ $thumb ] = $thumb . ' (' . $sizes['width'] . 'x' . $sizes['height'] . ') ' . $crop;
					}
					?>
					<script type='text/javascript'>
					var MEMEMEadmin = {
						'categories': '<?php echo wp_json_encode( $categories ); ?>',
						'per_page_default' : <?php echo esc_attr( get_option( 'posts_per_page' ) ); ?>,
						'thumbsize' : '<?php echo wp_json_encode( $thumb_sizes ); ?>',
						'_random_memes' : '<?php echo esc_html__( 'Display random Memes', 'mememe' ); ?>',
						'_author' : '<?php echo esc_html__( 'Current author memes', 'mememe' ); ?>',
						'_all_categories' : '<?php echo esc_html__( 'All Meme Categories', 'mememe' ); ?>',
						'_category' : '<?php echo esc_html__( 'Category', 'mememe' ); ?>',
						'_columns' : '<?php echo esc_html__( 'Columns', 'mememe' ); ?>',
						'_gallery' : '<?php echo esc_html__( 'Gallery', 'mememe' ); ?>',
						'_generator' : '<?php echo esc_html__( 'Generator', 'mememe' ); ?>',
						'_list_memes' : '<?php echo esc_html__( 'Published Memes gallery', 'mememe' ); ?>',
						'_list_templates' : '<?php echo esc_html__( 'Meme Templates gallery', 'mememe' ); ?>',
						'_margin' : '<?php echo esc_html__( 'Thumbnails margin', 'mememe' ); ?>',
						'_order' : '<?php echo esc_html__( 'Order', 'mememe' ); ?>',
						'_orderby' : '<?php echo esc_html__( 'Order by', 'mememe' ); ?>',
						'_responsive' : '<?php echo esc_html__( 'Responsive', 'mememe' ); ?>',
						'_templates' : '<?php echo esc_html__( 'Templates', 'mememe' ); ?>',
						'_hide_carousel' : '<?php echo esc_html__( 'Hide Templates Carousel', 'mememe' ); ?>',
						'_random_templates' : '<?php echo esc_html__( 'Random templates', 'mememe' ); ?>',
						'_max_templates' : '<?php echo esc_html__( 'Limit carousel items', 'mememe' ); ?>',
						'_posts_per_page' : '<?php echo esc_html__( 'Posts per page', 'mememe' ); ?>',
						'_thumbnail_size' : '<?php echo esc_html__( 'Thumbnail size', 'mememe' ); ?>',
						'_custom_class' : '<?php echo esc_html__( 'CSS Class (optional)', 'mememe' ); ?>',
						'_show_title' : '<?php echo esc_html__( 'Show Title', 'mememe' ); ?>',
						'_autoplay_carousel' : '<?php echo esc_html__( 'Carousel autoplay', 'mememe' ); ?>',
					};
					</script>
					<?php
				}
			}
		}

		/**
		 * Adds our tinymce plugin.
		 *
		 * @param array $plugin_array MCE plugins array.
		 * @return array
		 */
		public function mce_external_plugins( $plugin_array ) {
			$plugin_array[ $this->shortcode_tag ] = plugins_url( 'js/mce-button.js', __FILE__ );
			return $plugin_array;
		}

		/**
		 * Adds our tinymce button
		 *
		 * @param array $buttons Buttons arrat.
		 * @return array $buttons
		 */
		public function mce_buttons( $buttons ) {
			array_push( $buttons, $this->shortcode_tag );
			return $buttons;
		}


		/**
		 * Enqueue admin js
		 *
		 * @param  str $hook The current page.
		 * @return void
		 */
		public function mememe_enqueue( $hook ) {

			$settingshook = get_plugin_page_hook( 'mememe_options', 'toplevel_page' );
			$templateshook = get_plugin_page_hook( 'mememe_templates', 'mememe_options' );

			if ( $settingshook == $hook || $templateshook == $hook ) {
				wp_enqueue_style( 'mememe-admin', plugins_url( 'css/admin.css', __FILE__ ), array(), MEMEME_PLUGIN_VERSION );
			}

			if ( $settingshook == $hook ) {
				wp_enqueue_script( 'mememe-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery', 'jquery-ui-autocomplete', 'jquery-ui-tabs' ), MEMEME_PLUGIN_VERSION, true );
			}

			if ( 'widgets.php' == $hook ) {

				wp_register_script( 'mememe-widgets', plugin_dir_url( __FILE__ ) . 'js/widgets.js', array( 'jquery', 'jquery-ui-autocomplete' ), MEMEME_PLUGIN_VERSION, true );

				$template_tags = get_terms(
					array(
						'taxonomy' => 'mememe_template_tag',
						'hide_empty' => true,
					)
				);
				$available_tags = array();
				if ( ! empty( $template_tags ) && ! is_wp_error( $template_tags ) ) {
					foreach ( $template_tags as $term ) {
						$available_tags[] = $term->slug;
					}
				}

				// Localize vars.
				$widget_vars = array(
					'available_tags' => $available_tags,
				);
				wp_localize_script( 'mememe-widgets', 'MEMEMEwidgets', $widget_vars );
				wp_enqueue_script( 'mememe-widgets' );
			}
		}
	}

	/**
	 * Helper function to get/return the MeMeMe_Admin object
	 *
	 * @return MeMeMe_Admin object
	 */
	function mememe_admin() {
		return MeMeMe_Admin::get_instance();
	}

	// Get it started.
	mememe_admin();
}
