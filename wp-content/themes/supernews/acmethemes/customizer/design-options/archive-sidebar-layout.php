<?php
/*adding sections for default layout options panel*/
$wp_customize->add_section( 'supernews-archive-sidebar-layout', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Category/Archive Sidebar Layout', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*Sidebar Layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-archive-sidebar-layout]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-archive-sidebar-layout'],
    'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_sidebar_layout();
$wp_customize->add_control( 'supernews_theme_options[supernews-archive-sidebar-layout]', array(
    'choices'  	    => $choices,
    'label'		    => __( 'Category/Archive Sidebar Layout', 'supernews' ),
    'description'   => __( 'Sidebar Layout for listing pages like category, author etc', 'supernews' ),
    'section'       => 'supernews-archive-sidebar-layout',
    'settings'      => 'supernews_theme_options[supernews-archive-sidebar-layout]',
    'type'	  	    => 'select'
) );