<?php
namespace ImageOptimization\Modules\Stats;

use ImageOptimization\Classes\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends Module_Base {
	public function get_name(): string {
		return 'stats';
	}

	public static function routes_list() : array {
		return [
			'Get_Stats',
		];
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		$this->register_routes();
	}
}
