<?php
// Category Color Options
$wp_customize->add_section('supernews_category_color_setting', array(
    'priority'      => 40,
    'title'         => __('Category Color Options', 'supernews'),
    'description'   => __('Change the highlighted color of each category items as you want.', 'supernews'),
    'panel'          => 'supernews-design-panel'
));

$i = 1;
$args = array(
    'orderby' => 'id',
    'hide_empty' => 0
);
$categories = get_categories( $args );
$wp_category_list = array();
foreach ($categories as $category_list ) {
    $wp_category_list[$category_list->cat_ID] = $category_list->cat_name;

    $wp_customize->add_setting('supernews_theme_options[cat-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']', array(
        'default'           => $defaults['supernews-primary-color'],
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color'
    ));

    $wp_customize->add_control(
    	new WP_Customize_Color_Control(
    		$wp_customize,
		    'supernews_theme_options[cat-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']',
		    array(
		    	'label'     => sprintf(__('"%s" Color', 'supernews'), $wp_category_list[$category_list->cat_ID] ),
			    'section'   => 'supernews_category_color_setting',
			    'settings'  => 'supernews_theme_options[cat-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']',
			    'priority'  => $i
		    )
	    )
    );
	$wp_customize->add_setting('supernews_theme_options[cat-hover-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']', array(
		'default'           => $defaults['supernews-cat-hover-color'],
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color'
	));

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'supernews_theme_options[cat-hover-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']',
			array(
				'label'     => sprintf(__('"%s" Hover Color', 'supernews'), $wp_category_list[$category_list->cat_ID] ),
				'section'   => 'supernews_category_color_setting',
				'settings'  => 'supernews_theme_options[cat-hover-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']',
				'priority'  => $i
			)
		)
	);

	/*adding hr between cats*/
	$wp_customize->add_setting('supernews_theme_options[cat-hr-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']', array(
		'capability'		=> 'edit_theme_options',
		'default'			=> '',
		'sanitize_callback' => 'esc_attr'
	));

	$wp_customize->add_control(
		new Supernews_Customize_Message_Control(
			$wp_customize,
			'supernews_theme_options[cat-hr-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']',
			array(
				'section'   => 'supernews_category_color_setting',
				'description'    => "<hr>",
				'settings'  => 'supernews_theme_options[cat-hr-'.get_cat_id($wp_category_list[$category_list->cat_ID]).']',
				'type'	  	=> 'message',
				'priority'  => $i
			)
		)
	);
    $i++;
}