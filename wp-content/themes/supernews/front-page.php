<?php
/**
 * The front-page template file.
 *
 * @package Acme Themes
 * @subpackage SuperNews
 * @since SuperNews 1.0.0
 */
get_header();

/**
 * supernews_action_front_page hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_front_page -  10
 */
do_action( 'supernews_action_front_page' );

get_sidebar( 'left' );
get_sidebar( );
get_footer();