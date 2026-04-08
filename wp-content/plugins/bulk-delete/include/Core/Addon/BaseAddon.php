<?php

namespace BulkWP\BulkDelete\Core\Addon;

use BulkWP\BulkDelete\Core\BulkDelete;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Encapsulates the logic for a add-on.
 *
 * @since 6.0.0
 */
abstract class BaseAddon {
	/**
	 * Details of the Add-on.
	 *
	 * @var \BulkWP\BulkDelete\Core\Addon\AddonInfo
	 */
	protected $addon_info;

	/**
	 * Initialize and setup variables.
	 *
	 * @return void
	 */
	abstract protected function initialize();

	/**
	 * Register the add-on.
	 *
	 * This method will be called in the `bd_loaded` hook.
	 *
	 * @return void
	 */
	abstract public function register();

	/**
	 * Create a new instance of the add-on.
	 *
	 * @param \BulkWP\BulkDelete\Core\Addon\AddonInfo $addon_info Add-on Details.
	 */
	public function __construct( $addon_info ) {
		$this->addon_info = $addon_info;

		$this->initialize();
	}

	/**
	 * Get details about the add-on.
	 *
	 * @return \BulkWP\BulkDelete\Core\Addon\AddonInfo Add-on Info.
	 */
	public function get_info() {
		return $this->addon_info;
	}

	/**
	 * Get reference to the main Bulk Delete object.
	 *
	 * @return \BulkWP\BulkDelete\Core\BulkDelete BulkDelete object.
	 */
	public function get_bd() {
		return BulkDelete::get_instance();
	}
}
