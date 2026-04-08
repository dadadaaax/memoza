<?php
/**
 * Dynamic css
 *
 * @since SuperNews 1.0.0
 *
 * @param null
 * @return null
 *
 */
if ( ! function_exists( 'supernews_dynamic_css' ) ) :

    function supernews_dynamic_css() {

        global $supernews_customizer_all_values;
        /*Color options */
        $supernews_primary_color = esc_attr( $supernews_customizer_all_values['supernews-primary-color'] );

        $custom_css = '';
        /*background*/
        $custom_css .= "mark,
            .comment-form .form-submit input,
            .read-more,
            .header-latest-posts .bn-title,
            .slider-section .cat-links a,
            .featured-desc .above-entry-meta .cat-links a,
            #calendar_wrap #wp-calendar #today,
            #calendar_wrap #wp-calendar #today a,
            .wpcf7-form input.wpcf7-submit:hover,
            .breadcrumb,
            .post-thumb .cat-links > a,
            article.post.sticky,
            .slicknav_btn{
            background: {$supernews_primary_color};
            }";

        /*color*/
        $custom_css .= "
             a:hover,
            .screen-reader-text:focus,
            .bn-content a:hover,
            .socials a:hover,
            .site-title a,
            .widget_search input#s,
            .search-block #searchsubmit,
            .widget_search #searchsubmit,
            .footer-sidebar .featured-desc .below-entry-meta a:hover,
            .slider-section .slide-title:hover,
            .slider-feature-wrap a:hover,
            .featured-desc .below-entry-meta span:hover,
            .posted-on a:hover,
            .cat-links a:hover,
            .comments-link a:hover,
            .edit-link a:hover,
            .tags-links a:hover,
            .byline a:hover,
            .nav-links a:hover,
            #supernews-breadcrumbs a:hover,
            .wpcf7-form input.wpcf7-submit,
            .header-wrapper .menu li:hover > a,
            .header-wrapper .menu > li.current-menu-item > a,
            .header-wrapper .menu > li.current-menu-parent > a,
            .header-wrapper .menu > li.current_page_parent > a,
            .header-wrapper .menu > li.current_page_ancestor > a,
            .header-wrapper .main-navigation ul ul.sub-menu li:hover > a,
            .top-block li a:hover
            {
                color: {$supernews_primary_color};
            }";

        /*border*/
        $custom_css .= "
            .widget_search input#s,
            .tagcloud a{
                border: 1px solid {$supernews_primary_color};
            }
            .footer-wrapper .footer-copyright,
            .nav-links .nav-previous a:hover,
            .nav-links .nav-next a:hover{
                border-top: 1px solid {$supernews_primary_color};
            }
            .widget-title:before,
            .page-header .page-title:before,
            .single .entry-header .entry-title:before,
            .blog-no-image article.post.sticky{
                border-bottom: 3px solid {$supernews_primary_color};
            }
            .wpcf7-form input.wpcf7-submit{
                border: 2px solid {$supernews_primary_color};
            }
            .bn-title::after,
            .breadcrumb::after {
                border-left: 5px solid {$supernews_primary_color};
            }
            .rtl .bn-title::after,
            .rtl .breadcrumb::after {
                border-right: 5px solid {$supernews_primary_color};
                border-left: medium none;
            }";

        /*media width*/
        $custom_css .= "
        @media screen and (max-width:992px){
                .slicknav_btn{
                    border: 1px solid {$supernews_primary_color};
                }
                .slicknav_btn.slicknav_open{
                    border: 1px solid #ffffff;
                }
                .slicknav_nav li:hover > a,
                .slicknav_nav li.current-menu-ancestor a,
                .slicknav_nav li.current-menu-item  > a,
                .slicknav_nav li.current_page_item a,
                .slicknav_nav li.current_page_item .slicknav_item span,
                .slicknav_nav li .slicknav_item:hover a{
                    color: {$supernews_primary_color};
                }
            }";

	    /*category color*/
	    /*category color options*/
	    $args = array(
		    'orderby' => 'id',
		    'hide_empty' => 0
	    );
	    $categories = get_categories( $args );
	    $wp_category_list = array();
	    $i = 1;
	    foreach ($categories as $category_list ) {
		    $wp_category_list[$category_list->cat_ID] = $category_list->cat_name;

		    $cat_color = 'cat-'.esc_attr( get_cat_id($wp_category_list[$category_list->cat_ID]) );
		    $cat_hover_color = 'cat-hover-'.esc_attr( get_cat_id($wp_category_list[$category_list->cat_ID]) );

		    if( isset( $supernews_customizer_all_values[$cat_color] )){
			    $cat_color = $supernews_customizer_all_values[$cat_color];
			    if( !empty( $cat_color )){
				    $custom_css .= "
                    .cat-links .at-cat-item-{$category_list->cat_ID}{
                    background: {$cat_color}!important;
                    color : #fff!important;
                    }
                    ";

				    /*widget tittle*/
				    $custom_css .= "
                    .at-cat-color-wrap-{$category_list->cat_ID} .widget-title::before,
                    body.category-{$category_list->cat_ID} .page-header .page-title::before
                    {
                     border-bottom: 3px solid {$cat_color};
                    }
                    ";
				    $custom_css .= "
                    .at-cat-color-wrap-{$category_list->cat_ID} .widget-title,
                     body.category-{$category_list->cat_ID} .page-header .page-title
                    {
                     border-bottom: 1px solid {$cat_color};
                    }";
			    }
		    }
		    else{
			    $custom_css .= "
                    .cat-links .at-cat-item-{$category_list->cat_ID}{
                    background: {$supernews_primary_color}!important;
                    color : #fff!important;
                    }
                    ";
		    }
		    if( isset( $supernews_customizer_all_values[$cat_hover_color] )){
			    $cat_hover_color = $supernews_customizer_all_values[$cat_hover_color];
			    if( !empty( $cat_hover_color )){
				    $custom_css .= "
                    .cat-links .at-cat-item-{$category_list->cat_ID}:hover{
                    background: {$cat_hover_color}!important;
                    color : #fff!important;
                    }
                    ";
			    }
		    }
		    else{
			    $custom_css .= "
                    .cat-links .at-cat-item-{$category_list->cat_ID}:hover{
                    background: #2d2d2d!important;
                    color : #fff!important;
                    }
                    ";
		    }
		    $i++;
	    }
	    /*category color end*/

        /*custom css*/
        $supernews_custom_css = wp_strip_all_tags ( $supernews_customizer_all_values['supernews-custom-css'] );
        if ( ! empty( $supernews_custom_css ) ) {
            $custom_css .= $supernews_custom_css;
        }
        wp_add_inline_style( 'supernews-style', $custom_css );
    }
endif;
add_action( 'wp_enqueue_scripts', 'supernews_dynamic_css', 99 );