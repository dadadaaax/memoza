<?php
/*adding sections for sidebar options */
$wp_customize->add_section( 'supernews-wc-shop-archive-option', array(
	'priority'       => 20,
	'capability'     => 'edit_theme_options',
	'title'          => esc_html__( 'Shop Archive Sidebar Layout', 'supernews' ),
	'panel'          => 'supernews-wc-panel'
) );

/*Sidebar Layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-wc-shop-archive-sidebar-layout]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-wc-shop-archive-sidebar-layout'],
	'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_sidebar_layout();
$wp_customize->add_control( 'supernews_theme_options[supernews-wc-shop-archive-sidebar-layout]', array(
	'choices'  	=> $choices,
	'label'		=> esc_html__( 'Shop Archive Sidebar Layout', 'supernews' ),
	'section'   => 'supernews-wc-shop-archive-option',
	'settings'  => 'supernews_theme_options[supernews-wc-shop-archive-sidebar-layout]',
	'type'	  	=> 'select'
) );

/*wc-product-column-number*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-wc-product-column-number]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-wc-product-column-number'],
	'sanitize_callback' => 'absint'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-wc-product-column-number]', array(
	'label'		=> esc_html__( 'Products Per Row', 'supernews' ),
	'section'   => 'supernews-wc-shop-archive-option',
	'settings'  => 'supernews_theme_options[supernews-wc-product-column-number]',
	'type'	  	=> 'number'
) );

/*wc-shop-archive-total-product*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-wc-shop-archive-total-product]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-wc-shop-archive-total-product'],
	'sanitize_callback' => 'absint'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-wc-shop-archive-total-product]', array(
	'label'		=> esc_html__( 'Total Products Per Page', 'supernews' ),
	'section'   => 'supernews-wc-shop-archive-option',
	'settings'  => 'supernews_theme_options[supernews-wc-shop-archive-total-product]',
	'type'	  	=> 'number'
) );