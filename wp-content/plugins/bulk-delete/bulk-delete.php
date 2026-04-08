<?php
/**
 * Plugin Name: Bulk Delete
 * Plugin URI: https://bulkwp.com/
 * Description: Bulk delete users and posts from selected categories, tags, post types, custom taxonomies or by post status like drafts, scheduled posts, revisions etc.
 * Version: 6.11
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Author: WebFactory Ltd
 * Author URI: https://www.webfactoryltd.com/
 * Text Domain: bulk-delete
 */

/**
 * Copyright 2025-2026 WebFactory Ltd
 * Copyright 2009 Sudar Muthu  (email : sudar@sudarmuthu.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

define('BULK_DELETE_FILE', __FILE__);
define('BULK_DELETE_PATH', plugin_dir_path(__FILE__));
define('BULK_DELETE_URL',  plugins_url() . '/bulk-delete/');

// Include the stub of the old `Bulk_Delete` class, so that old add-ons don't generate a fatal error.
require_once 'include/Deprecated/old-bulk-delete.php';

// PHP is at least 5.3, so we can safely include namespace code.
require_once 'load-bulk-delete.php';

bulk_delete_load( __FILE__ );
