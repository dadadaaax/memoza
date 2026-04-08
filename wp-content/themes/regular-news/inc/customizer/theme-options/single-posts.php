<?php
/**
 * Excerpt options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add excerpt section
$wp_customize->add_section( 'regular_news_single_post_section', array(
	'title'             => esc_html__( 'Single Post','regular-news' ),
	'description'       => esc_html__( 'Options to change the single posts globally.', 'regular-news' ),
	'panel'             => 'regular_news_theme_options_panel',
) );

// Archive date meta setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[single_post_hide_date]', array(
	'default'           => $options['single_post_hide_date'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[single_post_hide_date]', array(
	'label'             => esc_html__( 'Hide Date', 'regular-news' ),
	'section'           => 'regular_news_single_post_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );

// Archive author meta setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[single_post_hide_author]', array(
	'default'           => $options['single_post_hide_author'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[single_post_hide_author]', array(
	'label'             => esc_html__( 'Hide Author', 'regular-news' ),
	'section'           => 'regular_news_single_post_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );

// Archive author category setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[single_post_hide_category]', array(
	'default'           => $options['single_post_hide_category'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[single_post_hide_category]', array(
	'label'             => esc_html__( 'Hide Category', 'regular-news' ),
	'section'           => 'regular_news_single_post_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );

// Archive tag category setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[single_post_hide_tags]', array(
	'default'           => $options['single_post_hide_tags'],
	'sanitize_callback' => 'regular_news_sanitize_switch_control',
) );

$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[single_post_hide_tags]', array(
	'label'             => esc_html__( 'Hide Tag', 'regular-news' ),
	'section'           => 'regular_news_single_post_section',
	'on_off_label' 		=> regular_news_hide_options(),
) ) );
