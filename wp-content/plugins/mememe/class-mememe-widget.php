<?php
/**
 * Widgets
 *
 * @package MeMeMe
 */

/**
 * MeMeme generator Widget
 *
 * @category  WordPress_Plugin
 * @package   MeMeMe
 * @author    Nicola Franchini
 */
class MeMeMe_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Instantiate the parent object.
		parent::__construct(
			false, // Base ID.
			esc_html__( 'MeMeMe Generator', 'mememe' ), // Name.
			array( 'description' => esc_html__( 'The Generator', 'mememe' ) )
		);
	}

	/**
	 * Display widget
	 *
	 * @param array $args  Array of updated fields.
	 * @param obj   $instance Instance of widget.
	 */
	public function widget( $args, $instance ) {

		// $title = esc_attr( $instance['title'] );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		$data_escaped = $args['before_widget'];
		if ( isset( $title ) && strlen( $title ) > 0 ) {
			$data_escaped .= $args['before_title'] . $title . $args['after_title'];
		}

		$limit = isset( $instance['limit'] ) ? ' limit="' . esc_attr( $instance['limit'] ) . '"' : '';
		$random = isset( $instance['random'] ) ? ' random="1"' : '';
		$carousel = isset( $instance['carousel'] ) ? '' : ' nocarousel="1"';
		$autoplay = isset( $instance['autoplay'] ) ? '' : ' autoplay="1"';
		$template = isset( $instance['template'] ) ? ' template="' . esc_attr( $instance['template'] ) . '"' : '';

		$data_escaped .= do_shortcode( '[mememe' . $limit . $random . $carousel . $template . ']' );
		$data_escaped .= $args['after_widget'];

		echo $data_escaped; // XSS ok.
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance Array of updated fields.
	 * @param array $old_instance Instance of widget.
	 * @return array updated instance
	 */
	public function update( $new_instance, $old_instance ) {
		// Save widget options.
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit'] = ! empty( $new_instance['limit'] ) ? strip_tags( $new_instance['limit'] ) : get_option( 'posts_per_page' );
		$instance['template'] = ! empty( $new_instance['template'] ) ? strip_tags( $new_instance['template'] ) : '';

		if ( isset( $new_instance['random'] ) ) {
			$instance['random'] = $new_instance['random'];
		} else {
			unset( $instance['random'] );
		}
		if ( isset( $new_instance['carousel'] ) ) {
			$instance['carousel'] = $new_instance['carousel'];
		} else {
			unset( $instance['carousel'] );
		}
		if ( isset( $new_instance['autoplay'] ) ) {
			$instance['autoplay'] = $new_instance['autoplay'];
		} else {
			unset( $instance['autoplay'] );
		}
		return $instance;
	}

	/**
	 * Backend widget form
	 *
	 * @param array $instance Array of options to pass.
	 */
	public function form( $instance ) {
		// Output admin widget options form.
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$limit = isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : get_option( 'posts_per_page' );
		$randomcheck = isset( $instance['random'] ) ? 'checked' : '';
		$carouselcheck = isset( $instance['carousel'] ) ? 'checked' : '';
		$autoplaycheck = isset( $instance['autoplay'] ) ? 'checked' : '';

		$template = isset( $instance['template'] ) ? esc_attr( $instance['template'] ) : '';
		$templates = array_reverse( array_filter( get_option( 'mememe_options_templates', array() ) ) );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php echo esc_html__( 'Title', 'mememe' ); ?>:
			</label> 
			<input class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
			type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>" <?php echo esc_attr( $carouselcheck ); ?> 
			name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>" type="checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>"><?php echo esc_html__( 'Display templates carousel', 'mememe' ); ?></label>
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>" <?php echo esc_attr( $randomcheck ); ?> 
			name="<?php echo esc_attr( $this->get_field_name( 'random' ) ); ?>" type="checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>"><?php echo esc_html__( 'Load random templates', 'mememe' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
				<?php echo esc_html__( 'Number of templates to show', 'mememe' ); ?>:
			</label>
			<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" 
			value="<?php echo esc_attr( $limit ); ?>">
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>" <?php echo esc_attr( $autoplaycheck ); ?> 
			name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>" type="checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>"><?php echo esc_html__( 'Carousel autoplay', 'mememe' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>">
				<?php echo esc_html__( 'Default template', 'mememe' ); ?>:
			</label>
			<select class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>">
			<option value="">
				--
			</option>
			<?php
			$templist = array();
			foreach ( $templates as $key ) {
				$tempslug = get_post_field( 'post_name', $key );
				?>
				<option <?php selected( $template, $key ); ?> value="<?php echo esc_attr( $key ); ?>">
					<?php echo esc_attr( $tempslug ); ?>
				</option>
				<?php
			}
			?>
			</select>
		</p>
		<?php
	}
}

/**
 * MeMeme List generated Widget
 *
 * @category  WordPress_Plugin
 * @package   MeMeMe
 * @author    Nicola Franchini
 */
class MeMeMe_List_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Instantiate the parent object.
		parent::__construct(
			false, // Base ID.
			esc_html__( 'MeMeMe Gallery', 'mememe' ), // Name.
			array( 'description' => esc_html__( 'Recent Memes', 'mememe' ) )
		);
	}

	/**
	 * Display widget
	 *
	 * @param array $args  Array of updated fields.
	 * @param obj   $instance Instance of widget.
	 */
	public function widget( $args, $instance ) {
		// Widget output.
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		$category = isset( $instance['category'] ) ? ' category="' . esc_attr( $instance['category'] ) . '"' : '';
		$columns = isset( $instance['columns'] ) ? ' columns="' . esc_attr( $instance['columns'] ) . '"' : '';
		$limit = isset( $instance['limit'] ) ? ' limit="' . esc_attr( $instance['limit'] ) . '"' : '';
		$thmbsize = isset( $instance['thumbsize'] ) ? ' thumbsize="' . esc_attr( $instance['thumbsize'] ) . '"' : '';
		$margin = isset( $instance['margin'] ) ? ' margin="' . esc_attr( $instance['margin'] ) . '"' : '';
		$random = isset( $instance['random'] ) ? ' orderby="rand"' : '';
		$author = isset( $instance['author'] ) ? ' author="1"' : '';
		$filters = isset( $instance['filters'] ) ? ' filters="1"' : ' filters="0"';

		$data_escaped = $args['before_widget'];
		if ( isset( $title ) && strlen( $title ) > 0 ) {
			$data_escaped .= $args['before_title'] . $title . $args['after_title'];
		}
		$data_escaped .= do_shortcode( '[mememe-list' . $category . $columns . $limit . $thmbsize . $margin . $random . $author . $filters . ']' );
		$data_escaped .= $args['after_widget'];

		echo $data_escaped; // XSS ok.
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance  Array of updated fields.
	 * @param array $old_instance Instance of widget.
	 * @return array updated instance
	 */
	public function update( $new_instance, $old_instance ) {
		// Save widget options.
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? strip_tags( $new_instance['category'] ) : 0;
		$instance['columns'] = ( ! empty( $new_instance['columns'] ) ) ? strip_tags( $new_instance['columns'] ) : 0;
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : get_option( 'posts_per_page' );
		$instance['thumbsize'] = ( ! empty( $new_instance['thumbsize'] ) ) ? strip_tags( $new_instance['thumbsize'] ) : 'mememe-thumb';
		$instance['margin'] = ( ! empty( $new_instance['margin'] ) ) ? strip_tags( $new_instance['margin'] ) : 0;
		if ( isset( $new_instance['random'] ) ) {
			$instance['random'] = $new_instance['random'];
		} else {
			unset( $instance['random'] );
		}
		if ( isset( $new_instance['author'] ) ) {
			$instance['author'] = $new_instance['author'];
		} else {
			unset( $instance['author'] );
		}
		if ( isset( $new_instance['filters'] ) ) {
			$instance['filters'] = $new_instance['filters'];
		} else {
			unset( $instance['filters'] );
		}
		return $instance;
	}

	/**
	 * Backend widget form
	 *
	 * @param array $instance Array of options to pass.
	 */
	public function form( $instance ) {
		// Output admin widget options form..
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$category = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : 0;
		$columns = isset( $instance['columns'] ) ? esc_attr( $instance['columns'] ) : 0;
		$limit = isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : get_option( 'posts_per_page' );
		$thumbsize = isset( $instance['thumbsize'] ) ? esc_attr( $instance['thumbsize'] ) : 'mememe-thumb';
		$margin = isset( $instance['margin'] ) ? esc_attr( $instance['margin'] ) : 0;
		$checked = isset( $instance['random'] ) ? 'checked' : '';
		$authorchecked = isset( $instance['author'] ) ? 'checked' : '';
		$filterschecked = isset( $instance['filters'] ) ? 'checked' : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php echo esc_html__( 'Title', 'mememe' ); ?>:
			</label> 
			<input class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
			type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
				<?php echo esc_html__( 'Number of memes to show', 'mememe' ); ?>:
			</label>
			<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" 
			value="<?php echo esc_attr( $limit ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>">
				<?php echo esc_html__( 'Columns', 'mememe' ); ?>:
			</label>
			<select class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>">
				<option value="0"><?php echo esc_html__( 'Responsive', 'mememe' ); ?></option>
			<?php
			for ( $col = 1; $col < 10; $col++ ) {
				?>
				<option value="<?php echo esc_attr( $col ); ?>" <?php selected( $columns, $col ); ?>>
					<?php echo esc_attr( $col ); ?>
				</option>
				<?php
			}
			?>
			</select>
		</p>
		<p>
			<?php
			$image_sizes = mememe_plugin()->available_thumbs_size();
			?>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbsize' ) ); ?>">
				<?php echo esc_html__( 'Thumbnail size', 'mememe' ); ?>:
			</label>
			<select class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'thumbsize' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'thumbsize' ) ); ?>">
			<?php
			foreach ( $image_sizes as $size_name => $size_val ) {
				$crop = $size_val['crop'] ? __( 'Proportional', 'mememe' ) : '';
				?>
				<option <?php selected( $thumbsize, $size_name ); ?> value="<?php echo esc_attr( $size_name ); ?>">
					<?php echo esc_attr( $size_name ) . ' (' . esc_attr( $size_val['width'] ) . 'x' . esc_attr( $size_val['height'] ) . ') ' . esc_attr( $crop ); ?>
				</option>
				<?php
			}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'margin' ) ); ?>">
				<?php echo esc_html__( 'Thumbnails margin', 'mememe' ); ?>:
			</label>
			<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'margin' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'margin' ) ); ?>" 
			value="<?php echo esc_attr( $margin ); ?>"> px
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'filters' ) ); ?>" <?php echo esc_attr( $filterschecked ); ?> 
			name="<?php echo esc_attr( $this->get_field_name( 'filters' ) ); ?>" type="checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'filters' ) ); ?>"><?php echo esc_html__( 'Filters', 'mememe' ); ?></label>
		</p>
		<?php
		$available_cats = get_terms( 'mememe_category' );
		if ( ! is_wp_error( $available_cats ) ) {
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
					<?php echo esc_html__( 'Category', 'mememe' ); ?>:
				</label>
				<select class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" 
					name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
					<option value="0"><?php echo esc_html__( 'All Meme Categories', 'mememe' ); ?></option>
				<?php
				foreach ( $available_cats as $term ) {
					?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $category, $term->slug ); ?>>
						<?php echo esc_attr( $term->name ); ?>
					</option>
					<?php
				}
				?>
				</select>
			</p>
			<?php
		}
		?>
			<p>
				<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>" <?php echo esc_attr( $checked ); ?> 
				name="<?php echo esc_attr( $this->get_field_name( 'random' ) ); ?>" type="checkbox">
				<label for="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>"><?php echo esc_html__( 'Display random Memes', 'mememe' ); ?></label>
			</p>
			<p>
				<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>" <?php echo esc_attr( $authorchecked ); ?> 
				name="<?php echo esc_attr( $this->get_field_name( 'author' ) ); ?>" type="checkbox">
				<label for="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>"><?php echo esc_html__( 'Current author memes', 'mememe' ); ?></label>
			</p>
		<?php
	}
}

/**
 * MeMeme List Templates Widget
 *
 * @category  WordPress_Plugin
 * @package   MeMeMe
 * @author    Nicola Franchini
 */
class MeMeMe_Templates_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Instantiate the parent object.
		parent::__construct(
			false, // Base ID.
			esc_html__( 'MeMeMe Templates', 'mememe' ), // Name.
			array( 'description' => esc_html__( 'Templates gallery', 'mememe' ) )
		);
	}

	/**
	 * Display widget
	 *
	 * @param array $args  Array of updated fields.
	 * @param obj   $instance Instance of widget.
	 */
	public function widget( $args, $instance ) {
		// Widget output.
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );
		$columns = isset( $instance['columns'] ) ? ' columns="' . esc_attr( $instance['columns'] ) . '"' : '';
		$limit = isset( $instance['limit'] ) ? ' limit="' . esc_attr( $instance['limit'] ) . '"' : '';
		$random = isset( $instance['random'] ) ? ' random="1"' : '';
		$thumbsize = isset( $instance['thumbsize'] ) ? ' thumbsize="' . esc_attr( $instance['thumbsize'] ) . '"' : '';
		$margin = isset( $instance['margin'] ) ? ' margin="' . esc_attr( $instance['margin'] ) . '"' : '';
		$filters = isset( $instance['filters'] ) ? ' filters="1"' : ' filters="0"';
		if ( isset( $instance['tags'] ) ) {
			$tagarr = explode( ',', $instance['tags'] );
			$trimtags = array_map( 'trim', $tagarr );
			$result = array_unique( $trimtags );
			$tags = ' tags="' . rtrim( trim( implode( ', ', $result ) ), ',' ) . '"';
		} else {
			$tags = '';
		}
		$data_escaped = $args['before_widget'];
		if ( isset( $title ) && strlen( $title ) > 0 ) {
			$data_escaped .= $args['before_title'] . $title . $args['after_title'];
		}
		$data_escaped .= do_shortcode( '[mememe-templates' . $columns . $limit . $random . $thumbsize . $margin . $tags . $filters . ']' );
		$data_escaped .= $args['after_widget'];

		echo $data_escaped; // XSS ok.
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance  Array of updated fields.
	 * @param array $old_instance Instance of widget.
	 * @return array updated instance
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Save widget options.
		$instance = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['columns'] = ! empty( $new_instance['columns'] ) ? strip_tags( $new_instance['columns'] ) : 0;
		$instance['limit'] = ! empty( $new_instance['limit'] ) ? strip_tags( $new_instance['limit'] ) : get_option( 'posts_per_page' );
		$instance['thumbsize'] = ! empty( $new_instance['thumbsize'] ) ? strip_tags( $new_instance['thumbsize'] ) : 'thumbnail';
		$instance['margin'] = ! empty( $new_instance['margin'] ) ? strip_tags( $new_instance['margin'] ) : 0;
		if ( isset( $new_instance['tags'] ) ) {
			$tagarr = explode( ',', $new_instance['tags'] );
			$trimtags = array_map( 'trim', $tagarr );
			$result = array_unique( $trimtags );
			$instance['tags'] = rtrim( trim( implode( ', ', $result ) ), ',' );
		} else {
			$instance['tags'] = '';
		}
		if ( isset( $new_instance['random'] ) ) {
			$instance['random'] = $new_instance['random'];
		} else {
			unset( $instance['random'] );
		}
		if ( isset( $new_instance['filters'] ) ) {
			$instance['filters'] = $new_instance['filters'];
		} else {
			unset( $instance['filters'] );
		}
		return $instance;
	}

	/**
	 * Backend widget form
	 *
	 * @param array $instance Array of options to pass.
	 */
	public function form( $instance ) {
		// Output admin widget options form..
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$columns = isset( $instance['columns'] ) ? esc_attr( $instance['columns'] ) : 0;
		$limit = isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : get_option( 'posts_per_page' );
		$checked = isset( $instance['random'] ) ? 'checked' : '';
		$thumbsize = isset( $instance['thumbsize'] ) ? esc_attr( $instance['thumbsize'] ) : 'thumbnail';
		$margin = isset( $instance['margin'] ) ? esc_attr( $instance['margin'] ) : 0;
		$checked_filters = isset( $instance['filters'] ) ? 'checked' : '';
		if ( isset( $instance['tags'] ) ) {
			$tagarr = explode( ',', $instance['tags'] );
			$trimtags = array_map( 'trim', $tagarr );
			$result = array_unique( $trimtags );
			$tags = rtrim( trim( implode( ', ', $result ) ), ',' );
		} else {
			$tags = '';
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php echo esc_html__( 'Title', 'mememe' ); ?>:
			</label> 
			<input class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
			type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
				<?php echo esc_html__( 'Number of templates to show', 'mememe' ); ?>:
			</label>
			<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" 
			value="<?php echo esc_attr( $limit ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>">
				<?php echo esc_html__( 'Columns', 'mememe' ); ?>:
			</label>
			<select class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>">
				<option value="0"><?php echo esc_html__( 'Responsive', 'mememe' ); ?></option>
			<?php
			for ( $col = 1; $col < 10; $col++ ) {
				?>
				<option value="<?php echo esc_attr( $col ); ?>" <?php selected( $columns, $col ); ?>>
					<?php echo esc_attr( $col ); ?>
				</option>
				<?php
			}
			?>
			</select>
		</p>
		<p>
			<?php
			$image_sizes = mememe_plugin()->available_thumbs_size();
			?>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbsize' ) ); ?>">
				<?php echo esc_html__( 'Thumbnail size', 'mememe' ); ?>:
			</label>
			<select class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'thumbsize' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'thumbsize' ) ); ?>">
			<?php
			foreach ( $image_sizes as $size_name => $size_val ) {
				$crop = $size_val['crop'] ? __( 'Proportional', 'mememe' ) : '';
				?>
				<option <?php selected( $thumbsize, $size_name ); ?> value="<?php echo esc_attr( $size_name ); ?>">
					<?php echo esc_attr( $size_name ) . ' (' . esc_attr( $size_val['width'] ) . 'x' . esc_attr( $size_val['height'] ) . ') ' . esc_attr( $crop ); ?>
				</option>
				<?php
			}
			?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'margin' ) ); ?>">
				<?php echo esc_html__( 'Thumbnails margin', 'mememe' ); ?>:
			</label>
			<input type="number" class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'margin' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'margin' ) ); ?>" 
			value="<?php echo esc_attr( $margin ); ?>"> px
		</p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'filters' ) ); ?>" <?php echo esc_attr( $checked_filters ); ?> 
			name="<?php echo esc_attr( $this->get_field_name( 'filters' ) ); ?>" type="checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'filters' ) ); ?>"><?php echo esc_html__( 'Filters', 'mememe' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>">
				<?php echo esc_html__( 'Tags', 'mememe' ); ?>:
			</label> 
			<input class="widefat mememe-tag-suggest" id="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'tags' ) ); ?>" type="text" value="<?php echo esc_attr( $tags ); ?>" />
		</p>
		<p><em><?php echo esc_html__( 'Separate with commas or the Enter key.', 'mememe' ); ?></em><br>
		<?php echo esc_html__( 'Show only templates with some tags', 'mememe' ); ?></p>
		<p>
			<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>" <?php echo esc_attr( $checked ); ?> 
			name="<?php echo esc_attr( $this->get_field_name( 'random' ) ); ?>" type="checkbox">
			<label for="<?php echo esc_attr( $this->get_field_id( 'random' ) ); ?>"><?php echo esc_html__( 'Display random templates', 'mememe' ); ?></label>
		</p>
		<?php
	}
}
