<?php
/**
 * Blog Section options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add Blog section
$wp_customize->add_section( 'regular_news_blog_section', array(
	'title'             => esc_html__( 'Blog','regular-news' ),
	'description'       => esc_html__( 'Blog Section options.', 'regular-news' ),
	'panel'             => 'regular_news_front_page_panel',
) );

// Blog content enable control and setting
$wp_customize->add_setting( 'regular_news_theme_options[blog_section_enable]', array(
	'default'			=> 	$options['blog_section_enable'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[blog_section_enable]', array(
	'label'             => esc_html__( 'Blog Section Enable', 'regular-news' ),
	'section'           => 'regular_news_blog_section',
	'on_off_label' 		=> regular_news_switch_options(),
) ) );

// blog title setting and control
$wp_customize->add_setting( 'regular_news_theme_options[blog_title]', array(
	'sanitize_callback' => 'sanitize_text_field',
	'default'			=> $options['blog_title'],
	'transport'			=> 'postMessage',
) );

$wp_customize->add_control( 'regular_news_theme_options[blog_title]', array(
	'label'           	=> esc_html__( 'Title', 'regular-news' ),
	'section'        	=> 'regular_news_blog_section',
	'active_callback' 	=> 'regular_news_is_blog_section_enable',
	'type'				=> 'text',
) );

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
    $wp_customize->selective_refresh->add_partial( 'regular_news_theme_options[blog_title]', array(
		'selector'            => '#inner-content-wrapper .section-header h2',
		'settings'            => 'regular_news_theme_options[blog_title]',
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => 'regular_news_blog_title_partial',
    ) );
}

// Blog content type control and setting
$wp_customize->add_setting( 'regular_news_theme_options[blog_content_type]', array(
	'default'          	=> $options['blog_content_type'],
	'sanitize_callback' => 'regular_news_sanitize_select',
) );

$wp_customize->add_control( 'regular_news_theme_options[blog_content_type]', array(
	'label'             => esc_html__( 'Content Type', 'regular-news' ),
	'section'           => 'regular_news_blog_section',
	'type'				=> 'select',
	'active_callback' 	=> 'regular_news_is_blog_section_enable',
	'choices'			=> array( 
		'category' 	=> esc_html__( 'Category', 'regular-news' ),
		'recent' 	=> esc_html__( 'Recent', 'regular-news' ),
	),
) );

// Add dropdown category setting and control.
$wp_customize->add_setting(  'regular_news_theme_options[blog_content_category]', array(
	'sanitize_callback' => 'regular_news_sanitize_single_category',
) ) ;

$wp_customize->add_control( new Regular_News_Dropdown_Taxonomies_Control( $wp_customize,'regular_news_theme_options[blog_content_category]', array(
	'label'             => esc_html__( 'Select Category', 'regular-news' ),
	'description'      	=> esc_html__( 'Note: Latest three posts will be shown from selected category', 'regular-news' ),
	'section'           => 'regular_news_blog_section',
	'type'              => 'dropdown-taxonomies',
	'active_callback'	=> 'regular_news_is_blog_section_content_category_enable'
) ) );

// Add dropdown categories setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[blog_category_exclude]', array(
	'sanitize_callback' => 'regular_news_sanitize_category_list',
) ) ;

$wp_customize->add_control( new Regular_News_Dropdown_Category_Control( $wp_customize,'regular_news_theme_options[blog_category_exclude]', array(
	'label'             => esc_html__( 'Select Excluding Categories', 'regular-news' ),
	'description'      	=> esc_html__( 'Note: Select categories to exclude. Press CTRL key select multilple categories.', 'regular-news' ),
	'section'           => 'regular_news_blog_section',
	'type'              => 'dropdown-categories',
	'active_callback'	=> 'regular_news_is_blog_section_content_recent_enable'
) ) );
