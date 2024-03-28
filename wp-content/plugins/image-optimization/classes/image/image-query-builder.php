<?php

namespace ImageOptimization\Classes\Image;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Query_Builder {
	private array $query;

	public function return_images_only_with_backups(): self {
		$this->query['meta_query'][] = [
			'compare' => 'NOT LIKE',
			'value' => '"backups";a:0', // Serialized empty array of backups
			'key' => Image_Meta::IMAGE_OPTIMIZER_METADATA_KEY,
		];

		return $this;
	}

	public function return_not_optimized_images(): self {
		$this->query['meta_query'][] = [
			'relation' => 'OR',
			[
				'key' => Image_Meta::IMAGE_OPTIMIZER_METADATA_KEY,
				'compare' => 'NOT EXISTS',
			],
			[
				'compare' => 'LIKE',
				'value' => '-failed";',
				'key' => Image_Meta::IMAGE_OPTIMIZER_METADATA_KEY,
			],
			[
				'compare' => 'LIKE',
				'value' => ':"not-optimized";',
				'key' => Image_Meta::IMAGE_OPTIMIZER_METADATA_KEY,
			],
		];

		return $this;
	}

	public function return_optimized_images(): self {
		$this->query['meta_query'][] = [
			'compare' => 'LIKE',
			'value' => '"status";s:9:"optimized"',
			'key' => Image_Meta::IMAGE_OPTIMIZER_METADATA_KEY,
		];

		return $this;
	}

	public function set_paging_size( int $paging_size ): self {
		$this->query['posts_per_page'] = $paging_size;

		return $this;
	}

	public function set_current_page( int $current_page ): self {
		$this->query['paged'] = $current_page;

		return $this;
	}

	public function set_image_ids( array $image_ids ): self {
		$this->query['post__in'] = $image_ids;

		return $this;
	}

	public function execute(): WP_Query {
		return new WP_Query( $this->query );
	}

	public function __construct() {
		$basic_query = [
			'post_type' => 'attachment',
			'post_mime_type' => Image::get_supported_mime_types(),
			'post_status' => 'any',
			'fields' => 'ids',
			'posts_per_page' => -1,
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => '_wp_attachment_metadata', // Images without this field considered invalid
					'compare' => 'EXISTS',
				],
			],
		];

		$this->query = $basic_query;
	}
}
