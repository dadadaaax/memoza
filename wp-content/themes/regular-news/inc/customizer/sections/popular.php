<?php
/**
 * Popular Section options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add Popular section
$wp_customize->add_section( 'regular_news_popular_section', array(
	'title'             => esc_html__( 'Popular Section','regular-news' ),
	'description'       => esc_html__( 'Popular Section options.', 'regular-news' ),
	'panel'             => 'regular_news_front_page_panel',
) );

// Popular content enable control and setting
$wp_customize->add_setting( 'regular_news_theme_options[popular_section_enable]', array(
	'default'			=> 	$options['popular_section_enable'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[popular_section_enable]', array(
	'label'             => esc_html__( 'Popular Section Enable', 'regular-news' ),
	'section'           => 'regular_news_popular_section',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// Add dropdown category setting and control.
$wp_customize->add_setting(  'regular_news_theme_options[popular_content_category]', array(
	'sanitize_callback' => 'regular_news_sanitize_single_category',
) ) ;

$wp_customize->add_control( new Regular_News_Dropdown_Taxonomies_Control( $wp_customize,'regular_news_theme_options[popular_content_category]', array(
	'label'             => esc_html__( 'Select Category', 'regular-news' ),
	'description'      	=> esc_html__( 'Note: Latest five posts will be shown from selected category', 'regular-news' ),
	'section'           => 'regular_news_popular_section',
	'type'              => 'dropdown-taxonomies',
	'active_callback'	=> 'regular_news_is_popular_section_enable'
) ) );
