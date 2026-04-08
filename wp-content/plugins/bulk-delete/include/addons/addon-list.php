<?php
/**
 * Addon related util functions.
 *
 * @since      5.5
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Util\Addon
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Display information about all available addons.
 *
 * @since 5.5
 */
function bd_display_available_addon_list() { //phpcs:ignore
	echo '<p>';
  echo '<a href="#" data-feature="scheduling" class="button-primary open-upsell">Schedule a new job</a><br class="clear"><br>';
	esc_html_e( 'Tasks available for scheduling;', 'bulk-delete' ) . '<span class="open-upsell pro-feature-inline">Available in PRO</span>';
	echo '</p>';

	echo '<ul style="list-style:disc; padding-left:35px">';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler Email', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Sends an email every time a Bulk Delete scheduler runs', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Posts by Category', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of posts based on category', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Posts by Tag', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of posts based on tag', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Posts by Custom Taxonomy', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of posts based on custom taxonomy', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Posts by Custom Post Type', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of posts based on custom post type', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Posts by Post Status', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of posts based on post status like drafts, pending posts, scheduled posts etc.', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Pages by Status', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete pages based on status', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Users by User Role', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of users based on user role', 'bulk-delete' );
	echo '</li>';

	echo '<li>';
	echo '<strong>', esc_html__( 'Scheduler for deleting Users by User Meta', 'bulk-delete' ), '</strong>', ' - ';
	echo esc_html__( 'Adds the ability to schedule auto delete of users based on user meta', 'bulk-delete' );
	echo '</li>';

	echo '</ul>';
}
