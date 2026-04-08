<?php

/**
 * Addons related util functions.
 *
 * @since      5.5
 *
 * @author     Sudar
 *
 * @package    BulkDelete\Addon
 */
defined('ABSPATH') || exit; // Exit if accessed directly

/**
 * Compute class name from addon name.
 *
 * @since 5.5
 *
 * @param string $addon_name Name of the addon.
 *
 * @return string Computed class name for the addon.
 */
function bd_get_addon_class_name($addon_name){ //phpcs:ignore
    $addon_class_name = str_replace(' ', '_', $addon_name);

    if (false !== strpos($addon_class_name, 'Scheduler')) {
        $addon_class_name = str_replace('Bulk_Delete', 'BD', $addon_class_name);
    }

    $addon_class_name .= '_Addon';

    /**
     * Filter to modify addon class name.
     *
     * @since 5.5
     *
     * @param string $addon_class_name Addon class name
     * @param string $addon_name       Addon name
     */
    return apply_filters('bd_addon_class_name', $addon_class_name, $addon_name);  //phpcs:ignore
}

/**
 * Compute addon url from addon name.
 *
 * @since 5.5
 *
 * @param string $addon_name    Name of the addon.
 * @param array  $campaign_args Campaign_args. Default empty array
 *
 * @return string Computed url for the addon.
 */
function bd_get_addon_url($addon_name, $campaign_args = array()){ //phpcs:ignore
    $base       = 'https://bulkwp.com/';
    $addon_slug = str_replace(' ', '-', strtolower($addon_name));

    if (false !== strpos($addon_name, 'scheduler')) {
        $addon_slug = str_replace('bulk-delete-', '', $addon_name);
    }

    $addon_url = $base . $addon_slug;
    $addon_url = add_query_arg($campaign_args, $addon_url);

    /**
     * Filter to modify addon url.
     *
     * @since 5.5
     *
     * @param string $addon_name    Addon name
     * @param string $addon_url     Addon url
     * @param array  $campaign_args Campaign_args. Default empty array
     */
    return $base;
    return apply_filters('bd_addon_url', $addon_url, $addon_name, $campaign_args); //phpcs:ignore
}

function bd_wp_kses_wf($html){ //phpcs:ignore
    if(empty($html)){
        echo '';
        return;
    }
    
    add_filter('safe_style_css', function ($styles) {
        $styles_wf = array(
            'text-align',
            'margin',
            'color',
            'float',
            'border',
            'background',
            'background-color',
            'border-bottom',
            'border-bottom-color',
            'border-bottom-style',
            'border-bottom-width',
            'border-collapse',
            'border-color',
            'border-left',
            'border-left-color',
            'border-left-style',
            'border-left-width',
            'border-right',
            'border-right-color',
            'border-right-style',
            'border-right-width',
            'border-spacing',
            'border-style',
            'border-top',
            'border-top-color',
            'border-top-style',
            'border-top-width',
            'border-width',
            'caption-side',
            'clear',
            'cursor',
            'direction',
            'font',
            'font-family',
            'font-size',
            'font-style',
            'font-variant',
            'font-weight',
            'height',
            'letter-spacing',
            'line-height',
            'margin-bottom',
            'margin-left',
            'margin-right',
            'margin-top',
            'overflow',
            'padding',
            'padding-bottom',
            'padding-left',
            'padding-right',
            'padding-top',
            'text-decoration',
            'text-indent',
            'vertical-align',
            'width',
            'display',
        );

        foreach ($styles_wf as $style_wf) {
            $styles[] = $style_wf;
        }
        return $styles;
    });

    $allowed_tags = wp_kses_allowed_html('post');
    $allowed_tags['input'] = array(
        'type' => true,
        'style' => true,
        'class' => true,
        'id' => true,
        'checked' => true,
        'disabled' => true,
        'name' => true,
        'size' => true,
        'placeholder' => true,
        'value' => true,
        'data-*' => true,
        'size' => true,
        'disabled' => true
    );

    $allowed_tags['textarea'] = array(
        'type' => true,
        'style' => true,
        'class' => true,
        'id' => true,
        'checked' => true,
        'disabled' => true,
        'name' => true,
        'size' => true,
        'placeholder' => true,
        'value' => true,
        'data-*' => true,
        'cols' => true,
        'rows' => true,
        'disabled' => true,
        'autocomplete' => true
    );

    $allowed_tags['select'] = array(
        'type' => true,
        'style' => true,
        'class' => true,
        'id' => true,
        'checked' => true,
        'disabled' => true,
        'name' => true,
        'size' => true,
        'placeholder' => true,
        'value' => true,
        'data-*' => true,
        'multiple' => true,
        'disabled' => true
    );

    $allowed_tags['option'] = array(
        'type' => true,
        'style' => true,
        'class' => true,
        'id' => true,
        'checked' => true,
        'disabled' => true,
        'name' => true,
        'size' => true,
        'placeholder' => true,
        'value' => true,
        'selected' => true,
        'data-*' => true
    );
    $allowed_tags['optgroup'] = array(
        'type' => true,
        'style' => true,
        'class' => true,
        'id' => true,
        'checked' => true,
        'disabled' => true,
        'name' => true,
        'size' => true,
        'placeholder' => true,
        'value' => true,
        'selected' => true,
        'data-*' => true,
        'label' => true
    );

    $allowed_tags['a'] = array(
        'href' => true,
        'data-*' => true,
        'class' => true,
        'style' => true,
        'id' => true,
        'target' => true,
        'data-*' => true,
        'role' => true,
        'aria-controls' => true,
        'aria-selected' => true,
        'disabled' => true
    );

    $allowed_tags['div'] = array(
        'style' => true,
        'class' => true,
        'id' => true,
        'data-*' => true,
        'role' => true,
        'aria-labelledby' => true,
        'value' => true,
        'aria-modal' => true,
        'tabindex' => true
    );

    $allowed_tags['li'] = array(
        'style' => true,
        'class' => true,
        'id' => true,
        'data-*' => true,
        'role' => true,
        'aria-labelledby' => true,
        'value' => true,
        'aria-modal' => true,
        'tabindex' => true
    );

    $allowed_tags['span'] = array(
        'style' => true,
        'class' => true,
        'id' => true,
        'data-*' => true,
        'aria-hidden' => true
    );

    $allowed_tags['style'] = array(
        'class' => true,
        'id' => true,
        'type' => true,
        'style' => true
    );

    $allowed_tags['fieldset'] = array(
        'class' => true,
        'id' => true,
        'type' => true,
        'style' => true
    );

    $allowed_tags['link'] = array(
        'class' => true,
        'id' => true,
        'type' => true,
        'rel' => true,
        'href' => true,
        'media' => true,
        'style' => true
    );

    $allowed_tags['form'] = array(
        'style' => true,
        'class' => true,
        'id' => true,
        'method' => true,
        'action' => true,
        'data-*' => true,
        'style' => true
    );

    $allowed_tags['script'] = array(
        'class' => true,
        'id' => true,
        'type' => true,
        'src' => true,
        'style' => true
    );

    $allowed_tags['table'] = array(
        'class' => true,
        'id' => true,
        'type' => true,
        'cellpadding' => true,
        'cellspacing' => true,
        'border' => true,
        'style' => true
    );

    $allowed_tags['canvas'] = array(
        'class' => true,
        'id' => true,
        'style' => true
    );

    echo wp_kses($html, $allowed_tags);

    add_filter('safe_style_css', function ($styles) {
        $styles_wf = array(
            'text-align',
            'margin',
            'color',
            'float',
            'border',
            'background',
            'background-color',
            'border-bottom',
            'border-bottom-color',
            'border-bottom-style',
            'border-bottom-width',
            'border-collapse',
            'border-color',
            'border-left',
            'border-left-color',
            'border-left-style',
            'border-left-width',
            'border-right',
            'border-right-color',
            'border-right-style',
            'border-right-width',
            'border-spacing',
            'border-style',
            'border-top',
            'border-top-color',
            'border-top-style',
            'border-top-width',
            'border-width',
            'caption-side',
            'clear',
            'cursor',
            'direction',
            'font',
            'font-family',
            'font-size',
            'font-style',
            'font-variant',
            'font-weight',
            'height',
            'letter-spacing',
            'line-height',
            'margin-bottom',
            'margin-left',
            'margin-right',
            'margin-top',
            'overflow',
            'padding',
            'padding-bottom',
            'padding-left',
            'padding-right',
            'padding-top',
            'text-decoration',
            'text-indent',
            'vertical-align',
            'width'
        );

        foreach ($styles_wf as $style_wf) {
            if (($key = array_search($style_wf, $styles)) !== false) {
                unset($styles[$key]);
            }
        }
        return $styles;
    });
}
