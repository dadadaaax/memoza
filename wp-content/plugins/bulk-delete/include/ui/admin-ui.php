<?php
/**
 * Customize admin UI for Bulk Delete plugin.
 *
 * @since      5.0
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Admin
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Add rating links to the admin dashboard.
 *
 * @since     5.0
 *
 * @param string $footer_text The existing footer text
 *
 * @return string
 */
function bd_add_rating_link( $footer_text ) { //phpcs:ignore
	/* translators: %1$s is the bulkwp.com website url, %2$s is the WordPress rating page url */
	$rating_text = sprintf( __( 'Thank you for using the <a target="_blank" href="%1$s">Bulk Delete</a> plugin! Please <a target="_blank" href="%2$s">rate it</a>.', 'bulk-delete' ),
		'https://bulkwp.com/',
		'https://wordpress.org/support/view/plugin-reviews/bulk-delete?filter=5#postform'
	);

	$rating_text = apply_filters( 'bd_rating_link', $rating_text ); //phpcs:ignore

	return str_replace( '</span>', '', $footer_text ) . ' | ' . $rating_text . '</span>';
}

/**
 * Modify admin footer in Bulk Delete plugin pages.
 *
 * @since     5.0
 */
function bd_modify_admin_footer() { //phpcs:ignore
	add_filter( 'admin_footer_text', 'bd_add_rating_link' );
}

// Modify admin footer
add_action( 'bd_admin_footer_posts_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_pages_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_cron_page' , 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_addon_page', 'bd_modify_admin_footer' );
add_action( 'bd_admin_footer_info_page' , 'bd_modify_admin_footer' );
