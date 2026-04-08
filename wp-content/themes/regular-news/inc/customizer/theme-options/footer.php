<?php
/**
 * Footer options
 *
 * @package Theme Palace
 * @subpackage Regular News
 * @since Regular News 1.0.0
 */

// Footer Section
$wp_customize->add_section( 'regular_news_section_footer',
	array(
		'title'      			=> esc_html__( 'Footer Options', 'regular-news' ),
		'priority'   			=> 900,
		'panel'      			=> 'regular_news_theme_options_panel',
	)
);

// footer text
$wp_customize->add_setting( 'regular_news_theme_options[copyright_text]',
	array(
		'default'       		=> $options['copyright_text'],
		'sanitize_callback'		=> 'regular_news_santize_allow_tag',
		'transport'				=> 'postMessage',
	)
);
$wp_customize->add_control( 'regular_news_theme_options[copyright_text]',
    array(
		'label'      			=> esc_html__( 'Copyright Text', 'regular-news' ),
		'section'    			=> 'regular_news_section_footer',
		'type'		 			=> 'textarea',
    )
);

// Abort if selective refresh is not available.
if ( isset( $wp_customize->selective_refresh ) ) {
    $wp_customize->selective_refresh->add_partial( 'regular_news_theme_options[copyright_text]', array(
		'selector'            => '.site-info .copyright span',
		'settings'            => 'regular_news_theme_options[copyright_text]',
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => 'regular_news_copyright_text_partial',
    ) );
}

// scroll top visible
$wp_customize->add_setting( 'regular_news_theme_options[scroll_top_visible]',
	array(
		'default'       	=> $options['scroll_top_visible'],
		'sanitize_callback' => 'regular_news_sanitize_switch_control',
	)
);
$wp_customize->add_control( new Regular_News_Switch_Control( $wp_customize, 'regular_news_theme_options[scroll_top_visible]',
    array(
		'label'      		=> esc_html__( 'Display Scroll Top Button', 'regular-news' ),
		'section'    		=> 'regular_news_section_footer',
		'on_off_label' 		=> regular_news_switch_options(),
    )
) );