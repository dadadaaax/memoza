<?php
if (!function_exists('newsphere_header_section')) :
    /**
     * Banner Slider
     *
     * @since Newsphere 1.0.0
     *
     */
    function newsphere_header_section()
    {

        $header_layout = newsphere_get_option('header_layout');
        ?>

        <header id="masthead" class="header-style1 <?php echo esc_attr($header_layout); ?>">

            <?php

                newsphere_get_block('layout-1', 'header');

            ?>


     
        </header>

        <!-- end slider-section -->
        <?php
    }
endif;
add_action('newsphere_action_header_section', 'newsphere_header_section', 40);