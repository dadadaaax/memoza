<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */

/**
 * supernews_action_before_head hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_set_global -  0
 * @hooked supernews_doctype -  10
 */
do_action( 'supernews_action_before_head' );?>
	<head>

		<?php
		/**
		 * supernews_action_before_wp_head hook
		 * @since SuperNews 1.0.0
		 *
		 * @hooked supernews_before_wp_head -  10
		 */
		do_action( 'supernews_action_before_wp_head' );

		wp_head();
		?>

	</head>
<body <?php body_class();
/**
 * supernews_action_body_attr hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_body_attr- 10
 */
do_action( 'supernews_action_body_attr' );?>>

<?php
/**
 * WordPress Default ook
 * Triggered after the opening <body> tag.
 * wp_body_open hook
 *
 * @since WordPress 5.2
 *
 */
do_action( 'wp_body_open' );
/**
 * supernews_action_before hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_page_start - 10
 * @hooked supernews_page_start - 15
 */
do_action( 'supernews_action_before' );

/**
 * supernews_action_before_header hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_skip_to_content - 10
 */
do_action( 'supernews_action_before_header' );

/**
 * supernews_action_header hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_after_header - 10
 */
do_action( 'supernews_action_header' );

/**
 * supernews_action_after_header hook
 * @since SuperNews 1.0.0
 *
 * @hooked null
 */
do_action( 'supernews_action_after_header' );

/**
 * supernews_action_before_content hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_before_content - 10
 */
do_action( 'supernews_action_before_content' );