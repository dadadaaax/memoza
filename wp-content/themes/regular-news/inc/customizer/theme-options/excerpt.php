<?php
/**
 * Excerpt options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Add excerpt section
$wp_customize->add_section( 'regular_news_excerpt_section', array(
	'title'             => esc_html__( 'Excerpt','regular-news' ),
	'description'       => esc_html__( 'Excerpt section options.', 'regular-news' ),
	'panel'             => 'regular_news_theme_options_panel',
) );


// long Excerpt length setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[long_excerpt_length]', array(
	'sanitize_callback' => 'regular_news_sanitize_number_range',
	'validate_callback' => 'regular_news_validate_long_excerpt',
	'default'			=> $options['long_excerpt_length'],
) );

$wp_customize->add_control( 'regular_news_theme_options[long_excerpt_length]', array(
	'label'       		=> esc_html__( 'Blog Page Excerpt Length', 'regular-news' ),
	'description' 		=> esc_html__( 'Note: Min 5 & Max 100. Total words to be displayed in archive page/search page.', 'regular-news' ),
	'section'     		=> 'regular_news_excerpt_section',
	'type'        		=> 'number',
	'input_attrs' 		=> array(
		'style'       => 'width: 80px;',
		'max'         => 100,
		'min'         => 5,
	),
) );

// read more text setting and control
$wp_customize->add_setting( 'regular_news_theme_options[read_more_text]', array(
	'sanitize_callback' => 'sanitize_text_field',
	'default'			=> $options['read_more_text'],
) );

$wp_customize->add_control( 'regular_news_theme_options[read_more_text]', array(
	'label'           	=> esc_html__( 'Read More Text Label', 'regular-news' ),
	'section'        	=> 'regular_news_excerpt_section',
	'type'				=> 'text',
) );
