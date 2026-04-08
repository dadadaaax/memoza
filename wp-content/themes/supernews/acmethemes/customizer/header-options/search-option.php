<?php
/*adding sections for header options panel*/
$wp_customize->add_section( 'supernews-header-search', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Search Options', 'supernews' ),
    'panel'          => 'supernews-header-panel'
) );

/*header show search*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-show-search]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-header-show-search'],
    'sanitize_callback' => 'supernews_sanitize_checkbox',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-header-show-search]', array(
    'label'		=> __( 'Show Search On Header', 'supernews' ),
    'section'   => 'supernews-header-search',
    'settings'  => 'supernews_theme_options[supernews-header-show-search]',
    'type'	  	=> 'checkbox',
    'priority'  => 45
) );