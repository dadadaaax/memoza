<?php
/**
 * Custom columns of category with various options
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */
if ( ! class_exists( 'Supernews_posts_col' ) ) {
    /**
     * Class for adding widget
     *
     *  @package Acme Themes
     * @subpackage SuperNews_posts_col
     * @since 1.0.0
     */
    class Supernews_posts_col extends WP_Widget {

        /*defaults values for fields*/
        private $defaults = array(
	        'supernews_cat_title' => '',
            'supernews_cat' => -1,
            'supernews_enable_posts_featured' => 1,
            'supernews_post_col_first_featured_image_layout' => 'large',
            'supernews_post_col_normal_image_layout' => 'large'
        );

        function __construct() {
            parent::__construct(
            /*Base ID of your widget*/
                'supernews_posts_col',
                /*Widget name will appear in UI*/
                __('AT Posts Column', 'supernews'),
                /*Widget description*/
                array( 'description' => __( 'Show posts from selected category', 'supernews' ), )
            );
        }

        /*Widget Backend*/
        public function form( $instance ) {
            $instance = wp_parse_args( (array) $instance, $this->defaults);
	        $supernews_col_posts_title = esc_attr( $instance['supernews_cat_title'] );
            $supernews_selected_cat = esc_attr( $instance['supernews_cat'] );
            $supernews_enable_posts_featured = esc_attr( $instance['supernews_enable_posts_featured'] );
	        /*first featured image*/
	        $supernews_post_col_first_featured_image_layout = $instance['supernews_post_col_first_featured_image_layout'];

	        /*normal featured image*/
	        $supernews_post_col_normal_image_layout = $instance['supernews_post_col_normal_image_layout'];

	        $choices = supernews_get_image_sizes_options();
           ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'supernews_cat_title' ); ?>"><?php _e( 'Title:', 'supernews' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'supernews_cat_title' ); ?>" name="<?php echo $this->get_field_name( 'supernews_cat_title' ); ?>" type="text" value="<?php echo $supernews_col_posts_title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('supernews_cat'); ?>"><?php esc_html_e('Select Category', 'supernews'); ?></label>
                <?php
                $supernews_dropown_cat = array(
                    'show_option_none'   => __('From Recent Posts','supernews'),
                    'orderby'            => 'name',
                    'order'              => 'asc',
                    'show_count'         => 1,
                    'hide_empty'         => 1,
                    'echo'               => 1,
                    'selected'           => $supernews_selected_cat,
                    'hierarchical'       => 1,
                    'name'               => $this->get_field_name('supernews_cat'),
                    'id'                 => $this->get_field_name('supernews_cat'),
                    'class'              => 'widefat',
                    'taxonomy'           => 'category',
                    'hide_if_empty'      => false,
                );
                wp_dropdown_categories($supernews_dropown_cat);
                ?>
            </p>
            <p>
                <input class="widefat" id="<?php echo $this->get_field_id( 'supernews_enable_posts_featured' ); ?>" name="<?php echo $this->get_field_name( 'supernews_enable_posts_featured' ); ?>" type="checkbox" <?php checked( 1, esc_attr( $supernews_enable_posts_featured ), 1 ); ?>/>
                <label for="<?php echo $this->get_field_id( 'supernews_enable_posts_featured' ); ?>"><?php esc_html_e( 'Enable Posts Featured' ,'supernews'); ?></label>
                <br />
            </p>
            <p>
                <small><?php esc_html_e( 'Note: Some of the features only work in "Home main content area" due to minimum width in other areas.' ,'supernews'); ?></small>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'supernews_post_col_first_featured_image_layout' ); ?>">
			        <?php _e( 'First Featured Post Image', 'supernews' ); ?>
                    <br />
                    <small><?php _e( 'Enable First Post Featured to work this layout', 'supernews' ); ?></small>
                </label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'supernews_post_col_first_featured_image_layout' ); ?>" name="<?php echo $this->get_field_name( 'supernews_post_col_first_featured_image_layout' ); ?>">
			        <?php
			        foreach( $choices as $key => $supernews_column_array ){
				        echo ' <option value="'.$key.'" '.selected( $supernews_post_col_first_featured_image_layout, $key, 0).'>'.$supernews_column_array.'</option>';
			        }
			        ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'supernews_post_col_normal_image_layout' ); ?>">
			        <?php _e( 'Normal Featured Post Image', 'supernews' ); ?>
                    <br />
                    <small><?php _e( 'Enable First Post Featured to work this layout', 'supernews' ); ?></small>
                </label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'supernews_post_col_normal_image_layout' ); ?>" name="<?php echo $this->get_field_name( 'supernews_post_col_normal_image_layout' ); ?>">
			        <?php
			        foreach( $choices as $key => $supernews_column_array ){
				        echo ' <option value="'.$key.'" '.selected( $supernews_post_col_normal_image_layout, $key, 0). '>'.$supernews_column_array.'</option>';
			        }
			        ?>
                </select>
            </p>
            <?php
        }

        /**
         * Function to Updating widget replacing old instances with new
         *
         * @access public
         * @since 1.0.0
         *
         * @param array $new_instance new arrays value
         * @param array $old_instance old arrays value
         * @return array
         *
         */
        public function update( $new_instance, $old_instance ) {
            $instance = array();
	        $instance['supernews_cat_title'] = ( isset( $new_instance['supernews_cat_title'] ) ) ? sanitize_text_field( $new_instance['supernews_cat_title'] ) : '';
	        $instance['supernews_cat'] = ( isset( $new_instance['supernews_cat'] ) ) ? esc_attr( $new_instance['supernews_cat'] ) : '';
            $instance['supernews_enable_posts_featured'] = isset($new_instance['supernews_enable_posts_featured'])? 1 : 0;
	        $instance['supernews_post_col_first_featured_image_layout'] = isset($new_instance['supernews_post_col_first_featured_image_layout'])? esc_attr( $new_instance['supernews_post_col_first_featured_image_layout'] ) : 'large';
	        $instance['supernews_post_col_normal_image_layout'] = isset($new_instance['supernews_post_col_normal_image_layout'])? esc_attr( $new_instance['supernews_post_col_normal_image_layout'] ) : 'large';

            return $instance;
        }
        /**
         * Function to Creating widget front-end. This is where the action happens
         *
         * @access public
         * @since 1.0.0
         *
         * @param array $args widget setting
         * @param array $instance saved values
         * @return void
         *
         */
        public function widget($args, $instance) {
            if( isset( $args['id'] )){
	            $supernews_sidebar_id = $args['id'];
            }
            else{
	            $supernews_sidebar_id = 'supernews-home';
            }

            $instance = wp_parse_args( (array) $instance, $this->defaults);

	        $supernews_selected_cat = esc_attr( $instance['supernews_cat'] );

	        /*Main title*/
	        $supernews_col_posts_title = !empty( $instance['supernews_cat_title'] ) ? esc_attr( $instance['supernews_cat_title'] ) : get_cat_name($supernews_selected_cat);
	        $supernews_col_posts_title = apply_filters( 'widget_title', $supernews_col_posts_title, $instance, $this->id_base );

	        $supernews_enable_posts_featured = esc_attr( $instance['supernews_enable_posts_featured'] );
	        /*first featured post layout*/
	        $supernews_post_col_first_featured_image_layout = esc_attr( $instance['supernews_post_col_first_featured_image_layout'] );

	        /*normal featured image*/
	        $supernews_post_col_normal_image_layout = esc_attr( $instance['supernews_post_col_normal_image_layout'] );

            /**
             * Filter the arguments for the Recent Posts widget.
             *
             * @since 1.0.0
             *
             * @see WP_Query
             *
             */
            $supernews_number = 6;
            $supernews_cat_post_args = array(
                'posts_per_page'      => $supernews_number,
                'no_found_rows'       => true,
                'post_status'         => 'publish',
                'ignore_sticky_posts' => true
            );
            if( -1 != $supernews_selected_cat ){
                $supernews_cat_post_args['cat'] = $supernews_selected_cat;
            }
         
            $supernews_featured_query = new WP_Query($supernews_cat_post_args);

            if ($supernews_featured_query->have_posts()) :
                echo $args['before_widget'];
	            if ( !empty( $supernews_col_posts_title ) ){
		            if( -1 != $supernews_selected_cat ){
			            echo "<div class='at-cat-color-wrap-".$supernews_selected_cat."'>";
		            }
		            echo $args['before_title'] . $supernews_col_posts_title . $args['after_title'];

		            if( -1 != $supernews_selected_cat ){
			            echo "</div>";
		            }
	            }
               
                $supernews_post_col_layout_class = '';
                if( 1 == $supernews_enable_posts_featured ){
                    $supernews_post_col_layout_class = ' featured-posts';
                }
                ?>
                <div class="featured-entries-col <?php echo esc_attr( $supernews_post_col_layout_class ); ?> <?php echo esc_attr( $supernews_sidebar_id ); ?>">
                    <?php
                    $supernews_featured_index = 1;
                    while ( $supernews_featured_query->have_posts() ) :$supernews_featured_query->the_post();
	                    $thumb = $supernews_post_col_normal_image_layout;
                        $supernews_list_classes = 'acme-col-2';
                        $supernews_words = 21;
                        if( $supernews_featured_index % 2 == 1 ){
                            echo "<div class='clearfix'></div>";
                            $supernews_list_classes .= ' odd';
                        }
                        if( 1 == $supernews_enable_posts_featured ){
                            if( $supernews_featured_index <= 2 ){
                                $supernews_list_classes .= ' first-two';
	                            $thumb = $supernews_post_col_first_featured_image_layout;
                            }
                            else{
                                $supernews_list_classes .= ' small-posts';
	                            $thumb = $supernews_post_col_normal_image_layout;
                            }
                        }
                        ?>
                        <div class="<?php echo esc_attr( $supernews_list_classes ); ?>">
                            <!--post thumbnal options-->
                            <div class="post-thumb">
                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if( has_post_thumbnail() ):
                                        the_post_thumbnail( $thumb );
                                    else:
                                        ?>
                                        <div class="no-image-widgets">
                                            <?php
                                            the_title( sprintf( '<h2 class="caption-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
                                            if( !get_the_title() ){
                                                the_date( '', sprintf( '<h2 class="caption-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    endif;
                                    ?>
                                </a>
                                <?php
                                if(1 == $supernews_enable_posts_featured ){
                                    if(  $supernews_featured_index <= 2  ){
                                        supernews_list_category();
                                    }
                                }
                                else{
                                    supernews_list_category();
                                }
                                ?>
                            </div><!-- .post-thumb-->
                            <div class="post-content">
                                <div class="entry-header">
                                    <?php the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
                                    <div class="entry-meta">
                                        <?php
                                        if ( 'post' === get_post_type() ) :
                                            supernews_posted_on();
                                        endif;
                                        supernews_entry_footer();
                                        ?>
                                    </div><!-- .entry-meta -->
                                </div><!-- .entry-header -->
                                <?php
                                if(1 == $supernews_enable_posts_featured ){
                                    if(  $supernews_featured_index <= 2  ){
                                        ?>
                                        <div class="entry-content">
                                            <?php
                                            $content = supernews_words_count( get_the_excerpt(), $supernews_words );
                                            echo '<div class="details">'.esc_html( $content ).'</div>';
                                            ?>
                                        </div><!-- .entry-content -->
                                        <?php
                                    }
                                }
                                else{
                                    ?>
                                    <div class="entry-content">
                                        <?php
                                        $content = supernews_words_count( get_the_excerpt(), $supernews_words );
                                        echo '<div class="details">'.esc_html( $content ).'</div>';
                                        ?>
                                    </div><!-- .entry-content -->
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                        $supernews_featured_index++;
                    endwhile;
                    ?>
                </div>
                <?php echo $args['after_widget'];
                echo "<div class='clearfix'></div>";
                // Reset the global $the_post as this query will have stomped on it
                wp_reset_postdata();
            endif;
        }
    } // Class Supernews_posts_col ends here
}

if ( ! function_exists( 'supernews_posts_col' ) ) :
    /**
     * Function to Register and load the widget
     *
     * @since 1.0.0
     *
     * @param null
     * @return void
     *
     */
    function supernews_posts_col() {
        register_widget( 'Supernews_posts_col' );
    }
endif;
add_action( 'widgets_init', 'supernews_posts_col' );
