<?php
/*adding sections for footer options*/
$wp_customize->add_section( 'supernews-footer-option', array(
    'priority'       => 80,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Footer Option', 'supernews' )
) );

/*copyright*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-footer-copyright]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-footer-copyright'],
    'sanitize_callback' => 'wp_kses_post'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-footer-copyright]', array(
    'label'		=> __( 'Copyright Text', 'supernews' ),
    'section'   => 'supernews-footer-option',
    'settings'  => 'supernews_theme_options[supernews-footer-copyright]',
    'type'	  	=> 'text',
    'priority'  => 10
) );