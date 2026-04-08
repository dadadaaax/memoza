<?php
/**
 * Reset options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

/**
* Reset section
*/
// Add reset enable section
$wp_customize->add_section( 'regular_news_reset_section', array(
	'title'             => esc_html__('Reset all settings','regular-news'),
	'description'       => esc_html__( 'Caution: All settings will be reset to default. Refresh the page after clicking Save & Publish.', 'regular-news' ),
) );

// Add reset enable setting and control.
$wp_customize->add_setting( 'regular_news_theme_options[reset_options]', array(
	'default'           => $options['reset_options'],
	'sanitize_callback' => 'regular_news_sanitize_checkbox',
	'transport'			  => 'postMessage',
) );

$wp_customize->add_control( 'regular_news_theme_options[reset_options]', array(
	'label'             => esc_html__( 'Check to reset all settings', 'regular-news' ),
	'section'           => 'regular_news_reset_section',
	'type'              => 'checkbox',
) );
