<?php

namespace ImageOptimization\Modules\Oauth\Components;

use ImageOptimization\Modules\Core\Components\Pointers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Connect_Pointer {
	const CURRENT_POINTER_SLUG = 'image-optimization-auth-connect';

	public function admin_print_script() {
		if ( Connect::is_connected() ) {
			return;
		}

		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );

		$pointer_content = '<h3>' . esc_html__( 'Start by connecting your license', 'image-optimization' ) . '</h3>';
		$pointer_content .= '<p>' . esc_html__( 'You’re one click away from improving your site’s performance dramatically!', 'image-optimization' ) . '</p>';
		?>
		<script>
			jQuery( document ).ready( function( $ ) {
				console.log( $( '.image-optimization-stats-connect-button' ) );

				const intervalId = setInterval( () => {
					if ( ! $( '.image-optimization-stats-connect-button' ).length ) {
						return;
					}

					clearInterval(intervalId);

					$( '.image-optimization-stats-connect-button' ).first().pointer( {
						content: '<?php echo $pointer_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>',
						pointerClass: 'image-optimization-auth-connect-pointer',
						position: {
							edge: 'top',
							align: 'right'
						},
					} ).pointer( 'open' );
				}, 100 );
			} );
		</script>

		<style>
			.image-optimization-auth-connect-pointer .wp-pointer-arrow {
				top: 4px;
				left: 78%;
			}

			.image-optimization-auth-connect-pointer .wp-pointer-arrow-inner {
				top: 10px;
			}
		</style>
		<?php
	}

	public function __construct() {
		add_action( 'in_admin_header', [ $this, 'admin_print_script' ] );
	}
}
