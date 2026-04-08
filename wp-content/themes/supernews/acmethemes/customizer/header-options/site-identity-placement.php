<?php
/*adding sections for menu */
$wp_customize->add_section( 'supernews-site-identity-placement', array(
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Header Placement', 'supernews' ),
    'panel'          => 'supernews-header-panel'
) );

/*header menu type*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-logo-ads-display-position]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-header-logo-ads-display-position'],
    'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_header_logo_menu_display_position();
$wp_customize->add_control( 'supernews_theme_options[supernews-header-logo-ads-display-position]', array(
    'choices'  	=> $choices,
    'label'		=> __( 'Logo and Advertisement Position', 'supernews' ),
    'section'   => 'supernews-site-identity-placement',
    'settings'  => 'supernews_theme_options[supernews-header-logo-ads-display-position]',
    'type'	  	=> 'select',
    'priority'  => 15
) );
