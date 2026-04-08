<?php
/*adding sections for header social options */
$wp_customize->add_section( 'supernews-header-social', array(
	'priority'       => 20,
	'capability'     => 'edit_theme_options',
	'theme_supports' => '',
	'title'          => __( 'Social Options', 'supernews' ),
	'panel'          => 'supernews-header-panel'
) );

/*enable social*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-enable-social]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-enable-social'],
	'sanitize_callback' => 'supernews_sanitize_checkbox',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-enable-social]', array(
	'label'		=> __( 'Enable social', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-enable-social]',
	'type'	  	=> 'checkbox',
	'priority'  => 10
) );

/*facebook url*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-facebook-url]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-facebook-url'],
	'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-facebook-url]', array(
	'label'		=> __( 'Facebook url', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-facebook-url]',
	'type'	  	=> 'url',
	'priority'  => 20
) );

/*twitter url*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-twitter-url]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-twitter-url'],
	'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-twitter-url]', array(
	'label'		=> __( 'Twitter url', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-twitter-url]',
	'type'	  	=> 'url',
	'priority'  => 25
) );

/*youtube url*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-youtube-url]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-youtube-url'],
	'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-youtube-url]', array(
	'label'		=> __( 'Youtube url', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-youtube-url]',
	'type'	  	=> 'url',
	'priority'  => 25
) );

/*Instagram url*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-instagram-url]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-instagram-url'],
	'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-instagram-url]', array(
	'label'		=> __( 'Instagram url', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-instagram-url]',
	'type'	  	=> 'url',
	'priority'  => 30
) );

/*@since Version: 1.3.1
 * google plusurl*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-google-plus-url]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-google-plus-url'],
	'sanitize_callback' => 'esc_url_raw'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-google-plus-url]', array(
	'label'		=> __( 'Google Plus Url', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-google-plus-url]',
	'type'	  	=> 'url',
	'priority'  => 70
) );

/*Pinterest  url*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-pinterest-url]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-pinterest-url'],
	'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-pinterest-url]', array(
	'label'		=> __( 'Pinterest url', 'supernews' ),
	'section'   => 'supernews-header-social',
	'settings'  => 'supernews_theme_options[supernews-pinterest-url]',
	'type'	  	=> 'url',
	'priority'  => 35
) );