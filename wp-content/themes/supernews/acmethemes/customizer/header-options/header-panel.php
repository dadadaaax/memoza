<?php
/*adding header options panel*/
$wp_customize->add_panel( 'supernews-header-panel', array(
    'priority'       => 30,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Header Options', 'supernews' ),
    'description'    => __( 'Customize your awesome site header ', 'supernews' )
) );

/*
* file for header logo options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/header-logo.php');

/*
* file for header date options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/header-date.php');

/*
* file for header news options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/header-news.php');

/*
* file for header social options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/social-options.php');

/*
* file for header add options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/ad-option.php');

/*
* file for header menu options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/search-option.php');

/*
* file for header menu options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/site-identity-placement.php');

/*
* file for header media display option
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/header-media-display-options.php');

/*
* file for menu options
*/
require_once supernews_file_directory('acmethemes/customizer/header-options/menu-option.php');