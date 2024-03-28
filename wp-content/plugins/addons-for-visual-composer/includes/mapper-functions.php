<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

function lvca_get_vc_map_carousel_display_options() {

    $map = array(
        array(
            'type' => 'lvca_number',
            'param_name' => 'display_columns',
            'heading' => __('Columns per row', 'livemesh-vc-addons'),
            'min' => 1,
            'max' => 8,
            'integer' => true,
            'value' => 3,
            'group' => __('Desktop', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'lvca_number',
            'param_name' => 'scroll_columns',
            'heading' => __('Columns to scroll', 'livemesh-vc-addons'),
            'min' => 1,
            'max' => 8,
            'integer' => true,
            'value' => 3,
            'group' => __('Desktop', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'lvca_number',
            'param_name' => 'gutter',
            'heading' => __('Gutter', 'livemesh-vc-addons'),
            'description' => __('Space between columns.', 'livemesh-vc-addons'),
            'value' => 10,
            'group' => __('Desktop', 'livemesh-vc-addons'),
        ),

        array(
            'type' => 'lvca_number',
            'param_name' => 'tablet_display_columns',
            'heading' => __('Columns per row', 'livemesh-vc-addons'),
            'min' => 1,
            'max' => 4,
            'integer' => true,
            'value' => 2,
            'group' => __('Tablet', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'lvca_number',
            'param_name' => 'tablet_scroll_columns',
            'heading' => __('Columns to scroll', 'livemesh-vc-addons'),
            'min' => 1,
            'max' => 4,
            'integer' => true,
            'value' => 2,
            'group' => __('Tablet', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'lvca_number',
            'param_name' => 'tablet_gutter',
            'heading' => __('Gutter', 'livemesh-vc-addons'),
            'description' => __('Space between columns.', 'livemesh-vc-addons'),
            'value' => 10,
            'group' => __('Tablet', 'livemesh-vc-addons'),
        ),

        array(
            'type' => 'textfield',
            'param_name' => 'tablet_width',
            'heading' => __('Resolution', 'livemesh-vc-addons'),
            'description' => __('The resolution to treat as a tablet resolution.', 'livemesh-vc-addons'),
            'std' => 800,
            'sanitize' => 'intval',
            'group' => __('Tablet', 'livemesh-vc-addons'),
        ),

        array(
            'type' => 'lvca_number',
            'param_name' => 'mobile_display_columns',
            'heading' => __('Columns per row', 'livemesh-vc-addons'),
            'min' => 1,
            'max' => 3,
            'integer' => true,
            'value' => 1,
            'group' => __('Mobile', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'lvca_number',
            'param_name' => 'mobile_scroll_columns',
            'heading' => __('Columns to scroll', 'livemesh-vc-addons'),
            'min' => 1,
            'max' => 3,
            'integer' => true,
            'value' => 1,
            'group' => __('Mobile', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'lvca_number',
            'param_name' => 'mobile_gutter',
            'heading' => __('Gutter', 'livemesh-vc-addons'),
            'description' => __('Space between columns.', 'livemesh-vc-addons'),
            'value' => 10,
            'group' => __('Mobile', 'livemesh-vc-addons'),
        ),

        array(
            'type' => 'textfield',
            'param_name' => 'mobile_width',
            'heading' => __('Resolution', 'livemesh-vc-addons'),
            'description' => __('The resolution to treat as a mobile resolution.', 'livemesh-vc-addons'),
            'std' => 480,
            'sanitize' => 'intval',
            'group' => __('Mobile', 'livemesh-vc-addons'),
        )
    );


    return apply_filters('lvca_vc_map_carousel_display_options', $map);
}

function lvca_get_vc_map_carousel_options($group = '') {

    $map = array(
        array(
            'type' => 'checkbox',
            "param_name" => "arrows",
            'heading' => __('Prev/Next Arrows?', 'livemesh-vc-addons'),
            'value' => array(__('Yes', 'livemesh-vc-addons') => 'yes'),
            'group' => $group
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "dots",
            'heading' => __('Show dot indicators for navigation?', 'livemesh-vc-addons'),
            'value' => array(__('Yes', 'livemesh-vc-addons') => 'yes'),
            'group' => $group
        ),

        array(
            'type' => 'checkbox',
            'param_name' => 'autoplay',
            'heading' => __('Autoplay?', 'livemesh-vc-addons'),
            'description' => __('Should the carousel autoplay as in a slideshow.', 'livemesh-vc-addons'),
            'value' => array(__('Yes', 'livemesh-vc-addons') => 'yes'),
            'group' => $group
        ),

        array(
            'type' => 'lvca_number',
            "param_name" => "autoplay_speed",
            'heading' => __('Autplay speed', 'livemesh-vc-addons'),
            'value' => 3000,
            'group' => $group
        ),

        array(
            'type' => 'lvca_number',
            "param_name" => "animation_speed",
            'heading' => __('Autoplay animation speed in ms', 'livemesh-vc-addons'),
            'value' => 300,
            'group' => $group
        ),

        array(
            'type' => 'checkbox',
            "param_name" => "pause_on_hover",
            'heading' => __('Pause on Hover', 'livemesh-vc-addons'),
            'description' => __('Should the slider pause on mouse hover over the slider.', 'livemesh-vc-addons'),
            'value' => array(__('Yes', 'livemesh-vc-addons') => 'yes'),
            'group' => $group,
        ));

    return apply_filters('lvca_vc_map_carousel_options', $map, $group);
}

function lvca_get_default_atts_carousel() {

    $atts = array(
        'arrows' => '',
        'dots' => '',
        'autoplay' => '',
        'autoplay_speed' => 3000,
        'animation_speed' => 300,
        'pause_on_hover' => '',
        'display_columns' => 3,
        'scroll_columns' => 3,
        'gutter' => 10,
        'tablet_display_columns' => 2,
        'tablet_scroll_columns' => 2,
        'tablet_gutter' => 10,
        'tablet_width' => 800,
        'mobile_display_columns' => 1,
        'mobile_scroll_columns' => 1,
        'mobile_gutter' => 10,
        'mobile_width' => 480,
    );

    return apply_filters('lvca_carousel_default_atts', $atts);

}

function lvca_get_vc_map_slider_arrow_options() {

    $map = array(
        array(
            'type' => 'dropdown',
            'param_name' => 'arrows_placement',
            'heading' => __('Placement', 'livemesh-vc-addons'),
            'value' => array(
                __('Middle Center', 'livemesh-vc-addons') => 'middle-center',
                __('Bottom Center', 'livemesh-vc-addons') => 'bottom-center',
                __('Top Left', 'livemesh-vc-addons') => 'top-left',
                __('Top Right', 'livemesh-vc-addons') => 'top-right',
                __('Bottom Left', 'livemesh-vc-addons') => 'bottom-left',
                __('Bottom Right', 'livemesh-vc-addons') => 'bottom-right'
            ),
            'std' => 'middle-center',
            'group' => __('Slider Arrow', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'dropdown',
            'param_name' => 'arrows_shape',
            'heading' => __('Shape', 'livemesh-vc-addons'),
            'value' => array(
                __('Square', 'livemesh-vc-addons') => 'square',
                __('Square with Rounded Corners', 'livemesh-vc-addons') => 'rounded-corners',
                __('Circle', 'livemesh-vc-addons') => 'circle',
            ),
            'std' => 'circle',
            'group' => __('Slider Arrow', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'dropdown',
            'param_name' => 'arrows_color',
            'heading' => __('Color', 'livemesh-vc-addons'),
            'value' => array(
                __('Dark', 'livemesh-vc-addons') => 'dark',
                __('Light', 'livemesh-vc-addons') => 'light',
            ),
            'std' => 'dark',
            'group' => __('Slider Arrow', 'livemesh-vc-addons'),
        ),
        array(
            'type' => 'dropdown',
            'param_name' => 'arrows_visibility',
            'heading' => __('Visibility', 'livemesh-vc-addons'),
            'value' => array(
                __('Show Always', 'livemesh-vc-addons') => 'always',
                __('Show on Hover', 'livemesh-vc-addons') => 'on-hover',
            ),
            'std' => 'always',
            'group' => __('Slider Arrow', 'livemesh-vc-addons'),
        ),
    );

    return apply_filters('lvca_vc_map_slider_arrow_options', $map);
}