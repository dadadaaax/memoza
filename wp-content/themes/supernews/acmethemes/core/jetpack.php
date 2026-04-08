<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.me/
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 */
if ( ! function_exists( 'supernews_jetpack_setup' ) ) :
	function supernews_jetpack_setup() {
		add_theme_support( 'infinite-scroll', array(
			'container' => 'main',
			'render'    => 'supernews_infinite_scroll_render',
			'footer'    => 'page',
		) );
	} // end function supernews_jetpack_setup
endif;
add_action( 'after_setup_theme', 'supernews_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
if ( ! function_exists( 'supernews_infinite_scroll_render' ) ) :
	function supernews_infinite_scroll_render() {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_format() );
		}
	} // end function supernews_infinite_scroll_render
endif;