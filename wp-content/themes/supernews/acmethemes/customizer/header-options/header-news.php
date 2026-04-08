<?php
/*adding sections for header news options*/
$wp_customize->add_section( 'supernews-header-news', array(
    'priority'       => 60,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Breaking News Options', 'supernews' ),
    'panel'          => 'supernews-header-panel'
) );

/*header news enable*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-enable-breaking-news]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-enable-breaking-news'],
	'sanitize_callback' => 'supernews_sanitize_checkbox'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-enable-breaking-news]', array(
	'label'		=> __( 'Enable Breaking News', 'supernews' ),
	'section'   => 'supernews-header-news',
	'settings'  => 'supernews_theme_options[supernews-enable-breaking-news]',
	'type'	  	=> 'checkbox',
	'priority'  => 10
) );

/*header news title*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-breaking-news-title]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-breaking-news-title'],
    'sanitize_callback' => 'sanitize_text_field'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-breaking-news-title]', array(
    'label'		=> __( 'Breaking News Title', 'supernews' ),
    'section'   => 'supernews-header-news',
    'settings'  => 'supernews_theme_options[supernews-breaking-news-title]',
    'type'	  	=> 'text',
    'priority'  => 10
) );