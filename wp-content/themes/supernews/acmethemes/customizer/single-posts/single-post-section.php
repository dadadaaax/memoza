<?php
/*adding sections for Single post options*/
$wp_customize->add_section( 'supernews-single-post', array(
    'priority'       => 90,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Single Post Options', 'supernews' )
) );

/*single image layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-single-image-layout]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-single-image-layout'],
    'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_get_image_sizes_options(1);
$wp_customize->add_control( 'supernews_theme_options[supernews-single-image-layout]', array(
    'choices'  	=> $choices,
    'label'		=> __( 'Image Layout Options', 'supernews' ),
    'section'   => 'supernews-single-post',
    'settings'  => 'supernews_theme_options[supernews-single-image-layout]',
    'type'	  	=> 'select',
    'priority'  => 20
) );

/*show related posts*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-show-related]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-show-related'],
    'sanitize_callback' => 'supernews_sanitize_checkbox'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-show-related]', array(
    'label'		=> __( 'Show Related Posts In Single Post', 'supernews' ),
    'section'   => 'supernews-single-post',
    'settings'  => 'supernews_theme_options[supernews-show-related]',
    'type'	  	=> 'checkbox',
    'priority'  => 30
) );

/*Related title*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-related-title]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-related-title'],
	'sanitize_callback' => 'sanitize_text_field'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-related-title]', array(
	'label'		=> __( 'Related Posts title', 'supernews' ),
	'section'   => 'supernews-single-post',
	'settings'  => 'supernews_theme_options[supernews-related-title]',
	'type'	  	=> 'text',
	'priority'  => 40
) );

/*related post by tag or category*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-related-post-display-from]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-related-post-display-from'],
	'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_related_post_display_from();
$wp_customize->add_control( 'supernews_theme_options[supernews-related-post-display-from]', array(
	'choices'  	=> $choices,
	'label'		=> __( 'Related Post Display From Options', 'supernews' ),
	'section'   => 'supernews-single-post',
	'settings'  => 'supernews_theme_options[supernews-related-post-display-from]',
	'type'	  	=> 'select',
	'priority'  => 50
) );