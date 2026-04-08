<?php
/*adding sections for custom css options */
$wp_customize->add_section( 'supernews-design-custom-css-option', array(
    'priority'       => 60,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Custom CSS', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*custom-css*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-custom-css]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-custom-css'],
    'sanitize_callback'    => 'wp_strip_all_tags'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-custom-css]', array(
    'label'		=> __( 'Custom CSS', 'supernews' ),
    'section'   => 'supernews-design-custom-css-option',
    'settings'  => 'supernews_theme_options[supernews-custom-css]',
    'type'	  	=> 'textarea',
    'priority'  => 10
) );