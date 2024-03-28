<?php

class SwiftXRViewerAdmin {

    private $db;
    public $product_append_name = 'swiftxr-product-append';
    public $product_append_height = 'swiftxr-product-height';

    function __construct($database){

        $this->db = $database;

        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_script') );

    }
        
    function enqueue_admin_script() {
        wp_enqueue_script( 'swiftxr-admin-js', plugin_dir_url( __FILE__ ) . '/js/swiftxr-viewer-admin.js' );

        wp_enqueue_style( 'swiftxr-admin-css', plugin_dir_url( __FILE__ ) . '/css/swiftxr-viewer-admin.css' );

        wp_localize_script( 'swiftxr-admin-js', 'my_script_vars', array(
            'admin_url' => esc_url( admin_url( 'admin.php?page=swiftxr-app-dashboard' ) ),
        ) );
    }

    public function render_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $shortcodes = $this->db->get_all_shortcode_entries();

        include( plugin_dir_path( __FILE__ ) . 'views/dashboard.php' );

    }

    public function render_tutorial() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        include( plugin_dir_path( __FILE__ ) . 'views/tutorial.php' );

    }

    public function render_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $message = '';
        $message_type = '';
        $height = '400px';

        // Check if form submitted
        if ( isset( $_POST['swiftxr-settings-submit'] ) ) {
            
            $product_append = sanitize_text_field( $_POST['swiftxr-product-append'] );
            $height = sanitize_text_field( $_POST['swiftxr-height'] );

            $height_unit = sanitize_text_field( $_POST['swiftxr-h-unit'] );

            // Update the product placement
            update_option( $this->product_append_name, $product_append );

            // Update the Height based on the settings value
            update_option( $this->product_append_height, $height . $height_unit );

            $message = 'Settings Saved.';
            $message_type = 'success';
            
        }

        $product_placement = get_option( $this->product_append_name);
        $product_height = get_option( $this->product_append_height);

        if($product_height){
            $height = $product_height;
        }

        include( plugin_dir_path( __FILE__ ) . 'views/settings.php' );

    }

    public function render_form() {

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $message = '';
        $message_type = '';
        $shortcode = null;
        $id = null;
        $wc_product = null;

        $query_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : null;

        if($query_id){
            $shortcode = $this->db->get_shortcode_entry_by_id($query_id);

            if($shortcode['id']){
                $id = $shortcode['id'];
            }
        }

        $width = '200px';
        $height = '200px';

        $woo_commerce_products = $this->get_woocommerce_products();

        // Check if form submitted
        if ( isset( $_POST['swiftxr-shortcode-submit'] ) ) {

            $url = sanitize_text_field( $_POST['swiftxr-url'] );
            $width = sanitize_text_field( $_POST['swiftxr-width'] );
            $height = sanitize_text_field( $_POST['swiftxr-height'] );
            $width_unit = sanitize_text_field( $_POST['swiftxr-w-unit'] );
            $height_unit = sanitize_text_field( $_POST['swiftxr-h-unit'] );

            $wc_id = sanitize_text_field( $_POST['swiftxr-woocommerce-product-id'] );

            $wc_product = $this->get_woocommerce_product_by_id($wc_id);

            if ( empty( $url ) ) {

                $message = 'Please enter a URL.';
                $message_type = 'error';
            } 
            else {

                if ( $id ) {
                    // Update existing shortcode

                    $update_state = $this->db->add_update_shortcode_entry($id, $url, $width . $width_unit, $height . $height_unit, $wc_id);

                    if(! $update_state){

                        $message = 'Could not update entry, try again.';
                        $message_type = 'error';
                    }
                    else{

                        $message = 'Entry updated.';
                        $message_type = 'success';

                    }
                } 
                else {
                    // Add new shortcode

                    $add_state = $this->db->add_update_shortcode_entry(null, $url, $width . $width_unit, $height . $height_unit, $wc_id);

                    if(!$add_state){

                        $message = 'Could not create Entry, try again.';
                        $message_type = 'error';
                    }
                    else{

                        $message = 'Entry added.';
                        $message_type = 'success';

                        $id = $add_state;
                    }
                    
                }
            }

        }
        elseif ( isset($_GET['action']) && $_GET['action'] == 'swiftxr-shortcode-delete' ) {

            //Delete Shortcode

            if ( $id ) {

                $del_state = $this->db->delete_shortcode_entry($id);


                if(! $del_state){

                    $message = 'Could not delete Entry.';
                    $message_type = 'error';
                }
                else{

                    $message = 'Entry Deleted.';
                    $message_type = 'success';

                }

            }
        }
        else{

            // If we're updating a shortcode, populate the form with its current values

            if ( $id !== null ) {

                $url = $shortcode['url'];
                $width = $shortcode['width'];
                $height = $shortcode['height'];
                $wc_id = $shortcode['wc_product_id'];

                $wc_product = $this->get_woocommerce_product_by_id($wc_id);

                $width_unit = $this->get_dimension_unit($width);

                $height_unit = $this->get_dimension_unit($height);

            }

        }

        include( plugin_dir_path( __FILE__ ) . 'views/form.php' );

    }

    function get_dimension_unit($string){

        if(strpos($string, 'px') !== false){
            return 'px';
        }

        return '%';
        
    }

    function get_woocommerce_products(){

        if ( !class_exists( 'WC_Product_Query' ) ) {
            return null;
        }
        

        $query = new WC_Product_Query( array(
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'objects',
        ) );

        $products = $query->get_products();

        return $products;

    }

    function get_woocommerce_product_by_id( $product_id ) {

        if ( ! class_exists( 'WC_Product_Query' ) ) {
            return null;
        }

        $product = wc_get_product( $product_id );

        return $product;
    }

    public function get_shortcode_index_by_id( $shortcodes, $shortcode_id ) {
        foreach ( $shortcodes as $index => $shortcode ) {
            if ( $shortcode['id'] == $shortcode_id ) {
                return $index;
            }
        }
        return false; // Shortcode not found
    }
}
