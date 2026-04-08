<?php
/*adding sections for sidebar options */
$wp_customize->add_section( 'supernews-design-sidebar-sticky-option', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Sticky Sidebar Option', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*sticky sidebar enable disable*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-enable-sticky-sidebar]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-enable-sticky-sidebar'],
    'sanitize_callback' => 'supernews_sanitize_checkbox'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-enable-sticky-sidebar]', array(
    'label'		=> __( 'Enable Sticky Sidebar', 'supernews' ),
    'section'   => 'supernews-design-sidebar-sticky-option',
    'settings'  => 'supernews_theme_options[supernews-enable-sticky-sidebar]',
    'type'	  	=> 'checkbox',
    'priority'  => 30
) );