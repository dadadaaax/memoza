<?php
/*adding feature options panel*/
$wp_customize->add_panel( 'supernews-feature-panel', array(
    'priority'       => 70,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __( 'Featured Section Options', 'supernews' ),
    'description'    => __( 'Customize your awesome site feature section ', 'supernews' )
) );

/*
* file for feature slider category
*/
require_once supernews_file_directory('acmethemes/customizer/feature-section/feature-category.php');

/*
* file for feature section enable
*/
require_once supernews_file_directory('acmethemes/customizer/feature-section/feature-enable.php');