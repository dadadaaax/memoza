<?php
/**
 * pagination options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add sidebar section
$wp_customize->add_section( 'regular_news_pagination', array(
	'title'               => esc_html__('Pagination','regular-news'),
	'description'         => esc_html__( 'Pagination section options.', 'regular-news' ),
	'panel'               => 'regular_news_theme_options_panel',
) );

// Sidebar position setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[pagination_enable]', array(
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
	'default'             => $options['pagination_enable'],
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[pagination_enable]', array(
	'label'               => esc_html__( 'Pagination Enable', 'regular-news' ),
	'section'             => 'regular_news_pagination',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// Site layout setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[pagination_type]', array(
	'sanitize_callback'   => 'regular_news_sanitize_select',
	'default'             => $options['pagination_type'],
) );

$wp_customize->add_control( 'regular_news_theme_options[pagination_type]', array(
	'label'               => esc_html__( 'Pagination Type', 'regular-news' ),
	'section'             => 'regular_news_pagination',
	'type'                => 'select',
	'choices'			  => regular_news_pagination_options(),
	'active_callback'	  => 'regular_news_is_pagination_enable',
) );
