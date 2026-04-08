<?php
/*adding sections for sidebar options */
$wp_customize->add_section( 'supernews-wc-single-product-options', array(
	'priority'       => 20,
	'capability'     => 'edit_theme_options',
	'title'          => esc_html__( 'Single Product', 'supernews' ),
	'panel'          => 'supernews-wc-panel'
) );

/*Sidebar Layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-wc-single-product-sidebar-layout]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-wc-single-product-sidebar-layout'],
	'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_sidebar_layout();
$wp_customize->add_control( 'supernews_theme_options[supernews-wc-single-product-sidebar-layout]', array(
	'choices'  	=> $choices,
	'label'		=> esc_html__( 'Single Product Sidebar Layout', 'supernews' ),
	'section'   => 'supernews-wc-single-product-options',
	'settings'  => 'supernews_theme_options[supernews-wc-single-product-sidebar-layout]',
	'type'	  	=> 'select'
) );