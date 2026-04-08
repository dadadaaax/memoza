<?php

/**
 * SuperNews functions.
 * @package SuperNews
 * @since 2.0.0
 */

/**
 * check if WooCommerce activated
 */
function supernews_is_woocommerce_active() {
	return class_exists( 'WooCommerce' ) ? true : false;
}
add_action( 'init', 'supernews_remove_wc_breadcrumbs' );
function supernews_remove_wc_breadcrumbs() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
}


/**
 * Woo Commerce Number of row filter Function
 */
if (!function_exists('supernews_loop_columns')) {
	function supernews_loop_columns() {
		$supernews_customizer_all_values = supernews_get_theme_options();
		$supernews_wc_product_column_number = $supernews_customizer_all_values['supernews-wc-product-column-number'];
		if ($supernews_wc_product_column_number) {
			$column_number = $supernews_wc_product_column_number;
		}
		else {
			$column_number = 3;
		}
		return $column_number;
	}
}
add_filter('loop_shop_columns', 'supernews_loop_columns');

function supernews_loop_shop_per_page( $cols ) {
	// $cols contains the current number of products per page based on the value stored on Options -> Reading
	// Return the number of products you wanna show per page.
	$supernews_customizer_all_values = supernews_get_theme_options();
	$supernews_wc_product_total_number = $supernews_customizer_all_values['supernews-wc-shop-archive-total-product'];
	if ($supernews_wc_product_total_number) {
		$cols = $supernews_wc_product_total_number;
	}
	return $cols;
}
add_filter( 'loop_shop_per_page', 'supernews_loop_shop_per_page', 20 );