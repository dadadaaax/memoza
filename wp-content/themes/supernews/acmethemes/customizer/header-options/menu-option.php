<?php
/*Menu Section*/
$wp_customize->add_section( 'supernews-header-menu', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Menu Options', 'supernews' ),
    'panel'          => 'supernews-header-panel'
) );

/*sticky menu*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-enable-sticky-menu]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-enable-sticky-menu'],
    'sanitize_callback' => 'supernews_sanitize_checkbox'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-enable-sticky-menu]', array(
    'label'		=> __( 'Enable Sticky Menu', 'supernews' ),
    'section'   => 'supernews-header-menu',
    'settings'  => 'supernews_theme_options[supernews-enable-sticky-menu]',
    'type'	  	=> 'checkbox',
    'priority'  => 70
) );