<?php
/*adding sections for front page */
$wp_customize->add_section( 'supernews-front-page-options', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Front Page Options', 'supernews' ),
    'panel'          => 'supernews-options'
) );

/*Show Hide Front Page Content*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-hide-front-page-content]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-hide-front-page-content'],
    'sanitize_callback' => 'supernews_sanitize_checkbox'
) );

$wp_customize->add_control( 'supernews_theme_options[supernews-hide-front-page-content]', array(
    'label'		=> __( 'Hide Blog Posts or Static Page on Front Page', 'supernews' ),
    'section'   => 'supernews-front-page-options',
    'settings'  => 'supernews_theme_options[supernews-hide-front-page-content]',
    'type'	  	=> 'checkbox',
    'priority'  => 1
) );