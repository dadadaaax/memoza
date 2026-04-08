<?php
/**
 * Archive options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add archive section
$wp_customize->add_section( 'regular_news_archive_section', array(
	'title'             => esc_html__( 'Blog/Archive','regular-news' ),
	'description'       => esc_html__( 'Archive section options.', 'regular-news' ),
	'panel'             => 'regular_news_theme_options_panel',
) );

// Your latest posts title setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[your_latest_posts_title]', array(
	'default'           => $options['your_latest_posts_title'],
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( 'regular_news_theme_options[your_latest_posts_title]', array(
	'label'             => esc_html__( 'Your Latest Posts Title', 'regular-news' ),
	'description'       => esc_html__( 'This option only works if Static Front Page is set to "Your latest posts."', 'regular-news' ),
	'section'           => 'regular_news_archive_section',
	'type'				=> 'text',
	'active_callback'   => 'regular_news_is_latest_posts'
) );

// Archive category setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[hide_category]', array(
	'default'           => $options['hide_category'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[hide_category]', array(
	'label'             => esc_html__( 'Hide Category', 'regular-news' ),
	'section'           => 'regular_news_archive_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );

// Archive date setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[hide_date]', array(
	'default'           => $options['hide_date'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[hide_date]', array(
	'label'             => esc_html__( 'Hide Date', 'regular-news' ),
	'section'           => 'regular_news_archive_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );

// Archive author setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[hide_author]', array(
	'default'           => $options['hide_author'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[hide_author]', array(
	'label'             => esc_html__( 'Hide Author', 'regular-news' ),
	'section'           => 'regular_news_archive_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );