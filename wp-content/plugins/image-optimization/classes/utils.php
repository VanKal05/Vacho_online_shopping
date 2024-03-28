<?php

namespace ImageOptimization\Classes;

use ImageOptimization\Classes\Client\Client;
use ImageOptimization\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Utils {
	/**
	 * get_elementor
	 * @param $instance
	 *
	 * @return \Elementor\Plugin|false|mixed|null
	 */
	public static function get_elementor( $instance = false ) {
		static $_instance = null;
		if ( false !== $instance ) {
			$_instance = $instance;

			return $instance;
		}
		if ( null !== $_instance ) {
			return $_instance;
		}
		if ( class_exists( 'Elementor\Plugin' ) ) {
			return \Elementor\Plugin::instance(); // @codeCoverageIgnore
		}

		return false;
	}

	public static function get_api_client(): ?Client {
		return Client::get_instance();
	}

	public static function get_module( $module_name = '' ) {
		return Plugin::instance()->modules_manager->get_modules( $module_name );
	}

	public static function get_module_component( $module_name, $component ) {
		$module = self::get_module( $module_name );
		if ( $module ) {
			return $module->get_component( $component );
		}
		return null;
	}

	/**
	 * is_elementor_installed
	 * @return bool
	 */
	public static function is_elementor_installed(): bool {
		$plugins = get_plugins();
		return isset( $plugins['elementor/elementor.php'] );
	}

	/**
	 * is_elementor_installed_and_active
	 * should be used only after `plugins_loaded` action
	 * @return bool
	 */
	public static function is_elementor_installed_and_active(): bool {
		return did_action( 'elementor/loaded' );
	}

	public static function is_media_page(): bool {
		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return false;
		}

		return 'upload' === $current_screen->id && 'attachment' === $current_screen->post_type;
	}

	public static function is_plugin_page(): bool {
		$current_screen = get_current_screen();

		return str_contains( $current_screen->id, 'image-optimization-' );
	}

	public static function user_is_admin(): bool {
		return current_user_can( 'manage_options' );
	}
}
