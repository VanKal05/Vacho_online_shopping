<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
if ( ! defined( 'ABSPATH' ) ) exit; 

function ar_wp_advance_post_type() {
// Set UI labels for ar_woo_advance Post Type
    $labels = array(
        'name' => _x('AR Models', 'Post Type General Name', 'ar-for-wordpress'),
        'singular_name' => _x('Models', 'Post Type Singular Name', 'ar-for-wordpress'),
        'menu_name' => __('AR Models', 'ar-for-wordpress'),
        'parent_item_colon' => __('Parent Models', 'ar-for-wordpress'),
        'all_items' => __('All AR Models', 'ar-for-wordpress'),
        'view_item' => __('View Models', 'ar-for-wordpress'),
        'add_new_item' => __('Add New Models', 'ar-for-wordpress'),
        'add_new' => __('Add New', 'ar-for-wordpress'),
        'edit_item' => __('Edit Models', 'ar-for-wordpress'),
        'update_item' => __('Update Models', 'ar-for-wordpress'),
        'search_items' => __('Search Models', 'ar-for-wordpress'),
        'not_found' => __('Not Found', 'ar-for-wordpress'),
        'not_found_in_trash' => __('Not found in Trash', 'ar-for-wordpress'),
    );

    // Set other options for ar_woo_advance Post Type

    $args = array(
        'label' => __('AR Models', 'ar-for-wordpress'),
        'description' => __('Models news and reviews', 'ar-for-wordpress'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'taxonomies' => array('genres'),
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
        'show_in_rest' => true,
        'menu_icon' => plugins_url( "assets/images/menu_icon.png", __FILE__ ),
	
    );
    // Registering your ar_woo_advance Post Type
    register_post_type('AR Models', $args);
}

add_action('init', 'ar_wp_advance_post_type', 0);


//taxonomy, make it hierarchical like categories
add_action('init', 'ar_wp_advance_model_hierarchical_taxonomy', 0);

function ar_wp_advance_model_hierarchical_taxonomy() {
    $labels = array(
        'name' => _x('AR Categories', 'taxonomy general name', 'ar-for-wordpress'),
        'singular_name' => _x('Models Category', 'taxonomy singular name', 'ar-for-wordpress'),
        'search_items' => __('Search AR Categories', 'ar-for-wordpress'),
        'all_items' => __('All AR Categories', 'ar-for-wordpress'),
        'parent_item' => __('Parent Models Category', 'ar-for-wordpress' ),
        'parent_item_colon' => __('Parent Models Category:', 'ar-for-wordpress'),
        'edit_item' => __('Edit Models Category', 'ar-for-wordpress'),
        'update_item' => __('Update Models Category', 'ar-for-wordpress'),
        'add_new_item' => __('Add New Models Category', 'ar-for-wordpress'),
        'new_item_name' => __('New Models Category Name', 'ar-for-wordpress'),
        'menu_name' => __('AR Categories', 'ar-for-wordpress'),
        
    );
    
    // Now register the taxonomy
    register_taxonomy('model_category', array('armodels'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'model_category'),
    ));
}

?>
