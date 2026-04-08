<?php
/*adding sections for breadcrumb */
$wp_customize->add_section( 'supernews-breadcrumb-options', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Breadcrumb Options', 'supernews' ),
    'panel'          => 'supernews-options'
) );

/*show breadcrumb*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-show-breadcrumb]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-show-breadcrumb'],
    'sanitize_callback' => 'supernews_sanitize_checkbox'
) );

$wp_customize->add_control( 'supernews_theme_options[supernews-show-breadcrumb]', array(
    'label'		=> __( 'Enable Breadcrumb', 'supernews' ),
    'section'   => 'supernews-breadcrumb-options',
    'settings'  => 'supernews_theme_options[supernews-show-breadcrumb]',
    'type'	  	=> 'checkbox',
    'priority'  => 10
) );

/*You are here text*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-you-are-here-text]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-you-are-here-text'],
	'sanitize_callback' => 'sanitize_text_field'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-you-are-here-text]', array(
	'label'		=> __( 'You are here Text', 'supernews' ),
	'section'   => 'supernews-breadcrumb-options',
	'settings'  => 'supernews_theme_options[supernews-you-are-here-text]',
	'type'	  	=> 'text'
) );