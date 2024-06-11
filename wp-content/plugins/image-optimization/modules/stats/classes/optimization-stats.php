<?php

namespace ImageOptimization\Modules\Stats\Classes;

use ImageOptimization\Classes\Image\{
	Image,
	Image_Meta,
	Image_Query_Builder,
	WP_Image_Meta,
	Exceptions\Invalid_Image_Exception
};
use ImageOptimization\Classes\File_System\Exceptions\File_System_Operation_Error;
use ImageOptimization\Classes\File_System\File_System;
use ImageOptimization\Classes\Logger;
use ImageOptimization\Modules\Optimization\Classes\Exceptions\Image_Validation_Error;
use ImageOptimization\Modules\Optimization\Classes\Validate_Image;
use ImageOptimization\Modules\Settings\Classes\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Optimization_Stats {
	const PAGING_SIZE = 25000;

	/**
	 * Returns image stats.
	 * If the library is too big, it queries images in chunks.
	 *
	 * @return array{total_image_count: int, optimized_image_count: int, current_image_size: int, initial_image_size:
	 *     int}
	 */
	public static function get_image_stats( ?int $image_id = null ): array {
		$output = self::get_image_stats_chunk( 1, $image_id );
		$pages_count = $output['pages'];

		if ( $pages_count > 1 ) {
			// $i initially is 2 bc we already got the first page, so we don't have to query it again
			for ( $i = 2; $i <= $pages_count; $i ++ ) {
				$chunk = self::get_image_stats_chunk( $i );

				foreach ( array_keys( $chunk ) as $key ) {
					if ( isset( $output[ $key ] ) ) {
						$output[ $key ] += $chunk[ $key ];
						continue;
					}

					$output[ $key ] = $chunk[ $key ];
				}
			}
		}

		unset( $output['pages'] );

		return $output;
	}

	/**
	 * @return array{pages: int, total_image_count: int, optimized_image_count: int, current_image_size: int,
	 *     initial_image_size: int}
	 */
	public static function get_image_stats_chunk( int $paged = 1, ?int $image_id = null ): array {
		$output = [
			'pages' => 1,
			'total_image_count' => 0,
			'optimized_image_count' => 0,
			'current_image_size' => 0,
			'initial_image_size' => 0,
		];

		$query = ( new Image_Query_Builder() )
			->set_paging_size( self::PAGING_SIZE )
			->set_current_page( $paged );

		if ( $image_id ) {
			$query->set_image_ids( [ $image_id ] );
		}

		$query = $query->execute();

		$output['pages'] = $query->max_num_pages;

		foreach ( $query->posts as $attachment_id ) {
			try {
				Validate_Image::is_valid( $attachment_id );
				$wp_meta = new WP_Image_Meta( $attachment_id );
			} catch ( Invalid_Image_Exception | Image_Validation_Error $ie ) {
				Logger::log( Logger::LEVEL_ERROR, $ie->getMessage() );

				continue;
			}

			$meta = new Image_Meta( $attachment_id );
			$image_sizes = $wp_meta->get_size_keys();

			$current_sizes = self::filter_only_enabled_sizes( $image_sizes );
			$optimized_sizes = self::filter_only_enabled_sizes( $meta->get_optimized_sizes() );

			$output['total_image_count'] += count( $current_sizes );
			$output['optimized_image_count'] += count( $optimized_sizes );

			foreach ( $image_sizes as $image_size ) {
				$output['current_image_size'] += self::calculate_current_image_file_size( $attachment_id, $wp_meta, $image_size );
				$output['initial_image_size'] += self::calculate_initial_image_file_size( $attachment_id, $meta, $wp_meta, $image_size );
			}
		}

		return $output;
	}

	private static function calculate_current_image_file_size( int $image_id, WP_Image_Meta $wp_meta, string $image_size ): int {
		$size_from_meta = $wp_meta->get_file_size( $image_size );

		if ( $size_from_meta ) {
			return $size_from_meta;
		}

		try {
			return File_System::size( ( new Image( $image_id ) )->get_file_path( $image_size ) );
		} catch ( File_System_Operation_Error $e ) {
			return 0;
		}
	}

	private static function calculate_initial_image_file_size( int $image_id, Image_Meta $meta, WP_Image_Meta $wp_meta, string $image_size ): int {
		$size_from_meta = $meta->get_original_file_size( $image_size ) ?? $wp_meta->get_file_size( $image_size );

		if ( $size_from_meta ) {
			return $size_from_meta;
		}

		try {
			return File_System::size( ( new Image( $image_id ) )->get_file_path( $image_size ) );
		} catch ( File_System_Operation_Error $e ) {
			return 0;
		}
	}

	private static function filter_only_enabled_sizes( array $size_keys ): array {
		$enabled_sizes = Settings::get( Settings::CUSTOM_SIZES_OPTION_NAME );

		if ( 'all' === $enabled_sizes ) {
			return array_filter( $size_keys, fn( string $size_key ) => ! str_starts_with( $size_key, 'elementor_' ) );
		}

		return array_filter($size_keys, function( string $size ) use ( $enabled_sizes ) {
			if ( Image::SIZE_FULL === $size ) {
				return true;
			}

			if ( in_array( $size, $enabled_sizes, true ) ) {
				return true;
			}

			return false;
		});
	}
}
