<?php
/*adding sections for blog layout options*/
$wp_customize->add_section( 'supernews-design-blog-layout-option', array(
    'priority'       => 30,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Default Blog/Archive Layout', 'supernews' ),
    'panel'          => 'supernews-design-panel'
) );

/*blog layout*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-blog-archive-layout]', array(
    'capability'		=> 'edit_theme_options',
    'default'			=> $defaults['supernews-blog-archive-layout'],
    'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_blog_layout();
$wp_customize->add_control( 'supernews_theme_options[supernews-blog-archive-layout]', array(
    'choices'  	=> $choices,
    'label'		=> __( 'Default Blog/Archive Layout', 'supernews' ),
    'description'=> __( 'Image display options', 'supernews' ),
    'section'   => 'supernews-design-blog-layout-option',
    'settings'  => 'supernews_theme_options[supernews-blog-archive-layout]',
    'type'	  	=> 'select'
) );

/*blog image size*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-blog-archive-image-layout]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-blog-archive-image-layout'],
	'sanitize_callback' => 'supernews_sanitize_select'
) );
$choices = supernews_get_image_sizes_options();
$wp_customize->add_control( 'supernews_theme_options[supernews-blog-archive-image-layout]', array(
	'choices'  	=> $choices,
	'label'		=> __( 'Image Layout Options', 'supernews' ),
	'section'   => 'supernews-design-blog-layout-option',
	'settings'  => 'supernews_theme_options[supernews-blog-archive-image-layout]',
	'type'	  	=> 'select',
) );

/*Read More Text*/
$wp_customize->add_setting( 'supernews_theme_options[supernews-blog-archive-more-text]', array(
	'capability'		=> 'edit_theme_options',
	'default'			=> $defaults['supernews-blog-archive-more-text'],
	'sanitize_callback' => 'sanitize_text_field'
) );
$wp_customize->add_control( 'supernews_theme_options[supernews-blog-archive-more-text]', array(
	'label'		=> __( 'Read More Text', 'supernews' ),
	'section'   => 'supernews-design-blog-layout-option',
	'settings'  => 'supernews_theme_options[supernews-blog-archive-more-text]',
	'type'	  	=> 'text'
) );