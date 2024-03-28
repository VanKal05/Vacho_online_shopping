<?php

namespace ImageOptimization\Modules\Settings\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings {
	public const COMPRESSION_LEVEL_OPTION_NAME = 'image_optimizer_compression_level';
	public const OPTIMIZE_ON_UPLOAD_OPTION_NAME = 'image_optimizer_optimize_on_upload';
	public const CONVERT_TO_WEBP_OPTION_NAME = 'image_optimizer_convert_to_webp';
	public const RESIZE_LARGER_IMAGES_OPTION_NAME = 'image_optimizer_resize_larger_images';
	public const RESIZE_LARGER_IMAGES_SIZE_OPTION_NAME = 'image_optimizer_resize_larger_images_size';
	public const STRIP_EXIF_METADATA_OPTION_NAME = 'image_optimizer_exif_metadata';
	public const BACKUP_ORIGINAL_IMAGES_OPTION_NAME = 'image_optimizer_original_images';
	public const CUSTOM_SIZES_OPTION_NAME = 'image_optimizer_custom_sizes';

	/**
	 * Returns plugin settings data by option name typecasted to an appropriate data type.
	 *
	 * @param string $option_name
	 * @return mixed
	 */
	public static function get( string $option_name ) {
		$data = get_option( $option_name );

		switch ( $option_name ) {
			case self::RESIZE_LARGER_IMAGES_SIZE_OPTION_NAME:
				return (int) $data;

			case self::CONVERT_TO_WEBP_OPTION_NAME:
			case self::RESIZE_LARGER_IMAGES_OPTION_NAME:
			case self::STRIP_EXIF_METADATA_OPTION_NAME:
			case self::BACKUP_ORIGINAL_IMAGES_OPTION_NAME:
				return (bool) $data;

			case self::CUSTOM_SIZES_OPTION_NAME:
				if ( 'all' === $data ) {
					return $data;
				}

				if ( empty( $data ) ) {
					return [];
				}

				return explode( ',', $data );

			default:
				return $data;
		}
	}
}
