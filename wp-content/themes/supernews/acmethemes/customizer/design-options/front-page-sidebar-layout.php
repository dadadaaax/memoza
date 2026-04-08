<?php
/*adding sections for default layout options panel*/
$wp_customize->add_section( 'supernews-front-page-sidebar-layout', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Front/Home Sidebar Layout', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*Sidebar Layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-front-page-sidebar-layout]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-front-page-sidebar-layout'],
    'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_sidebar_layout();
$wp_customize->add_control( 'supernews_theme_options[supernews-front-page-sidebar-layout]', array(
    'choices'  	=> $choices,
    'label'		=> __( 'Front/Home Sidebar Layout', 'supernews' ),
    'section'   => 'supernews-front-page-sidebar-layout',
    'settings'  => 'supernews_theme_options[supernews-front-page-sidebar-layout]',
    'type'	  	=> 'select'
) );