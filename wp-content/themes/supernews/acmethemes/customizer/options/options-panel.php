<?php
/*adding theme options panel*/
$wp_customize->add_panel( 'supernews-options', array(
    'priority'       => 210,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Theme Options', 'supernews' ),
    'description'    => __( 'Customize your awesome site with theme options ', 'supernews' )
) );

/*
* file for front page
*/
require_once supernews_file_directory('acmethemes/customizer/options/front-page.php');

/*
* file for header breadcrumb options
*/
require_once supernews_file_directory('acmethemes/customizer/options/breadcrumb.php');

/*
* file for header search options
*/
require_once supernews_file_directory('acmethemes/customizer/options/search.php');