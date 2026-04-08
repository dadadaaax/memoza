<?php
/**
 * Base class for all BD Addons.
 *
 * @since   5.5
 *
 * @author  Sudar
 *
 * @package BulkDelete\Addons\Base
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Base class for BD Addons.
 *
 * @abstract
 *
 * @since 5.5
 */
abstract class BD_Addon { //phpcs:ignore
	/**
	 * @var string Addon Name.
	 */
	protected $addon_name;

	/**
	 * @var string Addon Code.
	 */
	protected $addon_code;

	/**
	 * @var string Addon File.
	 */
	protected $addon_file;

	/**
	 * @var string Addon Version.
	 */
	protected $addon_version;

	/**
	 * @var string Addon Author.
	 */
	protected $addon_author = 'Sudar Muthu';

	/**
	 * @var Module Name.
	 */
	protected $module;

	/**
	 * Initialize and setup variables.
	 *
	 * @since 5.5
	 * @abstract
	 *
	 * @return void
	 */
	abstract protected function initialize();

	/**
	 * Use `factory()` method to create instance of this class.
	 * Don't create instances directly.
	 *
	 * @since 5.5
	 * @see factory()
	 */
	public function __construct() {
		$this->setup();
	}

	/**
	 * Setup the module.
	 *
	 * @access protected
	 *
	 * @since 5.5
	 */
	protected function setup() {
		$this->initialize();
		if ( $this->dependencies_met() ) {
			$this->setup_hooks();
		}
	}

	/**
	 * Check if all dependencies are met.
	 * To check for dependencies overload this method in the child class.
	 *
	 * @return bool True if dependencies met, False otherwise.
	 */
	protected function dependencies_met() {
		return true;
	}

	/**
	 * Get addon class name.
	 *
	 * @since 5.5
	 *
	 * @return string Addon class name
	 */
	protected function get_addon_class_name() {
		return bd_get_addon_class_name( $this->addon_name );
	}
}
