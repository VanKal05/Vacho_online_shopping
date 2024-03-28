<?php

namespace ImageOptimization\Classes\Client;

use Exception;
use ImageOptimization\Modules\Oauth\Classes\Exceptions\Quota_Exceeded_Error;
use ImageOptimization\Modules\Optimization\Classes\Exceptions\Bulk_Token_Expired_Error;
use ImageOptimization\Modules\Optimization\Classes\Exceptions\Image_Already_Optimized_Error;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Client_Response {
	private array $known_errors;
	/**
	 * @var mixed
	 */
	private $response;

	/**
	 * @throws Throwable|Quota_Exceeded_Error|Bulk_Token_Expired_Error|Image_Already_Optimized_Error
	 */
	public function handle() {
		if ( ! is_wp_error( $this->response ) ) {
			return $this->response;
		}

		$message = $this->response->get_error_message();

		if ( isset( $this->known_errors[ $message ] ) ) {
			throw $this->known_errors[ $message ];
		}

		throw new Exception( $message );
	}

	public function __construct( $response ) {
		$this->known_errors = [
			'user reached limit' => new Quota_Exceeded_Error( esc_html__( 'Plan quota reached', 'image-optimizer' ) ),
			'Bulk token expired' => new Bulk_Token_Expired_Error( esc_html__( 'Bulk token expired', 'image-optimizer' ) ),
			'Image already optimized' => new Image_Already_Optimized_Error( esc_html__( 'Image already optimized', 'image-optimizer' ) ),
		];
		$this->response = $response;
	}
}
