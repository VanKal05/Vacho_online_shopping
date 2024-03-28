<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//REST API - Field name arrays
$ar_api_array = array('title','author','date','status');
$ar_api_meta_array = array('usdz_file','glb_file','skybox_file','ar_environment','ar_placement','ar_x','ar_y','ar_z','ar_field_of_view','ar_zoom_in','ar_zoom_out','ar_exposure','ar_shadow_intensity','ar_shadow_softness','ar_rotate','ar_variants','ar_environment_image','ar_resizing','ar_view_hide','ar_qr_hide','ar_hide_dimensions','ar_animation','ar_autoplay','ar_cta','ar_cta_url');

//REST API - Get AR Models
add_action( 'rest_api_init', function () {
    register_rest_route( 'ar-display', '/models/', array(
        'methods' => 'GET',
        'callback' => 'ar_api',
        'permission_callback' => '__return_true'
    ) );
} );


add_action( 'rest_api_init', function () {
    register_rest_route( 'ar-display', '/update/', array(
        'methods' => 'POST',
        'callback' => 'ar_api_update',
        'permission_callback' => '__return_true'
    ) );
} );


add_action( 'rest_api_init', function () {
    register_rest_route( 'ar-display', '/delete/', array(
        'methods' => 'POST',
        'callback' => 'ar_api_delete',
        'permission_callback' => '__return_true'
    ) );
} );


add_action( 'rest_api_init', function () {
    register_rest_route( 'ar-display', '/featuredimage/', array(
        'methods' => 'POST',
        'callback' => 'ar_api_featured_image',
        'permission_callback' => '__return_true'
    ) );
} );

//REST API - Get AR Models callback function
if (!function_exists('ar_api')){
    function ar_api(){
        global $wpdb, $ar_api_array, $ar_api_meta_array;
        
        //If retrieving list then licence key in url
        $ar_licence_key = get_option('ar_licence_key');
        $licence_result = ar_api_licence_check($ar_licence_key);
            

        if (!array_key_exists('json',$_POST)){
            if (substr($licence_result,0,5)=='Valid'){
            //if ($_REQUEST['key']==$ar_licence_key){
                //TEMPLATE
                
                if (isset($_REQUEST['template'])){ 
                    $output = array();
                        $output_group = (array) null;
                        //Get Post fields
                        $output_group['id'] = '';
                        foreach ($ar_api_array as $k=>$v){
                            $field_name = 'post_'.$v;
                            $output_group[$v] = '' ; 
                        }
                        //Get Post Meta Fields
                        foreach ($ar_api_meta_array as $k=>$v){
                            $field_name = '_'.$v;
                            $output_group[$v] = '';
                        }
                        $output[] = $output_group;
                    //}
                }else{ 
                    //REQUEST MODELS
                    
                    //Single Model ID
                    if (isset($_REQUEST['id'])){
                        $post_id = $_REQUEST['id'];
                    }
                    $args = array( 
                        'post_type' => 'armodels', 
                        'p' => $post_id,
                        //'post_status' => 'publish', 
                        //'posts_per_page' => '1',
                        'nopaging' => true 
                    );
                    $query = new WP_Query( $args );
                    $posts = $query->get_posts();
                    
                    $output = array();
                    foreach( $posts as $post ) {
                        $output_group = (array) null;
                        //Get Post fields
                        $output_group['id'] = $post->ID;
                        foreach ($ar_api_array as $k=>$v){
                            $field_name = 'post_'.$v;
                            $output_group[$v] = $post->$field_name ; 
                        }
                        //Get Post Meta Fields
                        foreach ($ar_api_meta_array as $k=>$v){
                            $field_name = '_'.$v;
                            $output_group[$v] = get_post_meta($output_group['id'], $field_name, true );
                        }
                        $output[] = $output_group;
                    }
                }
                wp_send_json( $output ); // getting data in json format.
            }
        }
    }
}


if (!function_exists('ar_api_update')){
    function ar_api_update(){
        global $wpdb, $ar_api_array, $ar_api_meta_array;
        
        
        if (array_key_exists('json',$_POST)){
            //POST MODELS          
            
            //Check if json file posted
            if (isset($_POST['json'])){

                $json = json_decode(stripcslashes($_POST['json']), true);
                $ar_licence_key = get_option('ar_licence_key');
                $licence_result = ar_api_licence_check($ar_licence_key);

                //When Adding models, licence key is to be passed in json or via url and used to authenticate
                if (substr($licence_result,0,5)=='Valid'){
                //if (($_REQUEST['key']==$ar_licence_key)OR($_POST['key']==$ar_licence_key)){
                    foreach($json as $data){  
                        
                        $new_post = array();
                        $new_post['post_author'] = $data['author'];
                        $new_post['post_title'] = $data['title'];
                        $new_post['post_date'] = $data['date'];
                        $new_post['post_status'] = $data['status'];
                        //$featured_image = $data['featured_image'];

                        if ( get_post_type($data['id']) != 'armodels' || FALSE === get_post_status( $data['id'] ) || !$data['id'] ) {
            
                            $new_post_id = wp_insert_post( $new_post );
                            set_post_type( $new_post_id, 'armodels' );
                            $id_provided ='';
                            if ($data['id']!=''){
                                $id_provided = ' - ID provided '.$data['id'];
                            }
                            $data = sync_ar_fields($data);
                            update_ar_option_fields($new_post_id, $data);
                            $output[$new_post_id]='created'.$id_provided;

                        } else {

                              $new_post['ID'] = $data['id'];
                              $new_post_id = $data['id'];

                              wp_update_post( $new_post );
                              set_post_type( $data['id'], 'armodels' );
                              $data = sync_ar_fields($data);
                              update_ar_option_fields($new_post['ID'], $data);
                              $output[$new_post['ID']]='updated';
                        }


                        $featured_image = $data['_featured_image'];
                       

                        if(isset($featured_image)){
                            //set featured image
                            //echo "with featured image HERERE <BR />";
                            $image_name = $new_post['post_title']."_model_poster_image.png";
                            $plugin_folder = substr($_SERVER["SCRIPT_URI"],0,strrpos($_SERVER["SCRIPT_URI"],"/")+1); 
                            
                            $attachment_id = upload_image($image_name, $featured_image, $new_post_id, $new_post['post_title'], 1);


                            set_post_thumbnail( $new_post_id, $attachment_id );
                        }
                    }
                    return ($output);
                }

            }
        }
    }
}

if (!function_exists('sync_ar_fields')){
    function sync_ar_fields( $data ) {
        foreach($data as $key => $value){
            $data_output['_'.$key] = sanitize_text_field($value);
        }
        return $data_output;
    }
}

if (!function_exists('update_ar_option_fields')){
    function update_ar_option_fields( $post_id, $post_data ) {
        global $ar_plugin_id;
        $ar_post ='';
        
        if ( isset( $post_data['_usdz_file'] ) ) {
            update_post_meta( $post_id, '_usdz_file',  $post_data['_usdz_file']  );
        }
        if (( isset( $post_data['_glb_file'] ) ) || ( isset( $post_data['_asset_file'] ) )):
            if (  $post_data['_asset_file'] !='' ){
                //Asset Builder overrides the GLB field
                $path_parts = pathinfo( $post_data['_asset_file'] );
            }else{
                $path_parts = pathinfo( $post_data['_glb_file'] );
            }
            /***ZIP***/
            /***if zip file, then extract it and put gltf into _glb_file***/
            $zip_gltf='';
            if (isset($path_parts['extension'])){
                if (strtolower($path_parts['extension'])=='zip'){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_path = $upload_dir['path'].'/ar_asset_'.$post_id.'/';
                    if ( $post_data['_asset_file'] !='' ){
                        
                        $src_file=$destination_path.'/temp.zip';
                    }else{
                        //$destination_path = $upload_dir['path'].'/'.$path_parts['filename'].'/';
                        $src_file=$upload_dir['path'].'/'.$path_parts['basename'];
                    }
                    //Delete old Asset folder
                    if (file_exists($destination_path)) {
                        ar_remove_asset($destination_path);
                    }
                    //Create new Asset folder
                    if (!mkdir($destination_path, 0755, true)) {
                        die('Failed to create folders...');
                    }
                    
                    if (  $_POST['_asset_file'] !='' ){
                        // If the function it's not available, require it.
                        if ( ! function_exists( 'download_url' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                        }
                        
                        //copy zip from asset_builder to local site
                        $src_file = download_url(  $_POST['_asset_file'] );
                        $unzipfile = unzip_file( $src_file  , $destination_path);
                        unlink($src_file);
                    }else{
                        $unzipfile = unzip_file( $src_file, $destination_path);
                    }
                    if ( $unzipfile ) {
                        //echo 'Successfully unzipped the file! '. sanitize_text_field( $_POST['_asset_file']);       
                    } else {
                        _e('There was an error unzipping the file.', $ar_plugin_id );
                    }
                        
                    if ( $unzipfile ) {
                        $file= glob($destination_path . "/*.gltf");
                        foreach($file as $filew){
                            $path_parts2=pathinfo($filew);
                            if ( $post_data['_asset_file'] !='' ){
                                
                                if (( isset( $post_data['_asset_file'] ) )AND( isset( $post_data['_asset_texture_file_0'] ) )){
                                    for($i=0;$i<10;$i++){
                                        if (isset($_POST['_asset_texture_file_'.$i])){
                                            $asset_textures[$i]['newfile']=$post_data['_asset_texture_file_'.$i];
                                            $asset_textures[$i]['filename']=$_POST['_asset_texture_id_'.$i];
                                        }
                                    }
                                    $flip = $post_data['_asset_texture_flip'];
                                    asset_builder_texture($upload_dir['path'].'/ar_asset_'.$post_id.'/',$path_parts2['basename'],$asset_textures,$flip);
                                }
                            }else{
                               // $_POST['_glb_file'] = $path_parts['dirname'].'/'.$path_parts['filename'].'/'.$path_parts2['basename'];
                            }
                            $post_data['_glb_file'] = $upload_dir['url'].'/ar_asset_'.$post_id.'/'.$path_parts2['basename'];
                            $zip_gltf='1'; //If set to 1 then ignore the model conversion process below
                        }
                    } else {
                        _e('There was an error unzipping the file.', $ar_plugin_id );
                    }
                }
            }
            
            /***Model Conversion***/
            /***if model file for conversion then convert and put gltf into _glb_file***/
            $allowed_files=array('dxf', 'dae', '3ds','obj','pdf','ply','stl','zip');
            if (isset($path_parts['extension'])){
                if ((in_array(strtolower($path_parts['extension']),$allowed_files))AND($zip_gltf=='')){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_file = $upload_dir['path'].'/'.$path_parts['filename'].'.glb';;
                    $open = fopen( $destination_file, "w" ); 
                    $write = fputs( $open,  ar_model_conversion( $post_data['_glb_file'] ) ); 
                    fclose( $open );
                    $_POST['_glb_file']= $path_parts['dirname'].'/'.$path_parts['filename'].'.glb';
                }
            }
            
            update_post_meta( $post_id, '_glb_file',  $post_data['_glb_file']  );
        endif;
        if ((isset( $post_data['_usdz_file'] )) OR( isset($post_data['_glb_file']))){
            update_post_meta( $post_id, '_ar_placement', $post_data['_ar_placement'] );
            update_post_meta( $post_id, '_ar_display', '1' );
        }else{
            update_post_meta( $post_id, '_ar_display', '' );
        }

        $field_array=array('_skybox_file','_ar_environment','_ar_variants','_ar_rotate','_ar_x','_ar_y','_ar_z','_ar_field_of_view','_ar_zoom_out','_ar_zoom_in','_ar_exposure','_ar_camera_orbit','_ar_environment_image','_ar_shadow_intensity','_ar_shadow_softness','_ar_resizing','_ar_view_hide','_ar_qr_hide','_ar_hide_dimensions','_ar_animation','_ar_autoplay','_ar_hotspots','_ar_cta','_ar_cta_url','_ar_css_override','_ar_css_positions','_ar_css');

        foreach ($field_array as $k => $v){
            if ( isset( $post_data[$v] ) ) {
                update_post_meta( $post_id, $v, $post_data[$v] );
                //echo $v." = ".$post_data[$v].'<br />';
            }else{
                update_post_meta( $post_id, $v, '');
            }
            
        }
        
        update_post_meta( $post_id, '_ar_shortcode', '[ardisplay id='.$post_id.']');
        
    }
 }



if (!function_exists('ar_wc_api_licence_check')){
    function ar_wc_api_licence_check($licence_key) {
        global $wpdb;
        $link = 'https://augmentedrealityplugins.com/ar/ar_subscription_licence_check.php';
        ob_start();
        $query = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE (meta_key = '_ar_display' AND meta_value = '1')");
        $model_count= count($query);
        $data = array(
            'method'      => 'POST',
            'body'        => array(
            'domain_name' => site_url(),
            'licence_key' => $licence_key,
            'model_count' => $model_count
        ));
        $response = wp_remote_post( $link, $data);
        if (!is_wp_error($response)){return $response['body'];}else{return 'error';}
        ob_flush();
    }
}


if (!function_exists('ar_api_delete')){
    function ar_api_delete(){
        global $wpdb, $ar_api_array, $ar_api_meta_array;
        
        
        if (array_key_exists('json',$_POST)){

            $json = json_decode(stripcslashes($_POST['json']), true);
            if(count($json)){
                foreach($json as $data){  
                    $post_id = 0;                  
                    if($data['key'] == 'id'){
                        //delete AR model using ID
                    
                        wp_trash_post( $data['value'] );


                    } else if($data['key'] == 'title'){
                        
                        $my_post = get_page_by_title( $data['value'], OBJECT, 'armodels' );
                        wp_trash_post( $my_post->ID );

                    } else if($data['key'] == 'usdz_file' || $data['key'] == 'glb_file') {
                        
                        $tbl = $wpdb->prefix.'postmeta';

                        $prepare_guery = $wpdb->prepare( "SELECT post_id FROM $tbl where meta_key ='_".$data['key']."' and meta_value = '%s'", addslashes($data['value']) );
                        
                        $get_values = $wpdb->get_col( $prepare_guery );

                        if(count($get_values)){
                            foreach($get_values as $pid){
                                wp_trash_post( $pid );
                            }
                        }

                    } else {
                        continue;
                    }
                    
                }
            }

        }
    }
}


if (!function_exists('ar_api_featured_image')){
    function ar_api_featured_image(){
        global $wpdb, $ar_api_array, $ar_api_meta_array;
        
        if (array_key_exists('json',$_POST)){

            $json = json_decode(stripcslashes($_POST['json']), true);
            if(count($json)){
                foreach($json as $data){                    
                    if($data['id']){
                        $post   = get_post( $data['id'] );
                        if($post){
                        //update featured image of post
                            $image_name = $post->post_title."_model_poster_image.png";
                            $plugin_folder = substr($_SERVER["SCRIPT_URI"],0,strrpos($_SERVER["SCRIPT_URI"],"/")+1); 
                            
                            $attachment_id = upload_image($image_name, $data['featured_image'], $data['id'], $post->post_title, 1);


                            set_post_thumbnail( $data['id'], $attachment_id );

                            //die($attachment_id);
                        }
                                        
                    } else {
                        continue;
                    }
                }
            }

        }
    }
}



 if (!function_exists('ar_api_licence_check')){
    function ar_api_licence_check($licence_key) {
        global $wpdb;
        $link = 'https://augmentedrealityplugins.com/ar/ar_subscription_licence_check.php';
        ob_start();
        $query = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE (meta_key = '_ar_display' AND meta_value = '1')");
        $model_count= count($query);
        $data = array(
            'method'      => 'POST',
            'body'        => array(
            'domain_name' => site_url(),
            'licence_key' => $licence_key,
            'model_count' => $model_count
        ));
        $response = wp_remote_post( $link, $data);
        if (!is_wp_error($response)){return $response['body'];}else{return 'error';}
        ob_flush();
    }
}
