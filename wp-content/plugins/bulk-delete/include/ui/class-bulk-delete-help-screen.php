<?php
/**
 * Bulk Delete Help Screen.
 *
 * Displays the help tab on top of Bulk Delete Admin pages
 *
 * @since      5.1
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Help
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

class Bulk_Delete_Help_Screen {
	/**
	 * Get the list of help tabs for a given screen.
	 *
	 * @since 5.1
	 * @static
	 * @access private
	 *
	 * @param string $screen Screen name
	 *
	 * @return array $help_tabs List of tabs
	 */
	private static function get_help_tabs( $screen ) {
		$bd        = BULK_DELETE();
		$help_tabs = array();

		switch ( $screen ) {
			case $bd->posts_page:
				$overview_tab = array(
					'title'    => __( 'Overview', 'bulk-delete' ),
					'id'       => 'overview_tab',
					'content'  => '<p>' . __( 'This screen contains different modules that allows you to delete posts or schedule them for deletion.', 'bulk-delete' ) . '</p>',
					'callback' => false,
				);

				$help_tabs['overview_tab'] = $overview_tab;
				break;

			case $bd->pages_page:
				// Overview tab
				$overview_tab = array(
					'title'    => __( 'Overview', 'bulk-delete' ),
					'id'       => 'overview_tab',
					'content'  => '<p>' . __( 'This screen contains different modules that allows you to delete pages or schedule them for deletion.', 'bulk-delete' ) . '</p>',
					'callback' => false,
				);

				$help_tabs['overview_tab'] = $overview_tab;
				break;
		}

		// about plugin tab
		$about_plugin_tab = array(
			'title'    => __( 'About Plugin', 'bulk-delete' ),
			'id'       => 'about_plugin_tab',
			'content'  => '',
			'callback' => array( 'Bulk_Delete_Help_Screen', 'print_about_plugin_tab_content' ),
		);

		$help_tabs['about_plugin_tab'] = $about_plugin_tab;

		/**
		 * Filters help tab content for admin screens.
		 *
		 * @since 5.1
		 */
		return apply_filters( 'bd_admin_help_tabs', $help_tabs, $screen ); //phpcs:ignore
	}

	/**
	 * print the about plugin tab content.
	 *
	 * @since 5.1
	 * @static
	 */
	public static function print_about_plugin_tab_content() {
		echo '<p>' . esc_html__( 'This plugin allows you to perform bulk operations in WordPress easily.', 'bulk-delete' ) . '</p>';
		echo '<p>' . esc_html__( 'This plugin can be used to delete the posts, pages or users using various filters and conditions.', 'bulk-delete' ) . '</p>';
	}
}
