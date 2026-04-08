<?php
/**
 * Must Read Section options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add Must Read section
$wp_customize->add_section( 'regular_news_must_read_section', array(
	'title'             => esc_html__( 'Must Read','regular-news' ),
	'description'       => esc_html__( 'Must Read Section options.', 'regular-news' ),
	'panel'             => 'regular_news_front_page_panel',
) );

// Must Read content enable control and setting
$wp_customize->add_setting( 'regular_news_theme_options[must_read_section_enable]', array(
	'default'			=> 	$options['must_read_section_enable'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[must_read_section_enable]', array(
	'label'             => esc_html__( 'Must Read Section Enable', 'regular-news' ),
	'section'           => 'regular_news_must_read_section',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// must_read title setting and control
$wp_customize->add_setting( 'regular_news_theme_options[must_read_title]', array(
	'sanitize_callback' => 'sanitize_text_field',
	'default'			=> $options['must_read_title'],
	'transport'			=> 'postMessage',
) );

$wp_customize->add_control( 'regular_news_theme_options[must_read_title]', array(
	'label'           	=> esc_html__( 'Title', 'regular-news' ),
	'section'        	=> 'regular_news_must_read_section',
	'active_callback' 	=> 'regular_news_is_must_read_section_enable',
	'type'				=> 'text',
) );

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
    $wp_customize->selective_refresh->add_partial( 'regular_news_theme_options[must_read_title]', array(
		'selector'            => '#must-read .section-header h2',
		'settings'            => 'regular_news_theme_options[must_read_title]',
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => 'regular_news_must_read_title_partial',
    ) );
}

// Add dropdown category setting and control.
$wp_customize->add_setting(  'regular_news_theme_options[must_read_content_category]', array(
	'sanitize_callback' => 'regular_news_sanitize_single_category',
) ) ;

$wp_customize->add_control( new Regular_News_Dropdown_Taxonomies_Control( $wp_customize,'regular_news_theme_options[must_read_content_category]', array(
	'label'             => esc_html__( 'Select Category', 'regular-news' ),
	'description'      	=> esc_html__( 'Note: Latest four posts will be shown from selected category', 'regular-news' ),
	'section'           => 'regular_news_must_read_section',
	'type'              => 'dropdown-taxonomies',
	'active_callback'	=> 'regular_news_is_must_read_section_enable'
) ) );
