<?php
/**
 * Uninstall
 *
 * @package MeMeMe
 * @version 1.0.0
 */

// If uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'mememe_options' );
delete_option( 'mememe_plugin_version' );
delete_option( 'mememe_googlefont_cache' );
delete_option( 'mememe_googlefont_cache_last' );
