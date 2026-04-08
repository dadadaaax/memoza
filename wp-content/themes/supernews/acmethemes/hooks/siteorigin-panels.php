<?php
/**
 * Adds SuperNews Theme Widgets in SiteOrigin Pagebuilder Tabs
 *
 * @since SuperNews 1.5.0
 *
 * @param null
 * @return null
 *
 */
function supernews_widgets($widgets) {
    $theme_widgets = array(
        'supernews_ad_widget',
        'supernews_posts_col'
    );
    foreach($theme_widgets as $theme_widget) {
        if( isset( $widgets[$theme_widget] ) ) {
            $widgets[$theme_widget]['groups'] = array('supernews');
            $widgets[$theme_widget]['icon']   = 'dashicons dashicons-screenoptions';
        }
    }
    return $widgets;
}
add_filter('siteorigin_panels_widgets', 'supernews_widgets');

/**
 * Add a tab for the theme widgets in the page builder
 *
 * @since SuperNews 1.5.0
 *
 * @param null
 * @return null
 *
 */
function supernews_widgets_tab($tabs){
    $tabs[] = array(
        'title'  => __('AT SuperNews Widgets', 'supernews'),
        'filter' => array(
            'groups' => array('supernews')
        )
    );
    return $tabs;
}
add_filter('siteorigin_panels_widget_dialog_tabs', 'supernews_widgets_tab', 20);