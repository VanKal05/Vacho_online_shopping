<?php

namespace ImageOptimization\Modules\Optimization\Classes;

use ImageOptimization\Classes\Image\{
	Exceptions\Invalid_Image_Exception,
	Image,
	WP_Image_Meta,
};
use ImageOptimization\Classes\File_System\Exceptions\File_System_Operation_Error;
use ImageOptimization\Classes\File_System\File_System;
use ImageOptimization\Classes\File_Utils;
use ImageOptimization\Modules\Optimization\Classes\Exceptions\Image_Validation_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Validate_Image {
	public const MAX_FILE_SIZE = 10 * 1024 * 1024;

	/**
	 * Returns true if $image_id provided associated with an image that can be optimized.
	 *
	 * @param int $image_id Attachment id.
	 *
	 * @return true
	 * @throws Image_Validation_Error
	 * @throws Invalid_Image_Exception
	 */
	public static function is_valid( int $image_id ): bool {
		$attachment_object = get_post( $image_id );

		if ( ! $attachment_object ) {
			throw new Image_Validation_Error(
				__( 'Can\'t optimize this file. If the issue persists, Contact Support', 'image-optimization' )
			);
		}

		if (
			! wp_attachment_is_image( $attachment_object ) ||
			! in_array( $attachment_object->post_mime_type, Image::get_supported_mime_types(), true )
		) {
			throw new Image_Validation_Error( self::prepare_supported_formats_list_error() );
		}

		if ( ! file_exists( get_attached_file( $image_id ) ) ) {
			throw new Image_Validation_Error(
				esc_html__( 'File is missing. Verify the upload', 'image-optimization' )
			);
		}

		$wp_meta = new WP_Image_Meta( $image_id );

		try {
			$image_size = $wp_meta->get_file_size( Image::SIZE_FULL )
						  ?? File_System::size( ( new Image( $image_id ) )->get_file_path( Image::SIZE_FULL ) );
		} catch ( File_System_Operation_Error $e ) {
			throw new Image_Validation_Error(
				esc_html__( 'File is missing. Verify the upload', 'image-optimization' )
			);
		}

		if ( $image_size > self::MAX_FILE_SIZE ) {
			throw new Image_Validation_Error(
				sprintf(
					__( 'File is too large. Max size is %s', 'image-optimization' ),
					File_Utils::format_file_size( self::MAX_FILE_SIZE, 0 ),
				)
			);
		}

		return true;
	}

	/**
	 * Prepares the error message for the unsupported file formats.
	 *
	 * @return string The error message.
	 */
	private static function prepare_supported_formats_list_error(): string {
		$formats = Image::get_supported_formats();
		$last_item = strtoupper( array_pop( $formats ) );

		$formats_list = join( ', ', array_map( 'strtoupper', $formats ) );

		return sprintf(
			__( 'Wrong file format. Only %1$s, or %2$s are accepted', 'image-optimization' ),
			$formats_list,
			$last_item
		);
	}
}
