<?php
/*adding sections for sidebar options */
$wp_customize->add_section( 'supernews-design-sidebar-layout-option', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Default Sidebar Layout', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*Sidebar Layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-sidebar-layout]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-sidebar-layout'],
    'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_sidebar_layout();
$wp_customize->add_control( 'supernews_theme_options[supernews-sidebar-layout]', array(
    'choices'  	=> $choices,
    'label'		=> __( 'Default Sidebar Layout', 'supernews' ),
    'section'   => 'supernews-design-sidebar-layout-option',
    'settings'  => 'supernews_theme_options[supernews-sidebar-layout]',
    'type'	  	=> 'select'
) );