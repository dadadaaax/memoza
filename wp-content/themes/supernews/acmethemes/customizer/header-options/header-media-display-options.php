<?php
/*adding sections for header news options*/
$wp_customize->add_section( 'supernews-header-media', array(
	'priority'       => 20,
	'capability'     => 'edit_theme_options',
	'theme_supports' => '',
	'title'          => __( 'Header Media Position', 'supernews' ),
	'description'    => sprintf( __( 'Add Header Media from %1$s here %2$s. Header Media includes Video and Image', 'supernews' ), '<a href="#" class="at-customizer" data-section="header_image">','</a>' ),
	'panel'          => 'supernews-header-panel'
) );

/*header media position options*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-media-position]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-header-media-position'],
	'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_header_media_position();
$wp_customize->add_control( 'supernews_theme_options[supernews-header-media-position]', array(
	'choices'  	=> $choices,
	'label'		=> __( 'Header Media Position', 'supernews' ),
	'section'   => 'supernews-header-media',
	'settings'  => 'supernews_theme_options[supernews-header-media-position]',
	'type'	  	=> 'radio',
	'priority'  => 10
) );

/*header ad img link*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-image-link]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-header-image-link'],
	'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-header-image-link]', array(
	'label'		=> __( 'Header Image Link', 'supernews' ),
	'description'=> __( 'Left empty for no link', 'supernews' ),
	'section'   => 'supernews-header-media',
	'settings'  => 'supernews_theme_options[supernews-header-image-link]',
	'type'	  	=> 'url',
	'priority'  => 20
) );

/*header image in new tab*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-image-link-new-tab]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-header-image-link-new-tab'],
	'sanitize_callback' => 'supernews_sanitize_checkbox',
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-header-image-link-new-tab]', array(
	'label'		=> __( 'Check to Open New Tab Header Image Link', 'supernews' ),
	'section'   => 'supernews-header-media',
	'settings'  => 'supernews_theme_options[supernews-header-image-link-new-tab]',
	'type'	  	=> 'checkbox',
	'priority'  => 30
) );

/*adding hr between post and ad*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-header-media-customizer-link]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-header-media-customizer-link'],
	'sanitize_callback' => 'esc_attr'
) );
$wp_customize->add_control(
	new Supernews_Customize_Message_Control(
		$wp_customize,
		'supernews_theme_options[supernews-header-media-customizer-link]',
		array(
			'section'   => 'header_image',
			'description'    => sprintf( __( ' Header Media Position options available %1$s here %2$s', 'supernews' ), '<hr /><a href="#" class="at-customizer" data-section="supernews-header-media">','</a>' ),
			'settings'  => 'supernews_theme_options[supernews-header-media-customizer-link]',
			'type'	  	=> 'message',
			'priority'  => 74
		)
	)
);