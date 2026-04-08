<?php
/**
* Homepage (Static ) options
*
* @package Theme Palace
* @subpackage Regular News
* @since Regular News 1.0.0
*/

// Homepage (Static ) setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[enable_frontpage_content]', array(
	'sanitize_callback'   => 'regular_news_sanitize_checkbox',
	'default'             => $options['enable_frontpage_content'],
) );

$wp_customize->add_control( 'regular_news_theme_options[enable_frontpage_content]', array(
	'label'       	=> esc_html__( 'Enable Content', 'regular-news' ),
	'description' 	=> esc_html__( 'Check to enable content on static front page only.', 'regular-news' ),
	'section'     	=> 'static_front_page',
	'type'        	=> 'checkbox',
) );