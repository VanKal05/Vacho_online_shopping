<?php

namespace ImageOptimization\Modules\Optimization\Classes;

use ImageOptimization\Classes\Image\{
	Image_Meta,
	Image_Optimization_Error_Type,
	Image_Status
};
use ImageOptimization\Modules\Oauth\Classes\Data;
use ImageOptimization\Modules\Stats\Classes\Optimization_Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Optimization_Status {
	/**
	 *
	 *
	 * @param int[] $image_ids
	 * @return array
	 */
	public static function get_images_optimization_statuses( array $image_ids ): array {
		$output = [];
		$images_left = Data::images_left();

		foreach ( $image_ids as $image_id ) {
			$meta = new Image_Meta( $image_id );
			$data = [];

			$status = $meta->get_status();

			$data['status'] = $status;

			if ( Image_Status::OPTIMIZED === $status ) {
				$data['stats'] = Optimization_Stats::get_image_stats( $image_id );
			}

			if ( Image_Status::RESTORING_FAILED === $status ) {
				$data['message'] = esc_html__( 'Image restoring failed', 'image-optimization' );
			}

			if ( Image_Status::OPTIMIZATION_FAILED === $status ) {
				$error_type = $meta->get_error_type() ?? Image_Optimization_Error_Type::GENERIC;
				$error_message = Optimization_Error_Message::get_optimization_error_message( $error_type );

				$data['message'] = $error_message;
				$data['images_left'] = $images_left;
			}

			if ( Image_Status::REOPTIMIZING_FAILED === $status ) {
				$error_type = $meta->get_error_type() ?? Image_Optimization_Error_Type::GENERIC;
				$error_message = Optimization_Error_Message::get_reoptimization_error_message( $error_type );

				$data['message'] = $error_message;
				$data['images_left'] = $images_left;
			}

			$output[ $image_id ] = $data;
		}

		return $output;
	}
}
