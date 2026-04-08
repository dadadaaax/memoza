<?php

namespace BulkWP\BulkDelete\Core\Attachments;

use BulkWP\BulkDelete\Core\Base\BaseDeletePage;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Bulk Delete Attachments Page.
 *
 * Shows the list of modules that allows you to delete attachments.
 *
 * @since 6.0.0
 */
class DeleteAttachmentsPage extends BaseDeletePage {
	protected function initialize() {
		$this->page_slug = 'bulk-delete-attachments';
		$this->item_type = 'attachments';

		$this->label = array(
			'page_title' => __( 'Bulk Delete Attachments', 'bulk-delete' ),
			'menu_title' => __( 'Bulk Delete Attachments', 'bulk-delete' ),
		);

		$this->messages = array(
			'warning_message' => __( 'WARNING: There is no undo! Once deleted, attachments are gone. Use with caution.', 'bulk-delete' ),
		);

		$this->show_link_in_plugin_list = 0;
	}

	/**
	 * Add Help tabs.
	 *
	 * @param array $help_tabs Help tabs.
	 *
	 * @return array Modified list of tabs.
	 */
	protected function add_help_tab( $help_tabs ) {
		$overview_tab = array(
			'title'    => __( 'Overview', 'bulk-delete' ),
			'id'       => 'overview_tab',
			'content'  => '<p>' . __( 'This screen contains different modules that allows you to delete terms from taxonomies', 'bulk-delete' ) . '</p>',
			'callback' => false,
		);

		$help_tabs['overview_tab'] = $overview_tab;

		return $help_tabs;
	}
}
