<?php
/*customizing default colors section and adding new controls-setting too*/
$wp_customize->add_section( 'colors', array(
    'priority'       => 40,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Colors', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*Primary color*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-primary-color]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-primary-color'],
    'sanitize_callback' => 'sanitize_hex_color'
) );

$wp_customize->add_control(
	new WP_Customize_Color_Control(
		$wp_customize,
		'supernews_theme_options[supernews-primary-color]',
		array(
			'label'		=> __( 'Primary Color', 'supernews' ),
			'section'   => 'colors',
			'settings'  => 'supernews_theme_options[supernews-primary-color]',
			'type'	  	=> 'color'
		)
	)
);