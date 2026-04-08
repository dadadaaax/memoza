<?php
/*adding sections for Search Placeholder*/
$wp_customize->add_section( 'supernews-search', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Search', 'supernews' ),
    'panel'          => 'supernews-options'
) );

/*Search Placeholder*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-search-placholder]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-search-placholder'],
    'sanitize_callback' => 'sanitize_text_field'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-search-placholder]', array(
    'label'		=> __( 'Search Placeholder', 'supernews' ),
    'section'   => 'supernews-search',
    'settings'  => 'supernews_theme_options[supernews-search-placholder]',
    'type'	  	=> 'text',
    'priority'  => 10
) );