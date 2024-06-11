<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/******** AR Model Custom Fields (Save 3D Model Files and Images)************/
require_once(plugin_dir_path(__FILE__). 'ar-model-fields.php');


spl_autoload_register(function ($class) {
     $prefix = 'chillerlan\QRCode\\'; 
     $base_dir = plugin_dir_path( __FILE__ ) . '/includes/php-qrcode/src/'; 
     $len = strlen($prefix); 
     if (strncmp($prefix, $class, $len) !== 0) { 
        return; 
     } 
     $relative_class = substr($class, $len); 
     $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php'; 
     
     if (file_exists($file)) { require_once $file; } 
});

spl_autoload_register(function ($class) {
     $prefix = 'chillerlan\Settings\\'; 
     $base_dir = plugin_dir_path( __FILE__ ) . '/includes/php-settings-container/src/'; 
     $len = strlen($prefix); 
     if (strncmp($prefix, $class, $len) !== 0) { 
        return; 
     } 
     $relative_class = substr($class, $len); 
     $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php'; 
   
     if (file_exists($file)) { require_once $file; } 
});

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\LogoOptions;
use chillerlan\QRCode\QRImageWithLogo;

if(extension_loaded('imagick')) {
    
    defined('IMGCK_ENABLED') or DEFINE('IMGCK_ENABLED', 1);

} else {
    defined('IMGCK_ENABLED') or DEFINE('IMGCK_ENABLED', 0);
}

/********** AR Register Settings  **************/
if (!function_exists('ar_register_settings')){
    function ar_register_settings() {
        add_option( 'ar_licence_key', '');
        register_setting( 'ar_display_options_group', 'ar_licence_key' );
        add_option( 'ar_licence_valid', '');
        register_setting( 'ar_display_options_group', 'ar_licence_valid' );
        add_option( 'ar_licence_plan', '');
        register_setting( 'ar_display_options_group', 'ar_licence_plan' );
         add_option( 'ar_licence_renewal', '');
        register_setting( 'ar_display_options_group', 'ar_licence_renewal' );
        add_option( 'ar_dimensions_inches', '');
        register_setting( 'ar_display_options_group', 'ar_dimensions_inches' );
        add_option( 'ar_hide_dimensions', '');
        register_setting( 'ar_display_options_group', 'ar_hide_dimensions' );
        add_option( 'ar_no_posts', '');
        register_setting( 'ar_display_options_group', 'ar_no_posts' );
        add_option( 'ar_wl_file', '');
        register_setting( 'ar_display_options_group', 'ar_wl_file' );
        add_option( 'ar_view_file', '');
        register_setting( 'ar_display_options_group', 'ar_view_file' );
        add_option( 'ar_qr_file', '');
        register_setting( 'ar_display_options_group', 'ar_qr_file' );
        add_option( 'ar_qr_destination', '');
        register_setting( 'ar_display_options_group', 'ar_qr_destination' );
        add_option( 'ar_view_in_ar', '');
        register_setting( 'ar_display_options_group', 'ar_view_in_ar' );
        add_option( 'ar_view_in_3d', '');
        register_setting( 'ar_display_options_group', 'ar_view_in_3d' );
        add_option( 'ar_dimensions_units', '');
        register_setting( 'ar_display_options_group', 'ar_dimensions_units' );
        add_option( 'ar_fullscreen_file', '');
        register_setting( 'ar_display_options_group', 'ar_fullscreen_file' );
        add_option( 'ar_play_file', '');
        register_setting( 'ar_display_options_group', 'ar_play_file' );
        add_option( 'ar_pause_file', '');
        register_setting( 'ar_display_options_group', 'ar_pause_file' );
        add_option( 'ar_hide_qrcode', '');
        register_setting( 'ar_display_options_group', 'ar_hide_qrcode' );
        add_option( 'ar_hide_reset', '');
        register_setting( 'ar_display_options_group', 'ar_hide_reset' );
        add_option( 'ar_hide_fullscreen', '');
        register_setting( 'ar_display_options_group', 'ar_hide_fullscreen' );
        add_option( 'ar_hide_arview', '');
        register_setting( 'ar_display_options_group', 'ar_hide_arview' );
        add_option( 'ar_animation', '');
        register_setting( 'ar_display_options_group', 'ar_animation' );
        add_option( 'ar_animation_selection', '');
        register_setting( 'ar_display_options_group', 'ar_animation_selection' );
        add_option( 'ar_autoplay', '');
        register_setting( 'ar_display_options_group', 'ar_autoplay' );
        add_option( 'ar_emissive', '');
        register_setting( 'ar_display_options_group', 'ar_emissive' );
        add_option( 'ar_light_color', '');
        register_setting( 'ar_display_options_group', 'ar_light_color' );
        add_option( 'ar_disable_zoom', '');
        register_setting( 'ar_display_options_group', 'ar_disable_zoom' );
         add_option( 'ar_rotate_limit', '');
        register_setting( 'ar_display_options_group', 'ar_rotate_limit' );
        add_option( 'ar_scene_viewer', '');
        register_setting( 'ar_display_options_group', 'ar_scene_viewer' );
        add_option( 'ar_css_positions', '');
        register_setting( 'ar_display_options_group', 'ar_css_positions' );
        add_option( 'ar_css', '');
        register_setting( 'ar_display_options_group', 'ar_css' );
        add_option( 'ar_open_tabs', '');
        register_setting( 'ar_display_options_group', 'ar_open_tabs' );
        add_option( 'ar_open_tabs_remember', '');
        register_setting( 'ar_display_options_group', 'ar_open_tabs_remember' );
        
    }
}
add_action( 'admin_init', 'ar_register_settings' );

/******* Element Positions *******/
$ar_css_names = array ('AR Button'=> '.ar-button', 'Dimensions'=>'.dimension', 'Fullscreen Button'=>'.ar_popup-btn', 'QR Code'=>'.ar-qrcode', 'Thumbnail Slides'=>'.ar_slider', 'Play/Pause'=>'.ar-button-animation', 'Reset Button'=>'.ar-reset', 'Call To Action'=>'.ar_cta_button');
$ar_css_styles = array();
$ar_css_styles['Top Left'] = 'top: 6px !important; bottom: auto !important; left: 6px !important; right: auto !important; margin: 0 !important;';
$ar_css_styles['Top Center'] = 'top: 6px !important; bottom: auto !important; margin: 0 auto !important; left: 0 !important; right: 0 !important;';
$ar_css_styles['Top Right'] = 'top: 6px !important; bottom: auto !important; left: auto !important; right: 6px !important; margin: 0 !important;';
$ar_css_styles['Bottom Left'] = 'top: auto !important; bottom: 6px !important; left: 6px !important; right: auto !important; margin: 0 !important;';
$ar_css_styles['Bottom Center'] = 'top: auto !important; bottom: 6px !important; margin: 0 auto !important; left: 0 !important; right: 0 !important;';
$ar_css_styles['Bottom Right'] = 'top: auto !important; bottom: 6px !important; left: auto !important; right: 6px !important; margin: 0 !important;';

        
/******* Activate plugin *******/
register_activation_hook(__FILE__, 'ar_plugin_activation');
if (!function_exists('ar_plugin_activation')){
    function ar_plugin_activation() {
            wp_schedule_event( time(), 'daily', 'ar_cron' );
            ar_cron();
    }
}

/******* Deactivate plugin *******/
register_deactivation_hook(__FILE__, 'ar_plugin_deactivation');
if (!function_exists('ar_plugin_deactivation')){
    function ar_plugin_deactivation() {
        wp_clear_scheduled_hook( 'ar_cron' );
    }
}

if ((!isset($ar_wcfm))){
    $shortcode_examples = '
        <b>[ardisplay id=X]</b> - '.__('Displays the 3D model for a given model/post id.', $ar_plugin_id ).'<br>
        <b>[ardisplay id=\'X,Y,Z\']</b> - '.__('Displays the 3D models for multiple comma seperated model/post ids within 1 viewer and thumbnails to select model.', $ar_plugin_id ).'<br>
        <b>[ardisplay cat=X]</b> - '.__('Displays the 3D models for a given category within 1 viewer and thumbnails to select model.', $ar_plugin_id ).'<br>
        <b>[ardisplay cat=\'X,Y,Z\']</b> - '.__('Displays the 3D models for multiple comma seperated category ids within 1 viewer and thumbnails to select model.', $ar_plugin_id ).'<br>
        <b>[ar-view id=X text=true (OR) buttons=true]</b> - '.__('Display either the AR View button, the text link \'text=true\' "View in AR / View in 3D" or html buttons \'buttons=true\' for a given model/post id without the need for the 3D Model viewer being displayed.', $ar_plugin_id ).'<br>
        <b>[ar-qr]</b> - '.__('QR Code shortcode display for the page or post the shortcode is added to.</p>', $ar_plugin_id );
        
    $ar_rate_this_plugin = '<h3 style="margin-top:0px">'.__('Rate This Plugin', 'ar-for-wordpress' ).'</h3><img src="'.esc_url( plugins_url( "assets/images/5-stars.png", __FILE__ ) ).'" style="height:30px"><br>
    '.__('We really hope you like using AR For WordPress and would be very greatful if you could leave a rating for it on the WordPress Plugin repository.', $ar_plugin_id ).'<br>
    <a href="https://wordpress.org/support/plugin/ar-for-wordpress/reviews/" target="_blank">'.__('Please click here to leave a rating for AR For WordPress.', $ar_plugin_id ).'</a>';
}

/************* Check Licence Cron *******************/
if (!function_exists('ar_cron')){
    function ar_cron() { 
        $licence_result = ar_licence_check();
        if (substr($licence_result,0,5)=='Valid'){
            if (substr($licence_result,6,7)=='Premium'){
                update_option( 'ar_licence_plan', 'Premium');
                update_option( 'ar_licence_renewal', substr($licence_result,-10));
                $licence_result='Valid';
            }else{
                update_option( 'ar_licence_plan', '');
            }
            update_option( 'ar_licence_valid', $licence_result);
        //}elseif($licence_result=='error'){
        //   echo '<div id="upgrade_ribbon" class="notice notice-error is-dismissible"><p>Issue connecting to licence server. Please refresh and try again.</p></div>';
        }else{
            update_option( 'ar_licence_plan', '');
            update_option( 'ar_licence_valid', '');
        }
    }
}


/******* Enqueue Js ***********/
/* Called from Model Viewer */
if (!function_exists('ar_advance_register_script')){
    function ar_advance_register_script() {
        wp_enqueue_script('jquery_validate', plugins_url('assets/js/ar-admin.js', __FILE__), array('jquery'), '1.3', true);
        //wp_enqueue_script('jquery_validate', plugins_url('assets/js/jquery-validate-min.js', __FILE__), array('jquery'), '1.3');
        wp_enqueue_script('ar_copy_to_clipboard', plugins_url('assets/js/ar-display.js', __FILE__), array('jquery'), '1.0', true);

    }
}


/******* Enqueue Css ***********/
if (!function_exists('ar_advance_register_style')){
    function ar_advance_register_style() {
        wp_enqueue_style('ar_styles', plugins_url('assets/css/ar-display.css',__FILE__), false, '1.0.0', 'all');        
    }
}
add_action('wp_enqueue_scripts', 'ar_advance_register_style');
add_action('admin_enqueue_scripts', 'ar_advance_register_style');


/********** AR Licence Check **************/
if (!function_exists('ar_licence_check')){
    function ar_licence_check() {
        global $wpdb;
        $link = 'https://augmentedrealityplugins.com/ar/ar_subscription_licence_check.php';
        ob_start();
        $model_count = ar_model_count();
        $licence_key = get_option('ar_licence_key');
        if ($licence_key!=''){
            $data = array(
                'method'      => 'POST',
                'body'        => array(
                'domain_name' => site_url(),
                'licence_key' => get_option('ar_licence_key'),
                'model_count' => $model_count
            ));
            $response = wp_remote_post( $link, $data);
            if (!is_wp_error($response)){
                return $response['body'];
            }else{
                $curl_check = ar_curl($link.'?licence_key='.get_option('ar_licence_key').'&model_count='.$model_count);
                if ($curl_check){
                    return $curl_check;
                }else{
                    return 'error';
                }
            }
        }else{ //No Licence Key
            return 'error';
        }
        ob_flush();
    }
}






/********** list of 'armodels' posts **************/
if (!function_exists('display_armodels_posts')){

    // Function to display the list of published 'armodels' posts and WooCommerce products with _ar_display meta key
    function display_armodels_posts() {
        global $ar_plugin_id, $ar_wc_active, $ar_wp_active;
        
        if ($ar_wp_active==true){
            // Query to get published posts of type 'armodels', sorted by ID ascending
            $args_armodels = array(
                'post_type'   => 'armodels',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
            );
            $query_armodels = new WP_Query($args_armodels);
        }
        if ($ar_wc_active==true){
            // Query to get WooCommerce products with meta key _ar_display
            $args_products = array(
                'post_type'   => 'product',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key'     => '_ar_display',
                        'compare' => 'EXISTS',
                    ),
                ),
            );
            $query_products = new WP_Query($args_products);
        }
        // Display the list of posts and products
        echo '<ul>';
        if ($ar_wp_active==true){
            // Display 'armodels' posts
            if ($query_armodels->have_posts()) {
                while ($query_armodels->have_posts()) {
                    $query_armodels->the_post();
                    $post_id = get_the_ID();
                    $post_title = get_the_title();
        
                    // Display post ID and title with a delete link
                    echo '<li><b>Model:</b> <a href="post.php?post=' . $post_id . '&action=edit">' . $post_id . ' - ' . $post_title . '</a> <a href="' . esc_url(add_query_arg('delete_post_id', $post_id)) . '" onclick="return confirm(\'Are you sure you want to delete this AR Model?\');"><img src="'.esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) ).'" style="width: 15px;vertical-align: middle;cursor:pointer"></a></li>';
                }
            }
        }
        if ($ar_wc_active==true){
            // Display WooCommerce products
            if ($query_products->have_posts()) {
                while ($query_products->have_posts()) {
                    $query_products->the_post();
                    $post_id = get_the_ID();
                    $post_title = get_the_title();
        
                    // Display product ID and title with a delete link
                    echo '<li><b>Product:</b> <a href="post.php?post=' . $post_id . '&action=edit">' . $post_id . ' - ' . $post_title . '</a> <a href="' . esc_url(add_query_arg('delete_post_id', $post_id)) . '" onclick="return confirm(\'Are you sure you want to delete AR Model from this Product?\');"><img src="'.esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) ).'" style="width: 15px;vertical-align: middle;cursor:pointer"></a></li>';
                }
            }
        }
        echo '</ul>';
    
        // Restore original Post Data
        wp_reset_postdata();
    }
}






/*********** Display the AR Model Viewer ***********/
if (!function_exists('ar_display_model_viewer')){
    function ar_display_model_viewer($model_array, $atts_id=''){
        add_action('wp_enqueue_scripts', 'ar_advance_register_script');
        global $model_viewer_js_loaded, $ar_scale_js;
        $output='';
        //if (($model_array['glb_file']!='')OR($model_array['usdz_file']!='')){
            global $wp, $ar_plugin_id, $ar_whitelabel, $ar_css_names, $ar_css_styles;
            $model_style='';
            $model_id =  $model_array['model_id'];
            if ($model_array['skybox_file']!=''){
                $model_array['skybox_file']=' skybox-image="'.$model_array['skybox_file'].'"';
            }
            if ($model_array['ar_pop']=='pop'){
                $model_array['model_id'].='_'.$model_array['ar_pop'];
            }
            if ($model_array['ar_resizing']==1){
                $model_array['ar_resizing']=' ar-scale="fixed"';
            }
            if ($model_array['ar_scene_viewer']==1){
                $viewers = 'scene-viewer webxr quick-look';
            }else{
                $viewers = 'webxr scene-viewer quick-look';
            }
            if ($model_array['ar_hide_arview']!=''){
               $model_array['ar_hide_arview'] = ' nodisplay';
               $show_ar='';
            }else{
                $show_ar=' ar ar-modes="'.$viewers.'" ';
            }
            if ($model_array['ar_hide_model']!=''){
               $model_array['ar_hide_model'] = ' nodisplay';
               $model_array['ar_hide_arview'] = '';
               $show_ar=' ar ar-modes="'.$viewers.'" ';
            }
            if ($model_array['ar_autoplay']!=''){
                $model_array['ar_autoplay'] = 'autoplay';                
            }
            if ($model_array['ar_disable_zoom']!=''){
                $model_array['ar_disable_zoom'] = 'disable-zoom';                
            }
            if ($model_array['ar_field_of_view']!=''){
                $model_array['ar_field_of_view'] = 'field-of-view="'.$model_array['ar_field_of_view'].'deg"';                
            }else{
                $model_array['ar_field_of_view'] = 'field-of-view=""';
            }
            if (!isset($model_array['ar_qr_image'])){
                $model_array['ar_qr_image']='';
            }
            $min_theta = 'auto';
            $min_pi = 'auto';
            $min_zoom = '20%';
            $max_theta = 'Infinity';
            $max_pi = 'auto';
            $max_zoom = '300';
            if (($model_array['ar_zoom_in']!='')AND($model_array['ar_zoom_in']!='default')){
                $model_array['ar_zoom_in'] = 100 - $model_array['ar_zoom_in'];
                //$ar_zoom_in_output = 'min-camera-orbit="auto auto '.$model_array['ar_zoom_in'].'%"';  
                $min_zoom = $model_array['ar_zoom_in'].'%"';
            }else{
                //$ar_zoom_out_output = 'min-camera-orbit="Infinity auto 20%"';
            }
            
            if (($model_array['ar_zoom_out']!='')AND($model_array['ar_zoom_out']!='default')){
                $model_array['ar_zoom_out'] = (($model_array['ar_zoom_out']/100)*400)+100;
                $ar_zoom_out_output = 'max-camera-orbit="Infinity auto '.$model_array['ar_zoom_out'].'%"'; 
                $max_zoom = $model_array['ar_zoom_out'].'%"';
            }else{
                //$ar_zoom_in_output = 'max-camera-orbit="Infinity auto 300%"';
            }
            
            //set the X and Y rotation limits in min-camera-orbit and max-camera-orbit
            //
            //
            //
            if ($model_array['ar_rotate_limit']!=''){
                if ($model_array['ar_compass_top_value']!=''){
                    $min_pi = $model_array['ar_compass_top_value'];
                } 
                if ($model_array['ar_compass_bottom_value']!=''){
                    $max_pi = $model_array['ar_compass_bottom_value'];
                }
                if ($model_array['ar_compass_left_value']!=''){
                    $min_theta = $model_array['ar_compass_left_value'];
                }
                if ($model_array['ar_compass_right_value']!=''){
                    $max_theta = $model_array['ar_compass_right_value'];
                } 
            }
            $ar_zoom_out_output = 'min-camera-orbit="'.$min_theta.' '.$min_pi.' '.$min_zoom.'"';
            $ar_zoom_in_output = 'max-camera-orbit="'.$max_theta.' '.$max_pi.' '.$max_zoom.'"';
            
            
            if ($model_array['ar_exposure']!=''){
                $model_array['ar_exposure'] = 'exposure="'.$model_array['ar_exposure'].'"';                
            }
            if ($model_array['ar_shadow_intensity']!=''){
                $model_array['ar_shadow_intensity'] = 'shadow-intensity="'.$model_array['ar_shadow_intensity'].'"';                
            }
            if ($model_array['ar_shadow_softness']!=''){
                $model_array['ar_shadow_softness'] = 'shadow-softness="'.$model_array['ar_shadow_softness'].'"';                
            }
            if ($model_array['ar_camera_orbit']!=''){
                $model_array['ar_camera_orbit_reset'] = $model_array['ar_camera_orbit'];
                $model_array['ar_camera_orbit'] = 'camera-orbit="'.$model_array['ar_camera_orbit'].'"';                
            }else{
                $model_array['ar_camera_orbit_reset']='';
            }
            if ($model_array['ar_environment_image']!=''){
                $model_array['ar_environment_image'] = 'environment-image="legacy"';                
            }
            if ($model_array['ar_emissive']!=''){
                $model_array['ar_emissive'] = ' emissive ';                
            }
            if ($model_array['ar_light_color']!=''){
                $model_array['ar_light_color'] = 'light-color="'.$model_array['ar_light_color'].'"';               
            }
            
            //If on the admin page
            global $pagenow;
            $hotspot_js_click ='';
            if (( $pagenow == 'post.php' ) ) {
                // editing a page or product
                $hotspot_js_click = 'onclick="addHotspot()"';
            }
            
            $output='
            <div id="ardisplay_viewer_'.$model_array['model_id'].'" class="ardisplay_viewer'.$model_array['ar_pop'].$model_array['ar_hide_model'].'">';
            if (($model_viewer_js_loaded != 1)OR(is_admin())){
                $output.='<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>';
                
                $model_viewer_js_loaded ==1;
            }
            $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
            $isTab = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet")); 
            //If viewing on Android with Firefox then display message suggesting another browser
            $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android"));
            $isFireFox = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "firefox"));
            $isFireFoxiOS = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "fxios"));
            $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone")); 
            $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad")); 
            $isIOS = $isIPhone || $isIPad; 
            if (($isFireFox) || ($isFireFoxiOS)){
                if ($isAndroid){
                    $output .= '<b>For an optimal Augmented Reality experience it is suggested you use a different browser.</b> <a href="intent://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#Intent;scheme=https;package=com.android.chrome;end">Please click here to open in Chrome.</a>';
                }elseif($isIOS){
                    $output .= '<b>For an optimal Augmented Reality experience it is suggested you use a different browser such as Safari or <a href="googlechrome://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">Chrome.</a></b>';
                    
                }
            }  

            //If Alternative AR view model ID exists then display alternative model in



            $output.='<model-viewer '.$hotspot_js_click.' id="model_'.$model_array['model_id'].'" '.$show_ar.'  camera-controls';
                if (($model_array['ar_prompt']==true)){
                   $output .= ' interaction-prompt="none"  ';
                }
                if($model_array['ar_rotate']!=true){
                   $output .= ' auto-rotate ';
                }
                
                $output .= $model_array['ar_placement'].' 
                ios-src="'.$model_array['usdz_file'].'" 
                src="'. $model_array['glb_file'].'" 
                '. $model_array['skybox_file'].'
                '. $model_array['ar_environment'].'
                '. $model_array['ar_qr_image'].'
                '. $model_array['ar_qr_destination'].'
                '. $model_array['ar_resizing'].'
                '. $model_array['ar_field_of_view'].'
                '. $ar_zoom_in_output.'
                '. $ar_zoom_out_output.'
                '. $model_array['ar_camera_orbit'].'
                '. $model_array['ar_exposure'].'
                '. $model_array['ar_shadow_intensity'].'
                '. $model_array['ar_shadow_softness'].'
                '. $model_array['ar_environment_image'].' 
                '. $model_array['ar_emissive'].' 
                '. $model_array['ar_light_color'].' 
                poster="'.esc_url( get_the_post_thumbnail_url($model_array['model_id']) ).'"
                alt="AR Display 3D model" 
                class="ar-display-model-viewer" 
                quick-look-browsers="safari chrome" 
                '.$model_array['ar_autoplay'];
                if ($model_array['ar_animation'] !=''){
                    $output .=' animation-name="'.$model_array['ar_animation_selection'].'" ';
                }
                $output .= $model_array['ar_disable_zoom'].'
                '.$model_style.'>';
                
                if ((isset($model_array['ar_play_file']))AND($model_array['ar_play_file']!='')){
                    $play_btn= esc_url( $model_array['ar_play_file'] );
                }else{
                    $play_btn= esc_url( plugins_url( "assets/images/ar-play-btn.png", __FILE__ ) );  
                }
                if ((isset($model_array['ar_pause_file']))AND($model_array['ar_pause_file']!='')){
                    $pause_btn= esc_url( $model_array['ar_pause_file'] );
                }else{
                    $pause_btn= esc_url( plugins_url( "assets/images/ar-pause-btn.png", __FILE__ ) );  
                }
                if ($model_array['ar_animation']==true){
                    $play_hide='';
                }else{
                    $play_hide = 'display:none;';
                }
                $output .= '<div class="ar-animation-btn-container"><button id="animationButton_'.$model_array['ar_pop'].'" slot="hotspot-one" data-position="..." data-normal="..." class="ar-button-animation" type="button"><img src="'.$play_btn.'" class="ar-button-animation" id="ar-button-animation_'.$model_array['ar_pop'].'" style="'.$play_hide.'"></button></div>';
                
                if ($model_array['ar_view_file']==''){
                    if ($ar_whitelabel!=true){
                        if($model_array['ar_alternative_id'] && ( ($isMob) OR ($isTab) OR ($isIPhone) OR ($isIPad) OR ($isAndroid) )){

                            $output.='<button data-id="'.$model_array['model_id'].'" data-alt="'.$model_array['ar_alternative_id'].'" class="ar-button ar-button-default '.$model_array['ar_hide_arview'].'" id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( plugins_url( "assets/images/ar-view-btn.png", __FILE__ ) ).'" class="ar-button-img"></button>';

                            $output.='<button slot="ar-button" style="display:none;"" data-id="'.$model_array['model_id'].'" data-alt="'.$model_array['ar_alternative_id'].'" class="ar-button ar-button-default '.$model_array['ar_hide_arview'].'" id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( plugins_url( "assets/images/ar-view-btn.png", __FILE__ ) ).'" class="ar-button-img"></button>';

                        } else {

                            $output.='<button slot="ar-button" data-id="'.$model_array['model_id'].'" class="ar-button ar-button-default '.$model_array['ar_hide_arview'].'" id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( plugins_url( "assets/images/ar-view-btn.png", __FILE__ ) ).'" class="ar-button-img"></button>';

                        }
                    }
                }else{
                    if($model_array['ar_alternative_id'] && ( ($isMob) OR ($isTab) OR ($isIPhone) OR ($isIPad) OR ($isAndroid) )){

                        $output.='<button data-id="'.$model_array['model_id'].'" data-alt="'.$model_array['ar_alternative_id'].'" class="ar-button '.$model_array['ar_hide_arview'].'" id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( $model_array['ar_view_file'] ).'" class="ar-button-img"></button>';

                        $output.='<button slot="ar-button" style="display:none;" data-id="'.$model_array['model_id'].'" class="ar-button '.$model_array['ar_hide_arview'].'" id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( $model_array['ar_view_file'] ).'" class="ar-button-img"></button>';

                    } else {
                        $output.='<button slot="ar-button" data-id="'.$model_array['model_id'].'" class="ar-button '.$model_array['ar_hide_arview'].'" id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( $model_array['ar_view_file'] ).'" class="ar-button-img"></button>';
                    }
                }

                         
                //CTA Button
                if (($model_array['ar_cta']!='')AND($model_array['ar_cta_url']!='')){
                    $output.='<div class="ar-cta-button-container">
                        <center><a href="'.$model_array['ar_cta_url'].'"><button slot="ar-cta-button" class="ar_cta_button button" id="ar-cta-button">'.$model_array['ar_cta'].'</button></a></center>
                    </div>';
                }
                //Hotspots
                if ($model_array['ar_hotspots']!=''){
                    foreach ($model_array['ar_hotspots']['annotation'] as $k => $v){
                        if(isset($model_array['ar_hotspots']['link'])){
                            if ($model_array['ar_hotspots']['link'][$k] !=''){
                                $v = '<a href="'.$model_array['ar_hotspots']['link'][$k].'" target="_blank">'.$v.'</a>';
                            }
                        }
                        $output.='<button slot="hotspot-'.($k-1).'" class="hotspot" id="hotspot-'.$k.'" data-position="'.$model_array['ar_hotspots']['data-position'][$k].'" data-normal="'.$model_array['ar_hotspots']['data-normal'][$k].'"><div class="annotation">'.$v.'</div></button>';
                    }
                }
                if ($model_array['ar_hide_qrcode']==''){
                    $ar_qr_display = 'block';
                }else{
                    $ar_qr_display = 'none';
                }
                $qr_logo_image=esc_url( plugins_url( "assets/images/app_logo.png", __FILE__ ) );
                $ar_wl_logo = get_option('ar_wl_file'); 
                if ($model_array['ar_qr_file']!=''){
                    $qr_logo_image=$model_array['ar_qr_file'];
                }elseif ($ar_wl_logo){ //Show Whitelabel url in QR
                    $qr_logo_image=$ar_wl_logo;
                }

                $ar_qr_url = home_url( $wp->request );

                if ($model_array['ar_qr_destination'] == 'model-viewer'){
                    if (isset($model_array['ar_model_atts']['cat'])){
                        $ar_qr_url = get_site_url().'?ar-cat='.$model_array['ar_model_atts']['cat'];
                    }elseif (isset($model_array['ar_model_atts']['id'])){
                        //If Alternative mobile ID exists then display mobile model if viewing on mobile or tablet
                        if ($mob_id = get_post_meta($model_array['ar_model_atts']['id'], '_ar_mobile_id', true )){
                            $model_array['ar_model_atts']['id'] = $mob_id;
                        }
                        $ar_qr_url = get_site_url().'?ar-view='.$model_array['ar_model_atts']['id'];
                    }
                } else if($model_array['ar_qr_destination'] == 'parent-page'){
                    $ar_attid = isset($model_array['ar_model_atts']['cat']) ? $model_array['ar_model_atts']['cat'] : $model_array['ar_model_atts']['id'];
                    $ar_qr_url = esc_url( get_permalink($ar_attid) );
                } else {
                    $ar_qr_url = $model_array['ar_qr_destination'];
                }
                

                //Custom QR Image or generated QR Code
                if ($model_array['ar_qr_image']!=''){
                    $ar_qr_image_data = $model_array['ar_qr_image'];
                }else{                    
                    $ar_qr_image_data = base64_encode(ar_qr_code($qr_logo_image,$model_array['ar_model_atts']['id'],$ar_qr_url));
                    $ar_qr_image_data = 'data:image/png;base64,'.$ar_qr_image_data;
                }
                if ($ar_qr_image_data!=''){
                    $output.='<div class="ar-qrcode-btn-container hide_on_devices">
                    <button id="ar-qrcode_'.$model_array['model_id'].'" type="button" class="ar-qrcode hide_on_devices" onclick="this.classList.toggle(\'ar-qrcode-large\');" style="display: '.$ar_qr_display.'; background-image: url('.$ar_qr_image_data.');"></button>
                    </div>';
                }    
                
                //Reset View Button
                if ($model_array['ar_hide_reset']==1){ $reset_style = 'style="display:none"';}else{$reset_style='';}
                $output.='<div class="ar-reset-btn-container">
                <button id="ar-reset_'.$model_array['model_id'].'" class="ar-reset" '.$reset_style.' onclick="document.getElementById(\'model_'.$model_array['model_id'].'\').setAttribute(\'camera-orbit\', \''.$model_array['ar_camera_orbit_reset'].'\');return getData()"><img src="'.esc_url( plugins_url( "assets/images/reset.png", __FILE__ ) ).'"></button>
                </div>';
                   
                //If the popup is triggered by the ar-view shortcode then show close button   
                $ar_show_close_on_devices = '';
                if (!isset($model_array['ar_show_close_on_devices'])){
                    $ar_show_close_on_devices = 'hide_on_devices';
                }

                $output.='<div class="ar-popup-btn-container '.$ar_show_close_on_devices.'">';
                
                //Fullscreen option - if not disabled in settings
                $ar_hide_fullscreen='';
                if ((!isset($model_array['ar_hide_fullscreen']))OR($model_array['ar_hide_fullscreen']=='')){
                    if ($model_array['ar_pop']=='pop'){
                        if($atts_id)
                            $mdl_id = $atts_id;
                        else
                            $mdl_id = $model_array['model_id'];
                        
                        $output.='<button id="ar_close_'.$model_array['model_id'].'" class="ar_popup-btn '.$ar_show_close_on_devices.'" onclick="document.getElementById(\'ar_popup_'.$mdl_id.'\').style.display = \'none\';"><img src="'.esc_url( plugins_url( "assets/images/close.png", __FILE__ ) ).'" class="ar-fullscreen_btn-img"></button>';
                    }else{
                        if ($model_array['ar_fullscreen_file']!=''){
                            $ar_fullscreen_image = $model_array['ar_fullscreen_file'];
                        }else{
                            $ar_fullscreen_image = esc_url( plugins_url( "assets/images/fullscreen.png", __FILE__ ) );
                        }
                        
                        $output.='<button id="ar_pop_Btn_'.$model_array['model_id'].'" class="ar_popup-btn hide_on_devices" type="button"><img src="'.$ar_fullscreen_image.'" class="ar-fullscreen_btn-img"></button>';
                    }
                }
                $output.='</div>';
                    
                if ($model_array['ar_variants']!=''){
                    $output.='<div class="ar_controls"><select id="variant_'.$model_array['model_id'].'"></select></div> ';
                }
                
                /**** Thumbnail Slider ****/
                
                if ($model_array['ar_model_list']!=''){ 
                    $model_array['ar_model_list']=array_filter($model_array['ar_model_list']);
                    if (count($model_array['ar_model_list'])>1){ 
                        $output.='<div id="ar_slider" class="ar_slider">
                            <div class="ar_slides">';
                        $slide_count = 0;
                        foreach ($model_array['ar_model_list'] as $k =>$v){
                            $slide_count++;
                            $slide_selected = '';
                            if ($slide_count=='1'){$slide_selected = 'selected';}
                            $output.='<button class="ar_slide '.$slide_selected.'" onclick="switchSrc(\'model_'.$model_array['model_id'].'\', model_'.$model_array['model_id'].', \''.get_post_meta($v, '_glb_file', true ).'\', \''.get_post_meta($v, '_usdz_file', true ).'\')" style="background-image: url(\''.esc_url( get_the_post_thumbnail_url($v) ).'\');"></button>
                            ';
                        }
                        $output.='</div>
                        </div>';
                    }
                }

                
                //wp_die(get_post_meta($model_id, '_ar_alternative_id', true ));

                $output.='<input type="hidden" id="src_'.$model_array['model_id'].'" value="'. $model_array['glb_file'].'">';
                
                /**** Hide Dimensions ****/
                if ($model_array['ar_hide_dimensions']==''){
                    $ar_dimensions_display = 'block';
                }else{
                    $ar_dimensions_display = 'none';
                }
                
                    $ar_dimensions_label = __('Dimensions', $ar_plugin_id );
                   
                    $output.='
                    <button slot="hotspot-dot+X-Y+Z" class="dot nodisplay" data-position="1 -1 1" data-normal="1 0 0"></button>
                    <button slot="hotspot-dim+X-Y" class="dimension nodisplay" data-position="1 -1 0" data-normal="1 0 0"></button>
                    <button slot="hotspot-dot+X-Y-Z" class="dot nodisplay" data-position="1 -1 -1" data-normal="1 0 0"></button>
                    <button slot="hotspot-dim+X-Z" class="dimension nodisplay" data-position="1 0 -1" data-normal="1 0 0"></button>
                    <button slot="hotspot-dot+X+Y-Z" class="dot nodisplay" data-position="1 1 -1" data-normal="0 1 0"></button>
                    <button slot="hotspot-dim+Y-Z" class="dimension nodisplay" data-position="0 -1 -1" data-normal="0 1 0"></button>
                    <button slot="hotspot-dot-X+Y-Z" class="dot nodisplay" data-position="-1 1 -1" data-normal="0 1 0"></button>
                    <button slot="hotspot-dim-X-Z" class="dimension nodisplay" data-position="-1 0 -1" data-normal="-1 0 0"></button>
                    <button slot="hotspot-dot-X-Y-Z" class="dot nodisplay" data-position="-1 -1 -1" data-normal="-1 0 0"></button>
                    <button slot="hotspot-dim-X-Y" class="dimension nodisplay" data-position="-1 -1 0" data-normal="-1 0 0"></button>
                    <button slot="hotspot-dot-X-Y+Z" class="dot nodisplay" data-position="-1 -1 1" data-normal="-1 0 0"></button>
                    <div id="controls" class="dimension" style="display:'.$ar_dimensions_display.'">
                        <label for="show-dimensions_'.$model_array['model_id'].'" style="margin:0px !important;">'.$ar_dimensions_label.':</label>
                        <input id="show-dimensions_'.$model_array['model_id'].'" type="checkbox" style="cursor: pointer;">
                    </div>';
            $output.='
                </model-viewer>';
            /* Custom CSS Styling */
            if ($model_array['ar_css_positions']!=''){
                if (is_array($model_array['ar_css_positions'])){
                    $ar_no_move_ar_button = '';
                    $ar_no_move_ar_dimensions = '';
                    $ar_no_move_ar_reset = '';
                    $output .= '
                    <style>/* Custom CSS Styling */';
                        foreach($model_array['ar_css_positions'] as $element => $pos){
                            if (($pos != 'Default')AND($element != '')AND($pos != '')){
                                $output .= $ar_css_names[$element].'{'.$ar_css_styles[$pos].'}';
                                if ($element =='AR Button'){$ar_no_move_ar_button=1;}
                                if ($element =='Dimensions'){$ar_no_move_ar_dimensions=1;}
                                if ($element =='Reset Button'){$ar_no_move_ar_reset=1;}
                            }
                        }
                    $output .= '
                    </style>
                    ';  
                }
                if ((isset($model_array['ar_show_close_on_devices']))AND($ar_no_move_ar_button!='1')){
                    $output .='<style> #ar-button_'.$model_array['model_id'].'_pop{top:40px !important;}</style>';
                }
                if (($ar_no_move_ar_reset!='1')AND($ar_no_move_ar_dimensions!='1')AND($model_array['ar_hide_reset']!='1')){
                    $output .='<style> .dimension{left:50px !important;}</style>';
                }
            }
            if (($model_array['ar_css']!='')AND($model_array['ar_pop']!='pop')){
                $output .= '
                <style>
                    '.$model_array['ar_css'].'
                </style>
                ';               
            }
            /* Javascripts */
                
            if ($model_array['ar_variants']!=''){
                $output.='
                <script>
                    const modelViewerVariants_'.$model_array['model_id'].' = document.querySelector("model-viewer#model_'.$model_array['model_id'].'");
                    const select_'.$model_array['model_id'].' = document.querySelector(\'#variant_'.$model_array['model_id'].'\');
                    
                    modelViewerVariants_'.$model_array['model_id'].'.addEventListener(\'load\', () => {
                      const names_'.$model_array['model_id'].' = modelViewerVariants_'.$model_array['model_id'].'.availableVariants;
                      for (const name of names_'.$model_array['model_id'].') {
                        const option_'.$model_array['model_id'].' = document.createElement(\'option\');
                        option_'.$model_array['model_id'].'.value = name;
                        option_'.$model_array['model_id'].'.textContent = name;
                        select_'.$model_array['model_id'].'.appendChild(option_'.$model_array['model_id'].');
                      }
                    });
                    
                    select_'.$model_array['model_id'].'.addEventListener(\'input\', (event) => {
                      modelViewerVariants_'.$model_array['model_id'].'.variantName = event.target.value;
                    });
                    </script>';
            }
            if ((is_numeric($model_array['ar_x']))AND(is_numeric($model_array['ar_y']))AND(is_numeric($model_array['ar_z']))AND($model_array['ar_pop']!='pop')){
                $output.='<script>
                const modelViewerTransform'.$model_array['model_id'].' = document.querySelector("model-viewer#model_'.$model_array['model_id'].'");
                const updateScale'.$model_array['model_id'].' = () => {
                  modelViewerTransform'.$model_array['model_id'].'.scale = \''.$model_array['ar_x'].' '.$model_array['ar_y'].' '.$model_array['ar_z'].'\';
                };
                updateScale'.$model_array['model_id'].'();
                </script>';
                $ar_scale_js = 1;
            }
            
        
            /*Thumbnail slider*/
            if ($model_array['ar_model_list']!=''){ 
                if (count($model_array['ar_model_list'])>1){ 
                $output.='<script>
                //const modelViewerList = document.querySelector("model-viewer");
                  window.switchSrc = (modelid, element, name, usdz) => {
                    var modelViewerList = document.querySelector("#"+modelid);
                    modelViewerList.src = name;
                    modelViewerList.poster = name;
                    modelViewerList.iosSrc = usdz;
                    const slides = document.querySelectorAll(".ar_slide");
                    slides.forEach((element) => {element.classList.remove("selected");});
                    element.classList.add("selected");
                  };
                
                  document.querySelector(".ar_slider").addEventListener(\'beforexrselect\', (ev) => {
                    // Keep slider interactions from affecting the XR scene.
                    ev.preventDefault();
                  });
                  </script>';
                }
            }
            
            //Dimensions   
            $output.=' <script type="module">
              const modelViewer = document.querySelector(\'#model_'.$model_array['model_id'].'\');
              ';
            /*if ($model_array['ar_rotate']==true){
                $output.='modelViewer.cameraControls = false;
                ';
            }*/
            $output.=' modelViewer.querySelector(\'#src_'.$model_array['model_id'].'\').addEventListener(\'input\', (event) => {
                modelViewer.src = event.target.value;
              });
              const checkbox = modelViewer.querySelector(\'#show-dimensions_'.$model_array['model_id'].'\');
              checkbox.addEventListener(\'change\', () => {
                modelViewer.querySelectorAll(\'button\').forEach((hotspot) => {
                  if ((hotspot.classList.contains(\'dimension\'))||(hotspot.classList.contains(\'dot\'))){
                      if (checkbox.checked) {
                        hotspot.classList.remove(\'nodisplay\');
                      } else {
                        hotspot.classList.add(\'nodisplay\');
                      }
                  }';
                  if ((!isset($model_array['ar_hide_fullscreen']))OR($model_array['ar_hide_fullscreen']=='')){
                    $output .= '
                    if (document.getElementById("ar_pop_Btn_'.$model_id.'").classList.contains(\'nodisplay\')){
                        document.getElementById("ar_pop_Btn_'.$model_id.'").classList.remove(\'nodisplay\');
                        document.getElementById("ar_close_'.$model_id.'_pop").classList.remove(\'nodisplay\');
                    }';
                    
                  }
                  $output .= 'document.getElementById("ar-button_'.$model_array['model_id'].'").classList.remove(\'nodisplay\');
                  document.getElementById("ar-qrcode_'.$model_array['model_id'].'").classList.remove(\'nodisplay\');
                });
              });
            
              modelViewer.addEventListener(\'load\', () => {
                const center = modelViewer.getCameraTarget();
                const size = modelViewer.getDimensions();
                const x2 = size.x / 2;
                const y2 = size.y / 2;
                const z2 = size.z / 2;
            
                modelViewer.updateHotspot({
                  name: \'hotspot-dot+X-Y+Z\',
                  position: `${center.x + x2} ${center.y - y2} ${center.z + z2}`
                });
            
                modelViewer.updateHotspot({
                  name: \'hotspot-dim+X-Y\',
                  position: `${center.x + x2} ${center.y - y2} ${center.z}`
                });
                modelViewer.querySelector(\'button[slot="hotspot-dim+X-Y"]\').textContent = ';
                if (($model_array['ar_dimensions_units'] == 'inches')OR($model_array['ar_dimensions_inches'] == true)){
                    $output .= '`${(size.z * 39.370).toFixed(2)} in`;';
                }elseif ($model_array['ar_dimensions_units'] == 'cm'){
                    $output .= '`${(size.z * 100).toFixed(0)} cm`;';
                }elseif ($model_array['ar_dimensions_units'] == 'mm'){
                    $output .= '`${(size.z * 1000).toFixed(0)} mm`;';
                }else{
                    $output .= '`${(size.z).toFixed(2)} m`;';
                }
                
                $output .= '
                modelViewer.updateHotspot({
                  name: \'hotspot-dot+X-Y-Z\',
                  position: `${center.x + x2} ${center.y - y2} ${center.z - z2}`
                });
            
                modelViewer.updateHotspot({
                  name: \'hotspot-dim+X-Z\',
                  position: `${center.x + x2} ${center.y} ${center.z - z2}`
                });
                modelViewer.querySelector(\'button[slot="hotspot-dim+X-Z"]\').textContent = ';
                if (($model_array['ar_dimensions_units'] == 'inches')OR($model_array['ar_dimensions_inches'] == true)){
                    $output .= '`${(size.y * 39.370).toFixed(2)} in`;';
                }elseif ($model_array['ar_dimensions_units'] == 'cm'){
                    $output .= '`${(size.y * 100).toFixed(0)} cm`;';
                }elseif ($model_array['ar_dimensions_units'] == 'mm'){
                    $output .= '`${(size.y * 1000).toFixed(0)} mm`;';
                }else{
                    $output .= '`${(size.y).toFixed(2)} m`;';
                }
                
                $output .= '
                modelViewer.updateHotspot({
                  name: \'hotspot-dot+X+Y-Z\',
                  position: `${center.x + x2} ${center.y + y2} ${center.z - z2}`
                });
            
                modelViewer.updateHotspot({
                  name: \'hotspot-dim+Y-Z\',
                  position: `${center.x} ${center.y + y2} ${center.z - z2}`
                });
                modelViewer.querySelector(\'button[slot="hotspot-dim+Y-Z"]\').textContent = ';
                if (($model_array['ar_dimensions_units'] == 'inches')OR($model_array['ar_dimensions_inches'] == true)){
                    $output .= '`${(size.x * 39.370).toFixed(2)} in`;';
                }elseif ($model_array['ar_dimensions_units'] == 'cm'){
                    $output .= '`${(size.x * 100).toFixed(0)} cm`;';
                }elseif ($model_array['ar_dimensions_units'] == 'mm'){
                    $output .= '`${(size.x * 1000).toFixed(0)} mm`;';
                }else{
                    $output .= '`${(size.x).toFixed(2)} m`;';
                }
                
                $output .= '
                modelViewer.updateHotspot({
                  name: \'hotspot-dot-X+Y-Z\',
                  position: `${center.x - x2} ${center.y + y2} ${center.z - z2}`
                });
            
                modelViewer.updateHotspot({
                  name: \'hotspot-dim-X-Z\',
                  position: `${center.x - x2} ${center.y} ${center.z - z2}`
                });
                modelViewer.querySelector(\'button[slot="hotspot-dim-X-Z"]\').textContent  = ';
                if (($model_array['ar_dimensions_units'] == 'inches')OR($model_array['ar_dimensions_inches'] == true)){
                    $output .= '`${(size.y * 39.370).toFixed(2)} in`;';
                }elseif ($model_array['ar_dimensions_units'] == 'cm'){
                    $output .= '`${(size.y * 100).toFixed(0)} cm`;';
                }elseif ($model_array['ar_dimensions_units'] == 'mm'){
                    $output .= '`${(size.y * 1000).toFixed(0)} mm`;';
                }else{
                    $output .= '`${(size.z).toFixed(2)} m`;';
                }
                
                $output .= '
                modelViewer.updateHotspot({
                  name: \'hotspot-dot-X-Y-Z\',
                  position: `${center.x - x2} ${center.y - y2} ${center.z - z2}`
                });
            
                modelViewer.updateHotspot({
                  name: \'hotspot-dim-X-Y\',
                  position: `${center.x - x2} ${center.y - y2} ${center.z}`
                });
                modelViewer.querySelector(\'button[slot="hotspot-dim-X-Y"]\').textContent = ';
                if (($model_array['ar_dimensions_units'] == 'inches')OR($model_array['ar_dimensions_inches'] == true)){
                    $output .= '`${(size.z * 39.370).toFixed(2)} in`;';
                }elseif ($model_array['ar_dimensions_units'] == 'cm'){
                    $output .= '`${(size.z * 100).toFixed(0)} cm`;';
                }elseif ($model_array['ar_dimensions_units'] == 'mm'){
                    $output .= '`${(size.z * 1000).toFixed(0)} mm`;';
                }else{
                    $output .= '`${(size.z).toFixed(2)} m`;';
                }
                
                $output .= '
                modelViewer.updateHotspot({
                  name: \'hotspot-dot-X-Y+Z\',
                  position: `${center.x - x2} ${center.y - y2} ${center.z + z2}`
                });';

                

            $output .= '    
              });
            </script>';
                                    

            //Animation button
            if ($model_array['ar_animation']==true){
                $output .= '<script>
                    animationButton_'.$model_array['ar_pop'].'.addEventListener(\'click\', () => {
                      if (model_'.$model_array['model_id'].'.paused) {
                        document.getElementById("ar-button-animation_'.$model_array['ar_pop'].'").src="'.$play_btn.'";
                        model_'.$model_array['model_id'].'.play();
                      } else {
                        document.getElementById("ar-button-animation_'.$model_array['ar_pop'].'").src="'.$pause_btn.'";
                        model_'.$model_array['model_id'].'.pause();
                      }
                    });
                </script>';
            }


            
            $output.='
            </div>';
    
        //}
        return $output;
    }
}

/************* Model Viewer Short Code Display *******************/
if (!function_exists('ar_display_shortcode')){
    function ar_display_shortcode($atts, $variation_id='') {
        global $ar_plugin_id, $ar_wc_active, $ar_wp_active;
        $model_count = ar_model_count();


        $suffix = $variation_id ? "_var_".$variation_id : '';


        //Check if on a mobile and it supports AR.
        $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
        $isTab = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet")); 
        $isWin = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "windows")); 
        $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android")); 
        $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone")); 
        $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad")); 
        $isIOS = $isIPhone || $isIPad; 

        if ((get_option('ar_licence_valid')=='Valid')OR($model_count<=1)){
            
            $model_array=array();
            $model_array['ar_model_atts']=$atts;
            $model_array['ar_model_list']=array();
            $ar_model_list=array();
            /*Category - retrieve list of models*/
            if (isset($atts['cat'])){
                $ar_cat_list=explode(',',$atts['cat']);
                if ($ar_wp_active==true){
                    $args = array(
                    'post_type' => 'armodels',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'model_category', //double check your taxonomy name in you db 
                            'field'    => 'id',
                            'terms'    => $ar_cat_list,
                        ),
                        ),   
                    );
                    $the_query = new WP_Query( $args );
                    
                    // The Loop
                    if ( $the_query->have_posts() ) {
                        while ( $the_query->have_posts() ) {
                            $the_query->the_post();
                            $ar_model_list[]= get_the_ID();
                        }
                    }
                }
                if ($ar_wc_active==true){
                    foreach ($ar_cat_list as $k=>$v){
                        $ar_model_list_1=array();
                        $ar_model_list_1 = wc_get_term_product_ids( $v, 'product_cat' );
                        $ar_model_list=array_merge($ar_model_list,$ar_model_list_1);
                    }
                }
                if (count($ar_model_list)==0){
                    // no posts found
                    return 'no models found';
                }
                foreach (array_unique($ar_model_list) as $k => $v){
                    $model_array['ar_model_list'][] = preg_replace("/[^0-9]/", "",$v);
                }
                if (isset($ar_model_list[0])){
                    $atts['id'] = $ar_model_list[0];
                }else{ return 'no models found';}
            }elseif (isset($atts['id'])){
                $modl_id = $atts['id'];

                //If Alternative mobile ID exists then display mobile model if viewing on mobile or tablet
                if ($mob_id = get_post_meta($atts['id'], '_ar_mobile_id', true )){
                    
                    if(($isMob) OR ($isTab) OR ($isIPhone) OR ($isIPad) OR ($isAndroid)){  
                        $atts['id'] = $mob_id;
                    }
                }
           

                $ar_model_list=explode(',',$atts['id']);
                foreach ($ar_model_list as $k => $v){
                    $model_array['ar_model_list'][] = preg_replace("/[^0-9]/", "",$v);
                }
                $atts['id'] = $ar_model_list[0];
            }
            if (isset($atts['ar_hide_model'])){
                $model_array['ar_hide_model'] = $atts['ar_hide_model'];
            }else{
                $model_array['ar_hide_model'] = '';
            }

            $model_array['model_id'] = $atts['id'];

            $arpost = get_post( $atts['id'] ); 
            if (isset($arpost->post_type)){
                if($arpost->post_type == 'product_variation'){
                    $variation_id = $atts['id'];
                    $suffix = $variation_id ? "_var_".$variation_id : '';
                }
            }
            $model_array['usdz_file'] = get_post_meta($atts['id'], '_usdz_file'.$suffix, true );
            $model_array['glb_file'] = get_post_meta($atts['id'], '_glb_file'.$suffix, true );
            $model_array['ar_variants'] = get_post_meta($atts['id'], '_ar_variants'.$suffix, true );
            $model_array['ar_rotate'] = get_post_meta($atts['id'], '_ar_rotate'.$suffix, true );
            $model_array['ar_prompt'] = get_post_meta($atts['id'], '_ar_prompt'.$suffix, true );
            $model_array['ar_x'] = get_post_meta($atts['id'], '_ar_x'.$suffix, true );
            $model_array['ar_y'] = get_post_meta($atts['id'], '_ar_y'.$suffix, true );
            $model_array['ar_z'] = get_post_meta($atts['id'], '_ar_z'.$suffix, true );
            $model_array['ar_field_of_view'] = get_post_meta($atts['id'], '_ar_field_of_view'.$suffix, true );
            $model_array['ar_zoom_out'] = get_post_meta($atts['id'], '_ar_zoom_out'.$suffix, true );
            $model_array['ar_zoom_in'] = get_post_meta($atts['id'], '_ar_zoom_in'.$suffix, true );
            $model_array['ar_resizing'] = get_post_meta($atts['id'], '_ar_resizing'.$suffix, true );
            $model_array['ar_view_hide'] = get_post_meta($atts['id'], '_ar_view_hide'.$suffix, true );
            $model_array['ar_autoplay'] = get_post_meta($atts['id'], '_ar_autoplay'.$suffix, true );
            $model_array['ar_disable_zoom'] = get_post_meta($atts['id'], '_ar_disable_zoom'.$suffix, true );
            $model_array['ar_rotate_limit'] = get_post_meta($atts['id'], '_ar_rotate_limit'.$suffix, true );
            $model_array['ar_compass_top_value'] = get_post_meta($atts['id'], '_ar_compass_top_value'.$suffix, true );
            $model_array['ar_compass_bottom_value'] = get_post_meta($atts['id'], '_ar_compass_bottom_value'.$suffix, true );
            $model_array['ar_compass_left_value'] = get_post_meta($atts['id'], '_ar_compass_left_value'.$suffix, true );
            $model_array['ar_compass_right_value'] = get_post_meta($atts['id'], '_ar_compass_right_value'.$suffix, true );
            $model_array['ar_animation'] = get_post_meta($atts['id'], '_ar_animation'.$suffix, true );
            $model_array['ar_animation_selection'] = get_post_meta($atts['id'], '_ar_animation_selection'.$suffix, true );
        
            $model_array['skybox_file'] = get_post_meta($atts['id'], '_skybox_file'.$suffix, true );
            $model_array['ar_dimensions_inches']=get_option('ar_dimensions_inches');
            
            $model_array['ar_hide_dimensions'] = get_post_meta($atts['id'], '_ar_hide_dimensions'.$suffix, true );
            if ($model_array['ar_hide_dimensions']==''){
                $model_array['ar_hide_dimensions']=get_option('ar_hide_dimensions');
            }
            $model_array['ar_dimensions_inches']=get_option('ar_dimensions_inches');
            $model_array['ar_hide_arview']=get_option('ar_hide_arview');
            $model_array['ar_exposure']=get_post_meta($atts['id'], '_ar_exposure'.$suffix, true );
            $model_array['ar_shadow_intensity']=get_post_meta($atts['id'], '_ar_shadow_intensity'.$suffix, true );
            $model_array['ar_shadow_softness']=get_post_meta($atts['id'], '_ar_shadow_softness'.$suffix, true );
            $model_array['ar_camera_orbit']=get_post_meta($atts['id'], '_ar_camera_orbit'.$suffix, true );
            $model_array['ar_environment_image']=get_post_meta($atts['id'], '_ar_environment_image'.$suffix, true );
            $model_array['ar_emissive']=get_post_meta($atts['id'], '_ar_emissive'.$suffix, true );
            $model_array['ar_light_color']=get_post_meta($atts['id'], '_ar_light_color'.$suffix, true );
            $model_array['ar_hotspots']=get_post_meta($atts['id'], '_ar_hotspots'.$suffix, true );
            if (isset($atts['hide_qr'])){
                $model_array['ar_hide_qrcode']=1;
            }else{
                $model_array['ar_hide_qrcode']=get_option('ar_hide_qrcode');
            }
            if (isset($atts['hide_reset'])){
                $model_array['ar_hide_reset']=1;
            }else{
                $model_array['ar_hide_reset']=get_option('ar_hide_reset');
            }
            $model_array['ar_cta']=get_post_meta($atts['id'], '_ar_cta'.$suffix, true );
            $model_array['ar_cta_url']=get_post_meta($atts['id'], '_ar_cta_url'.$suffix, true );
            
            if (!isset($atts['ar_enable_fullscreen'])){
                $model_array['ar_hide_fullscreen']=get_option('ar_hide_fullscreen');
            }
            if (isset($atts['ar_show_close_on_devices'])){
                $model_array['ar_show_close_on_devices']=$atts['ar_show_close_on_devices'];
            }
            $model_array['ar_scene_viewer']=get_option('ar_scene_viewer');
            $model_array['ar_view_file']=get_option('ar_view_file');
            $model_array['ar_qr_file']=get_option('ar_qr_file');
            $model_array['ar_view_in_ar']=get_option('ar_view_in_ar');
            $model_array['ar_view_in_3d']=get_option('ar_view_in_3d');
            $model_array['ar_qr_destination']=get_post_meta($atts['id'], '_ar_qr_destination_mv'.$suffix, true );
            if ($model_array['ar_qr_destination']==''){
                $model_array['ar_qr_destination']=get_option('ar_qr_destination');
            }
            if (get_post_meta( $atts['id'], '_ar_qr_destination', true )){
                    $model_array['ar_qr_destination']=get_post_meta( $atts['id'], '_ar_qr_destination'.$suffix, true );
            }

            $model_array['ar_dimensions_units']=get_option('ar_dimensions_units');
            $model_array['ar_fullscreen_file']=get_option('ar_fullscreen_file');
            $model_array['ar_play_file']=get_option('ar_play_file');
            $model_array['ar_pause_file']=get_option('ar_pause_file');
            $ar_css_override = get_post_meta($atts['id'], '_ar_css_override'.$suffix, true );
            if (($ar_css_override==1) AND (get_post_meta($atts['id'], '_ar_css_positions'.$suffix, true )!='')){
                $model_array['ar_css_positions']=get_post_meta($atts['id'], '_ar_css_positions'.$suffix, true );
            }else{
                $model_array['ar_css_positions']=get_option('ar_css_positions');
            }
            if (($ar_css_override==1) AND (get_post_meta($atts['id'], '_ar_css'.$suffix, true )!='')){
                $model_array['ar_css']=get_post_meta($atts['id'], '_ar_css'.$suffix, true );
            }else{
                $model_array['ar_css']=get_option('ar_css');
            }
            $model_array['ar_pop']='';
            if (get_option('ar_open_tabs_remember')!=1){ 
                $model_array['ar_open_tabs'] = get_option('ar_open_tabs');
            }else{
                $model_array['ar_open_tabs'] = '';
            }
            
            if ($model_array['ar_hide_arview']==''){
                if (get_post_meta($atts['id'], '_ar_view_hide'.$suffix, true )!=''){
                    $model_array['ar_hide_arview'] = '1';
                }
            }
            
            if ($model_array['ar_hide_qrcode']==''){
                if (get_post_meta($atts['id'], '_ar_qr_hide'.$suffix, true )!=''){
                    $model_array['ar_hide_qrcode'] = '1';
                }
            }
            if ($model_array['ar_hide_reset']==''){
                if (get_post_meta($atts['id'], '_ar_hide_reset'.$suffix, true )!=''){
                    $model_array['ar_hide_reset'] = '1';
                }
            }
            //if (isset($model_array['usdz_file']) OR isset($model_array['glb_file'])){
                if (get_post_meta( $atts['id'], '_ar_placement'.$suffix, true )=='wall'){
                    $model_array['ar_placement']='ar-placement="wall"';
                }else{
                    $model_array['ar_placement']='';
                }
                if (get_post_meta( $atts['id'], '_ar_environment'.$suffix, true )){
                    $model_array['ar_environment']='environment-image="'.get_post_meta( $atts['id'], '_ar_environment'.$suffix, true ).'"';
                }else{
                    $model_array['ar_environment']='';
                }
                if (get_post_meta( $atts['id'], '_ar_qr_image'.$suffix, true )){
                    $model_array['ar_qr_image']=get_post_meta( $atts['id'], '_ar_qr_image'.$suffix, true ).'"';
                }
                
                /*Add https to http urls before displaying*/
                $ar_ssl_urls=array('usdz_file','glb_file','skybox_file','ar_environment','ar_qr_image');
                foreach ($ar_ssl_urls as $k=>$url){
                    if ( isset( $model_array[$url] ) ) {
                        if (substr(sanitize_text_field( $model_array[$url] ),0,7)=='http://'){
                            $model_array[$url] = 'https://'.substr(sanitize_text_field( $model_array[$url] ),7);
                        }
                    }
                }
                             

                //alternative ar model view
                $model_array['ar_alternative_id'] = 0;//print_r($model_array);   
                $alt_output = '';
                
                if ($ar_alternative_id = get_post_meta($modl_id, '_ar_alternative_id', true )){
                    $model_array['ar_alternative_id'] = $ar_alternative_id;

                    if(($isMob) OR ($isTab) OR ($isIPhone) OR ($isIPad) OR ($isAndroid)){  
                        $alt_output = ar_alternative_model($ar_alternative_id, $suffix);
                    }
                }
                
                $output = ar_display_model_viewer($model_array);
                $output = $output.$alt_output;
                
                //Fullscreen option - if not disabled in settings
                if ((!isset($model_array['ar_hide_fullscreen']))OR($model_array['ar_hide_fullscreen']=='')){
                    $model_array['ar_pop']='pop';
                    $model_array['skybox_file'] = get_post_meta($atts['id'], '_skybox_file'.$suffix, true );
                    $popup_output ='
                    <div id="ar_popup_'.$atts['id'].'" class="ar_popup">
                        <div class="ar_popup-content">
                            '.ar_display_model_viewer($model_array, $atts['id']).'
                        </div>
                    </div>
                    <script>
                        var ar_pop_'.$atts['id'].' = document.getElementById("ar_popup_'.$atts['id'].'");
                        var ar_close_'.$atts['id'].' = document.getElementById("ar_close_'.$atts['id'].'_pop");
                        if(document.getElementById("ar_pop_Btn_'.$atts['id'].'") !== null){
                            document.getElementById("ar_pop_Btn_'.$atts['id'].'").onclick = function() {
                              ar_pop_'.$atts['id'].'.style.display = "block";
                            }
                        }
                        ar_close_'.$atts['id'].'.onclick = function() {
                          ar_pop_'.$atts['id'].'.style.display = "none";
                        }
                        window.onclick = function(event) {
                          if (event.target == ar_pop_'.$atts['id'].') {
                            ar_pop_'.$atts['id'].'.style.display = "none";
                          }
                        }
                    </script>';

                    add_action( 'wp_footer', function( $arg ) use ( $popup_output ) {
                        echo $popup_output;
                    } );
                    add_action( 'admin_footer', function( $arg ) use ( $popup_output ) {
                        echo $popup_output;
                    } );
                }
            //}
        }else{
            //Invalid Licence
            if ($ar_plugin_id=='ar-for-wordpress'){
                $output = '<a href="/wp-admin/edit.php?post_type=armodels&page">';
            }else{
                $output = '<a href="/wp-admin/admin.php?page=wc-settings&tab=ar_display">';
            }
            $output .= '<b>'.__('AR Display Limits Exceeded', $ar_plugin_id ).'</b><br>';
            $output .= __('Check Settings', $ar_plugin_id ).'</a> - <a href="https://augmentedrealityplugins.com" target="_blank">'.__('Sign Up for Premium', $ar_plugin_id ).'</a>';
            
        }
        return $output;
    }
    add_shortcode('ardisplay', 'ar_display_shortcode');
}
/* * ************** End ***************** */

/************* AR View Short Code Display *******************/
if (!function_exists('ar_view_shortcode')){
    function ar_view_shortcode($atts) { 
        global $ar_plugin_id;
        

        if (get_option('ar_licence_valid')=='Valid'){
            $logo='';
            $ar_button_default='';
            if (get_option('ar_view_file')==''){
                $logo=esc_url( plugins_url( "assets/images/ar-view-btn.png", __FILE__ ) );
                $ar_button_default=' ar-button-default';
            }else{
                $logo=get_option('ar_view_file');
            }

            if ((!isset($atts))||(!isset($atts['id']))){
                return 'Please include AR model id in shortcode. [ar-view id=X]';
            }else{
                $mdl_id = $atts['id'];
                $atts['ar_hide_model']='1';
                $atts['ar_enable_fullscreen']='1';
                if (get_option('ar_view_in_ar')){
                    $ar_view_text = get_option('ar_view_in_ar');
                }else{
                    $ar_view_text =__('View in AR', $ar_plugin_id );
                }
                if (get_option('ar_view_in_3d')){
                    $ar_view_text_3d = get_option('ar_view_in_3d');
                }else{
                    $ar_view_text_3d =__('View in 3D', $ar_plugin_id );
                }
                $atts['ar_show_close_on_devices']='1';
                $ar_not_supported =__('Your device does not support Augmented Reality. You can view the 3D model or scan the QR code with an AR supported mobile device.', $ar_plugin_id );
                //Check if on a mobile and it supports AR.
                $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
                $isTab = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet")); 
                $isWin = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "windows")); 
                $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android")); 
                $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone")); 
                $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad")); 
                $isIOS = $isIPhone || $isIPad; 
                if(($isMob) OR ($isTab) OR ($isIPhone) OR ($isIPad) OR ($isAndroid)){  
                    //Mobile
                    //If Alternative mobile ID exists then display mobile model if viewing on mobile or tablet
                    if ($mob_id = get_post_meta($atts['id'], '_ar_mobile_id', true )){
                        $atts['id'] = $mob_id;
                    }
                    $popup_output = '<div id="arqr_popup_'.$atts['id'].'" class="ar_popup" style="display:none;"><div class="ar_popup-content arqr_popup-content"><div id="ar_qr_'.$atts['id'].'" class=" arqr_popup-container">'.ar_qrcode_shortcode($atts).'<p>'.__('Scan the QR with your device to view in Augmented Reality',$ar_plugin_id).'</p></div><div class="ar-popup-btn-container hide_on_devices"><button id="arqr_close_'.$atts['id'].'_pop" class="ar_popup-btn hide_on_devices"  onclick="document.getElementById(\'arqr_popup_'.$atts['id'].'\').style.display = \'none\';"><img src="'.esc_url( plugins_url( "assets/images/close.png", __FILE__ ) ).'" class="ar-fullscreen_btn-img"></button></div></div></div>';
                    $fallback = urlencode(get_permalink($atts['id']));

                    if ((isset($atts['text']))AND($atts['text']==true)){
                        //Text - Mobile
                        $atts['ar_hide_model']='0';
                        return '<span class="ar_view_text_link '.$ar_button_default.'" id="ar-button-standalone" onclick="document.getElementById(\'ar-button_'.$atts['id'].'\').click()">'.$ar_view_text.'</span> / <span class="ar_view_text_link ar_cursor_pointer '.$ar_button_default.'" id="ar-button-standalone_'.$atts['id'].'" onclick="document.getElementById(\'ardisplay_viewer_'.$atts['id'].'_pop\').classList.remove(\'nodisplay\');document.getElementById(\'ar_popup_'.$atts['id'].'\').style.display = \'block\'; ">'.$ar_view_text_3d.'</span>'.ar_display_shortcode($atts).'<script languange="javascript">document.getElementById(\'model_'.$atts['id'].'\').classList.add(\'nodisplay\');</script>';

                    }elseif ((isset($atts['buttons']))AND($atts['buttons']==true)){
                        $atts['ar_hide_model']='0';

                        if($isAndroid){
                            $glb_file = get_post_meta($atts['id'], '_glb_file', true );

                            if($ar_alternative_id = get_post_meta($mdl_id, '_ar_alternative_id', true )){
                                
                                $glb_file = get_post_meta($ar_alternative_id, '_glb_file', true );
                                if (substr(sanitize_text_field($glb_file),0,7)=='http://'){
                                    $glb_file = 'https://'.substr(sanitize_text_field($glb_file),7);
                                }

                            } else {
                                if (substr(sanitize_text_field($glb_file),0,7)=='http://'){
                                    $glb_file = 'https://'.substr(sanitize_text_field($glb_file),7);
                                }
                            }

                            $ar_plcmnt  = get_post_meta($atts['id'], '_ar_placement', true );
                           

                            $enable_vertical = '';

                            if($ar_plcmnt == 'wall'){
                                $enable_vertical = 'enable_vertical_placement=true&';
                            }

                            $arview_btn = '<a class="button ar_button" href="intent://arvr.google.com/scene-viewer/1.0?mode=ar_preferred&'.$enable_vertical.'disable_occlusion=true&file='.$glb_file.'#Intent;scheme=https;package=com.google.ar.core;action=android.intent.action.VIEW;S.browser_fallback_url='.$fallback.';end">'.$ar_view_text.'</a>';

                        } else {
                            $arview_btn = '<span class="button ar_button" id="ar-button-standalone" onclick="document.getElementById(\'ar-button_'.$atts['id'].'\').click();">'.$ar_view_text.'</span>';
                        }
                        return $arview_btn.'  <span class="button ar_button ar_cursor_pointer" id="ar-button-standalone_'.$atts['id'].'" onclick="document.getElementById(\'ardisplay_viewer_'.$atts['id'].'_pop\').classList.remove(\'nodisplay\');document.getElementById(\'ar_popup_'.$atts['id'].'\').style.display = \'block\';">'.$ar_view_text_3d.'</span>'.ar_display_shortcode($atts).'<script languange="javascript">document.getElementById(\'model_'.$atts['id'].'\').classList.add(\'nodisplay\'); jQuery(function() { jQuery(\'#ardisplay_viewer_'.$atts['id'].'_pop .ar-popup-btn-container\').removeClass(\'hide_on_devices\'); jQuery(\'#ardisplay_viewer_'.$atts['id'].'_pop  #ar_close_'.$atts['id'].'_pop\').removeClass(\'hide_on_devices\'); });</script>';
                    }else{
                        //AR Logo - Mobile
                        if($isAndroid){

                            $glb_file = get_post_meta($atts['id'], '_glb_file', true );

                            if (substr(sanitize_text_field($glb_file),0,7)=='http://'){
                                $glb_file = 'https://'.substr(sanitize_text_field($glb_file),7);
                            }

                            $ar_plcmnt  = get_post_meta($atts['id'], '_ar_placement', true );
                           

                            $enable_vertical = '';

                            if($ar_plcmnt == 'wall'){
                                $enable_vertical = 'enable_vertical_placement=true&';
                            }

                            return ar_display_shortcode($atts).'<a class="ar-button_standalone '.$ar_button_default.'" href="intent://arvr.google.com/scene-viewer/1.0?mode=ar_preferred&'.$enable_vertical.'disable_occlusion=true&file='.$glb_file.'#Intent;scheme=https;package=com.google.ar.core;action=android.intent.action.VIEW;S.browser_fallback_url='.$fallback.';end"><img id="ar-img_'.$atts['id'].'" src="'.$logo.'" class="ar-button-img"></a>';

                        } else {

                            return ar_display_shortcode($atts).'<span class="ar-button_standalone '.$ar_button_default.'" id="ar-button-standalone" onclick="document.getElementById(\'ar-button_'.$atts['id'].'\').click();"><img id="ar-img_'.$atts['id'].'" src="'.$logo.'" class="ar-button-img"></span>';
                        }
                    }    
                }else{ 
                    //Desktop

                    $atts['ar_hide_model']='0';
                    $atts['ar_qr_large']='1';
                    $popup_output = '<div id="arqr_popup_'.$atts['id'].'" class="ar_popup" style="display:none;"><div class="ar_popup-content arqr_popup-content"><div id="ar_qr_'.$atts['id'].'" class=" arqr_popup-container">'.ar_qrcode_shortcode($atts).'<p>'.__('Scan the QR with your device to view in Augmented Reality',$ar_plugin_id).'</p></div><div class="ar-popup-btn-container hide_on_devices"><button id="arqr_close_'.$atts['id'].'_pop" class="ar_popup-btn hide_on_devices"  onclick="document.getElementById(\'arqr_popup_'.$atts['id'].'\').style.display = \'none\';"><img src="'.esc_url( plugins_url( "assets/images/close.png", __FILE__ ) ).'" class="ar-fullscreen_btn-img"></button></div></div></div>';
                        
                    if ((isset($atts['text']))AND($atts['text']==true)){
                        //Text       
                        add_action( 'wp_footer', function( $arg ) use ( $popup_output ) {
                            echo $popup_output;
                        } );

                        return '<span class="ar_view_text_link ar_cursor_pointer '.$ar_button_default.'" id="ar-button-standalone" onclick="document.getElementById(\'arqr_popup_'.$atts['id'].'\').style.display = \'block\';document.getElementById(\'ar-qrcode\').classList.add(\'ar-qrcode-large\');">'.$ar_view_text.'</span> / 
                       <span class="ar_view_text_link ar_cursor_pointer '.$ar_button_default.'" id="ar-button-standalone_'.$atts['id'].'" onclick="document.getElementById(\'ardisplay_viewer_'.$atts['id'].'_pop\').classList.remove(\'nodisplay\');document.getElementById(\'ar_popup_'.$atts['id'].'\').style.display = \'block\';">'.$ar_view_text_3d.'</span>'.ar_display_shortcode($atts).'<script languange="javascript">document.getElementById(\'model_'.$atts['id'].'\').classList.add(\'nodisplay\');</script>';

                    }elseif ((isset($atts['buttons']))AND($atts['buttons']==true)){
                        //Buttons      
                        add_action( 'wp_footer', function( $arg ) use ( $popup_output ) {
                            echo $popup_output;
                        } );

                        return '<span class="button ar_button ar_cursor_pointer" id="ar-button-standalone" onclick="document.getElementById(\'arqr_popup_'.$atts['id'].'\').style.display = \'block\';document.getElementById(\'ar-qrcode\').classList.add(\'ar-qrcode-large\');">'.$ar_view_text.'</span> 
                       <span class="button ar_button ar_cursor_pointer" id="ar-button-standalone_'.$atts['id'].'" onclick="document.getElementById(\'ardisplay_viewer_'.$atts['id'].'_pop\').classList.remove(\'nodisplay\');document.getElementById(\'ar_popup_'.$atts['id'].'\').style.display = \'block\';">'.$ar_view_text_3d.'</span>'.ar_display_shortcode($atts).'<script languange="javascript">document.getElementById(\'model_'.$atts['id'].'\').classList.add(\'nodisplay\');</script>';

                    }else{
                        //AR Logo
                        return ar_display_shortcode($atts).'<span class="ar-button_standalone ar_cursor_pointer '.$ar_button_default.'" id="ar-button-standalone_'.$atts['id'].'" onclick="document.getElementById(\'ardisplay_viewer_'.$atts['id'].'_pop\').classList.remove(\'nodisplay\');document.getElementById(\'ar_popup_'.$atts['id'].'\').style.display = \'block\';"><img id="ar-img_'.$atts['id'].'" src="'.$logo.'" class="ar-button-img"></span><script languange="javascript">document.getElementById(\'model_'.$atts['id'].'\').classList.add(\'nodisplay\');</script>';
                    }
                }
            }
        }
    }
    add_shortcode('ar-view', 'ar_view_shortcode');
}

/************* QR Code Short Code Display *******************/
if (!function_exists('ar_qrcode_shortcode')){
    function ar_qrcode_shortcode($atts) { 
        if (get_option('ar_licence_valid')=='Valid'){
            global $wp;
            $qr_logo_image=esc_url( plugins_url( "assets/images/app_logo.png", __FILE__ ) );
                if (get_option('ar_qr_file')!=''){
                    $qr_logo_image=get_option('ar_qr_file');
                }
            //Check ar_qr_destination and if ids then pass shortcode ids to ar_qr_code, otherwise use url of parent page 
            $ar_qr_url = home_url( $wp->request );
            $ar_qr_destination='';
            if (isset($atts['id'])){
                //If Alternative mobile ID exists then display mobile model if viewing on mobile or tablet
                if ($mob_id = get_post_meta($atts['id'], '_ar_mobile_id', true )){
                    //Check if on a mobile and it supports AR.
                    $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
                    $isTab = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet")); 
                    $isWin = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "windows")); 
                    $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android")); 
                    $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone")); 
                    $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad")); 
                    $isIOS = $isIPhone || $isIPad; 
                    if(($isMob) OR ($isTab) OR ($isIPhone) OR ($isIPad) OR ($isAndroid)){  
                        $atts['id'] = $mob_id;
                    }
                }
                $ar_qr_destination=get_post_meta($atts['id'], '_ar_qr_destination_mv', true );
            }
            if ($ar_qr_destination==''){
                $ar_qr_destination=get_option('ar_qr_destination') ? get_option('ar_qr_destination') : 'parent-page';
            }
            if (isset($atts['id'])){
                if (get_post_meta( $atts['id'], '_ar_qr_destination', true )){
                        $ar_qr_destination=get_post_meta( $atts['id'], '_ar_qr_destination', true ).'"';
                }
            }

            if ($ar_qr_destination == 'model-viewer'){
                if (isset($atts['cat'])){
                    $ar_qr_url = get_site_url().'?ar-cat='.$atts['cat'];
                }elseif (isset($atts['id'])){
                    $ar_qr_url = get_site_url().'?ar-view='.$atts['id'];
                }
            }

             $ar_attid = 0;
            if (isset($atts['cat'])){
                $ar_attid = $atts['cat'];
            }elseif (isset($atts['id'])){
                $ar_attid = $atts['id'];
            }

            $ar_qr_image_data = base64_encode(ar_qr_code($qr_logo_image,$ar_attid,$ar_qr_url));
            $ar_qr_large ='';
            if (isset($atts['ar_qr_large'])){
                $ar_qr_large = 'ar-qrcode-large';
            }
            if ($ar_qr_image_data!=''){
                return '<button id="ar-qrcode" type="button" class="ar-qrcode_standalone hide_on_devices '.$ar_qr_large.'" onclick="this.classList.toggle(\'ar-qrcode-large\');" style="background-image: url(\'data:image/png;base64,'.$ar_qr_image_data.'\');"></button>';
            }
        }
    }
    add_shortcode('ar-qr', 'ar_qrcode_shortcode');
}

/************* Upload AR Model Files Javascript *******************/
if (!function_exists('ar_upload_button_js')){
    function ar_upload_button_js($model_id, $variation_id='') { 
        global $ar_plugin_id;
        
        $suffix = $variation_id ? "_var_".$variation_id : '';

        $arpost = get_post( $model_id ); 

        if($arpost->post_type == 'product'){
            $product=wc_get_product($model_id);
            $product_parent=$product->get_parent_id();

            if($product_parent==0){
                $product_parent = $model_id;
            }
        } else {
            $product_parent = $model_id;
        }

    $output='
    <script>
        jQuery(document).ready(function($){
            
            var custom_uploader;
            var button_clicked;

            
            $(document).on(\'click\',\'.upload_usdz_button, .upload_glb_button, .upload_skybox_button, .upload_environment_button, .upload_qr_image_button, .upload_asset_texture_button, .upload_asset_texture_button_0, .upload_asset_texture_button_1, .upload_asset_texture_button_2, .upload_asset_texture_button_3, .upload_asset_texture_button_4, .upload_asset_texture_button_5, .upload_asset_texture_button_6, .upload_asset_texture_button_7, .upload_asset_texture_button_8, .upload_asset_texture_button_9\', function(e) {
                window.button_clicked = $(this).attr(\'class\');
                e.preventDefault();

                var variation_id = \'\';
                var model_idd = \'\';
                var suffix = \'\';

                if(e.target.hasAttribute(\'data-variation\')){
                    variation_id = \'_var_\' + e.target.getAttribute(\'data-variation\');
                    model_idd = e.target.getAttribute(\'data-variation\');
                    suffix = variation_id;
                } else {
                    model_idd = \''.$product_parent.'\';
                }

                //console.log(\'button \' + button_clicked)
                //console.log(\'modelid \' + model_idd);
                //console.log(\'variation \' + variation_id);
                $(\'#uploader_modelid\').val(model_idd);
            
                //If the uploader object has already been created, reopen the dialog
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }
        
                //Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: \'';
                    $output .=__('Choose your AR Files', $ar_plugin_id );
                    
                    $output .= '\',
                    button: {
                        text: \'';
                    $output .=__('Choose your AR Files', $ar_plugin_id );
                    $output .= '\'
                    },
                    multiple: true
                });
        
                //When a file is selected, grab the URL and set it as the text field value
                custom_uploader.on(\'select\', function() {
                    var attachments = custom_uploader.state().get(\'selection\').map( 
                       function( attachment ) {
                           attachment.toJSON();
                           return attachment;
                      });

                      //console.log(window.button_clicked);

                     $.each(attachments, function( index, attachement ) {
                          
                          var fileurl=attachments[index].attributes.url;
                            var filetype = fileurl.substring(fileurl.length - 4, fileurl.length).toLowerCase();
                            var modl_id = $(\'#uploader_modelid\').val();
                            var sffx = \'\';

                            if(modl_id != \''.$product_parent.'\'){
                                sffx = \'_var_\' + modl_id;
                            }
                            //.reality files = lity (last 4 chars)
                            if ((filetype === \'usdz\') || (filetype === \'USDZ\') || (filetype === \'lity\') || (filetype === \'LITY\')){
                                $(\'#_usdz_file\' + sffx).attr(\'value\',fileurl);
                                var usdz_filename = fileurl.substring(fileurl.lastIndexOf(\'/\') + 1);
                                document.getElementById("usdz_filename"  + sffx).innerHTML= usdz_filename;
                                document.getElementById("usdz_thumb_img").src = \''.esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ).'\';
                                document.getElementById(\'usdz_thumb_img\').classList.add(\'ar_file_icons_pulse\');
                            }else if ((filetype === \'.glb\')||(filetype === \'gltf\')||(filetype === \'.zip\')||(filetype === \'.dae\')){
                                $(\'#_glb_file\'  + sffx).attr(\'value\',fileurl);
                                var glb_filename = fileurl.substring(fileurl.lastIndexOf(\'/\') + 1);
                                document.getElementById("glb_filename"  + sffx).innerHTML=glb_filename;
                                document.getElementById("glb_thumb_img").src = \''.esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ).'\';
                                var element = document.getElementById("model_" + modl_id);
                                
                                var element2 = document.getElementById("ar_admin_model_" + modl_id);
                                if (element2) {
                                    element2.style.display = "block";
                                }
                                //console.log("model_" + fileurl);
                                console.log(sffx);
                                element.setAttribute("src", fileurl);


                            }else if ((filetype === \'.hdr\') || (filetype === \'.jpg\') || (filetype === \'.png\')){

                                if (window.button_clicked.indexOf(\'upload_skybox_button\') != -1){
                                    console.log("skybox clicked");
                                    $(\'#_skybox_file\' + sffx).val(fileurl).trigger(\'change\'); 
                                    //$(\'#_skybox_file\' + sffx).trigger(\'change\'); 
                                }
                                else if (window.button_clicked.indexOf(\'upload_environment_button\') != -1){
                                    console.log("envi clicked");
                                    $(\'#_ar_environment\' + sffx).val(fileurl).trigger(\'change\');
                                    //$(\'#_ar_environment\' + sffx).trigger(\'change\');  
                                }
                                else if (window.button_clicked.indexOf(\'upload_qr_image_button\') != -1){
                                    $(\'#_ar_qr_image\' + sffx).val(fileurl); 
                                    $(\'#_ar_qr_image\' + sffx).trigger(\'change\'); 
                                }

                                console.log(window.button_clicked);

                                ';


                                
                                //Asset Builder Textures
                                for($i = 0; $i<10; $i++) { 
                                    $output.='
                                    if (window.button_clicked.indexOf(\'upload_asset_texture_button_'.$i.'\') != -1){
                                        $(\'#_asset_texture_file_'.$i.'\').val(fileurl).trigger(\'input\');
                                        if ($(\'#_ar_asset_file\').val()) {
                                            $(\'#ar_asset_builder_submit_container\').css(\'display\', \'block\');
                                        }
                                        $(\'#ar_asset_builder_texture_done\').html(\'&#10003;\');
                                    }';
                                }
                                $output.='
                                
                            }else{
                            ';
                    
                    //$js_alert =__('Invalid file type. Please choose a USDZ, REALITY, GLB, GLTF, ZIP, HDR, JPG, PNG, DAE, DXF, 3DS, OBJ, PLY or STL file.', $ar_plugin_id );
                    $js_alert =__('Invalid file type. Please choose a USDZ, REALITY, GLB or GLTF.', $ar_plugin_id );
                    $output .= '
                                 alert(\''.$js_alert.'\');
                            }

                            $(\'supports-drag-drop\').hide();
                     });
         
                });
                //Open the uploader dialog
                custom_uploader.open();

                e.stopPropagation();
            });  
            
            //Asset Builder
            // Initialize a flag to track whether the action has been triggered
            // Find the "Update" button in the Gutenberg editor
        const updateButton = $(\'.editor-post-save-draft\');

        // Find your custom button by its class or ID
        const customButton = $(\'#ar_asset_builder_submit\');

        // Add a click event handler to your custom button
        customButton.on(\'click\', function() {
            
            var asset_file = $(\'#_ar_asset_file\').val();
            var asset_orientation = $(\'#ar_asset_orientation\').val();
            var asset_ratio = $(\'#ar_asset_ratio\').val();
            var m = asset_file.lastIndexOf(\'.\');
            
            //console.log(\'Asset File:\', asset_file);
            //console.log(\'Last Index of Dot:\', m);
            
            if (m !== -1) {
                var asset_file_result = asset_file.substring(0, m);
                console.log(\'Result:\', asset_file_result);
            } else {
                console.log("No dot found in asset_file");
            }
            //console.log(\'Asset Ratio:\', asset_ratio);
            //console.log(\'Asset Orientation:\', asset_orientation);
            var asset_file_result = asset_file.substring(0, m) + \'_\' + asset_ratio + \'_\' + asset_orientation + \'.zip\' ;
            $(\'#_ar_asset_file\').val(asset_file_result);
            $(\'#_ar_placement\').val(\'wall\');
            //alert (asset_file_result);
            
            wp.data.dispatch(\'core/editor\').savePost();
            // Reload the page after a short delay to ensure the save action completes
            setTimeout(function() {
                location.reload();
            }, 1000); 
        });
        //asset_builder_button
            $( "#asset_builder_tab, #asset_builder_button" ).click(function() {
                    $("#asset_builder_iframe").html(\'<iframe src="https://augmentedrealityplugins.com/asset_builder/gallery.php?referrer='.urlencode(get_site_url()).'" style="width:100%;min-height:180px" id="asset_builder_iframe"></iframe>\');
            });
            
        
            /*$( "#ar_asset_iframe_acc" ).click(function() {
                if ($("#asset_builder_iframe").html() === \'\') {
                    $("#asset_builder_iframe").html(\'<iframe src="https://augmentedrealityplugins.com/asset_builder/gallery.php?referrer='.urlencode(get_site_url()).'" style="width:100%;height:160px" id="asset_builder_iframe"></iframe>\');
                }
            });*/
        });
        //List for Events from the Asset Builder iFrame
        var eventMethod = window.addEventListener
                ? "addEventListener"
                : "attachEvent";
        var eventer = window[eventMethod];
        var messageEvent = eventMethod === "attachEvent"
            ? "onmessage"
            : "message";
    
        eventer(messageEvent, function (e) {
            if (e.origin !== \'https://augmentedrealityplugins.com\') return;
            if (e.data.substring(0, 5)===\'https\'){
            //alert (e.data);
                document.getElementById(\'_ar_asset_file\').value = e.data;
                if (document.getElementById(\'_asset_texture_file_0\').value) {
                    document.getElementById(\'ar_asset_builder_model_done\').innerHTML = \'&#10003;\';
                    document.getElementById(\'ar_asset_builder_submit_container\').style.display = \'block\';
                }
                //ar_update_size_function();
            }else{
                //Show texture input fields and update their labels
                var details = e.data.split(\',\');
                document.getElementById(\'_asset_texture_flip\').value = \'\';
                var i;
                for (i = 0; i < 1; i++) { //Previously 10 - Cube will require 6
                  var texture = \'texture_\' + i;
                  var label = \'texture_label_\' + i;
                  var btn = \'upload_asset_texture_button_\' + i;
                  var field = \'_asset_texture_file_\' + i;
                  var field_id = \'_asset_texture_id_\' + i;
                  var element = document.getElementById(\'texture_container_\' + i);
                  element.classList.add("nodisplay");
                  if(details[i] === undefined){
                      document.getElementById(field).value = \'\';
                      document.getElementById(field_id).value = \'\';
                  }else if (details[i] ===\'flip\'){
                      //alert(details[i]);
                      document.getElementById(\'_asset_texture_flip\').value = \'flip\';
                  }else{
                      //element.classList.remove("nodisplay");
                      element.classList.remove("nodisplay");
                      //document.getElementById(\'texture_\' + i).classList.remove("nodisplay");
                      
                      var label_contents = details[i].charAt(0).toUpperCase() + details[i].slice(1);
                      label_contents=label_contents.substring(0,(label_contents.length -4));
                      document.getElementById(field_id).value = details[i];
                      document.getElementById(btn).value = label_contents.replace(\'_\',\' \');
                  }
                
                }
            }
        });
    </script>';
    return $output;
    }
}
 
/************* Asset Builder Textures *************/
if (!function_exists('asset_builder_texture')){
    function asset_builder_texture($dir, $gltf,$textures, $flip){
        $gltf_json = json_decode(get_local_file_contents($dir.$gltf),true);
        foreach ($textures as $texture_key=>$texture_array){
            $texture=$texture_array['newfile'];
            $orig_texture_filename=$texture_array['filename'];
            $src_texture_ext = strtolower(substr($texture,-3));
            if (($src_texture_ext=='jpg')||($src_texture_ext=='png')){
                //read gltf file
                foreach ($gltf_json['images'] as $k=>$v){
                    if (substr($v['uri'],7)==$orig_texture_filename){
                        $json_texture_ext = substr($v['uri'],-3);
                        //update gltf texture extension if need be.
                        if ($json_texture_ext!=$src_texture_ext){
                            $gltf_json['images'][$k]['uri'] = substr($v['uri'],0,-3).$src_texture_ext;
                            if ($src_texture_ext=='jpg'){
                                $gltf_json['images'][$k]['mimeType']='image/jpeg';
                            }elseif ($src_texture_ext=='png'){
                                $gltf_json['images'][$k]['mimeType']='image/png';
                            }
                            $destination_file = $dir.$gltf;
                            $open = fopen( $destination_file, "w" ); 
                            $write = fputs( $open,  json_encode($gltf_json)); 
                            fclose( $open );
                        }
                    }
                }
                //get name of texture file
                unlink($dir.'images/'.$orig_texture_filename);
                //copy texture file to zip folder and rename it to replace existing texture
                if ($flip=='flip'){
                    $texture = esc_url( plugins_url( "ar_asset_image.php", __FILE__ ) ).'?file='.urlencode($texture);
                }
                copy ($texture,$dir.'images/'.substr($orig_texture_filename,0,-3).$src_texture_ext);
            }
        }
    }
}

/********** AR Upgrade to Premium Version Banner Ribbon **************/
if (!function_exists('ar_admin_notice_upgrade_banner')){
    function ar_admin_notice_upgrade_banner() {
        global $ar_whitelabel;
        $plugin_check = get_option('ar_licence_valid');
        if (($plugin_check!='Valid')AND($ar_whitelabel!=true)){
            ar_upgrade_banner(); 
        }
    }
    add_action( 'admin_notices', 'ar_admin_notice_upgrade_banner' );
}

if (!function_exists('ar_upgrade_banner')){
    function ar_upgrade_banner() { 
        global $ar_plugin_id; 
        
        ?>
        <style>
        #upgrade_premium {
            cursor: pointer;
            padding: 10px 12px;
            margin-left: -17px;
            font-style: normal !important;
            font-size: 20px;
            margin-right: 12px;
            color:#fff;
            font-weight: bold;
        }
        #upgrade_premium a{
            color:#fff;
            text-decoration:none; 
            font-size:16px;
            
        }
        #upgrade_premium_meta {
            color:#fff;
            text-decoration:none; 
            font-size:14px;
            font-weight: normal;
        }
        #upgrade_premium_meta a{
            color:#fff;
            text-decoration:none; 
            font-size:14px;
        }
        #upgrade_premium_button{
            padding-bottom:10px;
        }
        .ar_button_orange{
            background-color: #f37a23 !important;
            padding:20px
            margin:20px;
            color:#fff !important;
            border-color: #fff !important;
            font-weight: bold;
        }
        
        </style>
        
        <div id="ar_shortcode_instructions" class="notice notice-warning is-dismissible">
                    <div style="width:100%;background-color:#12383d">
                        <div class="ar_admin_view_title">
                      <?php  
                        if ($ar_plugin_id == 'ar-for-wordpress'){
                            $plugin_url = 'wordpress';
                        }else{
                            $plugin_url = 'woocommerce';
                        }
                        echo '<img src="'.esc_url( plugins_url( "assets/images/".$ar_plugin_id."-box.jpg", __FILE__ ) ).'" style="padding: 10px 30px 10px 10px; height:60px" align="left">
                        <h1 style="color:#ffffff; padding-top:20px">'.__('AR Display',$ar_plugin_id).'</h1>
                        </div>';
                        echo '<div id="upgrade_premium">';
                            _e('Upgrade to Premium', $ar_plugin_id );
                            echo '<span id="upgrade_premium_meta"> - '.__('Unlimited Models & Full Settings', $ar_plugin_id ).'</span>';
                        echo '</div>
                            <div id="upgrade_premium_button">
                            <a href="https://augmentedrealityplugins.com/ar/'.$plugin_url.'/?ar_code=userUpgrade" target="_blank" class="button ar_button_orange">'.__('1st Month Free','ar-for-wordpress').'</a>.
                            </div>';
                        ?>
            </div>
        </div>
        <?php
       /* echo '<div id="upgrade_ribbon" class="notice notice-warning is-dismissible">
            
                <div id="upgrade_ribbon_top">
                    <div id="upgrade_ribbon_left">
                    </div>
                    <div id="upgrade_ribbon_base">
                        <span id="upgrade_premium"><a href="https://augmentedrealityplugins.com" target="_blank">';
                        _e('AR Display', $ar_plugin_id );
                            
                        echo '</a></span>
                        <span id="upgrade_premium_meta"><a href="https://augmentedrealityplugins.com" target="_blank">';
                        _e('Upgrade to Premium - Unlimited Models & Full Settings For Only', $ar_plugin_id );
                            
                        echo ' $20 per month!</a></span>
                    </div>
                    <div id="upgrade_ribbon_right">
                    </div>
                </div>
            </div>';*/
    }        
}            
            
/********** AR 3D Model Conversion **************/
if (!function_exists('ar_model_conversion')){
    function ar_model_conversion($model) {
        $link = 'https://augmentedrealityplugins.com/converters/glb_conversion.php';
        ob_start();
        $response = wp_remote_get( $link.'?model_url='.rawurlencode($model));
        if ( !is_wp_error($response) && isset( $response[ 'body' ] ) ) {
            return $response['body'];
        }
        ob_flush();
    }
 }

/************Allow USDZ mime upload in Wordpress Media Library *****************/
if (!function_exists('ar_my_file_types')){
    function ar_my_file_types($mime_types) { //Add Additional File Types
        $mime_types['usdz'] = 'model/vnd.usdz+zip';
        return $mime_types;
    }
}
add_filter('upload_mimes', 'ar_my_file_types', 1, 1);

if (!function_exists('ar_display_media_library')){
    function ar_display_media_library( $data, $file, $filename, $mimes ) {
        if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
            return $data;
        }
        $registered_file_types = [
            'usdz' => 'model/vnd.usdz+zip|application/octet-stream|model/x-vnd.usdz+zip',
            'USDZ' => 'model/vnd.usdz+zip|application/octet-stream|model/x-vnd.usdz+zip',
            'reality' => 'model/vnd.reality|application/octet-stream',
            'REALITY' => 'model/vnd.reality|application/octet-stream',
            'glb' => 'model/gltf-binary|application/octet-stream|model',
            'GLB' => 'model/gltf-binary|application/octet-stream|model',
            'gltf' => 'model/gltf+json',
            'GLTF' => 'model/gltf+json',
            'hdr' => 'model/gltf+json',
            'HDR' => 'model/gltf+json',
            'dxf' => 'application/dxf',
            'DXF' => 'application/dxf',
            'dae' => 'application/dae',
            'DAE' => 'application/dae',
            '3ds' => 'application/x-3ds',
            '3DS' => 'application/x-3ds',
            'obj' => 'model/obj',
            'OBJ' => 'model/obj',
            'ply' => 'application/octet-stream',
            'PLY' => 'application/octet-stream',
            'stl' => 'model/stl',
            'STL' => 'model/stl'
            ];
        $filetype = wp_check_filetype( $filename, $mimes );
        if ( ! isset( $registered_file_types[ $filetype['ext'] ] ) ) {
            return $data;
        }
        return [
            'ext' => $filetype['ext'],
            'type' => $filetype['type'],
            'proper_filename' => $data['proper_filename'],
        ];
    }
    add_filter( 'wp_check_filetype_and_ext', 'ar_display_media_library', 10, 4 );
}

if (!function_exists('ar_display_mimes')){
    function ar_display_mimes( $mime_types ) {
        if ( ! in_array( 'usdz', $mime_types ) ) { 
            $mime_types['usdz'] = 'model/vnd.usdz+zip|application/octet-stream|model/x-vnd.usdz+zip';
        }
        if ( ! in_array( 'reality', $mime_types ) ) { 
            $mime_types['reality'] = 'model/vnd.reality|application/octet-stream';
        }
        if ( ! in_array( 'glb', $mime_types ) ) { 
            $mime_types['glb'] = 'model/gltf-binary|application/octet-stream|model';
        }
        if ( ! in_array( 'gltf', $mime_types ) ) { 
            $mime_types['gltf'] = 'model/gltf+json';
        }
        if ( ! in_array( 'hdr', $mime_types ) ) { 
            $mime_types['hdr'] = 'image/vnd.radiance';
        }
        if ( ! in_array( 'dxf', $mime_types ) ) { 
            $mime_types['dxf'] = 'application/dxf';
        }
        if ( ! in_array( 'dae', $mime_types ) ) { 
            $mime_types['dae'] = 'application/dae';
        }
        if ( ! in_array( '3ds', $mime_types ) ) { 
            $mime_types['3ds'] = 'application/x-3ds';
        }
        if ( ! in_array( 'obj', $mime_types ) ) { 
            $mime_types['obj'] = 'model/obj';
        }
        if ( ! in_array( 'ply', $mime_types ) ) { 
            $mime_types['ply'] = 'application/octet-stream';
        }
        if ( ! in_array( 'stl', $mime_types ) ) { 
            $mime_types['stl'] = 'model/stl';
        }
        return $mime_types;
    }
    
    add_filter( 'upload_mimes', 'ar_display_mimes' );
}

/************* AR Custom column *************/
if (!function_exists('ar_advance_custom_armodels_column')){
    function ar_advance_custom_armodels_column( $column, $post_id ) {
        global $ar_plugin_id;
        $get_model_check = get_post_meta($post_id, '_usdz_file', true);
        if(empty($get_model_check)){
          $get_model_check = get_post_meta($post_id, '_glb_file', true);
        }
        if(!empty($get_model_check)){
            switch ( $column ) { 
                case 'Shortcode' :
                    
                    echo '<input id="ar_shortcode_'.$post_id.'" type="text" value="[ardisplay id='.$post_id.']" readonly style="width:150px" onclick="copyToClipboard(\'ar_shortcode_'.$post_id.'\');document.getElementById(\'copied_'.$post_id.'\').innerHTML=\'&nbsp;Copied!\';"><span id="copied_'.$post_id.'"></span>';
                    break;
                case 'thumbs' :
                    $ARimgSrc = esc_url( plugins_url( "assets/images/chair.png", __FILE__ ) );
                    $product_link = admin_url( 'post.php?post=' . $post_id ) . '&action=edit#ar_woo_advance_custom_attachment"';
                    echo '<a href="'.$product_link.'"><div class="ar_tooltip"><img src="'.$ARimgSrc.'" width="20"></div></a>';
                    break;
            }   
        }
    }
}

if (!function_exists('get_local_file_contents')){
    function get_local_file_contents( $file_path ) {
        ob_start();
        include $file_path;
        $contents = ob_get_clean();
    
        return $contents;
    }
}
if (!function_exists('ar_remove_asset')){
    function ar_remove_asset($dir) {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") ar_remove_asset($dir."/".$object); else unlink($dir."/".$object);
           }
         }
         reset($objects);
         rmdir($dir);
       }
    }
}

/********** Settings Page **********/
if (!function_exists('ar_subscription_setting')){
    function ar_subscription_setting() {
        global $wpdb, $ar_version, $ar_plugin_id, $ar_wc_active, $ar_wp_active, $ar_rate_this_plugin, $shortcode_examples, $woocommerce_featured_image, $ar_whitelabel, $ar_css_styles, $ar_css_names;
        $ar_licence_key = get_option('ar_licence_key');
        if ($_POST){
            //Save Settings
            if ($ar_licence_key != $_POST['ar_licence_key']){
                update_option( 'ar_licence_renewal', '');
                $ar_licence_key = $_POST['ar_licence_key'];
            }
            $settings_fields=array('ar_licence_key','ar_wl_file', 'ar_view_file', 'ar_qr_file', 'ar_qr_destination','ar_view_in_ar','ar_view_in_3d', 'ar_dimensions_units', 'ar_fullscreen_file', 'ar_play_file', 'ar_pause_file', 'ar_dimensions_inches', 'ar_hide_dimensions','ar_no_posts', 'ar_hide_arview', 'ar_hide_qrcode', 'ar_hide_reset', 'ar_hide_fullscreen','ar_scene_viewer','ar_css','ar_css_positions', 'ar_open_tabs_remember');
            foreach ($settings_fields as $k => $v){
                if (!isset($_POST[$v])){$_POST[$v]='';}
                update_option( $v, $_POST[$v]);
            }
        }
        //Delete Post
        if (isset($_GET['delete_post_id'])) {
            $post_id = intval($_GET['delete_post_id']);
            $post_type = get_post_type($post_id);
    
            if (current_user_can('delete_post', $post_id)) {
                if ($post_type === 'product') {
                    // Delete the _ar_display meta key from the product
                    delete_post_meta($post_id, '_ar_display');
                    echo '<div class="updated"><p>AR Model deleted from product successfully.</p></div>';
                } else {
                    // Delete the post
                    wp_delete_post($post_id, true);
                    echo '<div class="updated"><p>AR Model deleted successfully.</p></div>';
                }
            } else {
                echo '<div class="error"><p>You do not have permission to delete this post/product.</p></div>';
            }
        }
    
        $ar_logo = esc_url( plugins_url( 'assets/images/Ar_logo.png', __FILE__ ) ); 
        $ar_wl_logo = get_option('ar_wl_file'); 
        ?>
        <div class="message_set"></div>
      
        <div class="licence_key" id="key" style="float:left;">
            <form method="post" action="edit.php?post_type=armodels&page">
        <?php 
        //Renewal Date Check
        $renewal_check = get_option('ar_licence_renewal');
        if (($renewal_check=='')OR( strtotime($renewal_check) < strtotime(date('Y-m-d')) )) {
            ar_cron();
            $renewal_check = get_option('ar_licence_renewal');
        }
        $plugin_check = get_option('ar_licence_valid');
        $plan_check = get_option('ar_licence_plan');
        
        
        if ($ar_whitelabel!=true){ ?>   
            <div class="ar_site_logo">
                <a href = "https://augmentedrealityplugins.com" target = "_blank">              
                <img src="<?php echo $ar_logo;?>" style="width:300px; padding:0px;float:left" />
                </a>
            </div>
            <br clear="all">
            <?php
                echo '<h1>';
                if ($ar_plugin_id=='ar-for-woocommerce'){
                    _e('AR For Woocommerce', $ar_plugin_id );
                }else{
                    _e('AR For WordPress', $ar_plugin_id ); 
                }
                echo ' v'.$ar_version.'</h1>';
                echo '<h3>';
                _e('Subscription Plan', $ar_plugin_id );
                echo '</h3>';
            ?>
        <?php }else{
        //White Label Logo 
        ?>
        <div>
            <?php 
            
            if ($ar_wl_logo){
            ?>
                <div class="ar_site_logo">
                                
                    <img src="<?php echo $ar_wl_logo;?>" style="max-width:300px; padding:0px;float:left" />
                    <input type="hidden" name="ar_wl_file" id="ar_wl_file" class="regular-text" value="<?php echo $ar_wl_logo; ?>">
                </div>
                <br clear="all">
            <?php }
            if (!get_option('ar_licence_key')){  ?>
                  <div style="width:160px;float:left;"><strong>White Label Logo</strong></div>
                  <div style="float:left;"><input type="text" name="ar_wl_file" id="ar_wl_file" class="regular-text" value="<?php echo $ar_wl_logo; ?>"> <input id="ar_wl_file_button" class="button" type="button" value="White Label Logo File" /> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('ar_wl_file').value = ''"></div>
            <?php } ?>
            </div>
            <br  clear="all">
        
        
        <?php }?>
            <div class="licence_page">
            
                
                <?php settings_fields( 'ar_display_options_group' ); ?>  
                
                <div>
                  <div style="width:160px;float:left;"><strong>
                      <?php
                        _e('License Key', $ar_plugin_id );
                        ?></strong></div>
                  <div style="float:left;"><input type="text" id="ar_licence_key" name="ar_licence_key" class="regular-text" style="width:160px" value="<?php echo $ar_licence_key; ?>" /></div>
                </div>
                  
                <?php 
                //Model Count
                $model_count = ar_model_count();
                $disabled = '';
                if($plan_check=='Premium') { 
                    echo '<div style="float:left;margin-top:4px"><span style="color:green;margin-left: 7px; font-size: 19px;">&#10004;</span> '.get_option('ar_licence_plan').'</div>'; 
                } else { 
                    if ($ar_licence_key!=''){
                        echo '<div style="float:left;margin-top:4px"><span style="color:red;margin-left: 7px; font-size: 19px;">&#10008;</span></div>';
                    }
                    if ($model_count>=2){
                        $disabled =' disabled';
                    }
                }
                
                //Display Renewal Date
                if ($renewal_check !=''){ 
                ?>
                  <br clear="all"><br>
                  <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Renewal', $ar_plugin_id );
                            ?></strong></div>
                      <div style="float:left;"><?php echo date('j F Y', strtotime($renewal_check));?></div>
                    </div>
                <?php } 
                
                
                $alert = '';
                ?>
                  <br clear="all"><br>
                  <div>
                      <div style="float:right"><a href="https://augmentedrealityplugins.com/support/" target="_blank">Documentation</a></div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Model Count', $ar_plugin_id );
                            ?></strong></div>
                      <div style="float:left;"><?php 
                      if ($disabled!=''){
                          if ($ar_licence_key==''){
                               $alert = __('You have too many AR models for the free plugin',$ar_plugin_id);
                          }else{
                              $alert = __('Invalid or Expried Licence Key',$ar_plugin_id);
                          }
                      }
                      echo $model_count;
                      
                      ?></div>
                    </div>
                <?php if ($alert!=''){
                    echo '<br clear="all"><br><div id="upgrade_ribbon" class="notice notice-error is-dismissible"><p>'.$alert. '</p></div>';
                    if ($disabled!=''){
                        if ($ar_licence_key==''){
                              echo display_armodels_posts();
                        }
                      }
                }?>
                <?php
                if($plan_check!='Premium') { 
                    echo '<br clear="all"><br><a href="https://augmentedrealityplugins.com/" target="_blank" class="button" style="float:right;">'.'Sign Up For Premium'.'</a>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">';
                    $disabled =' disabled';
                }
                ?>
                <br clear="all">
                <hr>
                <h3> <?php
                        _e('Options', $ar_plugin_id );
                        if ($disabled!=''){echo ' - '.__('Premium Plans Only', $ar_plugin_id);}
                       
                        ?></h3>
                <p>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Custom AR Button', $ar_plugin_id );
                            $ar_logo_file_txt = __('AR Logo File', $ar_plugin_id);
                            
                            ?></strong></div>
                      <div style="float:left;"><input type="text" name="ar_view_file" id="ar_view_file" class="regular-text" value="<?php echo get_option('ar_view_file'); ?>" <?= $disabled;?>/> <input id="ar_view_file_button" class="button" type="button" value="<?php echo $ar_logo_file_txt;?>" <?= $disabled;?> /> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('ar_view_file').value = ''"></div>
                </div>
                <br  clear="all">
                <br>
                
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Custom Fullscreen Button', $ar_plugin_id );
                            $ar_logo_file_txt = __('AR Fullscreen File', $ar_plugin_id);
                            ?></strong></div>
                      <div style="float:left;"><input type="text" name="ar_fullscreen_file" id="ar_fullscreen_file" class="regular-text" value="<?php echo get_option('ar_fullscreen_file'); ?>" <?= $disabled;?>/> <input id="ar_fullscreen_file_button" class="button" type="button" value="<?php echo $ar_logo_file_txt;?>" <?= $disabled;?> /> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('ar_fullscreen_file').value = ''"></div>
                </div>
                <br  clear="all">
                <br>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Custom Play Button', $ar_plugin_id );
                            $ar_logo_file_txt = __('AR Play File', $ar_plugin_id);
                            ?></strong></div>
                      <div style="float:left;"><input type="text" name="ar_play_file" id="ar_play_file" class="regular-text" value="<?php echo get_option('ar_play_file'); ?>" <?= $disabled;?>/> <input id="ar_play_file_button" class="button" type="button" value="<?php echo $ar_logo_file_txt;?>" <?= $disabled;?> /> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('ar_play_file').value = ''"></div>
                </div>
                <br  clear="all">
                <br>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Custom Pause Button', $ar_plugin_id );
                            $ar_logo_file_txt = __('AR Pause File', $ar_plugin_id);
                            
                            ?></strong></div>
                      <div style="float:left;padding-right:40px"><input type="text" name="ar_pause_file" id="ar_pause_file" class="regular-text" value="<?php echo get_option('ar_pause_file'); ?>" <?= $disabled;?>/> <input id="ar_pause_file_button" class="button" type="button" value="<?php echo $ar_logo_file_txt;?>" <?= $disabled;?> /> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('ar_pause_file').value = ''"></div>
                </div>
                <br  clear="all">
                <br>
                <div>
                      <div style="float:left;"><strong>
                          <?php
                            _e('Custom QR Logo', $ar_plugin_id );
                            $qr_logo_file_txt = __('QR Logo File', $ar_plugin_id);
                            
                            ?></strong><br><?php _e('JPG file 250 x 250px', $ar_plugin_id);?> - <?php _e('Requires Imagick PHP Extension', $ar_plugin_id);?></div>
                      <div style="float:left;"><input type="text" name="ar_qr_file" id="ar_qr_file" class="regular-text" value="<?php echo get_option('ar_qr_file'); ?>" <?= $disabled;?>> <input id="ar_qr_file_button" class="button" type="button" value="<?php echo $qr_logo_file_txt;?>" <?= $disabled;?>/> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('ar_qr_file').value = ''"></div>
                </div>
                <br  clear="all">
                <br>
                <?php
                //Global Checkbox Fields 
                $field_array = array('ar_hide_dimensions' => 'Hide Dimensions', 'ar_hide_arview' => 'Hide AR View', 'ar_hide_qrcode' => 'Hide QR Code', 'ar_hide_reset' => 'Hide Reset', 'ar_hide_fullscreen' => 'Disable Fullscreen', 'ar_scene_viewer' => 'Android - Prioritise Scene Viewer over WebXR', 'ar_open_tabs_remember' =>'Disable Remembering Open Tabs');
                if ($ar_plugin_id!='ar-for-woocommerce'){
                    $field_array['ar_no_posts'] = 'Hide Posts';
                }
                $count = 0;
                foreach ($field_array as $field => $title){
                    $count++;
                ?>
                <div>
                  <div style="width:160px;float:left;"><label for="<?php echo $field;?>">
                      <?php
                            _e($title, $ar_plugin_id );
                            
                            ?></label></div>
                  <div style="float:left;padding-right:20px"><input type="checkbox" id="<?php echo $field;?>" name="<?php echo $field;?>" class="ar-ui-toggle"  value="1" <?php if (get_option($field)=='1'){echo 'checked'; } ?> <?= $disabled;?>/></div>
                </div>
                <?php 
                    if ($count==3){
                        $count=0;
                        echo '<br  clear="all"><br>'; 
                    } 
                } ?>
                <br  clear="all">
                <br>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('QR Code Destination', $ar_plugin_id );
                            
                            ?></strong></div>
                      <div style="float:left;padding-right:40px"><select id="ar_qr_destination" name="ar_qr_destination" class="ar-input" <?= $disabled;?>>
                          <option value="parent-page">Parent Page</option>
                          <option value="model-viewer" <?php
                            if (get_option('ar_qr_destination')=='model-viewer'){
                                echo 'selected';
                            }
                          ?>
                          >AR View</option>
                          </select>
                      </div>
                </div>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('Dimension Units', $ar_plugin_id );
                            $ar_dimensions_units = get_option('ar_dimensions_units');
                            ?></strong></div>
                      <div style="float:left;"><select id="ar_dimensions_units" name="ar_dimensions_units" class="ar-input" <?= $disabled;?>>
                          <option value="">Meters</option>
                          <option value="cm" <?php
                            if ($ar_dimensions_units=='cm'){
                                echo 'selected';
                            }
                          ?>
                          >Centimeters</option>
                          <option value="mm" <?php
                            if ($ar_dimensions_units=='mm'){
                                echo 'selected';
                            }
                          ?>
                          >Milimeters</option>
                          <option value="inches" <?php
                            if (($ar_dimensions_units=='inches')OR(get_option('ar_dimensions_inches')==true)){
                                echo 'selected';
                            }
                          ?>
                          >Inches</option>
                          </select>
                      </div>
                </div>
                <br  clear="all">
                <br>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('View in AR Text', $ar_plugin_id );
                            
                            ?></strong></div>
                      <div style="float:left;padding-right:40px">
                          <input id="ar_view_in_ar" name="ar_view_in_ar" class="ar-input" style="width:120px" value="<?= get_option('ar_view_in_ar'); ?>" <?= $disabled;?>>
                      </div>
                </div>
                <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                            _e('View in 3D Text', $ar_plugin_id );
                            
                            ?></strong></div>
                      <div style="float:left;padding-right:40px">
                          <input id="ar_view_in_3d" name="ar_view_in_3d" class="ar-input" style="width:120px" value="<?= get_option('ar_view_in_3d'); ?>" <?= $disabled;?>>
                      </div>
                </div>
                
                <br clear="all">
                <h3> <?php
                _e('Element Positions and CSS Styles', $ar_plugin_id );
                if ($disabled!=''){echo ' - '.__('Premium Plans Only', $ar_plugin_id);}
                
                ?></h3>
                <?php //CSS Positions
                $ar_css_positions = get_option('ar_css_positions');
                $count=0;
                foreach ($ar_css_names as $k => $v){
                    $count++;
                    ?>
                    <div>
                      <div style="width:160px;float:left;"><strong>
                          <?php
                                _e($k, $ar_plugin_id );
                                
                                ?> </strong></div>
                      <div style="float:left;padding-right:40px"><select id="ar_css_positions[<?=$k;?>]" name="ar_css_positions[<?=$k;?>]" class="ar-input" <?= $disabled;?>>
                          <option value="">Default</option>
                          <?php 
                          foreach ($ar_css_styles as $pos => $css){
                            echo '<option value = "'.$pos.'"';
                            if (is_array($ar_css_positions)){
                                if ($ar_css_positions[$k]==$pos){echo ' selected';}
                            }
                            echo '>'.$pos.'</option>';
                          }?>
                          
                          </select></div>
                    </div>
                    <?php 
                    if ($count==2){
                        $count=0;
                        echo '<br  clear="all"><br>'; 
                    }
                
                }
                ?>
                
                <br  clear="all"><br>
                <div>
                    <div style="width:160px;float:left;"><strong>
                    <?php
                    $ar_css = get_option('ar_css');
                    if ($ar_css==''){
                      $ar_css=ar_curl(esc_url( plugins_url( "assets/css/ar-display-custom.css", __FILE__ ) ));
                    }
                    _e('CSS Styling', $ar_plugin_id );
                    
                    ?> </strong></div>
                    <div style="float:left;"><textarea id="ar_css" name="ar_css" style="width: 450px; height: 200px;" <?= $disabled;?>><?php echo $ar_css; ?></textarea></div>
                </div>
                <br  clear="all"><br>
                
                <?php 
                if ($ar_plugin_id=='ar-for-wordpress'){
                    submit_button();
                } ?>
            </div>
        </div>
        <div class="licence_key" id="key" style="float:left;">
        <?php
        if (isset($_REQUEST['tab'])){
            if ($_REQUEST['tab']=='ar_display'){
                echo $woocommerce_featured_image;
            }
        }
        
        
                //Copy the Woocommerce Featured Product Template to Theme
                //if ($ar_plugin_id=='ar-for-woocommerce'){ 
                if ($ar_wc_active==true){    
                    ?>
                        <h3><?php _e('Set the WooCommerce Featured Product Image to AR Model',$ar_plugin_id);?></h3>
                        <?php _e('Copy the woocommerce single product template found in the AR for Woocommerce plugin "templates" folder to your theme.',$ar_plugin_id);?></p>
                    <?php 
                    $template_file = get_stylesheet_directory() . '/woocommerce/single-product/product-image.php';
                    // Check if the file exists
                    if (!file_exists($template_file) OR (isset($_POST['delete_template_file']))) {
                    ?>
                        <button id="copy-file-btn" type="button" class="button" style="float:left;margin-right:20px"><?php _e('Copy File',$ar_plugin_id);?></button>
                        <script>
                            jQuery(document).ready(function($) {
                              $('#copy-file-btn').click(function() {
                                var btn = $(this);
                                btn.text('<?php _e('Copying...',$ar_plugin_id);?>');
                                var data = {
                                  action: 'ar_copy_file_action',
                                };
                                $.post(ajaxurl, data, function(response) {
                                btn.text(response);
                                });
                              });
                            });
                        </script>
                        
                    <?php 
                    if (isset($_POST['delete_template_file'])){
                            check_and_delete_woocommerce_template();
                        }
                    }else{
                        
                            check_and_delete_woocommerce_template();
                        
                    }
                    ?>
                    <br  clear="all"><br  clear="all">
                <hr>
                <br  clear="all">
                <?php
                } ?>
                
        <?php 
        
        echo $ar_rate_this_plugin;
        //Changelog latest 3 updates
        $limit=3;
        echo '<br><br><hr><h3>'.__('What\'s New', $ar_plugin_id ).'</h3>';
        echo ar_changelog_retrieve($limit);
        
        echo '<br><br><hr><h3>Shortcodes</h3>';
        echo $shortcode_examples;
        /*?>
        <h3><?php
        _e('Dimensions', $ar_plugin_id );
        ?></h3> 
        
        <p><?php 
        _e('The dimensions show the X, Y, Z, (width, height, depth) directly from the 3D model file. You can turn this off site wide and/or on a per model basis.', $ar_plugin_id );
        */
        ?></p>
        <?php if ($ar_whitelabel!=true){ ?>
            <hr>
            <p class = "further_info"> <?php
            _e('For further information and assistance using the plugin and converting your models please visit', $ar_plugin_id );
            
            ?> <a href = "https://augmentedrealityplugins.com" target = "_blank">https://augmentedrealityplugins.com</a></p>
        <?php } ?>
        </div>
        <?php if ($ar_whitelabel!=true){ 
        $licence_result = ar_licence_check();
        if (substr($licence_result,0,5)!='Valid'){?>
            <div style="float:left;"><a href="https://augmentedrealityplugins.com" target="_blank"><img src="https://augmentedrealityplugins.com/ar/images/ar_wordpress_ad.jpg" style="padding:10px 10px 10px 0px;"><img src="https://augmentedrealityplugins.com/ar/images/ar_woocommerce_ad.jpg" style="padding:10px 10px 10px 0px;"></a></div>
        <?php } 
        }
        wp_enqueue_media();
        ?>
        <br clear="all">
        <script>
            jQuery(document).ready(function($){
            
            var custom_uploader;
            
            $('#ar_wl_file_button, #ar_view_file_button, #ar_qr_file_button, #ar_fullscreen_file_button, #ar_play_file_button, #ar_pause_file_button').click(function(e) {
                var button_clicked = event.target.id;
                var target = button_clicked.substr(0, button_clicked.length -7);
                e.preventDefault();
                //Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    
                    multiple: false
                });
        
                //When a file is selected, grab the URL and set it as the text field's value
                custom_uploader.on('select', function() {
                    var attachments = custom_uploader.state().get('selection').map( 
                       function( attachment ) {
                           attachment.toJSON();
                           return attachment;
                      });
                     $.each(attachments, function( index, attachement ) {
                          
                          var fileurl=attachments[index].attributes.url;
                            var filetype = fileurl.substring(fileurl.length - 4, fileurl.length).toLowerCase();
                            if ((filetype === '.jpg') || (filetype === '.png')){
                                $('#' + target).val(fileurl);  
                                
                            }else{
                                <?php
                           $js_alert = __('Invalid file type. Please choose a JPG or PNG file.', $ar_plugin_id );
                        ?>
                                 alert('<?php echo $js_alert;?>');
                            }
                     });
                });
                //Open the uploader dialog
                custom_uploader.open();
            });  
        });
        </script>
        <?php
    } 
}
//********* Copy Woocommerce Template File ********//
add_action( 'wp_ajax_ar_copy_file_action', 'ar_copy_file' );
add_action( 'wp_ajax_nopriv_ar_copy_file_action', 'ar_copy_file' );

if (!function_exists('ar_copy_file')){
    function ar_copy_file() {
      // Define the file to copy
      $file_to_copy = plugin_dir_path( __FILE__ ) . 'templates/woocommerce/single-product/product-image.php';
    
      // Define the destination path
      $destination_path = get_stylesheet_directory() . '/woocommerce/single-product/product-image.php';
    
    // Create the destination directory if it doesn't exist
      $destination_directory = dirname( $destination_path );
      if ( ! file_exists( $destination_directory ) ) {
        mkdir( $destination_directory, 0755, true );
      }
      
      // Copy the file
      if ( ! file_exists( $destination_path ) ) {
        if ( copy( $file_to_copy, $destination_path ) ) {
          _e('Copied',$ar_plugin_id);
        } else {
          _e('File copying failed',$ar_plugin_id);
        }
      } else {
        _e('File already exists in your theme',$ar_plugin_id);
      }
    
      wp_die();
    }
}
//********* Delete Woocommerce Template File ********//
if (!function_exists('check_and_delete_woocommerce_template')){
    function check_and_delete_woocommerce_template() {
        global $ar_plugin_id, $_POST;
        // Get the path to the template file
        $template_file = get_stylesheet_directory() . '/woocommerce/single-product/product-image.php';
        $ar_delete_template_css ="display:block";
        // If the file exists, display a button to delete the file
        if (isset($_POST['delete_template_file'])) {
            
            // If the delete button was clicked, delete the file
            if (unlink($template_file)) {
                echo '<div class="notice notice-success"><p>File deleted successfully.</p></div>';
                $ar_delete_template_css ="display:none";
            } else {
                echo '<div class="notice notice-error"><p>Failed to delete the file.</p></div>';
            }
        }
        echo '<div id="ar_delete_template" style="'.$ar_delete_template_css.'">';
        // Display the delete button
        echo '<form method="post">';
        echo '<input type="hidden" name="delete_template_file" value="1">';
        echo '<button type="submit" class="button button-danger" onclick="return confirm(\''.__('Are you sure you want to delete the woocommerce single product template file from your theme?', $ar_plugin_id ).'\');">Delete File</button>';
        echo '</form>';
        echo '</div>';
    }
}

/*** QR Code + Logo Generator */
if (!function_exists('ar_qr_code')){
    function ar_qr_code($logo,$id,$data='') {
        if ( filter_var( ini_get( 'allow_url_fopen' ), FILTER_VALIDATE_BOOLEAN ) ){
            $data = $data ? $data : esc_url( get_permalink($id) );
            $size = isset($size) ? $size : '250x250';
            $logo = isset($logo) ? $logo : FALSE;
            
            
            if(IMGCK_ENABLED){
                //wp_die('here');
                $options = new LogoOptions;

                $options->version          = QRCode::VERSION_AUTO;
                $options->eccLevel         = QRCode::ECC_H;
                $options->imageBase64      = false;
                $options->logoSpaceWidth   = 10;
                $options->logoSpaceHeight  = 10;
                $options->scale            = 5;
                $options->imageTransparent = false;

                if($logo !== FALSE){
                    $logo_data = ar_curl($logo);
                    $logo_str = @imagecreatefromstring($logo_data);
                    //die(gettype($logo_str));
                    
                //header('Content-type: image/png');

                    $qrOutputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($data));

                // dump the output, with an additional logo
                    $QR = $qrOutputInterface->dump(null, $logo_str);
                    //var_dump($logo_str);
                    //wp_die($QR);
                    if(strstr($QR, 'Error:')){
                        $QR = ar_qr_code_api($logo, $data);
                    }

                    return $QR;

                }

                
            } else {
                //wp_die('there');
                //use google api to generate qr
                return ar_qr_code_api($logo,$data);
            }            
            
        }
    }
}

if (!function_exists('ar_qr_code_api')){
    function ar_qr_code_api($logo, $data){
        $data = $data ? $data : 'https://augmentedrealityplugins.com';
        $size = isset($size) ? $size : '250x250';
        $logo = isset($logo) ? $logo : FALSE;

        if (function_exists('imagecreatefrompng')) {
            // GD library is enabled
            //google qr 'https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs='.$size.'&chl='.urlencode($data)
            
            
            $logo_url = urlencode($logo);
            $qr_source_url = 'https://quickchart.io/qr?text='.urlencode($data);
            $qr_source_url .= '&centerImageUrl='.$logo_url;
            $qr_source_url .= '&centerImageSizeRatio=0.35';
            //wp_die($qr_source_url);
            $QR = @imagecreatefrompng($qr_source_url);
            //var_dump($QR);
            //wp_die($QR);
        
            /*if($logo !== FALSE && $QR){
                $logo_data = ar_curl($logo);
                $logo = imagecreatefromstring($logo_data);
                if ($logo !== false) {
                    $QR_width = imagesx($QR);
                    $QR_height = imagesy($QR);
                    
                    $logo_width = imagesx($logo);
                    $logo_height = imagesy($logo);
                    
                    // Scale logo to fit in the QR Code
                    $logo_qr_width = intval($QR_width/3);
                    $scale = $logo_width/$logo_qr_width;
                    $logo_qr_height = intval($logo_height/$scale);
                    imagecopyresampled($QR, $logo, intval($QR_width/3), intval($QR_height/3), 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
                }
            }*/

            if($QR){                   
                ob_start();
                imagepng($QR);
                $imgData=ob_get_clean();
                if (!is_bool($QR)){
                    imagedestroy($QR);
                }
                return $imgData;

            }else{
                return '';
            }
        }else{
                //failed
                return '';
        }
    }
}

//AR View Standalone - Loads the AR Model viewer and triggers the AR view automatically
add_action( 'wp_head', 'ar_standalone' );
if (!function_exists('ar_standalone')){
    function ar_standalone() {
        global $wpdb, $_REQUEST;
        if ((isset($_REQUEST['ar-view']))OR(isset($_REQUEST['ar-cat']))){
            if ($_REQUEST['ar-view']!=''){
                $model_id = absint($_REQUEST['ar-view']);
                $output = do_shortcode ('[ardisplay id=\''.$model_id.'\']');
            }elseif ($_REQUEST['ar-cat']!=''){
                $cat_id = absint($_REQUEST['ar-cat']);
                $output = do_shortcode ('[ardisplay cat=\''.$cat_id.'\']');
            }
            echo '<center><span id="ar_standalone_loading">Loading</span></center>
                <div id="ar_standalone_container" style="/*opacity: 0;*/">'.$output.'</div>';
            
            //Trigger the AR button to open 
            ?>
            <script>
                const modelViewer = document.getElementById("model_<?php echo $model_id;?>");
                function checkagain() {
                    if (modelViewer.modelIsVisible === true) {
                        document.getElementById("ar-button_<?php echo $model_id; ?>").click();
                    }else {
                        checkagain2 = setTimeout(ar_open, 2);
                    }
                }
            
                function ar_open() {
                    if (modelViewer.modelIsVisible === true) {
                        document.getElementById("ar-button_<?php echo $model_id; ?>").click();
                    }else {
                        checkagain1 = setTimeout(checkagain, 2);
                    }
                } 
            
                modelViewer.addEventListener("load", function() {
                    ar_open();
                    document.getElementById("ar_standalone_loading").style.display="none";
                    document.getElementById("ar_standalone_container").style.opacity="100";
                });
                
                var count = 0;
                setInterval(function(){
                    count++;
                    document.getElementById('ar_standalone_loading').innerHTML = "Loading" + new Array(count % 5).join('.');
                }, 1000);
            </script>
            <?php
            echo '<center><a href="'.get_site_url().'"><button type="button" class="button">Return to Site</button></a></center>';
            if (ob_get_contents()) {
                if (ob_get_length() && !ini_get('zlib.output_compression')) {
                    while (ob_get_level()) {
                        ob_end_flush();
                    }
                }
            }
            exit;
        }
    }
}

remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
    if (ob_get_contents()) {
        if (ob_get_length() && !ini_get('zlib.output_compression')) {
            while (ob_get_level()) {
                @ob_end_flush();
            }
        }
    }
} );

//Encode custom CSS code for importing into text field
if (!function_exists('ar_encodeURIComponent')){
    function ar_encodeURIComponent($str) {
        $unescaped = array(
            '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
            '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
        );
        $reserved = array(
            '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
            '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
        );
        $score = array(
            '%23'=>'#'
        );
        return strtr(rawurlencode($str), array_merge($reserved,$unescaped,$score));
    }
}
/********** Whats New Page **********/
if (!function_exists('ar_whats_new')){
    function ar_whats_new() {
        global $ar_version, $ar_plugin_id, $woocommerce_featured_image, $ar_whitelabel;
        $ar_logo = esc_url( plugins_url( 'assets/images/Ar_logo.png', __FILE__ ) ); 
        $ar_wl_logo = get_option('ar_wl_file'); 
        ?>
        <div class="licence_key" id="key" style="float:left;">
        <?php 
        if ($ar_whitelabel!=true){ ?>   
            <div class="ar_site_logo">
                <a href = "https://augmentedrealityplugins.com" target = "_blank">              
                <img src="<?php echo $ar_logo;?>" style="width:300px; padding:0px;float:left" />
                </a>
            </div>
            <br clear="all">
            <?php
            if ($ar_plugin_id=='ar-for-wordpress'){
                echo '<h1>'.__('AR For WordPress', 'ar-for-wordpress' ).' - '.__('What\'s New', 'ar-for-wordpress' ).'</h1>';
                    
            }elseif ($ar_plugin_id=='ar-for-woocommerce'){
                echo '<h1>'.__('AR For Woocommerce', 'ar-for-woocommerce' ).' - '.__('What\'s New', 'ar-for-woocommerce' ).'</h1>';
                
            }
            ?>
        <?php }else{
        //White Label Logo 
        ?>
        <div>
            <?php 
            
            if ($ar_wl_logo){
            ?>
                <div class="ar_site_logo">
                    <img src="<?php echo $ar_wl_logo;?>" style="max-width:300px; padding:0px;float:left" />
                    <input type="hidden" name="ar_wl_file" id="ar_wl_file" class="regular-text" value="<?php echo $ar_wl_logo; ?>">
                </div>
                <br clear="all">
            <?php }
             ?>
            </div>
            <br  clear="all">
        <?php }?>
        <div class="licence_page" style="min-width:400px;>
        <br clear="all">
        <?php
        $limit=10;
        echo ar_changelog_retrieve($limit);
        
        ?>
        </div>
        <?php
        if (isset($_REQUEST['tab'])){
            if ($_REQUEST['tab']=='ar_display'){
                echo $woocommerce_featured_image;
            }
        }
        ?>
        
        <?php if ($ar_whitelabel!=true){ ?>
        <hr>
        <p class = "further_info"> <?php
            _e('For further information and assistance using the plugin and converting your models please visit', $ar_plugin_id );
            
            ?> <a href = "https://augmentedrealityplugins.com" target = "_blank">https://augmentedrealityplugins.com</a></p>
        <?php } ?>
        </div>
        <?php if ($ar_whitelabel!=true){ 
            $licence_result = ar_licence_check();
        if (substr($licence_result,0,5)!='Valid'){
        ?>
            <div style="float:left;"><a href="https://augmentedrealityplugins.com" target="_blank"><img src="https://augmentedrealityplugins.com/ar/images/ar_wordpress_ad.jpg" style="padding:10px 10px 10px 0px;"><img src="https://augmentedrealityplugins.com/ar/images/ar_woocommerce_ad.jpg" style="padding:10px 10px 10px 0px;"></a></div>
        <?php } 
        }
    }
}

/********** Whats New Change Log **********/
if (!function_exists('ar_changelog_retrieve')){
    function ar_changelog_retrieve($limit) {
        global $ar_plugin_id;
        $ar_readme= ar_curl(esc_url( plugins_url( 'readme.txt', __FILE__ ) ));
        $ar_changelog_pos = strpos($ar_readme, '== Changelog ==')+15;
        if ($ar_changelog_pos>15){
            $ar_changelog = substr($ar_readme, $ar_changelog_pos);
            $ar_changelog_array = array_filter(explode('=',$ar_changelog));
            if (isset($limit)){
                $ar_changelog_array = array_splice($ar_changelog_array, 0,($limit *2)+1);
            }
            $ar_highlight = false;
            $count=0;
            $output ='';
            foreach ($ar_changelog_array as $k => $v){
                if (strpos($v,'*')>=1){
                    $ar_highlight_style ='';
                    if ($ar_highlight == true){
                        $ar_highlight_style = 'font-weight:bold;font-size:16px';
                    }
                    $output .= '<ul style="list-style:disc;">';
                    $v = explode('*',$v);
                    $v = implode('<li style="margin-left:40px; '.$ar_highlight_style.'">',$v);
                    $output .= $v.'</ul>';
                    $count ++;
                }else{
                    if ($count == 0){
                        $output .= '<h2>'.$v.'</h2>';
                        $ar_highlight = true;
                    }else{
                        $output .= '<h3>'.$v.'</h3>';
                        $ar_highlight = false;
                    }
                }
            }
        }
        if ($ar_plugin_id=='ar-for-wordpress'){
            $output .= '<a href="https://augmentedrealityplugins.com/support/whats-new/" target="_blank">'.__('Please visit Augmented Reality Plugins to view the full change log.','ar-for-wordpress').'</a>';
        }elseif ($ar_plugin_id=='ar-for-woocommerce'){
            $output .= '<a href="https://augmentedrealityplugins.com/support/whats-new/" target="_blank">'.__('Please visit Augmented Reality Plugins to view the full change log.','ar-for-woocommerce').'</a>';
        }
        return $output;
    }
}
/******* Model Count***********/
if (!function_exists('ar_model_count')){
    function ar_model_count(){
        global $wpdb, $ar_wp_active, $ar_wc_active;
        $wp_count = 0;
        $wc_count = 0;
        $model_count = 0;
        if ($ar_wc_active== true){
            $result = $wpdb->get_col( "
                SELECT COUNT(p.ID)
                FROM {$wpdb->prefix}posts as p
                INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
                WHERE p.post_type LIKE '%product%'
                AND p.post_status = 'publish'
                AND pm.meta_key = '_ar_display'
                AND pm.meta_value = '1'
            " );
            $wc_count = reset($result);
        }
        if ($ar_wp_active == true){
            $wp_count = wp_count_posts( 'armodels' )->publish;
        }
        $model_count += $wp_count + $wc_count;
        return $model_count;
    }
}
/********** Curl Get File **********/
if (!function_exists('ar_curl')){
    function ar_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            $data = file_get_contents($url);
        }
        
        curl_close($ch);
        return $data;
    }
}

/********** Reload the Gutenburg editor when AR model post is updated **********/
if (!function_exists('ar_reload_page_after_publish')){
    function ar_reload_page_after_publish() {
        global $post;
    
        // Check if the current post type is 'armodels'
        if ($post && $post->post_type === 'armodels') {
            ?>
            <script>
                (function ($) {
                    $(document).ready(function () {
                        // Add the event listener for post updates
                        $(document).on('click', '#publish, .editor-post-publish-button, #save-post', function (e) {
                            // Check if the post is being published or updated
                            if ($('#original_post_status').val() !== $('#post_status').val()) {
                                // Reload the page after a short delay (adjust as needed)
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            }
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }
    }

    add_action('admin_footer', 'ar_reload_page_after_publish');
}


/*********** Display the AR Alternate Model ***********/
if (!function_exists('ar_alternative_model')){
    function ar_alternative_model($alt_id, $suffix=''){
        
        $output='';
        //if (($model_array['glb_file']!='')OR($model_array['usdz_file']!='')){
        global $wp, $ar_plugin_id, $ar_whitelabel, $ar_css_names, $ar_css_styles;
        $model_style='';
        $model_array = array();
        $atts = array();
        $model_array['model_id'] = $alt_id;
        $atts['id'] = $alt_id;
        $model_id =  $model_array['model_id'];
        
        $model_array['usdz_file'] = get_post_meta($atts['id'], '_usdz_file'.$suffix, true );
        $model_array['glb_file'] = get_post_meta($atts['id'], '_glb_file'.$suffix, true );
        $model_array['ar_variants'] = get_post_meta($atts['id'], '_ar_variants'.$suffix, true );
        $model_array['ar_rotate'] = get_post_meta($atts['id'], '_ar_rotate'.$suffix, true );
        $model_array['ar_prompt'] = get_post_meta($atts['id'], '_ar_prompt'.$suffix, true );
        $model_array['ar_x'] = get_post_meta($atts['id'], '_ar_x'.$suffix, true );
        $model_array['ar_y'] = get_post_meta($atts['id'], '_ar_y'.$suffix, true );
        $model_array['ar_z'] = get_post_meta($atts['id'], '_ar_z'.$suffix, true );
        $model_array['ar_field_of_view'] = get_post_meta($atts['id'], '_ar_field_of_view'.$suffix, true );
        $model_array['ar_zoom_out'] = get_post_meta($atts['id'], '_ar_zoom_out'.$suffix, true );
        $model_array['ar_zoom_in'] = get_post_meta($atts['id'], '_ar_zoom_in'.$suffix, true );
        $model_array['ar_resizing'] = get_post_meta($atts['id'], '_ar_resizing'.$suffix, true );
        $model_array['ar_view_hide'] = get_post_meta($atts['id'], '_ar_view_hide'.$suffix, true );
        $model_array['ar_autoplay'] = get_post_meta($atts['id'], '_ar_autoplay'.$suffix, true );
        $model_array['ar_disable_zoom'] = get_post_meta($atts['id'], '_ar_disable_zoom'.$suffix, true );
        $model_array['ar_scene_viewer']=get_option('ar_scene_viewer');
        $model_array['ar_exposure']=get_post_meta($atts['id'], '_ar_exposure'.$suffix, true );
        $model_array['ar_shadow_intensity']=get_post_meta($atts['id'], '_ar_shadow_intensity'.$suffix, true );
        $model_array['ar_shadow_softness']=get_post_meta($atts['id'], '_ar_shadow_softness'.$suffix, true );
        $model_array['ar_camera_orbit']=get_post_meta($atts['id'], '_ar_camera_orbit'.$suffix, true );
        $model_array['ar_environment_image']=get_post_meta($atts['id'], '_ar_environment_image'.$suffix, true );
        $model_array['ar_emissive']=get_post_meta($atts['id'], '_ar_emissive'.$suffix, true );
        $model_array['ar_light_color']=get_post_meta($atts['id'], '_ar_light_color'.$suffix, true );
        $model_array['ar_rotate_limit'] = get_post_meta($atts['id'], '_ar_rotate_limit'.$suffix, true );
        $model_array['ar_compass_top_value'] = get_post_meta($atts['id'], '_ar_compass_top_value'.$suffix, true );
        $model_array['ar_compass_bottom_value'] = get_post_meta($atts['id'], '_ar_compass_bottom_value'.$suffix, true );
        $model_array['ar_compass_left_value'] = get_post_meta($atts['id'], '_ar_compass_left_value'.$suffix, true );
        $model_array['ar_compass_right_value'] = get_post_meta($atts['id'], '_ar_compass_right_value'.$suffix, true );
        $model_array['ar_animation'] = get_post_meta($atts['id'], '_ar_animation'.$suffix, true );
        $model_array['ar_animation_selection'] = get_post_meta($atts['id'], '_ar_animation_selection'.$suffix, true );
    
        $model_array['skybox_file'] = get_post_meta($atts['id'], '_skybox_file'.$suffix, true );

        if (get_post_meta( $atts['id'], '_ar_environment'.$suffix, true )){
            $model_array['ar_environment']='environment-image="'.get_post_meta( $atts['id'], '_ar_environment'.$suffix, true ).'"';
        }else{
            $model_array['ar_environment']='';
        }

        if ($model_array['skybox_file']!=''){
            $model_array['skybox_file']=' skybox-image="'.$model_array['skybox_file'].'"';
        }        
        if ($model_array['ar_resizing']==1){
            $model_array['ar_resizing']=' ar-scale="fixed"';
        }
        
        if ($model_array['ar_scene_viewer']==1){
            $viewers = 'scene-viewer webxr quick-look';
        }else{
            $viewers = 'webxr scene-viewer quick-look';
        }

        
        $show_ar=' ar ar-modes="'.$viewers.'" ';
        
        if ($model_array['ar_autoplay']!=''){
            $model_array['ar_autoplay'] = 'autoplay';                
        }
        if ($model_array['ar_disable_zoom']!=''){
            $model_array['ar_disable_zoom'] = 'disable-zoom';                
        }
        if ($model_array['ar_field_of_view']!=''){
            $model_array['ar_field_of_view'] = 'field-of-view="'.$model_array['ar_field_of_view'].'deg"';                
        }else{
            $model_array['ar_field_of_view'] = 'field-of-view=""';
        }
        if (!isset($model_array['ar_qr_image'])){
            $model_array['ar_qr_image']='';
        }
        $min_theta = 'auto';
        $min_pi = 'auto';
        $min_zoom = '20%';
        $max_theta = 'Infinity';
        $max_pi = 'auto';
        $max_zoom = '300';
        if (($model_array['ar_zoom_in']!='')AND($model_array['ar_zoom_in']!='default')){
            $model_array['ar_zoom_in'] = 100 - $model_array['ar_zoom_in'];
            //$ar_zoom_in_output = 'min-camera-orbit="auto auto '.$model_array['ar_zoom_in'].'%"';  
            $min_zoom = $model_array['ar_zoom_in'].'%"';
        }else{
            //$ar_zoom_out_output = 'min-camera-orbit="Infinity auto 20%"';
        }
        
        if (($model_array['ar_zoom_out']!='')AND($model_array['ar_zoom_out']!='default')){
            $model_array['ar_zoom_out'] = (($model_array['ar_zoom_out']/100)*400)+100;
            $ar_zoom_out_output = 'max-camera-orbit="Infinity auto '.$model_array['ar_zoom_out'].'%"'; 
            $max_zoom = $model_array['ar_zoom_out'].'%"';
        }else{
            //$ar_zoom_in_output = 'max-camera-orbit="Infinity auto 300%"';
        }
        
        //set the X and Y rotation limits in min-camera-orbit and max-camera-orbit
        //
        //
        //
        if ($model_array['ar_rotate_limit']!=''){
            if ($model_array['ar_compass_top_value']!=''){
                $min_pi = $model_array['ar_compass_top_value'];
            } 
            if ($model_array['ar_compass_bottom_value']!=''){
                $max_pi = $model_array['ar_compass_bottom_value'];
            }
            if ($model_array['ar_compass_left_value']!=''){
                $min_theta = $model_array['ar_compass_left_value'];
            }
            if ($model_array['ar_compass_right_value']!=''){
                $max_theta = $model_array['ar_compass_right_value'];
            } 
        }

        $ar_zoom_out_output = 'min-camera-orbit="'.$min_theta.' '.$min_pi.' '.$min_zoom.'"';
        $ar_zoom_in_output = 'max-camera-orbit="'.$max_theta.' '.$max_pi.' '.$max_zoom.'"';
        
        
        if ($model_array['ar_exposure']!=''){
            $model_array['ar_exposure'] = 'exposure="'.$model_array['ar_exposure'].'"';                
        }
        if ($model_array['ar_shadow_intensity']!=''){
            $model_array['ar_shadow_intensity'] = 'shadow-intensity="'.$model_array['ar_shadow_intensity'].'"';                
        }
        if ($model_array['ar_shadow_softness']!=''){
            $model_array['ar_shadow_softness'] = 'shadow-softness="'.$model_array['ar_shadow_softness'].'"';                
        }
        if ($model_array['ar_camera_orbit']!=''){
            $model_array['ar_camera_orbit_reset'] = $model_array['ar_camera_orbit'];
            $model_array['ar_camera_orbit'] = 'camera-orbit="'.$model_array['ar_camera_orbit'].'"';                
        }else{
            $model_array['ar_camera_orbit_reset']='';
        }
        if ($model_array['ar_environment_image']!=''){
            $model_array['ar_environment_image'] = 'environment-image="legacy"';                
        }
        if ($model_array['ar_emissive']!=''){
            $model_array['ar_emissive'] = ' emissive ';                
        }
        if ($model_array['ar_light_color']!=''){
            $model_array['ar_light_color'] = 'light-color="'.$model_array['ar_light_color'].'"';               
        }
        $output.='<div class="ar_alternative_model_container">';
        $output.='<model-viewer id="model_'.$model_array['model_id'].'" '.$show_ar;   
        $output .= '
        ios-src="'.$model_array['usdz_file'].'" src="'. $model_array['glb_file'].'" 
        '. $model_array['skybox_file'].'
        '. $model_array['ar_environment'].'
        '. $model_array['ar_resizing'].'
        '. $model_array['ar_field_of_view'].'
        '. $ar_zoom_in_output.'
        '. $ar_zoom_out_output.'
        '. $model_array['ar_camera_orbit'].'
        '. $model_array['ar_exposure'].'
        '. $model_array['ar_shadow_intensity'].'
        '. $model_array['ar_shadow_softness'].'
        '. $model_array['ar_environment_image'].' 
        '. $model_array['ar_emissive'].'  
        '. $model_array['ar_light_color'].'  
        poster="'.esc_url( get_the_post_thumbnail_url($model_array['model_id']) ).'"
        alt="AR Display 3D model" 
        class="ar-display-model-viewer" 
        quick-look-browsers="safari chrome" 
        ';

        $output .= $model_array['ar_disable_zoom'].'>';
        
        
        $output.='<button slot="ar-button" data-id="'.$model_array['model_id'].'" class="ar-button ar-button-default " id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( plugins_url( "assets/images/ar-view-btn.png", __FILE__ ) ).'" class="ar-button-img"></button>';
           

        $output.='<input type="hidden" id="src_'.$model_array['model_id'].'" value="'. $model_array['glb_file'].'">';
        
        $output.='</model-viewer></div>';

        if ((is_numeric($model_array['ar_x']))AND(is_numeric($model_array['ar_y']))AND(is_numeric($model_array['ar_z']))){
            $output.='<script>
            const modelViewerTransform'.$model_array['model_id'].' = document.querySelector("model-viewer#model_'.$model_array['model_id'].'");
            const updateScale'.$model_array['model_id'].' = () => {
              modelViewerTransform'.$model_array['model_id'].'.scale = \''.$model_array['ar_x'].' '.$model_array['ar_y'].' '.$model_array['ar_z'].'\';
            };
            updateScale'.$model_array['model_id'].'();
            </script>';
            $ar_scale_js = 1;
        }
        //wp_die($output);
        return $output;
    }
}

if (get_option('ar_no_posts')){
 // Set the default visibility of new models to be private
    if (!function_exists('set_default_visibility_armodels')){
        add_filter('default_content', 'set_default_visibility_armodels', 10, 2);

        function set_default_visibility_armodels($content, $post) {
            // Check if the post type is 'armodels'
            if ($post->post_type === 'armodels') {
                // Set the default post status to 'private'
                $post->post_status = 'private';
            }
            return $content;
        }
    }
}

?>