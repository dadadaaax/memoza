<?php
/**
 * Topbar Section options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add Topbar section
$wp_customize->add_section( 'regular_news_topbar_section', array(
	'title'             => esc_html__( 'Topbar','regular-news' ),
	'description'       => esc_html__( 'Topbar Section options.', 'regular-news' ),
	'panel'             => 'regular_news_front_page_panel',
) );

// header_background image setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[header_background_image]', array(
	'sanitize_callback' => 'regular_news_sanitize_image'
) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'regular_news_theme_options[header_background_image]',
		array(
		'label'       		=> esc_html__( 'Header Background Image', 'regular-news' ),
		'description' 		=> sprintf( esc_html__( 'Recommended size: %1$dpx x %2$dpx ', 'regular-news' ), 1920, 172 ),
		'section'     		=> 'regular_news_topbar_section',
) ) );

// ads image setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[ads_image]', array(
	'sanitize_callback' => 'regular_news_sanitize_image'
) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'regular_news_theme_options[ads_image]',
		array(
		'label'       		=> esc_html__( 'Ads Image', 'regular-news' ),
		'description' 		=> sprintf( esc_html__( 'Recommended size: %1$dpx x %2$dpx ', 'regular-news' ), 900, 100 ),
		'section'     		=> 'regular_news_topbar_section',
) ) );

// ads link setting and control
$wp_customize->add_setting( 'regular_news_theme_options[ads_url]', array(
	'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( 'regular_news_theme_options[ads_url]', array(
	'label'           	=> esc_html__( 'Ads Url', 'regular-news' ),
	'section'        	=> 'regular_news_topbar_section',
	'type'				=> 'url',
) );