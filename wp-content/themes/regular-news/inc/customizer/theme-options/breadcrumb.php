<?php
/**
 * Breadcrumb options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

$wp_customize->add_section( 'regular_news_breadcrumb', array(
	'title'             => esc_html__( 'Breadcrumb','regular-news' ),
	'description'       => esc_html__( 'Breadcrumb section options.', 'regular-news' ),
	'panel'             => 'regular_news_theme_options_panel',
) );

// Breadcrumb enable setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[breadcrumb_enable]', array(
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
	'default'          	=> $options['breadcrumb_enable'],
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[breadcrumb_enable]', array(
	'label'            	=> esc_html__( 'Enable Breadcrumb', 'regular-news' ),
	'section'          	=> 'regular_news_breadcrumb',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// Breadcrumb separator setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[breadcrumb_separator]', array(
	'sanitize_callback'	=> 'sanitize_text_field',
	'default'          	=> $options['breadcrumb_separator'],
) );

$wp_customize->add_control( 'regular_news_theme_options[breadcrumb_separator]', array(
	'label'            	=> esc_html__( 'Separator', 'regular-news' ),
	'active_callback' 	=> 'regular_news_is_breadcrumb_enable',
	'section'          	=> 'regular_news_breadcrumb',
) );
