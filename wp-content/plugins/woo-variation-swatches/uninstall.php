<?php
/**
 * Uninstall plugin
 */

// If uninstall not called from WordPress exit.
defined( 'WP_UNINSTALL_PLUGIN' ) || die( 'Keep Silent' );

global $wpdb;

// Change to select type.
$wpdb->query(  $wpdb->prepare( "UPDATE {$wpdb->prefix}woocommerce_attribute_taxonomies SET `attribute_type` = %s WHERE `attribute_type` != %s", 'select', 'text' ) );

// Remove Option.
delete_option( 'woo_variation_swatches' );

// Site options in Multisite.
delete_site_option( 'woo_variation_swatches' );

// Delete term meta.

$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->termmeta} WHERE meta_key IN (%s, %s)", 'product_attribute_color', 'product_attribute_image'));

// Clear any cached data that has been removed.
wp_cache_flush();
