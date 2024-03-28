<?php

namespace ImageOptimization\Classes\Async_Operation\Queries;

use ImageOptimization\Classes\Async_Operation\{
	Async_Operation_Hook,
	Async_Operation_Queue,
	Interfaces\Operation_Query_Interface,
};
use TypeError;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Optimization_Operation_Query implements Operation_Query_Interface {
	private array $query;

	public function set_hook( string $hook ): self {
		if ( ! in_array( $hook, Async_Operation_Hook::get_values(), true ) ) {
			throw new TypeError( "Hook $hook is not a part of Async_Operation_Hook values" );
		}

		$this->query['hook'] = $hook;

		return $this;
	}

	/**
	 * @param string|array $status
	 * @return $this
	 */
	public function set_status( $status ): self {
		$this->query['status'] = $status;

		return $this;
	}

	public function set_image_id( int $image_id ): self {
		if ( empty( $this->query['args'] ) ) {
			$this->query['args'] = [];
		}

		$this->query['args']['attachment_id'] = $image_id;

		return $this;
	}

	public function set_bulk_operation_id( string $operation_id ): self {
		if ( empty( $this->query['args'] ) ) {
			$this->query['args'] = [];
		}

		$this->query['args']['operation_id'] = $operation_id;

		return $this;
	}

	public function set_limit( int $limit ): self {
		$this->query['per_page'] = $limit;

		return $this;
	}

	public function get_return_type(): string {
		return $this->query['_return_type'];
	}

	public function return_ids(): self {
		$this->query['_return_type'] = 'ids';

		return $this;
	}

	public function get_query(): array {
		return $this->query;
	}

	public function __construct() {
		$this->query = [
			'group' => Async_Operation_Queue::OPTIMIZE,
			'per_page' => 10,
			'orderby' => 'date',
			'order' => 'DESC',
			'partial_args_matching' => 'json',
			'_return_type' => 'object',
		];
	}
}
