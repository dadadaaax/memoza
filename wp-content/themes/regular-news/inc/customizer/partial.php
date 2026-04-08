<?php
/**
* Partial functions
*
* @package Theme Palace
* @subpackage Regular News
* @since Regular News 1.0.0
*/

if ( ! function_exists( 'regular_news_headline_title_partial' ) ) :
    // headline title
    function regular_news_headline_title_partial() {
        $options = regular_news_get_theme_options();
        return esc_html( $options['headline_title'] );
    }
endif;

if ( ! function_exists( 'regular_news_must_read_title_partial' ) ) :
    // must_read title
    function regular_news_must_read_title_partial() {
        $options = regular_news_get_theme_options();
        return esc_html( $options['must_read_title'] );
    }
endif;

if ( ! function_exists( 'regular_news_blog_title_partial' ) ) :
    // blog title
    function regular_news_blog_title_partial() {
        $options = regular_news_get_theme_options();
        return esc_html( $options['blog_title'] );
    }
endif;

if ( ! function_exists( 'regular_news_copyright_text_partial' ) ) :
    // copyright text
    function regular_news_copyright_text_partial() {
        $options = regular_news_get_theme_options();
        return esc_html( $options['copyright_text'] );
    }
endif;
