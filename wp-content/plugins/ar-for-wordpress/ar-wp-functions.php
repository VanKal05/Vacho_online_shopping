<?php

if (!function_exists('save_ar_wp_option_fields')){
    function save_ar_wp_option_fields( $post_id ) {
        global $ar_plugin_id;

        $post_type = get_post_type();
        
        
        if($post_type != 'armodels'){
            //die("HERERE");
            //exit;
            return 1;
        }
        

        $ar_post ='';
        if ( isset( $_POST['_usdz_file'] ) ) {
            update_post_meta( $post_id, '_usdz_file', sanitize_text_field( $_POST['_usdz_file'] ) );
        }
        
        if (( isset( $_POST['_glb_file'] ) ) || ( isset( $_POST['_ar_asset_file'] ) )):
            if (($_POST['_ar_asset_file'.$suffix] !='' )AND($_POST['_asset_texture_file_0'] !='')){
                //Add the ratio and orientation to the url.
                
                //Asset Builder overrides the GLB field
                $path_parts = pathinfo(sanitize_text_field( $_POST['_ar_asset_file'] ));

                $path_parts['filename'] .= '_' . $_POST['ar_asset_ratio'] . '_' . $_POST['ar_asset_orientation'];
                $path_parts['basename'] = $path_parts['filename'] . '.zip';
                
        //print_r($path_parts);print_r($_POST);exit;
            }else{
                $path_parts = pathinfo(sanitize_text_field( $_POST['_glb_file'] ));
            }
            
            /***ZIP***/
            /***if zip file, then extract it and put gltf into _glb_file***/
            $zip_gltf='';
            if (isset($path_parts['extension'])){
                if (strtolower($path_parts['extension'])=='zip'){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_path = $upload_dir['path'].'/ar_asset_'.$post_id.'/';
                    if ( $_POST['_ar_asset_file'] !='' ){
                        
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
                    
                    if (  $_POST['_ar_asset_file'] !='' ){
                        // If the function it's not available, require it.
                        if ( ! function_exists( 'download_url' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                        }
                        
                        //copy zip from asset_builder to local site
                        $src_file = download_url( sanitize_text_field( $path_parts['dirname'].'/'.$path_parts['basename'] ) );
                        $unzipfile = unzip_file( $src_file  , $destination_path);
                        unlink($src_file);
                    }else{
                        $unzipfile = unzip_file( $src_file, $destination_path);
                    }
                    if ( $unzipfile ) {
                        //echo 'Successfully unzipped the file! '. sanitize_text_field( $_POST['_ar_asset_file']);       
                    } else {
                        _e('There was an error unzipping the file.', $ar_plugin_id );
                    }
                        
                
                    if ( $unzipfile ) {
                        $file= glob($destination_path . "/*.gltf");
                        //echo $destination_path.'<br>';
                        foreach($file as $filew){
                            $path_parts2=pathinfo($filew);
                            if ( $_POST['_ar_asset_file'] !='' ){
                                //print_r($_POST);exit;
                                if (( isset( $_POST['_ar_asset_file'] ) )AND( isset( $_POST['_asset_texture_file_0'] ) )){
                                    for($i=0;$i<10;$i++){
                                        if (isset($_POST['_asset_texture_file_'.$i])){
                                            $asset_textures[$i]['newfile']=$_POST['_asset_texture_file_'.$i];
                                            $asset_textures[$i]['filename']=$_POST['_asset_texture_id_'.$i];
                                        }
                                    }
                                    $flip = $_POST['_asset_texture_flip'];
                                    asset_builder_texture($upload_dir['path'].'/ar_asset_'.$post_id.'/',$path_parts2['basename'],$asset_textures,$flip);
                                    
                                }
                            }else{
                               // $_POST['_glb_file'] = $path_parts['dirname'].'/'.$path_parts['filename'].'/'.$path_parts2['basename'];
                            }
                            $_POST['_glb_file'] = $upload_dir['url'].'/ar_asset_'.$post_id.'/'.$path_parts2['basename'];
                            $zip_gltf='1'; //If set to 1 then ignore the model conversion process below
                            //echo  $_POST['_glb_file'].'<br>';
                        }
                        
                    } else {
                        _e('There was an error unzipping the file.', $ar_plugin_id);
                               
                    }
                }
            }
            /***Hotspot saving***/
            if (isset($_POST['_ar_hotspots'])){
                if ( $_POST['_ar_hotspots'] !='' ){
                    $_ar_hotspots = json_encode($_POST['_ar_hotspots']);
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
                    $write = fputs( $open,  ar_model_conversion(sanitize_text_field( $_POST['_glb_file'] )) ); 
                    fclose( $open );
                    $_POST['_glb_file']= $path_parts['dirname'].'/'.$path_parts['filename'].'.glb';
                }
            }
            
            update_post_meta( $post_id, '_glb_file', sanitize_text_field( $_POST['_glb_file'] ) );
        endif;

        if ((isset( $_POST['_usdz_file'] )) OR( isset($_POST['_glb_file']))){
            update_post_meta( $post_id, '_ar_placement', $_POST['_ar_placement'] );
            update_post_meta( $post_id, '_ar_display', '1' );
        }else{
            update_post_meta( $post_id, '_ar_display', '' );
        }

        update_option( 'ar_open_tabs', $_POST['ar_open_tabs']);
        $field_array=array('_skybox_file','_ar_environment','_ar_qr_image','_ar_qr_destination','_ar_qr_destination_mv','_ar_variants','_ar_rotate','_ar_prompt','_ar_x','_ar_y','_ar_z','_ar_field_of_view','_ar_zoom_out','_ar_zoom_in','_ar_exposure','_ar_camera_orbit','_ar_environment_image','_ar_shadow_intensity','_ar_shadow_softness','_ar_resizing','_ar_view_hide','_ar_qr_hide','_ar_hide_dimensions','_ar_hide_reset','_ar_animation','_ar_autoplay','_ar_animation_selection','_ar_emissive','_ar_light_color','_ar_disable_zoom','_ar_rotate_limit','_ar_compass_top_value','_ar_compass_bottom_value','_ar_compass_left_value','_ar_compass_right_value','_ar_hotspots','_ar_cta','_ar_cta_url','_ar_css_override','_ar_css_positions','_ar_css','_ar_mobile_id','_ar_alternative_id');

        foreach ($field_array as $k => $v){
            if ( isset( $_POST[$v] ) ) {
                update_post_meta( $post_id, $v, $_POST[$v] );
            }else{
                update_post_meta( $post_id, $v, '');
            }
        }
        update_post_meta( $post_id, '_ar_shortcode', '[ardisplay id='.$post_id.']');
  
    }
 }


?>