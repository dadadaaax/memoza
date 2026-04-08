<?php
/*adding theme options panel*/
$wp_customize->add_panel( 'supernews-wc-panel', array(
	'priority'       => 100,
	'capability'     => 'edit_theme_options',
	'title'          => esc_html__( 'WooCommerce Options', 'supernews' )
) );

/*
* file for shop archive
*/
require_once supernews_file_directory('acmethemes/customizer/wc-options/shop-archive.php');

/*
* file for single product
*/
require_once supernews_file_directory('acmethemes/customizer/wc-options/single-product.php');