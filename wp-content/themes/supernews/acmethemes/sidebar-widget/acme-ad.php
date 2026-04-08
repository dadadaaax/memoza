<?php
/**
 * Custom advertisement
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */
if ( ! class_exists( 'supernews_ad_widget' ) ) :
    /**
     * Class for adding advertisement widget
     * A new way to add advertisement
     * @package Acme Themes
     * @subpackage SuperNews
     * @since 1.0.0
     */
    class supernews_ad_widget extends WP_Widget {
        /*defaults values for fields*/
        private $defaults = array(
            'supernews_ad_title' => '',
            'supernews_ad_image' => '',
            'supernews_ad_link'  => '',
            'supernews_ad_new_window' => 1,
            'supernews_ad_img_alt'  => ''
        );
        function __construct() {
            parent::__construct(
            /*Base ID of your widget*/
                'supernews_ad',
                /*Widget name will appear in UI*/
                __('AT Advertisement', 'supernews'),
                /*Widget description*/
                array( 'description' => __( 'Add advertisement with different options.', 'supernews' ), )
            );
        }

        /*Widget Backend*/
        public function form( $instance ) {
            /*merging arrays*/
            $instance = wp_parse_args( (array) $instance, $this->defaults);
            $supernews_ad_title  = esc_attr( $instance['supernews_ad_title'] );
            $supernews_ad_image  = esc_url( $instance['supernews_ad_image'] );
            $supernews_ad_link   = esc_url( $instance['supernews_ad_link'] );
            $supernews_ad_new_window = esc_attr( $instance['supernews_ad_new_window'] );
            $supernews_ad_img_alt = esc_attr( $instance['supernews_ad_img_alt'] );
            ?>
            <p class="description">
                <?php
                esc_html_e('You can use any size of Advertisement image but recommended to use proper image according to sidebar width.', 'supernews' );
                ?>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'supernews_ad_title' ); ?>"><?php esc_html_e( 'Title:', 'supernews' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'supernews_ad_title' ); ?>" name="<?php echo $this->get_field_name( 'supernews_ad_title' ); ?>" type="text" value="<?php echo esc_attr( $supernews_ad_title ); ?>" />
            </p>
            <h4 class="accordion-toggle"><?php esc_html_e( 'Advertisement Image', 'supernews' ); ?></h4>
            <p>
                <label for="<?php echo $this->get_field_id('supernews_ad_image'); ?>">
                    <?php esc_html_e( 'Select Advertisement Image', 'supernews' ); ?>
                </label>
                <?php
                $supernews_display_none = '';
                if ( empty( $supernews_ad_image ) ){
                    $supernews_display_none = ' style="display:none;" ';
                }
                ?>
                <span class="img-preview-wrap" <?php echo esc_attr( $supernews_display_none ); ?>>
                    <img class="widefat" src="<?php echo esc_url( $supernews_ad_image ); ?>" alt="<?php esc_attr_e( 'Image preview', 'supernews' ); ?>"  />
                </span><!-- .ad-preview-wrap -->
                <input type="text" class="widefat" name="<?php echo $this->get_field_name('supernews_ad_image'); ?>" id="<?php echo $this->get_field_id('supernews_ad_image'); ?>" value="<?php echo esc_url( $supernews_ad_image ); ?>" />
                <input type="button" value="<?php esc_attr_e( 'Upload Image', 'supernews' ); ?>" class="button media-image-upload" data-title="<?php _e( 'Select Ad Image','supernews'); ?>" data-button="<?php _e( 'Select Ad Image','supernews'); ?>"/>
                <input type="button" value="<?php esc_attr_e( 'Remove Image', 'supernews' ); ?>" class="button media-image-remove" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'supernews_ad_img_alt' ); ?>"><?php esc_html_e( 'Alt Text:', 'supernews' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'supernews_ad_img_alt' ); ?>" name="<?php echo $this->get_field_name( 'supernews_ad_img_alt' ); ?>" type="text" value="<?php echo esc_attr( $supernews_ad_img_alt ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'supernews_ad_link' ); ?>"><?php esc_html_e( 'Link URL:', 'supernews' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'supernews_ad_link' ); ?>" name="<?php echo $this->get_field_name( 'supernews_ad_link' ); ?>" type="text" value="<?php echo esc_attr( $supernews_ad_link ); ?>" />
            </p>
            <p>
                <input id="<?php echo $this->get_field_id( 'supernews_ad_new_window' ); ?>" name="<?php echo $this->get_field_name( 'supernews_ad_new_window' ); ?>" type="checkbox" <?php checked( 1 == $supernews_ad_new_window ? $instance['supernews_ad_new_window'] : 0); ?> />
                <label for="<?php echo $this->get_field_id( 'supernews_ad_new_window' ); ?>"><?php esc_html_e( 'Open in new window', 'supernews' ); ?></label>
            </p>
            <hr />
            <?php
        }

        /**
         * Function to Updating widget replacing old instances with new
         *
         * @access public
         * @since 1.0
         *
         * @param array $new_instance new arrays value
         * @param array $old_instance old arrays value
         * @return array
         *
         */
        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['supernews_ad_title'] = ( isset( $new_instance['supernews_ad_title'] ) ) ?  sanitize_text_field( $new_instance['supernews_ad_title'] ): '';
            $instance['supernews_ad_image'] = ( isset( $new_instance['supernews_ad_image'] ) ) ?  esc_url_raw( $new_instance['supernews_ad_image'] ): '';
            $instance['supernews_ad_link'] = ( isset( $new_instance['supernews_ad_link'] ) ) ?  esc_url_raw( $new_instance['supernews_ad_link'] ): '';
            $instance['supernews_ad_img_alt'] = ( isset( $new_instance['supernews_ad_img_alt'] ) ) ?  esc_attr( $new_instance['supernews_ad_img_alt'] ): '';
            $instance['supernews_ad_new_window'] = isset($new_instance['supernews_ad_new_window'])? 1 : 0;

            return $instance;
        }

        /**
         * Function to Creating widget front-end. This is where the action happens
         *
         * @access public
         * @since 1.0
         *
         * @param array $args widget setting
         * @param array $instance saved values
         * @return void
         *
         */
        function widget( $args, $instance ) {
            $instance = wp_parse_args( (array) $instance, $this->defaults);
            $supernews_ad_title = apply_filters( 'widget_title', $instance['supernews_ad_title'], $instance, $this->id_base );
            $supernews_ad_image          = esc_url( $instance['supernews_ad_image'] );
            $supernews_ad_link           = esc_url( $instance['supernews_ad_link'] );
            $supernews_ad_new_window = esc_attr( $instance['supernews_ad_new_window'] );
            $supernews_ad_img_alt           = esc_attr( $instance['supernews_ad_img_alt'] );

            echo $args['before_widget'];

            if ( !empty($supernews_ad_title) ) {
                echo $args['before_title'] . $supernews_ad_title . $args['after_title'];
            }
            $ad_content_image = '';
            if ( ! empty( $supernews_ad_image ) ) {
                /*creating add*/
                $img_html = '<img src="' . $supernews_ad_image . '" alt="'.$supernews_ad_img_alt . '" />';
                $link_open = '';
                $link_close = '';
                if ( ! empty( $supernews_ad_link ) ) {
                    $target_text = ( 1 == $supernews_ad_new_window ) ? ' target="_blank" ' : '';
                    $link_open = '<a href="' . esc_url( $supernews_ad_link ) . '" ' . $target_text . '>';
                    $link_close = '</a>';
                }
                $ad_content_image = $link_open . $img_html .  $link_close;
            }
            if ( !empty( $ad_content_image ) ) {
                echo '<div class="supernews-ainfo-widget">';
                echo $ad_content_image;
                echo '</div>';
            }
            echo $args['after_widget'];
        }
    }
endif;

if ( ! function_exists( 'supernews_ad_widget' ) ) :
    /**
     * Function to Register and load the widget
     *
     * @since 1.0
     *
     * @param null
     * @return void
     *
     */
    function supernews_ad_widget() {
        register_widget( 'supernews_ad_widget' );
    }
endif;
add_action( 'widgets_init', 'supernews_ad_widget' );