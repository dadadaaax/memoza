<?php
/**
 * Demo Import.
 *
 * This is the template that includes all the other files for core featured of Theme Palace
 *
 * @package Theme Palace
 * @subpackage Regular News 
 * @since Regular News  1.0.0
 */

function regular_news_ctdi_plugin_page_setup( $default_settings ) {
    $default_settings['menu_title']  = esc_html__( 'Theme Palace Demo Import' , 'regular-news' );

    return $default_settings;
}
add_filter( 'cp-ctdi/plugin_page_setup', 'regular_news_ctdi_plugin_page_setup' );


function regular_news_ctdi_plugin_intro_text( $default_text ) {
    $default_text .= sprintf( '<p class="about-description">%1$s <a href="%2$s">%3$s</a></p>', esc_html__( 'Demo content files for Regular News  Theme.', 'regular-news' ),
    esc_url( 'https://themepalace.com/download/regular-news' ), esc_html__( 'Click here for Demo File download', 'regular-news' ) );
    return $default_text;
}
add_filter( 'cp-ctdi/plugin_intro_text', 'regular_news_ctdi_plugin_intro_text' );