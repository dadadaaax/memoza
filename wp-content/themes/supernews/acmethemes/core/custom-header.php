<?php
/**
 * Custom header implementation
 *
 * @link https://codex.wordpress.org/Custom_Headers
 *
 * @package Acme Themes
 * @subpackage SuperNews
 * @since 1.2.0
 */

/**
 * Set up the WordPress core custom header feature.
 */
function supernews_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'supernews_custom_header_args', array(
		'default-image'				=> '',
		'header-text'				=> false,
		'width'						=> 1600,
		'height'					=> 460,
		'flex-width'				=> true,
		'flex-height'				=> true,
		'video'						=> true
    ) ) );
}
add_action( 'after_setup_theme', 'supernews_custom_header_setup' );