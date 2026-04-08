<?php
/*adding sections for header date*/
$wp_customize->add_section( 'supernews-header-date', array(
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Enable Date', 'supernews' ),
    'panel'          => 'supernews-header-panel'
) );

/*show date*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-show-date]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-show-date'],
    'sanitize_callback' => 'supernews_sanitize_checkbox',
) );

$wp_customize->add_control( 'supernews_theme_options[supernews-show-date]', array(
    'label'		=> __( 'Show Date', 'supernews' ),
    'section'   => 'supernews-header-date',
    'settings'  => 'supernews_theme_options[supernews-show-date]',
    'type'	  	=> 'checkbox',
    'priority'  => 10
) );

/*date format*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-date-format]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-header-date-format'],
	'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_header_date_format();
$wp_customize->add_control( 'supernews_theme_options[supernews-header-date-format]', array(
	'choices'  	=> $choices,
	'label'		=> __( 'Date Format', 'supernews' ),
	'section'   => 'supernews-header-date',
	'settings'  => 'supernews_theme_options[supernews-header-date-format]',
	'type'	  	=> 'select',
	'priority'  => 20
) );