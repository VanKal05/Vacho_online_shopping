<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function arwp_guten_block_cgb_block_assets() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'arwp_guten_block-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'arwp_guten_block-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'arwp_guten_block-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'arwp_guten_block-cgb-block-js',
		'cgbGlobal', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			// Add more data here that you want to access from `cgbGlobal` object.
		]
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'cgb/block-arwp-guten-block', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'arwp_guten_block-cgb-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'arwp_guten_block-cgb-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'arwp_guten_block-cgb-block-editor-css',

	        'render_callback' => 'arwp_gutenberg_block_callback',
			
			'attributes'      => array( 
				'id' => array(
					'type'    => 'number',
					'default' => 0,
				),
			),
		)
	);


	
}

// Create Block Category

function arwp_block_categories( $categories ) {
    $category_slugs = wp_list_pluck( $categories, 'slug' );
    return in_array( 'ar_display', $category_slugs, true ) ? $categories : array_merge(
        $categories,
        array(
            array(
                'slug'  => 'ar_display',
                'title' => __( 'AR Display', 'ar_display' ),
                'icon'  => null,
            ),
        )
    );
}
add_filter( 'block_categories_all', 'arwp_block_categories' );


// Hook: Block assets.
add_action( 'init', 'arwp_guten_block_cgb_block_assets' );


function arwp_gutenberg_block_callback($attributes){
	ob_start();

	$is_backend = defined('REST_REQUEST') && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_SPECIAL_CHARS );

  	if(isset($attributes['id']) && '' != $attributes['id'] && $attributes['id'] > 0){
      $attributes['id'] = $attributes['id'];
      $ar_model_display = ar_display_shortcode($attributes);


  	  if ( $is_backend ) {
	  	  echo '<p><span>[ardisplay id='.$attributes['id'].']';
	  	  echo '<p>'.get_the_post_thumbnail( $attributes['id'], 'thumbnail', array( 'class' => 'alignleft' ) ).'</p>';
	  } else {
	  	echo $ar_model_display;
	  }

  	} else {
  		echo '<p><span>&nbsp;</span></p>';  	
 	}

 	return ob_get_clean();

}

