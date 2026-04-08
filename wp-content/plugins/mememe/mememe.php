<?php
/**
 * Plugin Name: MeMeMe
 * Plugin URI: https://veno.es/mememe/
 * Description: Meme generator plugin.
 * Author: Nicola Franchini
 * Text Domain: mememe
 * Domain Path: /languages
 * Version: 1.9.0
 * Author URI: https://veno.es
 *
 * @package MeMeMe
 */

define( 'MEMEME_PLUGIN_VERSION', '1.9.0' );
if ( ! class_exists( 'MeMeMe_Plugin', false ) ) {
	require_once dirname( __FILE__ ) . '/class-mememe-plugin.php';
}
// Get it started.
mememe_plugin();
