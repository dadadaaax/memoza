<?php
/**
 * Treding News Section options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add Treding News section
$wp_customize->add_section( 'regular_news_headline_section', array(
	'title'             => esc_html__( 'Treding News','regular-news' ),
	'description'       => esc_html__( 'Treding News Section options.', 'regular-news' ),
	'panel'             => 'regular_news_front_page_panel',
) );

// Treding News content enable control and setting
$wp_customize->add_setting( 'regular_news_theme_options[headline_section_enable]', array(
	'default'			=> 	$options['headline_section_enable'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[headline_section_enable]', array(
	'label'             => esc_html__( 'Treding News Section Enable', 'regular-news' ),
	'section'           => 'regular_news_headline_section',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// headline title setting and control
$wp_customize->add_setting( 'regular_news_theme_options[headline_title]', array(
	'sanitize_callback' => 'sanitize_text_field',
	'default'			=> $options['headline_title'],
	'transport'			=> 'postMessage',
) );

$wp_customize->add_control( 'regular_news_theme_options[headline_title]', array(
	'label'           	=> esc_html__( 'Trending News Title', 'regular-news' ),
	'section'        	=> 'regular_news_headline_section',
	'active_callback' 	=> 'regular_news_is_headline_section_enable',
	'type'				=> 'text',
) );

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
    $wp_customize->selective_refresh->add_partial( 'regular_news_theme_options[headline_title]', array(
		'selector'            => '#breaking-news .news-header span.news-title',
		'settings'            => 'regular_news_theme_options[headline_title]',
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => 'regular_news_headline_title_partial',
    ) );
}

// Add dropdown category setting and control.
$wp_customize->add_setting(  'regular_news_theme_options[headline_content_category]', array(
	'sanitize_callback' => 'regular_news_sanitize_single_category',
) ) ;

$wp_customize->add_control( new Regular_News_Dropdown_Taxonomies_Control( $wp_customize,'regular_news_theme_options[headline_content_category]', array(
	'label'             => esc_html__( 'Select Category', 'regular-news' ),
	'description'      	=> esc_html__( 'Note: Latest three posts will be shown from selected category', 'regular-news' ),
	'section'           => 'regular_news_headline_section',
	'type'              => 'dropdown-taxonomies',
	'active_callback'	=> 'regular_news_is_headline_section_enable'
) ) );

