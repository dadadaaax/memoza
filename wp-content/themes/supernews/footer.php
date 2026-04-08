<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Acme Themes
 * @subpackage SuperNews
 */

/**
 * supernews_action_after_content hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_after_content - 10
 */
do_action( 'supernews_action_after_content' );

/**
 * supernews_action_before_footer hook
 * @since SuperNews 1.0.0
 *
 * @hooked null
 */
do_action( 'supernews_action_before_footer' );

/**
 * supernews_action_footer hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_footer - 10
 */
do_action( 'supernews_action_footer' );

/**
 * supernews_action_after_footer hook
 * @since SuperNews 1.0.0
 *
 * @hooked null
 */
do_action( 'supernews_action_after_footer' );

/**
 * supernews_action_after hook
 * @since SuperNews 1.0.0
 *
 * @hooked supernews_page_end - 10
 */
do_action( 'supernews_action_after' );
wp_footer(); ?>
</body>
</html>