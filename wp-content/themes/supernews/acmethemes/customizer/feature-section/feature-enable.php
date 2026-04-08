<?php
/*adding sections for enabling feature section in front page*/
$wp_customize->add_section( 'supernews-enable-feature', array(
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Enable Feature Section', 'supernews' ),
    'panel'          => 'supernews-feature-panel'
) );

/*enable feature section*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-enable-feature]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-enable-feature'],
    'sanitize_callback' => 'supernews_sanitize_checkbox'
) );

$wp_customize->add_control( 'supernews_theme_options[supernews-enable-feature]', array(
    'label'		=> __( 'Enable Feature Section', 'supernews' ),
    'description'	=> __( 'Feature section will display on front/home page.', 'supernews' ),
    'section'   => 'supernews-enable-feature',
    'settings'  => 'supernews_theme_options[supernews-enable-feature]',
    'type'	  	=> 'checkbox',
    'priority'  => 10
) );