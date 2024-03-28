<?php
/**
 * Plugin Name: Image Optimizer by Elementor – Compress, Resize and Optimize Images
 * Description: Automatically compress and enhance your images, boosting your website speed, appearance, and SEO. Get Image Optimizer and optimize your images in seconds.
 * Plugin URI: https://go.elementor.com/wp-repo-description-tab-io-product-page/
 * Version: 1.1.0
 * Author: Elementor.com
 * Author URI: https://go.elementor.com/wp-repo-description-tab-io-author-url/
 * Text Domain: image-optimization
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'IMAGE_OPTIMIZATION_VERSION', '1.1.0' );
define( 'IMAGE_OPTIMIZATION_PATH', plugin_dir_path( __FILE__ ) );
define( 'IMAGE_OPTIMIZATION_URL', plugins_url( '/', __FILE__ ) );
define( 'IMAGE_OPTIMIZATION_ASSETS_PATH', IMAGE_OPTIMIZATION_PATH . 'assets/' );
define( 'IMAGE_OPTIMIZATION_ASSETS_URL', IMAGE_OPTIMIZATION_URL . 'assets/' );
define( 'IMAGE_OPTIMIZATION_PLUGIN_FILE', basename( __FILE__ ) );

/**
 * ImageOptimization Class
 */
final class ImageOptimization {
	private const REQUIRED_EXTENSIONS = [
		'exif',
		'fileinfo',
		'gd',
	];

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		// Load translation
		add_action( 'init', [ $this, 'i18n' ] );

		// Init Plugin
		add_action( 'plugins_loaded', [ $this, 'init' ], -11 );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'image-optimization' );
	}

	public function insufficient_php_version() {
		$message = sprintf(
		/* translators: 1: `<h3>` opening tag, 2: `</h3>` closing tag, 3: PHP version. 4: Link opening tag, 5: Link closing tag. */
			esc_html__( '%1$sImage Optimizer isn’t running because PHP is outdated.%2$s Update to PHP version %3$s and get back to optimizing! %4$sShow me how%5$s', 'image-optimization' ),
			'<h3>',
			'</h3>',
			'7.4',
			'<a href="https://go.elementor.com/wp-dash-update-php/" target="_blank">',
			'</a>'
		);
		$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
		echo wp_kses_post( $html_message );
	}

	private function get_missed_extensions_list(): array {
		$output = [];

		foreach ( self::REQUIRED_EXTENSIONS as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				$output[] = $extension;
			}
		}

		return $output;
	}

	public function add_missed_extensions_notice() {
		$missed_extensions = $this->get_missed_extensions_list();

		$message = sprintf(
		/* translators: 1: `<h3>` opening tag, 2: Missed extension names, 3: `</h3>` closing tag. */
			esc_html__( '%1$sImage Optimizer isn’t running because the next required PHP extensions are missed: %2$s.%3$s', 'image-optimization' ),
			'<h3>',
			implode( ', ', $missed_extensions ),
			'</h3>',
		);

		$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );

		echo wp_kses_post( $html_message );
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that PHP version is sufficient.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {
		if ( ! version_compare( PHP_VERSION, '7.4', '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'insufficient_php_version' ] );
		} else if ( count( $this->get_missed_extensions_list() ) ) {
			add_action( 'admin_notices', [ $this, 'add_missed_extensions_notice' ] );
		} else {
			// Once we get here, We have passed all validation checks, so we can safely include our plugin
			require_once 'plugin.php';
		}
	}
}

// Instantiate ImageOptimization..
new ImageOptimization();
