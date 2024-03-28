<?php
/**
 * Plugin Name: AR For Wordpress Gutenberg Block
 * Plugin URI: https://webandprint.design/
 * Description: arwp-guten-block — is a Gutenberg plugin created via create-guten-block.
 * Author: webandprintdesign
 * Author URI: https://webandprint.design/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
