<?php
/**
 * MeMeMe Plugin
 *
 * @since  1.0.0
 *
 * @category  WordPress_Plugin
 * @package   MeMeMe
 * @author    Nicola Franchini
 */

/**
 * Main plugin class
 *
 * @since  1.0.0
 */
class MeMeMe_Plugin {
	/**
	 * Plugin name
	 *
	 * @var slug
	 */
	private $slug = 'mememe';
	/**
	 * Update checker
	 *
	 * @var string
	 */
	public $update_checker = null;
	/**
	 * Update checker
	 *
	 * @var update_url
	 */
	private $update_url = 'https://veno.es/updates/';
	/**
	 * Holds an instance of the object
	 *
	 * @var MeMeMe_Plugin
	 */
	protected static $instance = null;
	/**
	 * Returns the running object
	 *
	 * @return MeMeMe_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Init plugin
	 */
	public function __construct() {
		require_once dirname( __FILE__ ) . '/lib/cmb2/init.php';
		require_once dirname( __FILE__ ) . '/lib/cmb2/cmb2_post_search_field.php';

		require_once dirname( __FILE__ ) . '/class-mememe-widget.php';
		require_once dirname( __FILE__ ) . '/lib/puc/plugin-update-checker.php';

		$this->update_checker = Puc_v4_Factory::buildUpdateChecker(
			$this->update_url . '?action=get_metadata&slug=' . $this->slug, // Metadata URL.
			dirname( __FILE__ ) . '/' . $this->slug . '.php', // Full path to the main plugin file.
			$this->slug // Plugin slug.
		);
		require_once dirname( __FILE__ ) . '/class-mememe-admin.php';
		require_once dirname( __FILE__ ) . '/class-mememe-templates.php';
	}

	/**
	 * Initiate hooks
	 */
	public function hooks() {

		// add_action( 'cmb2_init', array( $this, 'mememe_form_register' ) );
		// Custom posts.
		add_action( 'init', array( $this, 'mememe_register_cpt' ) );
		register_activation_hook( __DIR__ . '/' . $this->slug . '.php', array( $this, 'mememe_rewrite_flush' ) );
		// Count tags on non attached images.
		add_action( 'init', array( $this, 'count_attachments_tags' ) );
		// Load Scripts and CSS.
		add_action( 'init', array( $this, 'mememe_register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'mememe_scripts' ) );
		// Shortcodes.
		add_shortcode( 'mememe', array( $this, 'mememe_do_shortcode' ) );
		add_shortcode( 'mememe-list', array( $this, 'mememe_list_do_shortcode' ) );
		add_shortcode( 'mememe-templates', array( $this, 'mememe_templates_do_shortcode' ) );
		// Process Meme.
		add_action( 'wp_ajax_mememe_process', array( $this, 'mememe_handle_submission' ) );
		add_action( 'wp_ajax_nopriv_mememe_process', array( $this, 'mememe_handle_submission' ) );
		// Widgets.
		add_action( 'widgets_init', array( $this, 'mememe_register_widgets' ) );
		// Plugin text domain.
		add_action( 'plugins_loaded', array( $this, 'mememe_load_plugin_textdomain' ) );
		// Add recaption button under cpt mememe content.
		add_filter( 'the_content', array( $this, 'mememe_add_ons' ) );

		// Add settings link to plugin list.
		add_filter( 'plugin_action_links_' . plugin_basename( __DIR__ . '/' . $this->slug . '.php' ), array( $this, 'mememe_add_settings_link' ) );
		// Load more posts.
		add_action( 'wp_ajax_mememe_loadmore', array( $this, 'mememe_handle_loadmore' ) ); // Called from js: wp_ajax_{action}.
		add_action( 'wp_ajax_nopriv_mememe_loadmore', array( $this, 'mememe_handle_loadmore' ) ); // Called from js: wp_ajax_nopriv_{action}.
		// Add mememe post type to search results.
		// add_action( 'pre_get_posts', array( $this, 'mememe_search' ) );
		// Set og:tags for single meme.
		add_action( 'wp_head', array( $this, 'set_og_tags' ) );
		// Check for plugin updates.
		add_action( 'init', array( $this, 'check_updates' ) );
		// Enable Gutenberg blocks.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

		// Add Rating system under cpt mememe content.
		if ( mememe_admin()->get_option( 'mememe_option_meme_rating', false ) ) {
			add_action( 'wp_ajax_nopriv_mememe-post-like', array( $this, 'post_like' ) );
			add_action( 'wp_ajax_mememe-post-like', array( $this, 'post_like' ) );
		}
		// Get img src from attachment ID.
		add_action( 'wp_ajax_mememe-get-template-link', array( $this, 'get_template_img' ) );
		add_action( 'wp_ajax_nopriv_mememe-get-template-link', array( $this, 'get_template_img' ) );
	}

	/**
	 * Check if this ip has already voted
	 *
	 * @return voted array
	 */
	public function has_voted() {

		if ( is_singular( 'mememe' ) && in_the_loop() && is_main_query() ) {

			$post_id = get_the_ID();
			$ip = $this->get_the_user_ip();

			$meta_ip = get_post_meta( $post_id, 'voted_ip' );
			$voted_ip = isset( $meta_ip[0] ) ? $meta_ip[0] : false;

			if ( $voted_ip ) {
				// Check if user already voted.
				if ( in_array( $ip, array_keys( $voted_ip ) ) ) {
					return $voted_ip[ $ip ];
				}
			}
		}
		return false;
	}

	/**
	 * Get user IP
	 *
	 * @return user ip
	 */
	public function get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// Check ip from share internet.
			$ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_STRING );
			// $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// to check ip is pass from proxy.
			$ip = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_STRING );
			// $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			// $ip = $_SERVER['REMOTE_ADDR'];
			$ip = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING );
		}
		return $ip;
	}

	/**
	 * Vote the meme
	 *
	 * @return void
	 */
	public function post_like() {
		// Check for nonce security.
		$nonce = filter_input( INPUT_POST, 'mememe_nonce', FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, 'mememe-ajax-nonce' ) ) {
			wp_die( 'Not allowed' );
			exit;
		}

		if ( isset( $_POST['post_like'] ) ) {
			$thisdate = time();
			// Retrieve user IP address.
			$ip = $this->get_the_user_ip();
			$post_id = filter_input( INPUT_POST, 'post_id', FILTER_VALIDATE_INT );
			$vote = filter_input( INPUT_POST, 'vote', FILTER_SANITIZE_STRING );

			// Get voters'IPs for the current post.
			$meta_ip = get_post_meta( $post_id, 'voted_ip' );
			$voted_ip = $meta_ip[0];

			// Get votes count for the current post.
			$meta_count_up = get_post_meta( $post_id, 'votes_count_up', true );
			$meta_count_down = get_post_meta( $post_id, 'votes_count_down', true );
			$meta_count_up = ! empty( $meta_count_up ) ? $meta_count_up : '0';
			$meta_count_down = ! empty( $meta_count_down ) ? $meta_count_down : '0';

			// Check if user already voted.
			if ( in_array( $ip, array_keys( $voted_ip ) ) ) {

				// Remove old vote if clicking the same.
				if ( $voted_ip[ $ip ]['vote'] == $vote ) {

					unset( $voted_ip[ $ip ] );

					if ( 'up' == $vote ) {
						--$meta_count_up;
					}

					if ( 'down' == $vote ) {
						--$meta_count_down;
					}
				} else {
					// Change old vote to new vote if changing option.
					if ( 'up' == $vote ) {
						++$meta_count_up;
						--$meta_count_down;
					}

					if ( 'down' == $vote ) {
						++$meta_count_down;
						--$meta_count_up;
					}

					$voted_ip[ $ip ] = array(
						'vote' => $vote,
						'date' => $thisdate,
					);
				}
			} else {
				// First time he votes.
				$voted_ip[ $ip ] = array(
					'vote' => $vote,
					'date' => $thisdate,
				);

				if ( 'up' == $vote ) {
					++$meta_count_up;
				}

				if ( 'down' == $vote ) {
					++$meta_count_down;
				}
			}

			$meta_count_up = ( $meta_count_up < 0 ) ? 0 : $meta_count_up;
			$meta_count_down = ( $meta_count_down < 0 ) ? 0 : $meta_count_down;

			// Save IP and increase votes count.
			update_post_meta( $post_id, 'votes_count_up', $meta_count_up );
			update_post_meta( $post_id, 'votes_count_down', $meta_count_down );
			update_post_meta( $post_id, 'voted_ip', $voted_ip );

			$meta_count_array = array();

			$meta_count_array['up'] = $meta_count_up;
			$meta_count_array['down'] = $meta_count_down;

			echo wp_json_encode( $meta_count_array );
		}
		exit;
	}

	/**
	 * GUTENBERG !
	 * Register block editor assets
	 */
	public function enqueue_block_editor_assets() {

		// Get categories list.
		$terms = get_terms( 'mememe_category' );

		if ( ! is_wp_error( $terms ) ) {
			$categories = array();

			foreach ( $terms as $term ) {
				$categories[ $term->slug ] = $term->name;
			}

			$image_sizes = $this->available_thumbs_size();
			$thumb_sizes = array();
			foreach ( $image_sizes as $thumb => $sizes ) {
				$crop = $sizes['crop'] ? __( 'Cropped', 'mememe' ) : '';
				$thumb_sizes[ $thumb ] = $thumb . ' (' . $sizes['width'] . 'x' . $sizes['height'] . ') ' . $crop;
			}
			wp_register_script(
				'mememe-block',
				plugins_url( 'gutenberg/block.js', __FILE__ ),
				array(
					'wp-blocks',
					'wp-element',
					'wp-block-editor',
					'wp-components',
				),
				MEMEME_PLUGIN_VERSION,
				true
			);

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

			$templates = array_reverse( array_filter( get_option( 'mememe_options_templates', array() ) ) );

			$templist = array();
			foreach ( $templates as $key ) {
				$templist[ $key ] = get_post_field( 'post_name', $key );
			}
			// Localize vars.
			$translation_array = array(
				'templates' => wp_json_encode( $templist ),
				'categories' => wp_json_encode( $categories ),
				'per_page_default' => esc_attr( get_option( 'posts_per_page' ) ),
				'thumbsize' => wp_json_encode( $thumb_sizes ),
				'_author' => __( 'Current author memes', 'mememe' ),
				'_random_memes' => __( 'Display random Memes', 'mememe' ),
				'_all_categories' => __( 'All Meme Categories', 'mememe' ),
				'_category' => __( 'Category', 'mememe' ),
				'_columns' => __( 'Columns', 'mememe' ),
				'_default_template' => __( 'Default template (Attachment ID)', 'mememe' ),
				'_gallery' => __( 'Gallery', 'mememe' ),
				'_generator' => __( 'Generator', 'mememe' ),
				'_list_memes' => __( 'Published Memes gallery', 'mememe' ),
				'_list_templates' => __( 'Meme Templates gallery', 'mememe' ),
				'_margin' => __( 'Thumbnails margin', 'mememe' ),
				'_order' => __( 'Order', 'mememe' ),
				'_orderby' => __( 'Order by', 'mememe' ),
				'_responsive' => __( 'Responsive', 'mememe' ),
				'_templates' => __( 'Templates', 'mememe' ),
				'_hide_carousel' => __( 'Hide Templates Carousel', 'mememe' ),
				'_random_templates' => __( 'Random templates', 'mememe' ),
				'_max_templates' => __( 'Limit carousel items', 'mememe' ),
				'_posts_per_page' => __( 'Posts per page', 'mememe' ),
				'_thumbnail_size' => __( 'Thumbnail size', 'mememe' ),
				'_custom_class' => __( 'CSS Class (optional)', 'mememe' ),
				'_show_title' => __( 'Show Title', 'mememe' ),
				'_style' => __( 'Style', 'mememe' ),
				'_filters' => __( 'Filters', 'mememe' ),
				'_tags' => __( 'Tags', 'mememe' ),
				'_tags_help' => __( 'Show only templates with some tags', 'mememe' ),
				'_autoplay_carousel' => __( 'Carousel autoplay', 'mememe' ),
				'available_tags' => $available_tags,
			);
			wp_localize_script( 'mememe-block', 'MEMEMEadmin', $translation_array );
			wp_enqueue_script( 'mememe-block' );
		}
	}

	/**
	 * Load plugin textdomain.
	 */
	public function mememe_load_plugin_textdomain() {
		load_plugin_textdomain( 'mememe', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register Widgets
	 */
	public function mememe_register_widgets() {
		register_widget( 'MeMeMe_Widget' );
		register_widget( 'MeMeMe_List_Widget' );
		register_widget( 'MeMeMe_Templates_Widget' );
	}

	/**
	 * Add link to settings page inside plugin table.
	 *
	 * @param array $links Array of links.
	 */
	public function mememe_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=mememe_options">' . __( 'Settings', 'mememe' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/** +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	------------------------- AUTO UPDATES ------------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

	/**
	 * Check plugin updates.
	 */
	public function check_updates() {

		$this->update_checker->addQueryArgFilter( array( $this, 'mememe_filter_update_args' ) );

		add_filter(
			'puc_manual_check_message-mememe',
			function ( $message, $status ) {
				if ( ! mememe_admin()->get_option( 'mememe_option_license_key', false ) ) {
					$message .= ' ' . __( 'Enter a Licence to activate the automatic updates.', 'mememe' );
				}

				if ( 1 == mememe_admin()->get_option( 'mememe_option_wrong_code', false ) ) {
					$message .= ' ' . __( 'Enter a valid Licence to activate the automatic updates.', 'mememe' );
				}
				return $message;
			},
			10,
			2
		);
	}

	/**
	 * Send vars to the update checker.
	 *
	 * @param array $query_args Array of arguments.
	 */
	public function mememe_filter_update_args( $query_args ) {
		$query_args['license_key'] = mememe_admin()->get_option( 'mememe_option_license_key', false );
		$query_args['site_url'] = urlencode( home_url() );

		return $query_args;
	}

	/** ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	----------------------------- GENERATOR -----------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Handles form submission on save. Redirects if save is successful, otherwise sets an error message as a cmb property
	 */
	public function mememe_handle_submission() {

		$response = array();

		// Check for nonce security.
		$nonce = filter_input( INPUT_POST, 'mememe_nonce', FILTER_SANITIZE_STRING );

		if ( ! wp_verify_nonce( $nonce, 'mememe-ajax-nonce' ) ) {
			$response['error'] = esc_html__( 'Security check failed.', 'mememe' );
			echo wp_json_encode( $response );
			wp_die();
			exit();
		}

		// If no form submission, bail.
		if ( empty( $_POST ) || ! isset( $_POST['object_id'] ) || ! isset( $_POST['page_url'] ) || empty( $_POST['page_url'] ) || ! isset( $_POST['form_id'] ) ) {
			wp_die();
			exit();
		}

		$post_data = array();

		$form_id = filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_STRING );

		$user_id = get_current_user_id();

		$post_status = mememe_admin()->get_option( 'mememe_option_status', 'pending' );

		// If the current user can publish we set the staus publish.
		if ( current_user_can( 'edit_others_posts' ) ) {
			$post_status = 'publish';
		}
		$post_author = $user_id ? $user_id : 1; // Current user, or admin.

		/**
		 * Fetch sanitized values
		 */
		$sanitized_values = array(
			'mememe_post_title' => false,
			'mememe_post_category' => false,
			'mememe_remote_data' => false,
			'page_url' => false,
			'mememe_template' => false,
		);

		// Check title submitted.
		$sanititle = filter_input( INPUT_POST, 'mememe_post_title_' . $form_id, FILTER_SANITIZE_STRING );

		unset( $_POST[ 'mememe_post_title_' . $form_id ] );

		if ( strlen( $sanititle ) > 90 ) {
			$sanititle = wordwrap( $sanititle, 90 );
			$sanititle = substr( $sanititle, 0, strpos( $sanititle, "\n" ) ) . '...';
		}

		$sanitized_values['mememe_post_title'] = $sanititle;

		if ( ! $sanitized_values['mememe_post_title'] ) {
			$sanitized_values['mememe_post_title'] = 'meme###';
		}

		// Set our post data arguments.
		$post_data['post_title'] = esc_attr( $sanitized_values['mememe_post_title'] );
		$post_data['post_type'] = 'mememe';
		$post_data['post_author'] = $post_author; // Current user, or admin.
		$post_data['post_status'] = $post_status; // 'publish' || 'pending'.

		// $post_data['post_excerpt'] = $post_data['post_title'];
		unset( $sanitized_values['mememe_post_title'] );

		// Leave the post empty, we will insert the image later.
		$post_data['post_content'] = '';

		// Create the new post.
		$new_submission_id = wp_insert_post( $post_data, true );

		// If we hit a snag, update the user.
		if ( is_wp_error( $new_submission_id ) ) {
			$response['error'] = $result->get_error_message();
		}

		$sanitized_values['mememe_remote_data'] = filter_input( INPUT_POST, 'mememe_remote_data', FILTER_SANITIZE_STRING );
		unset( $_POST['mememe_remote_data'] );

		if ( false == $sanitized_values['mememe_remote_data'] ) {
			$response['error'] = __( 'No image data sent.', 'mememe' );

			echo wp_json_encode( $response );
			wp_die(); // Ajax call must die to avoid trailing 0 in your response.
		}

		if ( $sanitized_values['mememe_remote_data'] ) {
			// Create and upload the final Meme.
			$final_meme = $this->mememe_process_image( $sanitized_values['mememe_remote_data'], $new_submission_id, $post_data['post_title'] );

			unset( $sanitized_values['mememe_remote_data'] );
		}

		// Save Fileds.
		if ( is_array( $sanitized_values ) ) {
			foreach ( $sanitized_values as $meta_key => $meta_value ) {
				update_post_meta( $new_submission_id, $meta_key, $meta_value );
			}
		}

		// If our photo upload was successful, set the featured image, the meme content, and the custom meta mememe_template.
		if ( $final_meme && ! is_wp_error( $final_meme ) ) {

			// Set the meme as thumbnail and content to the created post.
			set_post_thumbnail( $new_submission_id, $final_meme );
			$new_post_data = array(
				'ID' => $new_submission_id,
			);

			if ( 'meme###' == $post_data['post_title'] ) {
				$new_post_data['post_title'] = mememe_admin()->get_option( 'mememe_option_meme_title', __( 'Meme', 'mememe' ) ) . ' #' . $new_submission_id;
			} else {
				$new_post_data['post_title'] = $post_data['post_title'];
			}

			$new_post_data['post_content'] = wp_get_attachment_image(
				$final_meme,
				'full',
				false,
				array(
					'class' => 'aligncenter',
					'alt' => $new_post_data['post_title'],
				)
			);

			$new_post_data['post_author'] = $post_author;
			wp_update_post( $new_post_data );

			$sanitized_values['mememe_template'] = filter_input( INPUT_POST, 'mememe_template_' . $form_id, FILTER_SANITIZE_NUMBER_INT );

			if ( $sanitized_values['mememe_template'] ) {
				// Add meta key 'mememe_template' with attachment ID of the template used.
				update_post_meta( $new_submission_id, 'mememe_template', $sanitized_values['mememe_template'] );
			}

			// Set optional post category.
			$sanitized_values['mememe_post_category'] = filter_input( INPUT_POST, 'mememe_post_category_' . $form_id, FILTER_SANITIZE_STRING );

			unset( $_POST['mememe_post_category'] );

			if ( $sanitized_values['mememe_post_category'] ) {

				// $term = wpcom_vip_term_exists( $sanitized_values['mememe_post_category'], 'mememe_category' );
				$term = term_exists( $sanitized_values['mememe_post_category'], 'mememe_category' );
				if ( 0 !== $term && null !== $term ) {
					wp_set_object_terms( $new_submission_id, esc_attr( $sanitized_values['mememe_post_category'] ), 'mememe_category' );
				}
				unset( $sanitized_values['mememe_post_category'] );
			}
		} else {
			$response['error'] = is_wp_error( $final_meme ) ? $final_meme->get_error_message() : __( 'There is no image data.', 'mememe' );
		}

		if ( 'publish' === $post_status ) {
			$response['success'] = get_permalink( $new_submission_id );

			echo wp_json_encode( $response );
			wp_die(); // Ajax call must die to avoid trailing 0 in your response.

			/*
			// Redirect to post.
			wp_redirect( get_permalink( $new_submission_id ) );
			*/

			/*
			// Redirect to attachment.
			wp_redirect( get_permalink( $final_meme ) );
			*/
			exit;
		}

		$sanitized_values['page_url'] = filter_input( INPUT_POST, 'page_url', FILTER_SANITIZE_URL );
		unset( $_POST['page_url'] );

		if ( isset( $response['error'] ) ) {
			// Translators: the error outputted.
			$response['error'] = sprintf( __( 'There was an error in the submission: %s', 'mememe' ), '<strong>' . $response['error'] . '</strong>' );
		} else {
			$response['success'] = add_query_arg( 'meme_submitted', $new_submission_id, $sanitized_values['page_url'] );
		}

		unset( $sanitized_values['page_url'] );

		echo wp_json_encode( $response );
		wp_die(); // Ajax call must die to avoid trailing 0 in your response.
		exit;
	}

	/**
	 * Process image
	 *
	 * @param  str $img the file input name.
	 * @param  int $post_id the post ID (with 0 there is no post attached).
	 * @param  str $post_title the post title.
	 */
	public function mememe_process_image( $img, $post_id = 0, $post_title = 'meme###' ) {

		if ( ! $img ) {
			return false;
		}

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		// $upload_dir = wp_upload_dir()
		$img = str_replace( 'data:image/png;base64,', '', $img );
		$img = str_replace( ' ', '+', $img );
		$data = base64_decode( $img );

		$source = imagecreatefromstring( $data );

		if ( false !== $source ) {

			// Set a custom upload directory.
			add_filter( 'upload_dir', array( $this, 'custom_upload_dir' ) );
			$upload_dir = wp_upload_dir();

			$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
			// Reflect the meme slug.
			$meme_slug = mememe_admin()->get_option( 'mememe_option_slug', 'mememe' );

			$base_title = 'meme###' == $post_title ? $meme_slug : sanitize_title( $post_title );

			$image_name = $base_title . '-' . $post_id . '.jpg';

			$image_upload = imagejpeg( $source, $upload_path . $image_name, 100 );

			// @new use $file instead of $source
			$file             = array();
			$file['error']    = '';
			$file['tmp_name'] = $upload_path . $image_name;
			$file['name']     = $image_name;
			$file['type']     = 'image/png';
			$file['size']     = filesize( $upload_path . $image_name );

			// Upload file to server.
			$file_return = media_handle_sideload( $file, $post_id );

			imagedestroy( $source );
			// Reset to default uploads directory.
			remove_filter( 'upload_dir', array( $this, 'custom_upload_dir' ) );

			return $file_return;
		} else {
			return false;
		}
	}

	/**
	 * Set a custom upload directory.
	 *
	 * @param array $upload the default upload array.
	 */
	public function custom_upload_dir( $upload ) {
		$upload['subdir']   = '/' . $this->slug . $upload['subdir'];
		$upload['path']     = $upload['basedir'] . $upload['subdir'];
		$upload['url']      = $upload['baseurl'] . $upload['subdir'];
		return $upload;
	}

	/** ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	-------------------------- CPT & TAXONOMIES -------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Register mememe custom post type
	 * Register mememe_category taxonomy
	 * Register mememe_template_tag taxonomy for attachments
	 */
	public function mememe_register_cpt() {
		// Register mememe post type.
		$mememe_cpt_labels = array(
			'name' => _x( 'Memes', 'post type general name', 'mememe' ),
			'singular_name' => _x( 'Meme', 'post type singular name', 'mememe' ),
			'add_new' => __( 'Add New', 'mememe' ),
			'add_new_item' => __( 'Add New', 'mememe' ),
			'edit_item' => __( 'Edit Meme', 'mememe' ),
			'new_item' => __( 'New Meme', 'mememe' ),
			'all_items' => __( 'All Memes', 'mememe' ),
			'view_item' => __( 'View Meme', 'mememe' ),
			'search_items' => __( 'Search Memes', 'mememe' ),
			'not_found' => __( 'No Meme found.', 'mememe' ),
			'not_found_in_trash' => __( 'No Memes found in trash.', 'mememe' ),
			'menu_name' => __( 'Memes', 'mememe' ),
		);

		$mememe_cpt_args = array(
			'labels' => $mememe_cpt_labels,
			'public' => true,
			// 'rewrite' => true,
			'rewrite' => array(
				'slug' => mememe_admin()->get_option( 'mememe_option_slug', 'mememe' ),
			),
			'has_archive' => true,
			'hierarchical' => false,
			'map_meta_cap' => true,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
			// 'menu_icon' => 'dashicons-smiley',
			'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode( '<svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M0,0v20h20V0H0z M17.8,17.8H2.2V2.2h15.5V17.8z M15,10.4c0,2.8-2.2,5-5,5s-5-2.2-5-5h1 c0,2.2,1.8,4,4,4s4-1.8,4-4H15z M8.4,7.3c0,0.7-0.6,1.3-1.3,1.3S5.8,8,5.8,7.3S6.4,6,7.1,6S8.4,6.6,8.4,7.3z M14.2,7.3 c0,0.7-0.6,1.3-1.3,1.3S11.6,8,11.6,7.3S12.2,6,12.9,6S14.2,6.6,14.2,7.3z"/></svg>' ),
			'capabilities' => array(
				'create_posts' => false,
			),
			'show_in_rest' => true, // enable Gutenberg editor.
		);

		register_post_type( 'mememe', $mememe_cpt_args );

		// Enable categories.
		$mememe_cpt_category_labels = array(
			'name' => _x( 'Meme Categories', 'table column name', 'mememe' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'mememe' ),
			'search_items' => __( 'Search Meme Categories', 'mememe' ),
			'all_items' => __( 'All Meme Categories', 'mememe' ),
			'parent_item' => __( 'Parent Category', 'mememe' ),
			'parent_item_colon' => _x( 'Parent Category:', 'mememe' ),
			'edit_item' => __( 'Edit Category', 'mememe' ),
			'update_item' => __( 'Update Category', 'mememe' ),
			'add_new_item' => __( 'Add New Meme Category', 'mememe' ),
			'new_item_name' => __( 'New Meme Category Name', 'mememe' ),
			'menu_name' => __( 'Categories', 'mememe' ),
		);

		$mememe_cpt_category_args = array(
			'hierarchical' => true,
			'labels' => $mememe_cpt_category_labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'rewrite' => array( 'slug' => mememe_admin()->get_option( 'mememe_option_slug', 'mememe' ) . '-' . mememe_admin()->get_option( 'mememe_option_category_slug', 'category' ) ),
			'show_in_rest' => true, // enable Gutenberg editor.
		);

		register_taxonomy( 'mememe_category', array( 'mememe' ), $mememe_cpt_category_args );

		$labels = array(
			'name' => _x( 'MeMeMe Template Tags', 'taxonomy general name', 'mememe' ),
			'singular_name' => _x( 'Tag', 'taxonomy singular name', 'mememe' ),
			'search_items' => __( 'Search Tags', 'mememe' ),
			'popular_items' => __( 'Popular Tags', 'mememe' ),
			'all_items' => __( 'All Tags', 'mememe' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Tag', 'mememe' ),
			'update_item' => __( 'Update Tag', 'mememe' ),
			'add_new_item' => __( 'Add New Tag', 'mememe' ),
			'new_item_name' => __( 'New Tag Name', 'mememe' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'mememe' ),
			'add_or_remove_items' => __( 'Add or remove tags', 'mememe' ),
			'choose_from_most_used' => __( 'Choose from the most used tags', 'mememe' ),
			'menu_name' => __( 'MeMeMe Tags', 'mememe' ),
		);

		$mememe_tag_args = array(
			'hierarchical' => false, // Tags and not categories.
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			// 'show_in_menu' => false,
			'rewrite' => array( 'slug' => mememe_admin()->get_option( 'mememe_option_slug', 'mememe' ) . '-' . mememe_admin()->get_option( 'mememe_option_tag_slug', 'tag' ) ),
		);

		register_taxonomy( 'mememe_template_tag', array( 'attachment' ), $mememe_tag_args );
		// Gallery thumbs.
		$thumbsize = mememe_admin()->get_option(
			'mememe_option_thumb_size',
			array(
				'thumb_w' => 300,
				'thumb_h' => 225,
			)
		);
		add_image_size( 'mememe-thumb', $thumbsize['thumb_w'], $thumbsize['thumb_h'], true );
	}

	/**
	 * Rewrite permalinks on activation, after cpt registration
	 */
	public function mememe_rewrite_flush() {
		$this->mememe_register_cpt();
		flush_rewrite_rules();
	}

	/** ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	----------------------------- SHORTCODES ----------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Handle the [mememe] shortcode
	 *
	 * @param array $atts Array of shortcode attributes.
	 * @return string Form html + application
	 */
	public function mememe_do_shortcode( $atts = array() ) {

		static $form_id = 1;

		// New atts since 1.1.
		$args = shortcode_atts(
			array(
				'limit' => 0,
				'random' => 0,
				'autoplay' => 0,
				'nocarousel' => 0,
				'tags' => '',
				'template' => 0,
			),
			$atts
		);

		$set_template = (int) $args['template'];

		$taglist = esc_attr( $args['tags'] );

		$tags = strlen( $taglist ) ? array_map( 'trim', explode( ',', $taglist ) ) : false;

		$limit = (int) $args['limit'];
		$random = (int) $args['random'];
		$autoplay = (int) $args['autoplay'];
		$nocarousel = (int) $args['nocarousel'];

		$cmb = new_cmb2_box(
			array(
				'id'           => 'mememe-form_' . $form_id,
				'object_types' => array( 'post' ),
				'hookup'       => false,
				'save_fields'  => false,
				'cmb_styles' => false,
				'classes'    => 'mememe-inputs', // Extra cmb2-wrap classes.
				'attributes' => array( 'class' => 'custom-class' ),
			)
		);

		$cmb->add_field(
			array(
				'name'    => __( 'Title', 'mememe' ),
				'id'      => 'mememe_post_title_' . $form_id,
				'type'    => 'text',
				'attributes'  => array(
					'placeholder' => __( 'Title', 'mememe' ),
					'class'    => 'mememe-form-control',
				),
				'classes' => 'mememe-generator-title',
				// 'default' => mememe_admin()->get_option( 'mememe_option_meme_title', '' ),
			)
		);

		// We set text input hidden via css, because with YOAST and some themes they were outputted more times.
		$cmb->add_field(
			array(
				'id'      => 'mememe_template_' . $form_id,
				'type'    => 'text',
				'attributes'  => array(
					'class'    => 'hide-mememe',
				),
			)
		);

		$selectcats = mememe_admin()->get_option( 'mememe_option_select_category', 'none' );

		// Hide menu if no selection for users.
		$menuclass = 'none' == $selectcats ? ' hide-mememe' : '';
		// default dropdown if no selection for users.
		$selectcats = 'none' == $selectcats ? 'taxonomy_select' : $selectcats;

		$defcat = mememe_admin()->get_option( 'mememe_option_default_category', '' );
		$defval = 'taxonomy_select' == $selectcats ? $defcat : array( $defcat );

		$select_type = 'taxonomy_select' == $selectcats ? 'mememe-custom-select' : 'mememe-button-group-pills';

		$getcats = get_categories(
			array(
				'taxonomy' => 'mememe_category',
				'hide_empty' => 0,
			)
		);
		if ( count( $getcats ) > 0 ) {
			$cmb->add_field(
				array(
					'name'           => __( 'Select category', 'mememe' ),
					'id'             => 'mememe_post_category_' . $form_id,
					'taxonomy'       => 'mememe_category', // Enter Taxonomy Slug.
					'type'           => $selectcats,
					'show_option_none' => true, // Select first category by default, false to set none.
					'attributes'  => array(
						'class'    => $select_type,
					),
					'default' => $defval,
					'classes' => $menuclass,
				)
			);
		}

		$hide_title = mememe_admin()->get_option( 'mememe_option_hide_title', false );

		$hide_title_class = $hide_title ? ' mememe-hide-title-field' : '';

		// Initiate our output variable.
		$output = '<div class="wrap-mememe' . esc_attr( $hide_title_class ) . '" data-formid="' . esc_attr( $form_id ) . '">';

		if ( isset( $_GET['meme_submitted'] ) ) {
			// If the post was submitted successfully, notify the user.
			if ( get_post( absint( $_GET['meme_submitted'] ) ) ) {
				// Add notice of submission to our output.
				if ( ! mememe_admin()->get_option( 'mememe_option_hide_submission_message', false ) ) {
					$output .= '<h4 class="mememe-submission-message">' . __( 'Thank you, your new post has been submitted and is pending review by a site administrator.', 'mememe' ) . '</h4>';
				}

				// Output the generated image.
				$finalthumb = get_the_post_thumbnail_url( absint( $_GET['meme_submitted'] ), 'full' );
				$output .= '<div class="finalmeme-placeholder"><img src="' . $finalthumb . '"></div>';
				$output .= '<div class="mememe-addons">';
				$output .= '<a download class="mememe-btn mememe-addon mememe-download" target="_blank" href="' . $finalthumb . '">';
				$output .= '<svg fill="currentColor" width="1.5em" height="1.5em" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11.5 8h1v7.826l2.5-3.076.753.665-3.753 4.585-3.737-4.559.737-.677 2.5 3.064v-7.828zm7 12h-13c-2.481 0-4.5-2.019-4.5-4.5 0-2.178 1.555-4.038 3.698-4.424l.779-.14.043-.79c.185-3.447 3.031-6.146 6.48-6.146 3.449 0 6.295 2.699 6.479 6.146l.043.79.78.14c2.142.386 3.698 2.246 3.698 4.424 0 2.481-2.019 4.5-4.5 4.5m.979-9.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h13c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408"/></svg> ';
				$output .= '<span>' . __( 'Download image', 'mememe' ) . '</span>';
				$output .= '</a>';
				$output .= '<a class="mememe-btn mememe-addon" href="' . get_permalink() . '">';
				$output .= '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path d="M18 13.45l2-2.023v4.573h-2v-2.55zm-11-5.45h1.743l1.978-2h-3.721v2zm1.361 3.216l11.103-11.216 4.536 4.534-11.102 11.218-5.898 1.248 1.361-5.784zm1.306 3.176l2.23-.472 9.281-9.378-1.707-1.707-9.293 9.388-.511 2.169zm3.333 7.608v-2h-6v2h6zm-8-2h-3v-2h-2v4h5v-2zm13-2v2h-3v2h5v-4h-2zm-18-2h2v-4h-2v4zm2-6v-2h3v-2h-5v4h2z"/></svg> ';
				$output .= '<span>' . __( 'Create a new Meme', 'mememe' ) . '</span></a>';
				$output .= '</div>';
			}
		} else {

			// Get our form.
			$output .= cmb2_get_metabox_form( $cmb, 'mememe-oject-id' );
			// Get the app.
			// get uploaded templates.
			$templates = array_reverse( get_option( 'mememe_options_templates', array() ) );

			if ( $tags ) {
				$nu_templates = array();

				foreach ( $templates as $key ) {
					foreach ( $tags as $tag ) {
						if ( has_term( $tag, 'mememe_template_tag', $key ) ) {
							$nu_templates[] = $key;
						}
					}
				}
				$templates = $nu_templates;
			}

			$datatemplates = ' data-templatelist="' . implode( ',', $templates ) . '"';

			if ( $set_template ) {
				$datatemplates .= ' data-loadshortcodeimg="' . $set_template . '"';
			}

			$output .= '<div class="mememe-app"' . $datatemplates . '></div>';

			if ( ! $nocarousel && count( $templates ) > 1 ) {

				$output .= '<div class="mememe-template-list" data-autoplay="' . esc_attr( $autoplay ) . '">';

				$conta = 0;
				$limit = intval( $limit );

				if ( $random ) {
					$templates = $this->shuffle( $templates );
				}
				foreach ( $templates as $key ) {
						$bigimg = wp_get_attachment_image_src( $key, 'large' );
					if ( $bigimg ) {
						$conta++;
						$thumb = wp_get_attachment_image_src( $key, 'thumbnail' );
						$output .= '<a href="javascript:void(0)"><img data-template="' . $key . '" src="' . $thumb[0] . '" class="skip-lazy no-lazyload no-lazy" data-no-lazyload data-no-lazy="1"></a>';
						if ( $limit == $conta ) {
							break;
						}
					}
				}
				$output .= '</div>';
			}
		}
		$output .= '</div>';

		// Increase form_id each shortcode.
		$form_id++;

		if ( ! defined( 'WP_DEBUG' ) || true !== WP_DEBUG ) {
			wp_enqueue_script( 'mememe-app' );
		}

		return $output;
	}

	/**
	 * Handle the [mememe-templates] shortcode
	 *
	 * @param array $atts Array of shortcode attributes.
	 */
	public function mememe_templates_do_shortcode( $atts = array() ) {

		$args = shortcode_atts(
			array(
				'columns' => 0,
				'limit' => 0,
				'random' => 0,
				'thumbsize' => 'thumbnail',
				'paginate' => 0,
				'class' => '',
				'title' => 0,
				'margin' => 0,
				'filters' => 1,
				'tags' => 0,
			),
			$atts
		);

		$tags = esc_attr( $args['tags'] );

		$tags = $tags ? array_map( 'trim', explode( ',', $tags ) ) : false;

		$columns = (int) $args['columns'];
		$limit = (int) $args['limit'];
		$random = (int) $args['random'];
		$thumbsize = esc_attr( $args['thumbsize'] );
		$paginate = (int) $args['paginate'];
		$class = esc_attr( $args['class'] );
		$title = $args['title'];
		$margin = $args['margin'];
		$filters = $args['filters'];

		$thumbmargin = $margin ? 'style="padding:' . $margin . 'px"' : '';
		$dest_id = mememe_admin()->get_option( 'mememe_option_recaption_dest', false );
		$recaption_link = $dest_id ? get_permalink( $dest_id ) : '';
		$customclass = strlen( $class ) ? ' ' . $class : '';

		$templates = array_reverse( array_filter( get_option( 'mememe_options_templates', array() ) ) );

		if ( empty( $templates ) ) {
			$out = '<h3>' . __( 'No template found', 'mememe' ) . '</h3>';
		} else {

			if ( $tags ) {

				$nu_templates = array();

				foreach ( $templates as $key ) {
					foreach ( $tags as $tag ) {
						if ( has_term( $tag, 'mememe_template_tag', $key ) ) {
							$nu_templates[] = $key;
						}
					}
				}
				$templates = $nu_templates;
			}

			if ( $random ) {
				$templates = $this->shuffle( $templates );
			}

			if ( $limit ) {
				$templates = array_slice( $templates, 0, $limit );
			}

			$out = '<div class="mememe-wrap-gallery' . $customclass . ' mememe-template-gallery">';

			$meme_cats = get_terms(
				array(
					'taxonomy' => 'mememe_template_tag',
					'hide_empty' => true,
					'orderby' => 'count', // Order by tag count, default 'name'.
					'order' => 'DESC', // Higer value first, default ASC
					// 'number' => 3, // Limit Number.
				)
			);

			if ( ! empty( $meme_cats ) && ! is_wp_error( $meme_cats ) ) {

// $taginputs = array();

				// Print filters menu.
				if ( $filters ) {
// $outcats = array();
					// $filters = '<input type="text" class="quicksearch" placeholder="Search" />';
					$filters = '<div class="mememe-filters">';
					$filters .= '<label data-filter="*" class="mememe-filter active"><input type="radio" name="options"> ' . __( 'All', 'mememe' ) . '</label>';

					foreach ( $meme_cats as $key => $term ) {
						$filters .= '<label data-filter=".mememe-filter-' . $term->slug . '" class="mememe-filter"><input type="radio" name="options">' . $term->name . '</label>';
// $outcats[$term->term_id] = $term->slug;
					}

					$filters .= '</div>';
					$out .= $filters;
				}
			}

			$out .= '<div class="mememe-gallery mememe-hidden mememe-column-' . $columns . '" data-columns="' . $columns . '">';
			$out .= '<div class="grid-sizer"></div>';

			$tplnum = 0;
			$tplcount = count( $templates );
			$activateload = false;
			$inputs = '';

			foreach ( $templates as $id ) {
				$thumb = wp_get_attachment_image_src( $id, $thumbsize );
				if ( $thumb ) {
					$post = get_post( $id );
					$link = add_query_arg( 'mememe_tpl', $post->post_name, $recaption_link );

					$the_title = get_the_title( $id );

					// Get item taxonomy terms from mememe_template_tag.
					$list_categories = $this->mememe_list_cateogries( $id, 'mememe_template_tag' );

					$catclass = isset( $list_categories['classes'] ) ? $list_categories['classes'] : '';

					if ( $limit || $tplnum < $paginate || ! $paginate ) {
						$out .= '<div class="mememe-gallery-item ' . $catclass . '" ' . $thumbmargin . '><div class="mememe-card">';
						$out .= '<a title="' . $the_title . '" href="' . $link . '"><img src="' . $thumb[0] . '" class="skip-lazy no-lazyload no-lazy" data-no-lazyload data-no-lazy="1"></a>';

						if ( $title ) {
							$out .= '<a href="' . $link . '" class="mememe-card-body"><div class="mememe-card-overlay"></div><div class="mememe-card-title">' . $the_title . '</div>';
							$out .= '</a>';
						}
						$out .= '</div></div>';

					} else {

						if ( $paginate ) {
							if ( $tplnum == $paginate && $paginate < $tplcount ) {
								$activateload = true;
							}
							$inputs .= '<input type="hidden" class="mememe-load-tpl-list" value="' . $thumb[0] . '" data-tag="' . $catclass . '" data-columns="' . $columns . '" data-title="' . $the_title . '" data-link="' . $link . '">';
						}
					}
					$tplnum++;
				}
			}

			$out .= '</div>';

// $out .= 'TAGINPUTS<br>';
// $out .= wp_json_encode( $outcats );
// $out .= 'GALLOPTIONS<br>';
// $out .= 'COLUMNS: '.$columns;
			$showtitle = '';
			if ( $title ) {
				$showtitle .= ' data-showtitle="1"';
			}

			if ( $activateload ) {
				$out .= '<button class="mememe-btn mememe-outline-btn mememe-loadmore" data-margin="' . $margin . '" data-paging="' . $paginate . '"' . $showtitle . '><span class="preload-mememe"></span> ' . __( 'Load more', 'mememe' ) . '</button>';
			}

			$out .= '<div class="mememe-load-inputs">' . $inputs . '</div>';
			$out .= '</div>';
		}

		if ( ! defined( 'WP_DEBUG' ) || true !== WP_DEBUG ) {
			wp_enqueue_script( 'mememe-app' );
		}

		return $out;
	}

	/**
	 * Handle the [mememe-list] shortcode
	 *
	 * @param array $atts Array of shortcode attributes.
	 */
	public function mememe_list_do_shortcode( $atts = array() ) {

		$args = shortcode_atts(
			array(
				'columns' => 0,
				'order' => 'DESC', // ASC, DESC.
				'orderby' => 'date', // 'title', 'date', 'ID', 'rand'.
				// 'random' => 0, (NO rand with pagination)
				'category' => 0,
				'per_page' => get_option( 'posts_per_page' ), // New att since mememe v1.1.
				'limit' => 0,
				'class' => '',
				'thumbsize' => 'mememe-thumb',
				'margin' => 0,
				'filters' => 0,
				'author' => 0,
			),
			$atts
		);
		$columns = (int) $args['columns'];
		$order = $args['order'];
		$orderby = $args['orderby'];
		// $orderby = $args['random'] ? 'rand' : $orderby; // Radnom order only on widget, with no pagination
		$category = $args['category'];
		$per_page = $args['per_page'];
		$limit = (int) $args['limit'];
		$class = esc_attr( $args['class'] );
		$thumbsize = esc_attr( $args['thumbsize'] );
		$margin = esc_attr( $args['margin'] );
		$filters = $args['filters'];
		$author = (int) $args['author'];

		$thumbmargin = $margin ? 'style="padding:' . $margin . 'px"' : '';
		$customclass = strlen( $class ) > 0 ? ' ' . $class : '';

		$out = '<div class="mememe-wrap-gallery' . $customclass . '">';

		$tax = array();

		if ( $category ) {
			$out .= '<h4>' . __( 'Meme category: ', 'mememe' ) . $category . '</h4>';
			$tax = array(
				array(
					'taxonomy' => 'mememe_category',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		} else {

			if ( $filters ) {
				// Print filters menu.
				$meme_cats = get_terms(
					array(
						'taxonomy' => 'mememe_category',
						'hide_empty' => true,
						// 'orderby' => 'count', // default 'name'
						// 'order' => 'DESC',
						// 'number' => 30, // Limit Number.
					)
				);

				if ( ! empty( $meme_cats ) && ! is_wp_error( $meme_cats ) ) {

					$filters = '<div class="mememe-filters">';
					$filters .= '<label data-filter="*" class="mememe-filter active"><input type="radio" name="options"> ' . __( 'All', 'mememe' ) . '</label>';

					foreach ( $meme_cats as $key => $term ) {
							$filters .= '<label data-filter=".mememe-filter-' . $term->slug . '" class="mememe-filter"><input type="radio" name="options">' . $term->name . '</label>';
					}
					$filters .= '</div>';
					$out .= $filters;
				}
			}
		}

		$options = array(
			'post_type' => 'mememe',
			'paged' => 1, // Set always page 1.
			'order' => $order,
			'orderby' => $orderby,
			'tax_query' => $tax,
		);

		$options['posts_per_page'] = $per_page;

		if ( $limit ) {
			$options['posts_per_page'] = $limit;
			$options['no_found_rows'] = true;
		}

		if ( 0 !== $author ) {
			if ( get_current_user_id() === 0 ) {
				return false;
			} else {
				$options['author'] = get_current_user_id();
			}
		}

		$the_query = new WP_Query( $options );

		// Run the loop based on the query.
		if ( $the_query->have_posts() ) {

			$data_category = $category ? 'data-category="' . $category . '"' : false;
			$out .= '<div class="mememe-gallery mememe-hidden mememe-column-' . $columns . ' ' . $category . '" ' . $data_category . '>';

			$out .= '<div class="grid-sizer"></div>';

			while ( $the_query->have_posts() ) :

				$the_query->the_post();
				if ( has_post_thumbnail() ) {

					$list_categories = $this->mememe_list_cateogries( get_the_ID(), 'mememe_category' );

					$catclass = isset( $list_categories['classes'] ) ? $list_categories['classes'] : '';
					$catlist = isset( $list_categories['list'] ) ? $list_categories['list'] : '';

					$out .= '<div class="mememe-gallery-item' . $catclass . '" ' . $thumbmargin . '><div class="mememe-card">';
					// $out .= '<p><a href="'.get_permalink().'">'.get_the_title().'</a></p>';
					$out .= '<a title="' . get_the_title() . '" href="' . get_permalink() . '">';
					$out .= get_the_post_thumbnail(
						get_the_ID(),
						$thumbsize,
						array(
							'class' => 'skip-lazy no-lazyload no-lazy',
							'data-no-lazyload' => '1',
							'data-no-lazy' => '1',
						)
					) . '</a>';
					$out .= '<div class="mememe-card-body"><div class="mememe-card-overlay"></div>';
					$out .= '<h4 class="mememe-card-title">' . get_the_title() . '</h4>';

					$out .= $catlist;

					$out .= '<div class="mememe-card-links">';

					$relink = $this->get_template_link( get_the_ID() );
					if ( $relink ) {
						$out .= '<a class="mememe-card-link" title="' . esc_html__( 'Re-Caption this meme', 'mememe' ) . '" href="' . $relink . '"><i class="immm immm-quote"></i></a>';
					}
					$out .= '<a class="mememe-card-link" title="' . get_the_title() . '" href="' . get_permalink() . '"><i class="immm immm-angle-right"></i></a>';

					$out .= '</div>'; // Close card-links.
					$out .= '</div></div>';
					$out .= '</div>';
				}
			endwhile;

			wp_reset_postdata();

			$out .= '</div>'; // Close .mememe-gallery.

			$max_num_pages = $the_query->max_num_pages;

			// No post limit, build a pagination.
			if ( ! $limit && $max_num_pages > 1 ) {
				$datapost = ' data-current_page="1"';
				$datapost .= ' data-max_page="' . $max_num_pages . '"';
				$datapost .= ' data-thumbsize="' . $thumbsize . '"';

				if ( isset( $options['posts_per_page'] ) ) {
					$datapost .= ' data-per_page="' . $options['posts_per_page'] . '"';
				}
				if ( $category ) {
					$datapost .= ' data-category="' . $category . '"';
				}

				$out .= '<button class="mememe-loadmorememes mememe-btn mememe-btn mememe-outline-btn" ' . $datapost . ' data-margin="' . $margin . '"><span class="preload-mememe"></span> ' . __( 'Load more', 'mememe' ) . '</button>';
			}
		} else {
			$out .= '<h3>' . __( 'No Meme found', 'mememe' ) . '</h3>';
		}
		$out .= '</div>'; // Close .mememe-wrap-gallery.

		wp_reset_query();
		wp_reset_postdata();

		if ( ! defined( 'WP_DEBUG' ) || true !== WP_DEBUG ) {
			wp_enqueue_script( 'mememe-app' );
		}

		return $out;
	}

	/** ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	----------------------------- LOAD MORE -----------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Call next posts via ajax
	 */
	public function mememe_handle_loadmore() {

		// Check for nonce security.
		$nonce = filter_input( INPUT_POST, 'mememe_nonce', FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, 'mememe-ajax-nonce' ) ) {
			wp_die(); // Ajax call must die to avoid trailing 0 in your response.
			exit;
		}

		$args['paged'] = sanitize_key( filter_input( INPUT_POST, 'current_page', FILTER_VALIDATE_INT ) ) + 1;
		$args['posts_per_page'] = sanitize_key( filter_input( INPUT_POST, 'posts_per_page', FILTER_VALIDATE_INT ) );
		// $args['orderby'] = sanitize_key( filter_input( INPUT_POST, 'orderby', FILTER_VALIDATE_INT ) );
		$args['post_status'] = 'publish';
		$args['post_type'] = 'mememe';
		$args['meta_query'] = array( array( 'key' => '_thumbnail_id' ) );

		$thumbsize = filter_input( INPUT_POST, 'thumbsize', FILTER_SANITIZE_STRING );
		$thumbsize = $thumbsize ? $thumbsize : 'mememe-thumb';
		$category = filter_input( INPUT_POST, 'category', FILTER_SANITIZE_STRING );
		$margin = sanitize_key( filter_input( INPUT_POST, 'margin', FILTER_VALIDATE_INT ) );
		$padding = $margin ? 'style="padding: ' . $margin . 'px;"' : '';

		if ( $category ) {
			$tax = array(
				array(
					'taxonomy' => 'mememe_category',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
			$args['tax_query'] = $tax;
		}

		$the_query = new WP_Query( $args );

		// The Loop.
		if ( $the_query->have_posts() ) {

			$outpost = array();

			while ( $the_query->have_posts() ) {

				$the_query->the_post();

				$list_categories = $this->mememe_list_cateogries( get_the_ID(), 'mememe_category' );

				$catclass = isset( $list_categories['classes'] ) ? $list_categories['classes'] : '';
				$catlist = isset( $list_categories['list'] ) ? $list_categories['list'] : '';

				$out = '<div class="mememe-gallery-item' . $catclass . '" ' . $padding . '><div class="mememe-card">';
				$out .= '<a title="' . get_the_title() . '" href="' . get_permalink() . '">';
				$out .= get_the_post_thumbnail(
					get_the_ID(),
					$thumbsize,
					array(
						'class' => 'skip-lazy no-lazyload no-lazy',
						'data-no-lazyload' => '1',
						'data-no-lazy' => '1',

					)
				) . '</a>';
				$out .= '<div class="mememe-card-body">';
				$out .= '<h4 class="mememe-card-title">' . get_the_title() . '</h4>';

				$out .= $catlist;

				$out .= '<div class="mememe-card-links">';

				$relink = $this->get_template_link( get_the_ID() );

				if ( $relink ) {
					$out .= '<a class="mememe-card-link" title="' . esc_html__( 'Re-Caption this meme', 'mememe' ) . '" href="' . $relink . '"><i class="immm immm-quote"></i></a>';
				}
				$out .= '<a class="mememe-card-link" title="' . get_the_title() . '" href="' . get_permalink() . '"><i class="immm immm-angle-right"></i></a>';
				$out .= '</div>'; // .mememe-card-links
				$out .= '</div>'; // .mememe-card-body
				$out .= '</div></div>'; // .mememe-card .mememe-gallery-item

				$outpost[] = $out;

			}
			echo wp_json_encode( $outpost );
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		wp_die(); // Ajax call must die to avoid trailing 0 in your response.
		exit;
	}

	/**
	 * Get img src from attachment ID
	 */
	public function get_template_img() {

		// Check for nonce security.
		$nonce = filter_input( INPUT_POST, 'mememe_nonce', FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, 'mememe-ajax-nonce' ) ) {
			wp_die( 'Not allowed' );
		}

		$id = filter_input( INPUT_POST, 'imgID', FILTER_VALIDATE_INT );
		// $id = absint( $_POST['imgID'] );
		$image_attributes = wp_get_attachment_image_src( $id, 'full' );

		echo esc_url( $image_attributes[0] );
		wp_die();
	}

	/** +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	----------------------------- MEME POST LAYOUT ---------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Check if current Meme used an available template
	 *
	 * @param int  $id the id of the post.
	 * @param bool $attachment the post is attachment.

	 * @return string $link
	 */
	public function get_template_link( $id, $attachment = false ) {

		$link = false;
		$recaption_link = false;
		$dest_id = mememe_admin()->get_option( 'mememe_option_recaption_dest', 0 );
		$post = false;

		// Check if there's a destination page.
		if ( $dest_id ) {

			$template = $attachment ? $id : false;
			$template_meme = get_post_meta( $id, 'mememe_template', true );

			// Check if the attachment is a template.
			if ( $template ) {
				$templates = get_option( 'mememe_options_templates', array() );

				if ( in_array( $template, $templates ) ) {
					$recaption_link = get_permalink( $dest_id );
					$post = get_post( $template );
				}
			}
			// Check if there's a template associated to the meme.
			if ( $template_meme ) {
				// Check if the template still exists.
				$tempargs = array(
					'posts_per_page'   => 1,
					'fields'           => 'ids', // Only get post IDs.
					'include'          => array( $template_meme ),
					'post_type'        => 'attachment',
					'post_mime_type'   => 'image',
					// 'post_parent'      => 0, // unattached files
					'post_status'      => 'inherit',
				);
				$get_template = get_posts( $tempargs );

				if ( $get_template ) {
					$recaption_link = get_permalink( $dest_id );
					$post = get_post( $template_meme );
					// $link = add_query_arg( 'mememe_tpl', $template_meme, $recaption_link );
				}
			}
			if ( $post && $recaption_link ) {
				$link = add_query_arg( 'mememe_tpl', $post->post_name, $recaption_link );
			}
		}
		return $link;
	}

	/**
	 * Add ons below generated memes content
	 *
	 * @param mixed $content the original post content.
	 * @return updated $content
	 */
	public function mememe_add_ons( $content ) {

		if ( in_the_loop() && is_main_query() ) {

			$post_id = get_the_ID();

			if ( is_attachment() ) {
				$link = $this->get_template_link( $post_id, true );

				/*
				 * Re-caption link
				 */
				if ( $link ) {
					$content = wp_get_attachment_image( $post_id, 'large' );
					$content .= '<div class="mememe-addons">';
					$content .= '<a href="' . esc_url( $link ) . '" class="mememe-btn mememe-addon mememe-recaption-link">';
					$content .= '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path d="M18 13.45l2-2.023v4.573h-2v-2.55zm-11-5.45h1.743l1.978-2h-3.721v2zm1.361 3.216l11.103-11.216 4.536 4.534-11.102 11.218-5.898 1.248 1.361-5.784zm1.306 3.176l2.23-.472 9.281-9.378-1.707-1.707-9.293 9.388-.511 2.169zm3.333 7.608v-2h-6v2h6zm-8-2h-3v-2h-2v4h5v-2zm13-2v2h-3v2h5v-4h-2zm-18-2h2v-4h-2v4zm2-6v-2h3v-2h-5v4h2z"/></svg> ';
					$content .= '<span>' . esc_html__( 'Generate meme', 'mememe' ) . '</span></a>';
					$content .= '</div>';
				}
			}

			if ( is_singular( 'mememe' ) ) {

				$link = $this->get_template_link( $post_id );
				$permalink = get_permalink( $post_id );
				$image = get_the_post_thumbnail_url( $post_id, 'full' );
				$title = get_the_title( $post_id );

				$content .= '<div class="mememe-addons">';

				/*
				 * Download image
				 */
				if ( has_post_thumbnail( $post_id ) ) {
					$content .= '<a download class="mememe-btn mememe-addon mememe-download" target="_blank" href="' . $image . '">';
					$content .= '<svg fill="currentColor" width="1.5em" height="1.5em" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path d="M11.5 8h1v7.826l2.5-3.076.753.665-3.753 4.585-3.737-4.559.737-.677 2.5 3.064v-7.828zm7 12h-13c-2.481 0-4.5-2.019-4.5-4.5 0-2.178 1.555-4.038 3.698-4.424l.779-.14.043-.79c.185-3.447 3.031-6.146 6.48-6.146 3.449 0 6.295 2.699 6.479 6.146l.043.79.78.14c2.142.386 3.698 2.246 3.698 4.424 0 2.481-2.019 4.5-4.5 4.5m.979-9.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h13c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408"/></svg> ';
					$content .= '<span>' . __( 'Download image', 'mememe' ) . '</span>';
					$content .= '</a>';
				}

				/*
				 * Re-caption link
				 */
				if ( $link ) {
					$content .= '<a href="' . esc_url( $link ) . '" class="mememe-btn mememe-addon mememe-recaption-link">';
					$content .= '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path d="M18 13.45l2-2.023v4.573h-2v-2.55zm-11-5.45h1.743l1.978-2h-3.721v2zm1.361 3.216l11.103-11.216 4.536 4.534-11.102 11.218-5.898 1.248 1.361-5.784zm1.306 3.176l2.23-.472 9.281-9.378-1.707-1.707-9.293 9.388-.511 2.169zm3.333 7.608v-2h-6v2h6zm-8-2h-3v-2h-2v4h5v-2zm13-2v2h-3v2h5v-4h-2zm-18-2h2v-4h-2v4zm2-6v-2h3v-2h-5v4h2z"/></svg> ';
					$content .= '<span>' . esc_html__( 'Re-Caption this meme', 'mememe' ) . '</span></a>';
				}
				$content .= '</div>';

				/*
				 * Meme Ratings
				 */
				if ( mememe_admin()->get_option( 'mememe_option_meme_rating', false ) ) {
					$meta_count_up = get_post_meta( $post_id, 'votes_count_up', true );
					$meta_count_down = get_post_meta( $post_id, 'votes_count_down', true );

					$meta_count_up = ! empty( $meta_count_up ) ? $meta_count_up : '0';
					$meta_count_down = ! empty( $meta_count_down ) ? $meta_count_down : '0';

					$votedclass = $this->has_voted() ? ' mememe-voted-' . $this->has_voted()['vote'] : '';

					$content .= '<div class="mememe-post-like' . $votedclass . '" data-post_id="' . $post_id . '">';
					$content .= '<div class="mememe-vote-btn mememe-count-up" data-vote="up">';
					$content .= '<i class="mememe-vote-up immm immm-thumb_up"></i> <span>' . $meta_count_up . '</span></div> ';
					$content .= '<div class="mememe-vote-btn mememe-count-down" data-vote="down">';
					$content .= '<i class="mememe-vote-down immm immm-thumb_down"></i> <span>' . $meta_count_down . '</span></div></div>';
				}

				/*
				 * Socials
				 */
				$qs_key = mememe_admin()->get_option( 'mememe_option_qs_key', false );
				$qs_value = mememe_admin()->get_option( 'mememe_option_qs_value', '' );
				$permalink = $qs_key ? add_query_arg( $qs_key, $qs_value, $permalink ) : $permalink;
				$social_permalink = urlencode( $permalink );

				$all_socials = array(
					'facebook' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>',
						'link' => 'https://www.facebook.com/sharer/sharer.php?u=' . $social_permalink,
					),
					'twitter' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>',
						'link' => 'https://twitter.com/intent/tweet?text=' . $title . '&url=' . $social_permalink,
					),
					'linkedin' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>',
						'link' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $social_permalink,
					),
					'pinterest' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z" fill-rule="evenodd" clip-rule="evenodd"/></svg>',
						'link' => 'https://pinterest.com/pin/create/button/?url=' . $social_permalink . '&media=' . $image . '&description=' . $title,
					),
					'reddit' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M24 11.779c0-1.459-1.192-2.645-2.657-2.645-.715 0-1.363.286-1.84.746-1.81-1.191-4.259-1.949-6.971-2.046l1.483-4.669 4.016.941-.006.058c0 1.193.975 2.163 2.174 2.163 1.198 0 2.172-.97 2.172-2.163s-.975-2.164-2.172-2.164c-.92 0-1.704.574-2.021 1.379l-4.329-1.015c-.189-.046-.381.063-.44.249l-1.654 5.207c-2.838.034-5.409.798-7.3 2.025-.474-.438-1.103-.712-1.799-.712-1.465 0-2.656 1.187-2.656 2.646 0 .97.533 1.811 1.317 2.271-.052.282-.086.567-.086.857 0 3.911 4.808 7.093 10.719 7.093s10.72-3.182 10.72-7.093c0-.274-.029-.544-.075-.81.832-.447 1.405-1.312 1.405-2.318zm-17.224 1.816c0-.868.71-1.575 1.582-1.575.872 0 1.581.707 1.581 1.575s-.709 1.574-1.581 1.574-1.582-.706-1.582-1.574zm9.061 4.669c-.797.793-2.048 1.179-3.824 1.179l-.013-.003-.013.003c-1.777 0-3.028-.386-3.824-1.179-.145-.144-.145-.379 0-.523.145-.145.381-.145.526 0 .65.647 1.729.961 3.298.961l.013.003.013-.003c1.569 0 2.648-.315 3.298-.962.145-.145.381-.144.526 0 .145.145.145.379 0 .524zm-.189-3.095c-.872 0-1.581-.706-1.581-1.574 0-.868.709-1.575 1.581-1.575s1.581.707 1.581 1.575-.709 1.574-1.581 1.574z"/></svg>',
						'link' => 'http://www.reddit.com/submit?url=' . $social_permalink . '&title=' . $title,
					),
					'tumblr' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M19.512 17.489l-.096-.068h-3.274c-.153 0-.16-.467-.163-.622v-5.714c0-.056.045-.101.101-.101h3.82c.056 0 .101-.045.101-.101v-5.766c0-.055-.045-.1-.101-.1h-3.803c-.055 0-.1-.045-.1-.101v-4.816c0-.055-.045-.1-.101-.1h-7.15c-.489 0-1.053.362-1.135 1.034-.341 2.778-1.882 4.125-4.276 4.925l-.267.089-.068.096v4.74c0 .056.045.101.1.101h2.9v6.156c0 4.66 3.04 6.859 9.008 6.859 2.401 0 5.048-.855 5.835-1.891l.157-.208-1.488-4.412zm.339 4.258c-.75.721-2.554 1.256-4.028 1.281l-.165.001c-4.849 0-5.682-3.701-5.682-5.889v-7.039c0-.056-.045-.101-.1-.101h-2.782c-.056 0-.101-.045-.101-.101l-.024-3.06.064-.092c2.506-.976 3.905-2.595 4.273-5.593.021-.167.158-.171.159-.171h3.447c.055 0 .101.045.101.101v4.816c0 .056.045.101.1.101h3.803c.056 0 .101.045.101.1v3.801c0 .056-.045.101-.101.101h-3.819c-.056 0-.097.045-.097.101v6.705c.023 1.438.715 2.167 2.065 2.167.544 0 1.116-.126 1.685-.344.053-.021.111.007.13.061l.995 2.95-.024.104z" fill-rule="evenodd" clip-rule="evenodd"/></svg>',
						'link' => 'http://tumblr.com/widgets/share/tool?canonicalUrl=' . $social_permalink . '&title=' . $title,
					),
					'buffer' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 256 262"><path d="M136.442 1.515c38.04 17.645 76.703 35.867 114.69 53.622 2.027.948 4.685 1.593 4.688 4.463.004 2.875-2.65 3.515-4.682 4.464a76682.54 76682.54 0 0 1-114.45 53.437c-5.814 2.707-11.784 2.672-17.605-.041A104726.907 104726.907 0 0 1 4.403 63.907c-1.967-.92-4.442-1.616-4.365-4.413.072-2.617 2.44-3.297 4.332-4.182C42.747 37.38 81.513 19.166 119.94 1.344c4.062-1.881 12.27-1.753 16.502.17zm-8.562 260.047c-2.767 0-4.84-1.05-8.794-2.53-38.185-17.712-76.323-35.526-114.458-53.346-2.027-.947-4.652-1.616-4.628-4.504.025-2.873 2.652-3.507 4.68-4.471 6.325-3.008 12.683-5.948 19.038-8.893 6.35-2.943 12.657-2.902 19.025.083 25.28 11.85 50.586 23.64 75.876 35.465 6.28 2.936 12.506 2.875 18.778-.063a70166.124 70166.124 0 0 1 76.108-35.573c5.992-2.797 12.063-2.875 18.083-.147 6.77 3.067 13.49 6.244 20.214 9.405.997.469 2.008.998 2.843 1.7 1.797 1.515 1.821 3.566-.013 5.035-1.115.893-2.449 1.55-3.757 2.16-37.827 17.674-75.649 35.358-113.527 52.922-2.944 1.364-6.7 2.757-9.468 2.757zm-.134-70.823c-1.265 0-6.18-1.342-8.978-2.641-38.028-17.66-76.005-35.43-113.985-53.191-2.04-.954-4.7-1.57-4.753-4.412-.056-3.015 2.728-3.618 4.828-4.617 6.396-3.045 12.81-6.056 19.247-9.014 6.104-2.804 12.218-2.738 18.317.11 25.535 11.928 51.07 23.858 76.623 35.746 5.915 2.752 11.922 2.73 17.842-.025 25.627-11.933 51.236-23.903 76.86-35.844 5.924-2.761 11.91-2.76 17.838-.054 6.756 3.082 13.47 6.257 20.191 9.414.923.434 1.87.892 2.66 1.52 2.04 1.62 2.023 3.759-.04 5.38-.794.625-1.737 1.086-2.66 1.518-38.37 17.937-76.732 35.89-115.148 53.725-2.64 1.227-7.578 2.385-8.842 2.385z"/></svg>',
						'link' => 'https://buffer.com/add?url=' . $social_permalink . '&text=' . $title,
					),
					'whatsapp' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>',
						'link' => 'https://wa.me/?text=' . urlencode( $title . ': ' ) . $social_permalink,
					),
					'evernote' => array(
						'icon' => '<svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" width="1em" viewBox="0 0 32 32" fill="#7fce2c"><path d="M29.343 16.818c.1 1.695-.08 3.368-.305 5.045-.225 1.712-.508 3.416-.964 5.084-.3 1.067-.673 2.1-1.202 3.074-.65 1.192-1.635 1.87-2.992 1.924l-3.832.036c-.636-.017-1.278-.146-1.9-.297-1.192-.3-1.862-1.1-2.06-2.3-.186-1.08-.173-2.187.04-3.264.252-1.23 1-1.96 2.234-2.103.817-.1 1.65-.077 2.476-.1.205-.007.275.098.203.287-.196.53-.236 1.07-.098 1.623.053.207-.023.307-.26.305a7.77 7.77 0 0 0-1.123.053c-.636.086-.96.47-.96 1.112 0 .205.026.416.066.622.103.507.45.78.944.837 1.123.127 2.247.138 3.37-.05.675-.114 1.08-.54 1.16-1.208.152-1.3.155-2.587-.228-3.845-.33-1.092-1.006-1.565-2.134-1.7l-3.36-.54c-1.06-.193-1.7-.887-1.92-1.9-.13-.572-.14-1.17-.214-1.757-.013-.106-.074-.208-.1-.3-.04.1-.106.212-.117.326-.066.68-.053 1.373-.185 2.04-.16.8-.404 1.566-.67 2.33-.185.535-.616.837-1.205.8a37.76 37.76 0 0 1-7.123-1.353l-.64-.207c-.927-.26-1.487-.903-1.74-1.787l-1-3.853-.74-4.3c-.115-.755-.2-1.523-.083-2.293.154-1.112.914-1.903 2.04-1.964l3.558-.062c.127 0 .254.003.373-.026a1.23 1.23 0 0 0 1.01-1.255l-.05-3.036c-.048-1.576.8-2.38 2.156-2.622a10.58 10.58 0 0 1 4.91.26c.933.275 1.467.923 1.715 1.83.058.22.146.3.37.287l2.582.01 3.333.37c.686.095 1.364.25 2.032.42 1.165.298 1.793 1.112 1.962 2.256l.357 3.355.3 5.577.01 2.277zm-4.534-1.155c-.02-.666-.07-1.267-.444-1.784a1.66 1.66 0 0 0-2.469-.15c-.364.4-.494.88-.564 1.4-.008.034.106.126.16.126l.8-.053c.768.007 1.523.113 2.25.393.066.026.136.04.265.077zM8.787 1.154a3.82 3.82 0 0 0-.278 1.592l.05 2.934c.005.357-.075.45-.433.45L5.1 6.156c-.583 0-1.143.1-1.554.278l5.2-5.332c.02.013.04.033.06.053z"/></svg>',
						'link' => 'https://www.evernote.com/clip.action?url=' . $social_permalink . '&title=' . $title,
					),
				);

				$social_link = mememe_admin()->get_option( 'mememe_option_social_link', false );
				$social_share = mememe_admin()->get_option( 'mememe_option_social_share', array() );

				$content .= '<div class="mememe-social-container">';
				$content .= '<div class="mememe-social-row">';

				foreach ( $all_socials as $key => $social ) {
					if ( in_array( $key, $social_share ) ) {
						$content .= '<div class="mememe-social-col"><a class="' . $key . '-bg" target="_blank" href="' . $social['link'] . '">' . $social['icon'] . '</a></div>';
					}
				}
				$content .= '</div>';

				if ( $social_link ) {
					$share_url = '<input type="text" readonly onClick="this.select();" value="' . $permalink . '">';
					$content .= '<div class="mememe-social-row"><div class="mememe-social-col">' . $share_url . '</div></div>';
				}
				$content .= '</div>';

				/*
				 * Report abuse
				 */
				$report_mail = mememe_admin()->get_option( 'mememe_option_report', false );
				if ( $report_mail && is_email( $report_mail ) ) {

					$mail_subject = mememe_admin()->get_option( 'mememe_option_report_mail_title', 'Report meme abuse' );
					$mail_body = mememe_admin()->get_option( 'mememe_option_report_mail_text', false ) . ': ' . $permalink;

					$content .= '<a href="mailto:' . $report_mail . '?subject=' . $mail_subject . '&body=' . $mail_body . '" class="mememe-report mememe-flag-btn pull-right" title="' . esc_html__( 'Report abuse', 'mememe' ) . '"><i class="immm immm-flag"></i></a>';
				}
			}
		}
		return $content;
	}

	/** +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	----------------------------- SEO -----------------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Add CPT mememe to search results
	 *
	 * @param str $query search query.
	 */
	public function mememe_search( $query ) {

		if ( ! is_admin() && $query->is_main_query() ) {

			// Add attachments to search.
			if ( $query->is_search() ) {
				$query->set( 'post_status', array( 'publish', 'inherit' ) );
			}

			// Display template archive tags.
			if ( $query->is_tax( 'mememe_template_tag' ) ) {
				// $query->set( 'post_type', 'attachment' );
				$query->set( 'post_status', 'inherit' );
			}
		}
		return $query;
	}

	/**
	 * Set og tags inside header for meme sharing
	 */
	public function set_og_tags() {
		if ( is_singular( 'mememe' ) ) {
			$post_id = get_the_ID();
			$image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			$featured_img_url = isset( $image_data[0] ) ? $image_data[0] : false;
			// $title = get_the_title( $post_id ) . ' | ' . get_bloginfo( 'name' );
			$title = get_the_title( $post_id );
			$description = mememe_admin()->get_option( 'mememe_option_social_description', get_bloginfo( 'description' ) );
			$link = get_the_permalink( $post_id );
			?>
			<!-- MeMeMe sharing meta -->
			<?php
			if ( $featured_img_url ) {
				?>
			<link rel="image_src" href="<?php echo esc_url( $featured_img_url ); ?>" />
				<?php
			}
			?>
			<meta property="og:type" content="article" />
			<meta property="og:url" content="<?php echo esc_url( $link ); ?>" />
			<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
			<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
			<meta property="og:image" content="<?php echo esc_url( $featured_img_url ); ?>" />
			<?php
			if ( isset( $image_data[1] ) ) {
				?>
				<meta property="og:image:width" content="<?php echo esc_attr( $image_data[1] ); ?>" />
				<?php
			}
			?>
			<?php
			if ( isset( $image_data[2] ) ) {
				?>
				<meta property="og:image:height" content="<?php echo esc_attr( $image_data[2] ); ?>" />
				<?php
			}
			?>
			<meta name="twitter:card" content="summary_large_image">
			<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
			<meta name="twitter:image" content="<?php echo esc_url( $featured_img_url ); ?>">
			<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
			<!-- / MeMeMe sharing meta -->
			<?php
		}
	}

	/** ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	----------------------------- UTILS -----------------------------
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
	/**
	 * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.
	 *
	 * @param str $hex Colour as hexadecimal (with or without hash).
	 * @param num $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() ).
	 * @return str Lightened/Darkend colour as hexadecimal (with hash).
	 */
	public function color_luminance( $hex, $percent ) {

		// Validate hex string.
		$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
		$new_hex = '#';

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		}
		// Convert to decimal and change luminosity.
		for ( $i = 0; $i < 3; $i++ ) {
			$dec = hexdec( substr( $hex, $i * 2, 2 ) );
			$dec = min( max( 0, $dec + $dec * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ), 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;
	}

	/**
	 * Get size information for all currently-registered image sizes.
	 * Used to create an option select menu
	 *
	 * @global $_wp_additional_image_sizes
	 * @uses   get_intermediate_image_sizes()
	 * @return array $sizes Data for all currently-registered image sizes.
	 */
	public function available_thumbs_size() {
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}
		return $sizes;
	}

	/**
	 * Shuffle array to get random list
	 *
	 * @param  array $list the array to shuffle.
	 * @return  array $random the shuffled array.
	 */
	public function shuffle( $list ) {
		if ( is_array( $list ) ) {
			shuffle( $list );
		}
		return $list;
	}

	/**
	 * Count mememe_template_tag terms also on unpublished attachments
	 */
	public function count_attachments_tags() {
		global $wp_taxonomies;
		if ( ! taxonomy_exists( 'mememe_template_tag' ) ) {
			return false;
		}
		$wp_taxonomies['mememe_template_tag']->update_count_callback = '_update_generic_term_count';
	}

	/**
	 * Create categories list links
	 *
	 * @param int $id The id of the post.
	 * @param str $taxonomy The taxonomy to look in.
	 * @return Array with classes and links
	 */
	public function mememe_list_cateogries( $id, $taxonomy = 'mememe_category' ) {

		$catlist = '';
		$catclass = '';
		$catresult = false;

		if ( $id ) {
			$terms = get_the_terms( $id, $taxonomy );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$catlist = '<ul class="mememe-cat-list">';
				foreach ( $terms as $term ) {
					$catclass .= ' mememe-filter-' . $term->slug;
					$term_link = get_term_link( $term );
					$catlist .= '<li><a href="' . esc_url( $term_link ) . '">' . $term->name . '</a></li>';
				}
				$catlist .= '</ul>';
				$catresult = array(
					'list' => $catlist,
					'classes' => $catclass,
				);
			}
		}
		return $catresult;
	}

	/**
	 * Get attachment ID bby slug
	 *
	 * @param str $slug The attachment slug.
	 * @return int attachment id
	 */
	public function get_attachment_id_by_slug( $slug ) {
		$args = array(
			'post_type' => 'attachment',
			'name' => sanitize_title( $slug ),
			'posts_per_page' => 1,
			'post_status' => 'inherit',
		);
		$_attach = get_posts( $args );
		$attach = $_attach ? array_pop( $_attach ) : null;
		return $attach ? absint( $attach->ID ) : '';
	}

	/**
	 * Register JavaScript
	 */
	public function mememe_register_scripts() {
		if ( ! defined( 'WP_DEBUG' ) || true !== WP_DEBUG ) {
			wp_register_script(
				'mememe-app',
				plugin_dir_url( __FILE__ ) . 'js/mememe-wp-plugin.min.js',
				array(
					'jquery',
					'jquery-ui-draggable',
					'jquery-ui-resizable',
					'jquery-ui-widget',
					'jquery-ui-mouse',
					'jquery-touch-punch',
					'imagesloaded',
				),
				MEMEME_PLUGIN_VERSION,
				true
			);
		}
	}

	/**
	 * Enqueue JavaScript and CSS
	 */
	public function mememe_scripts() {

		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG && file_exists( dirname( __FILE__ ) . '/dev/' ) ) {
			wp_enqueue_style( 'mememe-icons', plugin_dir_url( __FILE__ ) . 'dev/iconmememe.css', array(), MEMEME_PLUGIN_VERSION );
			wp_enqueue_style( 'mememe-minicolors', plugin_dir_url( __FILE__ ) . 'dev/minicolors/jquery.minicolors.css', array(), '2.3.5' );
			wp_enqueue_style( 'mememe-style', plugin_dir_url( __FILE__ ) . 'dev/style.css', array(), MEMEME_PLUGIN_VERSION );

			wp_enqueue_script( 'mememe-fontloader', plugin_dir_url( __FILE__ ) . 'dev/webfontloader.js', array(), '1.6.28', true );
			wp_enqueue_script( 'mememe-isotope', plugin_dir_url( __FILE__ ) . 'dev/isotope.pkgd.min.js', array( 'jquery', 'imagesloaded' ), '3.0.6', true );
			wp_enqueue_script( 'mememe-isotope-packery', plugin_dir_url( __FILE__ ) . 'dev/packery-mode.pkgd.min.js', array( 'jquery', 'mememe-isotope' ), '2.0.1', true );
			wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'dev/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );
			wp_enqueue_script( 'mememe-minicolors', plugin_dir_url( __FILE__ ) . 'dev/minicolors/jquery.minicolors.min.js', array( 'jquery' ), '2.3.5', true );
			// wp_enqueue_script( 'exif-js', plugin_dir_url( __FILE__ ) . 'dev/exif.js', array(), '2.3.0', true );
			wp_enqueue_script( 'jquery-ui-rotatable', plugin_dir_url( __FILE__ ) . 'dev/jquery.ui.rotatable.min.js', array( 'jquery', 'jquery-ui-mouse', 'jquery-touch-punch' ), '2.3.0', true );

			wp_enqueue_script(
				'mememe-plugin',
				plugin_dir_url( __FILE__ ) . 'dev/mememe.js',
				array(
					'jquery-ui-draggable',
					'jquery-ui-resizable',
					'jquery-ui-rotatable',
					'jquery-ui-widget',
					'mememe-minicolors',
					'owl-carousel',
					// 'exif-js',
				),
				MEMEME_PLUGIN_VERSION,
				true
			);

			wp_enqueue_script(
				'mememe-app',
				plugin_dir_url( __FILE__ ) . 'dev/mememe-call.js',
				array(
					'mememe-fontloader',
					'mememe-isotope',
					'mememe-plugin',
				),
				MEMEME_PLUGIN_VERSION,
				true
			);

		} else {
			wp_enqueue_style( 'mememe-style', plugin_dir_url( __FILE__ ) . 'css/style.min.css', array(), MEMEME_PLUGIN_VERSION );
		}
		// Set custom style.
		$text_box_width = mememe_admin()->get_option( 'mememe_option_text_box_width', 70 ) . '%';
		$btncolor = mememe_admin()->get_option( 'mememe_option_btncolor', '#333333' );
		$btnbg = mememe_admin()->get_option( 'mememe_option_btnbg', '#D7DADA' );
		$btnbghover = $this->color_luminance( $btnbg, 0.2 );

		$bg_dark = mememe_admin()->get_option( 'mememe_option_bg_dark', '#2E2E2E' );
		$bg_light = mememe_admin()->get_option( 'mememe_option_bg_light', '#FFFFFF' );

		$rating_btncolor = mememe_admin()->get_option( 'mememe_option_rating_btncolor', '#333333' );
		$rating_btnbg = mememe_admin()->get_option( 'mememe_option_rating_btnbg', '#D7DADA' );
		$rating_btnbgactive = $this->color_luminance( $rating_btnbg, -0.2 );
		$bgcolor = mememe_admin()->get_option( 'mememe_option_bgcolor', '#EBEEEE' );

		$custom_css = ".mememe-wrap-gallery .mememe-card-link, .mememe-picker, .wrap-mememe .mememe-btn, .mememe .mememe-btn, .wrap-mememe .mememe-btn.disabled:hover, .mememe-filters .mememe-filter.active{ background: {$btnbg}; color: {$btncolor};} 
		.dragmememe{ width: {$text_box_width}; }
.mememe-placeholder{ background: {$bgcolor}; }
.mememe-dot, .mememe-outline{ background: {$btncolor}; }
.mememe-wrap-gallery .mememe-btn.mememe-outline-btn{ border-color: {$btnbg}; color: {$btnbg}; }
.mememe-button-group-pills input:checked + label,
.mememe-button-group-pills input:hover:checked + label,
.mememe-wrap-gallery .mememe-btn.mememe-outline-btn:hover,
.mememe-wrap-gallery .mememe-btn.mememe-outline-btn:active,
.mememe-wrap-gallery .mememe-btn.mememe-outline-btn:focus{
color:  {$btncolor};
background-color: {$btnbg};
}
.mememe-button-group-pills input:hover + label,
.mememe-btn.active,
.mememe-btn:hover,
.mememe-btn:focus,
.mememe-btn.focus,
.mememe-wrap-gallery .mememe-card-link:hover,
.mememe-filters .mememe-filter.active:hover,
.wrap-mememe .mememe-btn.active:hover,
.wrap-mememe .mememe-btn:hover,
.wrap-mememe .mememe-btn:focus,
.wrap-mememe .mememe-btn.focus {
background-color: {$btnbghover};
color: {$btncolor};
}
.mememe-wrap-gallery .mememe-card{
	color: {$bg_dark};
	background-color: {$bg_light};
}
.mememe-wrap-gallery.mmm-dark .mememe-card{
	color: {$bg_light};
	background-color: {$bg_dark};
}
.mememe-wrap-gallery .mememe-card-overlay{background-color: {$bg_light}}
.mememe-wrap-gallery.mmm-dark .mememe-card-overlay{background-color: {$bg_dark}}
.mememe-post-like .mememe-vote-btn{
  background-color: {$rating_btnbg};
  color: {$rating_btncolor}
}
.mememe-voted-down .mememe-count-down.mememe-vote-btn,
.mememe-voted-up .mememe-count-up.mememe-vote-btn{
  background-color: {$rating_btnbgactive};
}";
		$custom_css = str_replace( array( "\r", "\n" ), '', $custom_css );
		wp_add_inline_style( 'mememe-style', $custom_css );
		$loadimg = false;

		if ( isset( $_GET['mememe_tpl'] ) ) {
			$loadimg = $this->get_attachment_id_by_slug( sanitize_title( wp_unslash( $_GET['mememe_tpl'] ) ) );
		}

		$localizevars = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'mememe-ajax-nonce' ),
			'mode' => mememe_admin()->get_option( 'mememe_option_mode', 'text' ),
			'finalwidth' => mememe_admin()->get_option( 'mememe_option_final_size', 800 ),
			'color' => mememe_admin()->get_option( 'mememe_option_color', '#F2FFFF' ),
			'outcolor' => mememe_admin()->get_option( 'mememe_option_outcolor', '#000000' ),
			'bgcolor' => $bgcolor,
			'direction' => mememe_admin()->get_option( 'mememe_option_text_direction', 'LTR' ),
			'text_box_num' => mememe_admin()->get_option( 'mememe_option_text_box_num', 1 ),
			'textposition' => mememe_admin()->get_option( 'mememe_option_text_box_position', 'top-center' ),
			'placeholder' => mememe_admin()->get_option( 'mememe_option_placeholder', '' ),
			'stroke' => mememe_admin()->get_option( 'mememe_option_stroke', 4 ),
			'outline' => mememe_admin()->get_option( 'mememe_option_outline', 2 ),
			'watermark' => mememe_admin()->get_option( 'mememe_option_watermark', '' ),
			'watermark_image' => mememe_admin()->get_option( 'mememe_option_watermark_image', '' ),
			'watermarkposition' => mememe_admin()->get_option( 'mememe_option_watermark_position', 'bottom-right' ),
			'uploader' => mememe_admin()->get_option( 'mememe_option_uploader', false ),
			'random' => mememe_admin()->get_option( 'mememe_option_random', false ),
			'show_tools' => mememe_admin()->get_option( 'mememe_option_show_tools', false ),
			'spacer' => mememe_admin()->get_option( 'mememe_option_spacer', false ),
			'loadimg' => $loadimg,
			'url' => get_permalink(),
			'labels' => array(
				'color' => __( 'Color', 'mememe' ),
				'outline_color' => __( 'Outline / Shadow color', 'mememe' ),
				'mode' => __( 'Mode', 'mememe' ),
				'upload' => __( 'Upload Image', 'mememe' ),
				'tools' => __( 'Tools', 'mememe' ),
				'reset' => __( 'Clear', 'mememe' ),
				'save' => __( 'Save', 'mememe' ),
				'select_color' => __( 'Select color', 'mememe' ),
				'size' => __( 'Size', 'mememe' ),
				'stroke' => __( 'Stroke', 'mememe' ),
				'outline' => __( 'Outline', 'mememe' ),
				'shadow' => __( 'Shadow', 'mememe' ),
				'outlineshadow' => __( 'Outline / Shadow', 'mememe' ),
				'preview' => __( 'Preview mode', 'mememe' ),
				'font' => __( 'Select Font', 'mememe' ),
				'new_text_box' => __( 'Add new text box', 'mememe' ),
				'spacer' => __( 'Spacer', 'mememe' ),
				'none' => __( 'None', 'mememe' ),
				'top' => __( 'Top', 'mememe' ),
				'bottom' => __( 'Bottom', 'mememe' ),
				'topbottom' => __( 'Top-Bottom', 'mememe' ),
				'white' => __( 'White', 'mememe' ),
				'black' => __( 'Black', 'mememe' ),
				'default' => __( 'Default', 'mememe' ),
				'textalign' => __( 'Text alignment', 'mememe' ),
			),
			'textinit' => mememe_admin()->get_option( 'mememe_option_textinit', '' ),
		);

		$fontlist = false;
		// Get font list.
		$fonts = mememe_admin()->get_option( 'mememe_option_font', false );

		$fontlist = array();

		if ( $fonts ) {
			foreach ( $fonts as $font ) {
				if ( ! empty( $font['google_fonts'] ) ) {
					$fontlist[] = $font['google_fonts'];
				}
			}
		}

		// Get font list.
		$customfonts = mememe_admin()->get_option( 'mememe_option_custom_font', false );

		if ( $customfonts ) {
			foreach ( $customfonts as $font ) {
				if ( ! empty( $font['custom_fonts'] ) ) {
					$fontvariant = ':';
					if ( ! empty( $font['custom_fonts_weight'] ) ) {
						$fontvariant .= $font['custom_fonts_weight'];
					}
					if ( ! empty( $font['custom_fonts_style'] ) ) {
						$fontvariant .= $font['custom_fonts_style'];
					}
					$fontlist[] = $font['custom_fonts'] . $fontvariant . '**';
				}
			}
		}

		$fontlist = array_filter( $fontlist );

		if ( ! empty( $fontlist ) ) {
			$localizevars['fonts'] = json_encode( $fontlist );
		}

		// Localize data for script.
		wp_localize_script( 'mememe-app', 'MEMEME', $localizevars );
	}
}

/**
 * Helper function to get/return the MeMeMe_Plugin object
 *
 * @return MeMeMe_Plugin object
 */
function mememe_plugin() {
	return MeMeMe_Plugin::get_instance();
}
