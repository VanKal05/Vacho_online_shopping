<?php

// Kill Unauthorized Access
if ( !defined( 'ABSPATH' ) ) {
  die;
}


/**
* Plugin Name: SwiftXR (3D/AR/VR) Viewer
* Description: Easily add 3D/AR/VR views to your wesite and products to boost sales, engagement and delight customers
* Version: 1.0.5
* Author: SwiftXR
* Author URI: https://home.swiftxr.io/
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
**/


class SwiftXRViewerPlugin{

  private $admin;
  private $database;

  function __construct(){

    // Load the admin class
    require_once plugin_dir_path( __FILE__ ) . 'admin/swiftxr-viewer-admin.php';

    require_once plugin_dir_path( __FILE__ ) . 'swiftxr-db.php';

    $db_name = 'swiftxr_shortcodes';

    $this->database = new SwiftXRDatabase($db_name);

    $this->admin = new SwiftXRViewerAdmin($this->database);
    
    // Register the admin menu.
    add_action( 'admin_menu', array($this,'add_admin_menu') );

    // Register the shortcode.
    add_shortcode("swiftxr", array($this,'swiftxr_shortcode'));

    //Register WooCommerce Hooks

    //Get Stored Product Placement Position
    $product_placement = get_option( $this->admin->product_append_name);

    if ( ! $product_placement ) {

      $product_placement = 'woocommerce_before_single_product_summary';

      update_option( $this->admin->product_append_name, $product_placement );
    }

    add_action( $product_placement, array($this,'add_swiftxr_embed') );

  }

  function add_swiftxr_embed() {

    global $product;

    $product_id = $product->get_id();

    $swiftxr_entry = $this->database->get_shortcode_entry_by_wc_id($product_id);

    $product_height = get_option( $this->admin->product_append_height);

    if ( ! $product_height ) {

      $product_height = '400px';

      update_option( $this->admin->product_append_height, $product_height );
    }

    if(!$swiftxr_entry){
      echo '';
    }
    else{
      echo $this->swiftxr_viewer_html($swiftxr_entry['id'],$swiftxr_entry['url'],"100%",$product_height);
    }

  }

  function activate(){
    flush_rewrite_rules();
  }

  function deactivate(){
    flush_rewrite_rules();
  }

  function add_admin_menu() {

		add_menu_page(
      'SwiftXR Viewer',
      'SwiftXR Viewer',
      'manage_options',
      'swiftxr-app-dashboard',
      array( $this->admin, 'render_dashboard' ),
      'dashicons-image-filter',
      20
    );

    add_submenu_page(
      'swiftxr-app-dashboard',
      'My Published Links',
      'My Published Links',
      'manage_options',
      'swiftxr-app-dashboard',
      array( $this->admin, 'render_dashboard' )
    );

    add_submenu_page(
      'swiftxr-app-dashboard',
      'SwiftXR App Form',
      'Add New',
      'manage_options',
      'swiftxr-app-form',
      array( $this->admin, 'render_form' )
    );

    add_submenu_page(
      'swiftxr-app-dashboard',
      'SwiftXR Documentation',
      'Guide',
      'manage_options',
      'swiftxr-tutorial',
      array( $this->admin, 'render_tutorial' )
    );

    add_submenu_page(
      'swiftxr-app-dashboard',
      'SwiftXR Settings',
      'Settings',
      'manage_options',
      'swiftxr-settings',
      array( $this->admin, 'render_settings' )
    );
	}
  
  function swiftxr_shortcode( $atts ) {

    // Get the shortcode attributes
    $atts = shortcode_atts( array(
      'id' => '',
    ), $atts );

    $shortcode_data = $this->database->get_shortcode_entry_by_id($atts['id']);

    if ( ! isset($shortcode_data['id']) ) {
      return '';
    }

    // Extract the URL and dimensions from the SwiftXR data
    $swiftxr_url = isset( $shortcode_data['url'] ) ? $shortcode_data['url'] : '';
    $swiftxr_width = isset( $shortcode_data['width'] ) ? $shortcode_data['width'] : '250px';
    $swiftxr_height = isset( $shortcode_data['height'] ) ? $shortcode_data['height'] : '250px';

    return $this->swiftxr_viewer_html($atts['id'],$swiftxr_url,$swiftxr_width,$swiftxr_height);

  }

  function swiftxr_viewer_html($id,$url,$width, $height ){
    return '
      <iframe title="SwiftXR Embed ' . esc_attr( $id ) . '" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="fullscreen; autoplay; vr; camera; midi; encrypted-media" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" src="' . esc_url( $url ) . '"></iframe>
    ';
  }

}

if (class_exists('SwiftXRViewerPlugin')) {
  $swiftxrViewerPlugin = new SwiftXRViewerPlugin();
}

//Activate
register_activation_hook( __FILE__, array($swiftxrViewerPlugin,'activate') );

//Deactivate
register_deactivation_hook( __FILE__, array($swiftxrViewerPlugin,'deactivate') );




