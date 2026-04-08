<?php
/**
 * Menu options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add sidebar section
$wp_customize->add_section( 'regular_news_menu', array(
	'title'             => esc_html__('Header Menu','regular-news'),
	'description'       => esc_html__( 'Header Menu options.', 'regular-news' ),
	'panel'             => 'nav_menus',
) );

// search enable setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[nav_search_enable]', array(
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
	'default'           => $options['nav_search_enable'],
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[nav_search_enable]', array(
	'label'             => esc_html__( 'Enable search', 'regular-news' ),
	'section'           => 'regular_news_menu',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );