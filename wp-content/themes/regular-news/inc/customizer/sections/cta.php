<?php
/**
 * Call to Action Section options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add Call to Action section
$wp_customize->add_section( 'regular_news_cta_section', array(
	'title'             => esc_html__( 'Call to Action','regular-news' ),
	'description'       => esc_html__( 'Call to Action Section options.', 'regular-news' ),
	'panel'             => 'regular_news_front_page_panel',
) );

// Call to Action content enable control and setting
$wp_customize->add_setting( 'regular_news_theme_options[cta_section_enable]', array(
	'default'			=> 	$options['cta_section_enable'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[cta_section_enable]', array(
	'label'             => esc_html__( 'Call to Action Section Enable', 'regular-news' ),
	'section'           => 'regular_news_cta_section',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// cta posts drop down chooser control and setting
$wp_customize->add_setting( 'regular_news_theme_options[cta_content_post]', array(
	'sanitize_callback' => 'regular_news_sanitize_page',
) );

$wp_customize->add_control( new Regular_News_Dropdown_Chooser( $wp_customize, 'regular_news_theme_options[cta_content_post]', array(
	'label'             => esc_html__( 'Select Post', 'regular-news' ),
	'section'           => 'regular_news_cta_section',
	'choices'			=> regular_news_post_choices(),
	'active_callback'	=> 'regular_news_is_cta_section_enable',
) ) );

