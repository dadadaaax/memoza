<?php
if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Supernews_Customize_Category_Dropdown_Control' )):

    /**
     * Custom Control for category dropdown
     * @package Acme Themes
     * @subpackage SuperNews
     * @since 1.0.0
     *
     */
    class Supernews_Customize_Category_Dropdown_Control extends WP_Customize_Control {

        /**
         * Declare the control type.
         *
         * @access public
         * @var string
         */
        public $type = 'category_dropdown';

        /**
         * Function to  render the content on the theme customizer page
         *
         * @access public
         * @since 1.0.0
         *
         * @param null
         * @return void
         *
         */
        public function render_content() {
            $supernews_customizer_name = 'supernews_customizer_dropdown_categories_' . $this->id;;
            $supernews_dropdown_categories = wp_dropdown_categories(
                array(
                    'name'              => $supernews_customizer_name,
                    'echo'              => 0,
                    'show_option_none'  =>__('Select','supernews'),
                    'option_none_value' => '0',
                    'selected'          => $this->value(),
                )
            );
            $supernews_dropdown_final = str_replace( '<select', '<select ' . $this->get_link(), $supernews_dropdown_categories );
            printf(
                '<label><span class="customize-control-title">%s</span> %s</label>',
                $this->label,
                $supernews_dropdown_final
            );
        }
    }
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Supernews_Customize_Post_Dropdown_Control' )):

    /**
     * Custom Control for post dropdown
     * @package Acme Themes
     * @subpackage SuperNews
     * @since 1.0.0
     *
     */
    class Supernews_Customize_Post_Dropdown_Control extends WP_Customize_Control {
        /**
         * Declare the control type.
         *
         * @access public
         * @var string
         */
        public $type = 'post_dropdown';

        /**
         * Function to  render the content on the theme customizer page
         *
         * @access public
         * @since 1.0.0
         *
         * @param null
         * @return void
         *
         */
        public function render_content() {
            $supernews_customizer_post_args = array(
                'posts_per_page'   => 100,
            );
            $supernews_posts = get_posts( $supernews_customizer_post_args );
            if(!empty($supernews_posts))  {
                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <select <?php $this->link(); ?>>
                        <?php
                        $supernews_default_value = $this->value();
                        if( -1 == $supernews_default_value || empty($supernews_default_value)){
                            $supernews_default_selected = 1;
                        }
                        else{
                            $supernews_default_selected = 0;
                        }
                        printf('<option value="-1" %s>%s</option>',selected($supernews_default_selected, 1, false),__('Select','supernews'));
                        foreach ( $supernews_posts as $supernews_post ) {
                            printf('<option value="%s" %s>%s</option>', $supernews_post->ID, selected($this->value(), $supernews_post->ID, false), $supernews_post->post_title);
                        }
                        ?>
                    </select>
                </label>
                <?php
            }
        }
    }
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Supernews_Customize_Message_Control' )):
    /**
     * Custom Control for html display
     * @package Acme Themes
     * @subpackage SuperNews
     * @since 1.0.0
     *
     */
    class Supernews_Customize_Message_Control extends WP_Customize_Control {

        /**
         * Declare the control type.
         * @access public
         * @var string
         */
        public $type = 'message';

        /**
         * Function to  render the content on the theme customizer page
         *
         * @access public
         * @since 1.0.0
         *
         * @param null
         * @return void
         *
         */
	    public function render_content() {
		    if ( empty( $this->description ) ) {
			    return;
		    }
		    $allowed_html = array(
			    'a' => array(
				    'href' => array(),
				    'title' => array(),
				    'data-section' => array(),
				    'class' => array(),
				    'target' => array(),
			    ),
			    'hr' => array(),
			    'br' => array(),
			    'em' => array(),
			    'strong' => array(),
		    );
		    ?>
            <div class="supernews-customize-customize-message">
			    <?php
			    echo wp_kses( $this->description , $allowed_html )
			    ?>
            </div> <!-- .supernews-customize-customize-message -->
		    <?php
	    }
    }
endif;