<?php
/**
 * AR Display
 * AR For WordPress
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH'))
    exit;

add_action('admin_enqueue_scripts', 'ar_advance_register_script');

if (!function_exists('ar_wp_advance_update_edit_form')){
    add_action('post_edit_form_tag', 'ar_wp_advance_update_edit_form');
    function ar_wp_advance_update_edit_form() {
        echo ' enctype="multipart/form-data"';
    }
}

if (!function_exists('ar_wp_advance_the_upload_metabox')){
    add_action('add_meta_boxes', 'ar_wp_advance_the_upload_metabox');
    function ar_wp_advance_the_upload_metabox() {
        // Define the custom attachment for posts  
        add_meta_box('ar_wp_advance_custom_attachment', __( 'Augmented Reality Models', 'ar-for-wordpress' ), 'ar_wp_model_fields', "armodels", "normal", "high", null);
    }
}

// Add the View link to the All AR Models page
if (!function_exists('ar_modify_list_row_actions')){
    add_filter( 'post_row_actions', 'ar_modify_list_row_actions', 10, 2 );
    function ar_modify_list_row_actions( $actions, $post ) {
    	if ( $post->post_type == "armodels" ) {
    		$actions['View'] = sprintf( '<a href="%1$s">%2$s</a>',
    			esc_url( get_permalink($post) ),
    			esc_html( __( 'View', 'ar-for-wordpress' ) ) );
    	}
    	return $actions;
    }
}

function ar_change_featured_image_text( $content ) {
    if ( 'armodels' === get_post_type() ) {
        $content = str_replace( 'Set featured image', __( 'Set AR Poster image', 'ar' ), $content );
        $content = str_replace( 'Remove featured image', __( 'Remove AR Poster image', 'ar' ), $content );
    }
    return $content;
}
add_filter( 'admin_post_thumbnail_html', 'ar_change_featured_image_text' );

//Add the AR Model Fields editor to front end
add_shortcode('areditor', 'ar_wp_model_fields_public');

if (!function_exists('ar_wp_model_fields_public')){
    function ar_wp_model_fields_public($arr = NULL) {
        ob_start();
        ar_wp_model_fields($arr);
        $output = ob_get_clean();
        return $output;
    }
}
// Model File Fields
if (!function_exists('ar_wp_model_fields')){
    function ar_wp_model_fields($arr = NULL) {
        global $wpdb, $post, $shortcode_examples, $ar_whitelabel, $ar_css_styles, $ar_css_names;
        $public = '';
        //Check if on admin edit page or public page
        if (is_admin()){
            $screen = get_current_screen();
        }
        if (!isset($screen)){
            //Showing Editor on Public Side
            $post   = get_post( $arr['id'] );
            $public = 'y';
        }
        
        $plan_check = get_option('ar_licence_plan');
        //Model Count
        $model_count = ar_model_count();
        //Hide the post content area
        ?>
        <style>
            .postarea{display:none;}
            
        </style>
        <?php if ($public != 'y'){ ?>
        <div id="ardisplay_panel" class="panel woocommerce_options_panel">
            <div class="options_group">
        <?php } ?>
                <?php //Hide instructions and file uploads if showing on public side
                if ($public == 'y'){
                    echo '<div style="display:none">';
                }
                ?>
                    <div id="ar_shortcode_instructions">
                        <div style="width:100%;height:80px;background-color:#12383d">
                            <div class="ar_admin_view_title">
                                <img src="<?php echo esc_url( plugins_url( "assets/images/ar-for-wordpress-box.jpg", __FILE__ ) );?>" style="padding: 10px 30px 10px 10px; height:60px" align="left">
                                <h1 style="color:#ffffff; padding-top:20px;font-size:20px"><?php _e('AR for WordPress','ar-for-wordpress'); ?></h1>
                            </div>
                            <?php
                        if ((substr(get_option('ar_licence_valid'),0,5)!='Valid')AND($model_count>=2)){?>
                        
                        </div>
                            <p><b><a href="edit.php?post_type=armodels&page"><?php _e( 'Please check your subscription & license key.</a> If you are using the free version of the plugin then you have exceeded the limit of allowed models.', 'ar-for-wordpress' );?></a></b></p>
                    </div>
            </div>
        </div>
                        <?php
                        }else{
                            $model_array=array();
                            $model_array['id'] = $post->ID;
                        ?>
                    	<div  class="ar_admin_view_shortcode">
                    	    <center><b>Shortcode</b> <span id="copied" class="ar_label_tip"></span><br> 
                    	        <a heref="#" onclick="copyToClipboard('ar_shortcode');document.getElementById('copied').innerHTML='-&nbsp;Copied!';">
                    	        <input id="ar_shortcode" type="text" class="button ar_admin_button" value="[ardisplay id=<?=$model_array['id'];?>]" readonly style="width:164px;background: none !important; border: none !important;color:#f37a23 !important;font-size: 16px;"><span class="dashicons dashicons-admin-page" style="color:#fff"></span>
                    	        </a>
                    	        </center>
                    	   </div>
                		<div  class="ar_admin_view_post">
                    		<?php if (get_post_meta( $model_array['id'], '_glb_file', true )!=''){
                               // echo '<div class="ar_admin_view_post">'.sprintf( __('<a href="%s" target="_blank"><button type="button" class="button ar_admin_button" style="margin-right:20px">'.__('View Model Post','ar-for-wordpress').'</button></a>'), esc_url( get_permalink($model_array['id']) ) ).'</div>';
                                echo ''.sprintf( __('<a href="%s" target="_blank"><button type="button" class="button ar_admin_button" style="margin-right:20px">'.__('View Model Post','ar-for-wordpress').'</button></a>'), esc_url( get_permalink($model_array['id']) ) );
                            }
                            ?>
                    	</div>
                    </div>
                        	
            	</div>
                <div style="clear:both"></div>
                <!-- Tab links -->
                <div class="ar_tab">
                  <button class="ar_tablinks" onclick="ar_open_tab(event, 'model_files_content', 'model_files_tab')" id="model_files_tab" type="button"><?php _e( 'Model Files', 'ar-for-wordpress' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                  <button class="ar_tablinks" onclick="ar_open_tab(event, 'asset_builder_content', 'asset_builder_tab')" id="asset_builder_tab" type="button"><?php _e( '3D Gallery Builder', 'ar-for-wordpress' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                  <button class="ar_tablinks" onclick="ar_open_tab(event, 'instructions_content','instructions_tab')" id="instructions_tab" type="button"><?php _e( 'Shortcodes', 'ar-for-wordpress' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                  <a href="https://augmentedrealityplugins.com/support/" target="_blank"><button class="ar_tablinks" id="support_tab" type="button"> <?php _e( 'Support', 'ar-for-wordpress' );?><span style=" vertical-align: super;font-size: smaller;">&#8599;</span></button></a>
                </div>
                
                <div id="model_files_content" class="ar_tabcontent"><br>
                	<div class="ar_model_files_advert hide_on_devices">
                	    <center>
                	        <img src="<?php echo esc_url( plugins_url( "assets/images/ar_asset_ad_icon.jpg", __FILE__ ) ); ?>" style="height:60px">
                    	    <h3><?=_e('Hang your artwork in Augmented Reality with just a photo!', 'ar-for-wordpress' );?></h3>
                    	    <button type="button" id="asset_builder_button" onclick="ar_open_tab(event, 'asset_builder_content', 'asset_builder_tab');/*ar_activeclass('asset_builder_tab');*/" class="button ar_admin_button" style="margin-right:20px"><?=_e('3D Gallery Builder', 'ar-for-wordpress' );?></button>
                	        <p><a href="https://wordpress.org/support/plugin/ar-for-wordpress/reviews/#new-post" target="_blank">Rate this plugin!</a> <a href="https://wordpress.org/support/plugin/ar-for-wordpress/reviews/#new-post" target="_blank"><img src="<?=esc_url( plugins_url( "assets/images/5-stars.png", __FILE__ ) );?>" style="width: 45px;vertical-align: middle;"></a></p>
                	    </center>
                	</div>
                <div class="ar_model_files_fields">
                    <?php if (get_post_meta( $model_array['id'], '_glb_file', true )!=''){
                        $glb_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ); 
                        $path_parts = pathinfo(sanitize_text_field( get_post_meta( $model_array['id'], '_glb_file', true ) ));
                        $glb_filename = $path_parts['basename'];
                    }else{
                        $glb_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); 
                        $glb_filename = '';
                    }
                    if (get_post_meta( $model_array['id'], '_usdz_file', true )!=''){
                        $usdz_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) );
                        $path_parts = pathinfo(sanitize_text_field( get_post_meta( $model_array['id'], '_usdz_file', true ) ));
                        $usdz_filename = $path_parts['basename'];
                    }else{
                        $usdz_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); 
                        $usdz_filename = '';
                    }
                    ?>
                    <div style="width:48%; float:left;padding-right:10px; position:relative;">
                        <a href="#" id="toggle-model-fields" data-status='hidden'>Show Model Field</a>
                        <center>
                        <strong><?php _e( 'GLTF/GLB 3D Model', 'ar-for-wordpress' );?></strong> <br><br>
                        <img src="<?=$glb_upload_image;?>" id="glb_thumb_img" class="ar_file_icons" onclick="document.getElementById('upload_glb_button').click();document.getElementById('glb_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ); ?>';">
                         <a href="#" onclick="document.getElementById('_glb_file').value = '';document.getElementById('glb_filename').innerHTML = '';document.getElementById('glb_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); ?>';"><img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"></a>

                         

                        <br clear="all"><br><span id="glb_filename" class="ar_filenames"><?=$glb_filename;?></span>
                        <div align="center">                            
                            <input type="hidden" pattern="https?://.+" title="<?php _e('Secure URLs only','ar-for-wordpress'); ?> https://" placeholder="https://" name="_glb_file" id="_glb_file" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_glb_file', true );?>"> 
                            <input id="upload_glb_button" class="button nodisplay upload_glb_button" type="button" value="<?php _e( 'Upload', 'ar-for-wordpress' );?>" />
                        </div>
                        <input type="hidden" id="uploader_modelid" value="">
                        </center>
                    </div>
                    <div style="width:48%; float:left;">
                        <center>
                    	<strong><?php echo __( 'USDZ/REALITY 3D Model', 'ar-for-wordpress' ).' - '.__('<span class="ar_label_tip">Optional</span>', 'ar-for-wordpress' );?></strong><br><br>
                    	<img src="<?=$usdz_upload_image;?>" id="usdz_thumb_img"  class="ar_file_icons" onclick="document.getElementById('upload_usdz_button').click();document.getElementById('usdz_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ); ?>';">
                        <a href="#" onclick="document.getElementById('_usdz_file').value = '';document.getElementById('usdz_filename').innerHTML = '';document.getElementById('usdz_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); ?>';"><img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"></a>
                        <br clear="all"><br><span id="usdz_filename" class="ar_filenames"><?=$usdz_filename;?></span>
                        <div align="center">                            
                            <input type="hidden" pattern="https?://.+" title="<?php _e('Secure URLs only','ar-for-wordpress'); ?> https://" placeholder="https://" name="_usdz_file" id="_usdz_file" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_usdz_file', true );?>"> 
                            <input id="upload_usdz_button" class="button upload_usdz_button nodisplay" type="button" value="<?php _e( 'Upload', 'ar-for-wordpress' );?>" />
                        </div>
                        </center>
                    </div>
                    <div style="clear:both"></div><?php 
                    if($plan_check!='Premium') { 
                		    $premium_only = '<b> - '.__('Premium Plans Only', 'ar-for-wordpress').'</b>'; 
                		    $disabled = ' disabled';
                		    $readonly = ['readonly' => 'readonly'];
                		    $custom_attributes = $readonly;
                		    echo '<div style="pointer-events: none;">'; //disable mouse clicking 
                		}else{
                		    $disabled = '';
                		    $readonly = '';
                		    $premium_only = '';
                		    //Used for Scale inputs
                		    $custom_attributes = array(
                                'step' => '0.1',
                                'min' => '0.1');
                		}
                		?>
                    </div>
            	</div>
            	<?php /* Asset Builder */ ?>
            	<div id="asset_builder_content" class="ar_tabcontent" style="padding:0px;">
                    <div id="asset_builder">
                        <div id="asset_builder_top_content" style="padding:6px 10px;">
                            <img src="<?php echo plugins_url('assets/images/wall_art_guide.jpg', __FILE__); ?>" style="float:right;max-width:50%">
                            <!---<h3><?php _e( '3D Gallery Builder', 'ar-for-wordpress' );?></h3>-->
                            
                            <?php 
                            $nodisplay = ' class=""';
                            for($i = 0; $i<1; $i++) { //Previously 10 - Cube will require 6
                            if ($i>0){$nodisplay = ' class="nodisplay"';}
                            ?>
                               <div  id="texture_container_<?=$i?>" <?=$nodisplay;?> style="padding:10px 0px">
                                 <p><strong><?php _e( 'Image', 'ar-for-wordpress' );?></strong> <span id="ar_asset_builder_texture_done"></span><br>
                                <img src="<?php echo esc_url( plugins_url( "assets/images/ar_asset_icon.jpg", __FILE__ ) ); ?>" id="asset_thumb_img" style="max-heigth:200px"  class="ar_file_icons" onclick="document.getElementById('upload_asset_texture_button_<?php echo $i; ?>').click();">
                                <span id="texture_<?=$i?>">
                    	        <input type="hidden" name="_asset_texture_file_<?php echo $i; ?>" id="_asset_texture_file_<?php echo $i; ?>" class="regular-text"> <input id="upload_asset_texture_button_<?php echo $i; ?>" class="upload_asset_texture_button_<?php echo $i; ?> button nodisplay" type="button" value="<?php _e( 'Upload', 'ar-for-wordpress' );?>" /> <img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('_asset_texture_file_<?php echo $i; ?>').value = '';document.getElementById('ar_asset_builder_texture_done').innerHTML = '';document.getElementById('asset_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_asset_ad_icon.jpg", __FILE__ ) ); ?>';">
                    	        <input type="text" name="_asset_texture_id_<?php echo $i; ?>" id="_asset_texture_id_<?php echo $i; ?>" class="nodisplay"></span></p>
                    	        
                    	        </div>
                            
                            <?php }
                            ?><input type="text" name="_asset_texture_flip" id="_asset_texture_flip" class="nodisplay">
                            <br>
                            
                                <strong><?php _e( 'Frame', 'ar-for-wordpress' );?> <span id="ar_asset_builder_model_done"></span></strong>
                                <div id="ar_asset_iframe_panel">
                                    <div id="asset_builder_iframe" style="width:50%; min-height:200px"></div>
                                </div>
                            
                           
                            
                            
                    	<input type="hidden" name="_ar_asset_file" id="_ar_asset_file" class="regular-text" value="">
                        <input type="hidden" name="ar_asset_orientation" id="ar_asset_orientation" class="regular-text" value="portrait">
                        <input type="hidden" name="ar_asset_ratio" id="ar_asset_ratio" value="">
                        
      
                        <div style="min-height:100px">
                         <div id="ar_asset_size_container" style="display:none;">
                             <div style="float:left;">
                                 <strong><?php _e( 'Image Ratio', 'ar-for-wordpress' );?></strong> <span id="ar_asset_builder_ratio_done">&#10003;</span><br>
                                                    <select id="ar_asset_ratio_select" class="regular-text" style="max-width:80%">
                                        <option  id="ar_asset_ratio_options" value="1.0">1:1</option>
                                        <option  id="ar_asset_ratio_options" value="1.4142">A4-A1</option>
                                        <option  id="ar_asset_ratio_options" value="1.5">2:3</option>
                                        <option  id="ar_asset_ratio_options" value="1.25">4:5</option>
                                        <option  id="ar_asset_ratio_options" value="1.33">3:4</option>
                                  </select>
                                  
                              </div>
                              <div style="float:left;">
                                  <strong><?php _e( 'Print Size', 'ar-for-wordpress' );?></strong> <span id="ar_asset_builder_ratio_done">&#10003;</span><br>
                                  <select id="ar_asset_size" class="regular-text" style="max-width:80%">
                                        <option  id="ar_asset_size_options" value="-1" selected="selected">Select your Asset Below First</option>
                                  </select></p>
                              </div>
                          </div>
                            
                            <span id="ar_asset_builder_submit_container" style="display:none;">
                                <br clear="all"><!--<br>
                                <button id = "ar_asset_builder_submit" class="button ar_admin_button" >Build Asset</button>-->
                                
                                <strong><span style="color:#f37a23"><?php _e( 'Please Publish/Update your post to build the Gallery Asset. You may need to refresh your browser once updated to ensure the latest files are displayed.', 'ar-for-wordpress' );?></span></strong>
                                <br><br>
                                
                            </span>
                            </div>
                        </div>
                    </div>
                </div>   
                
                <?php /* Instructions */ ?>
            	<div id="instructions_content" class="ar_tabcontent">
                        <p>                		    
        		        <?php echo $shortcode_examples;
        		        //echo '<p>'.__( 'Models can be uploaded as a GLB or GLTF file for viewing in AR and within the broswer display. You can also upload a USDZ or REALITY file for iOS, otherwise a USDZ file is generated on the fly. The following formats can be uploaded and will be automatically converted to GLB format - DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped versions of these files. Model conversion accuracy cannot be guaranteed, please check your model carefully.', 'ar-for-wordpress' );
                        if (!$ar_whitelabel){
                		    echo '<p><a href="https://augmentedrealityplugins.com/support/" target="_blank">'.__('Documentation', 'ar-for-wordpress').'</a> | <a href="https://augmentedrealityplugins.com/support/3d-model-resources/" target="_blank">'.__('Sample 3D Models', 'ar-for-wordpress').'</a> | <a href="https://augmentedrealityplugins.com/support/3d-model-resources/#hdr" target="_blank">'.__('Sample HDR Images', 'ar-for-wordpress').'</a> ';
                		}
                		?>
                		</p>
                </div>
                <?php
                //if ($public == 'y'){
                    echo '</div>';
                //} 
                
                $ar_open_tabs=get_option('ar_open_tabs'); 
                $ar_open_tabs_array = explode(',',$ar_open_tabs);
                $jsArray = json_encode($ar_open_tabs_array);
                ?>   
                <div style="clear:both"></div>
                <div class="ar_admin_viewer">
                    <input type="hidden" name="ar_open_tabs" id="ar_open_tabs" value="<?=$ar_open_tabs;?>">
                	<button class="ar_accordian" id="ar_display_options_acc" type="button"><?php _e('Display Options', 'ar-for-wordpress' ); echo $premium_only;?></button>
                    <div id="ar_display_options_panel" class="ar_accordian_panel">
                        <br>
                        <?php if ($public != 'y'){ ?>
                            <div style="clear:both"></div>
                            <div class="ar_admin_label"><label for="_skybox_file"><?php _e( 'Skybox/Background Image', 'ar-for-wordpress' ); echo "<br>"; _e('<span class="ar_label_tip">HDR, JPG or PNG</span>', 'ar-for-wordpress' );?></label> </div>
                        	<div class="ar_admin_field"><input type="url" pattern="https?://.+" title="<?php _e('Secure URLs only','ar-for-wordpress');?> https://" placeholder="https://" name="_skybox_file" id="_skybox_file" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_skybox_file', true );?>" <?php echo $disabled;?>> <input id="upload_skybox_button" class="button upload_skybox_button" type="button" value="<?php _e( 'Upload', 'ar-for-wordpress' );?>"  <?php echo $disabled;?>/> <a href="#" onclick="document.getElementById('_skybox_file').value = ''"><img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"></a></div>
                            <div style="clear:both"></div>
                            <div class="ar_admin_label"><label for="_ar_environment"><?php _e( 'Environment Image', 'ar-for-wordpress' ); echo "<br>"; _e('<span class="ar_label_tip">HDR, JPG or PNG</span>', 'ar-for-wordpress' );?></label></div>
                            <div class="ar_admin_field"><input type="url" pattern="https?://.+" title="<?php _e('Secure URLs only','ar-for-wordpress'); ?> https://" placeholder="https://" name="_ar_environment" id="_ar_environment" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_ar_environment', true );?>" <?php echo $disabled;?>> <input id="upload_environment_button" class="button upload_environment_button" type="button" value="<?php _e( 'Upload', 'ar-for-wordpress' );?>" <?php echo $disabled;?>/> <a href="#" onclick="document.getElementById('_ar_environment').value = ''"><img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"></a></div>
                	        <div style="clear:both"></div>
                            <div class="ar_admin_label"><label for="_ar_placement"><?php _e( 'Model Placement', 'ar-for-wordpress' );?></label></div>
                        	<div class="ar_admin_field"><select name="_ar_placement" id="_ar_placement" class="ar-input ar-input-wide" <?php echo $disabled;?>>
                        			<option value="floor" <?php selected( get_post_meta( $model_array['id'], '_ar_placement', true ), 'floor' ); ?>><?php _e( 'Floor - Horizontal', 'ar-for-wordpress' );?></option>
                        			<option value="wall" <?php selected( get_post_meta( $model_array['id'], '_ar_placement', true ), 'wall' ); ?>><?php _e( 'Wall - Vertical', 'ar-for-wordpress' );?></option>
                        	</select></div>
                    	<?php } ?>
                    	<div style="clear:both"></div>
                    	<div class="ar_admin_label"><?php _e( 'Scale', 'ar-for-wordpress' );?><br><span class="ar_label_tip"><?php _e( '1 = 100%, only affects desktop view, not available in AR', 'ar-for-wordpress' );?></span></div>
                    	<?php
                    	$ar_x = 1;
                    	$ar_y = 1;
                    	$ar_z = 1;
                    	if (get_post_meta( $model_array['id'], '_ar_x', true )){
                    	    $ar_x = get_post_meta( $model_array['id'], '_ar_x', true );
                    	}
                    	if (get_post_meta( $model_array['id'], '_ar_y', true )){
                    	    $ar_y = get_post_meta( $model_array['id'], '_ar_y', true );
                    	}
                    	if (get_post_meta( $model_array['id'], '_ar_z', true )){
                    	    $ar_z = get_post_meta( $model_array['id'], '_ar_z', true );
                    	}
                    	?>
                        <div class="ar_admin_field"><span style="float:left">X: <input id="_ar_x" name="_ar_x" type="number" style="width: 60px;" class="ar-input" value="<?php echo $ar_x;?>" size="3" step="0.1" min="0.1" <?php echo $disabled;?>></span>
                            <span style="float:left">  Y: <input id="_ar_y" name="_ar_y" type="number" style="width: 60px;" class="ar-input" value="<?php echo $ar_y;?>" size="3" step="0.1" min="0.1" <?php echo $disabled;?>> </span>
                            <span style="float:left">  Z: <input id="_ar_z" name="_ar_z" type="number" style="width: 60px;" class="ar-input" value="<?php echo $ar_z;?>" size="3" step="0.1" min="0.1" <?php echo $disabled;?>></span>
                        </div>
                        <div style="clear:both"></div>
                        
                        <div class="ar_admin_label"><label for="_ar_field_of_view"><?php _e( 'Field of View', 'ar-for-wordpress' );?></label></div>
                        <?php 
                        $ar_field_of_view = get_post_meta( $model_array['id'], '_ar_field_of_view', true );
                        $ar_zoom_out = get_post_meta( $model_array['id'], '_ar_zoom_out', true );
                    	$ar_zoom_in = get_post_meta( $model_array['id'], '_ar_zoom_in', true );?>
                    	<div class="ar_admin_field"><select name="_ar_field_of_view" id="_ar_field_of_view" class="ar-input ar-input-wide" <?php echo $disabled;?>>
                            <option value=""><?php _e('Default','ar-for-wordpress');?></option>
                            <?php 
                            for ($x = 10; $x <= 180; $x+=10) {
                              echo '<option value="'.$x.'"';
                              if ($x==$ar_field_of_view){echo ' selected';}
                              echo '>'.$x.' ';
                              _e( 'Degrees', 'ar-for-wordpress' );
                              echo '</option>';
                            }
                            ?>
                            </select>
                        </div>
                        
                        <div style="clear:both"></div>
                        <?php $ar_exposure = get_post_meta( $model_array['id'], '_ar_exposure', true );
                        if ((!$ar_exposure)AND($ar_exposure!='0')){ $ar_exposure = 1; } ?>
                        <div class="ar_admin_label"><label for="_ar_exposure"><?php _e( 'Exposure', 'ar-for-wordpress' );?></label></div>
                    	<div class="ar_admin_field"><input id="_ar_exposure" name="_ar_exposure" type="range" class="ar-slider" min="0" max="2" step=".1" value="<?php echo $ar_exposure; ?>" <?php echo $disabled;?> oninput="this.nextElementSibling.value = this.value">&nbsp;<output><?php echo $ar_exposure; ?></output></div>
                    	<div style="clear:both"></div>
                        <?php $ar_shadow_intensity = get_post_meta( $model_array['id'], '_ar_shadow_intensity', true );
                        if ((!$ar_shadow_intensity)AND($ar_shadow_intensity!='0')){ $ar_shadow_intensity = 1; } ?>
                        <div class="ar_admin_label"><label for="_ar_shadow_intensity"><?php _e( 'Shadow Intensity', 'ar-for-wordpress' );?></label></div>
                    	<div class="ar_admin_field"><input id="_ar_shadow_intensity" name="_ar_shadow_intensity" type="range" class="ar-slider" min="0" max="2" step=".1" value="<?php echo $ar_shadow_intensity; ?>" <?php echo $disabled;?> oninput="this.nextElementSibling.value = this.value"> <output><?php echo $ar_shadow_intensity; ?></output></div>
                        <div style="clear:both"></div>
                        <?php $ar_shadow_softness = get_post_meta( $model_array['id'], '_ar_shadow_softness', true );
                        if ((!$ar_shadow_softness)AND($ar_shadow_softness!='0')){ $ar_shadow_softness = 1; } ?>
                        <div class="ar_admin_label"><label for="_ar_shadow_softness"><?php _e( 'Shadow Softness', 'ar-for-wordpress' );?></label></div>
                    	<div class="ar_admin_field"><input id="_ar_shadow_softness" name="_ar_shadow_softness" type="range" class="ar-slider" min="0" max="1" step=".1" value="<?php echo $ar_shadow_softness; ?>" <?php echo $disabled;?> oninput="this.nextElementSibling.value = this.value"> <output><?php echo $ar_shadow_softness; ?></output></div>
                        <div style="clear:both"></div>
                        <div class="ar_admin_label"><label for="_ar_zoom_in"><?php _e( 'Zoom Restraints', 'ar-for-wordpress' );?></label></div>
                        <span style="float:left">
                        <?php _e('In', 'ar-for-wordpress');?> <select name="_ar_zoom_in" id="_ar_zoom_in" class="ar-input" style="min-width:100px" <?php echo $disabled;?>>
                          <option value="default" <?php if (($ar_zoom_in == 'default')OR($ar_zoom_in == '')){echo 'selected';}?>><?php _e('Default', 'ar-for-wordpress');?></option>
                          <?php 
                          for ($x = 100; $x >= 0; $x-=10) {
                              echo '<option value="'.$x.'"';
                              if (($ar_zoom_in != 'default')AND($ar_zoom_in==$x)){echo ' selected';}
                              echo '>'.$x.'%</option>';
                          }
                          ?>
                        </select></span>
                        <span style="float:left">
                        <?php _e('Out', 'ar-for-wordpress');?> <select name="_ar_zoom_out" id="_ar_zoom_out" class="ar-input" style="min-width:100px" <?php echo $disabled;?>>
                          <option value="default" <?php if (($ar_zoom_out == 'default')OR($ar_zoom_out == '')){echo 'selected';}?>><?php _e('Default', 'ar-for-wordpress');?></option>
                          <?php 
                          for ($x = 0; $x <= 100; $x+=10) {
                              echo '<option value="'.$x.'"';
                              if (($ar_zoom_out != 'default')AND($ar_zoom_out==$x)){echo ' selected';}
                              echo '>'.$x.'%</option>';
                          }
                          ?>
                        </select></span>
                        <div style="clear:both"></div>
                        <br>
                        <?php 
                        //Checkbox Field Array
                        $hide_rotate_limit = '';
                        $field_array = array('_ar_animation' => 'Animation - Play/Pause button', '_ar_autoplay' => 'Animation - Auto Play', '_ar_environment_image' => 'Legacy lighting','_ar_variants' => 'Model includes variants', '_ar_rotate_limit' => 'Set Limits');
                        foreach ($field_array as $field => $title){
                        if ($field=='_ar_rotate_limit'){
                                if (get_post_meta( $model_array['id'], $field, true )=='1'){
                                    $hide_rotate_limit = 'border-color:#49848f';
                                }
                                ?>
                    	<div style="clear:both"></div>
                    </div> <!-- end of Accordian Panel -->
                    <button class="ar_accordian" id="ar_rotation_acc" type="button"><?php _e('Rotation Limits', 'ar-for-wordpress' ); echo $premium_only;?></button>
                    <div id="ar_rotation_panel" class="ar_accordian_panel"><br>
                            <?php
                            }
                            ?>
                        
                            <div style="float:left">
                                <div class="ar_admin_label"><label for="<?php echo $field?>"><?php _e( $title, 'ar-for-wordpress' );?> </label> </div>
                    	        <div class="ar_admin_field" style="padding-right:20px"><input type="checkbox" name="<?php echo $field?>" id="<?php echo $field?>" class="ar-ui-toggle" value="1" <?php if (get_post_meta( $model_array['id'], $field, true )=='1'){echo 'checked';} echo $disabled;?>></div>
                            </div>
                        <?php 
                            if ($field=='_ar_autoplay'){
                                //check in animations in the file and list
                                ?><div style="clear:both"></div>
                                <div id="animationDiv" style="display:none">
                                    <div class="ar_admin_label"><label for="_ar_animation_selection"><?php _e( 'Animation Selection', 'ar-for-wordpress' );?></label></div>
                        	        <div class="ar_admin_field"><select name="_ar_animation_selection" id="_ar_animation_selection" class="ar-input ar-input-wide" <?php echo $disabled;?>></select></div>
                    	        </div><div style="clear:both"></div>
       <?php
                            }
                            
                            
                        } 
                        //if ar_rotate_limit is true show limit options
                        $ar_compass_top_value = '';
                        $ar_compass_top_selected = '';
                        if (get_post_meta( $model_array['id'], '_ar_compass_top_value', true )){
                    	    $ar_compass_top_value = get_post_meta( $model_array['id'], '_ar_compass_top_value', true );
                    	    $ar_compass_top_selected = 'style="background-color:#f37a23 !important"';
                    	}
                    	$ar_compass_bottom_value = '';
                        $ar_compass_bottom_selected = '';
                        if (get_post_meta( $model_array['id'], '_ar_compass_bottom_value', true )){
                    	    $ar_compass_bottom_value = get_post_meta( $model_array['id'], '_ar_compass_bottom_value', true );
                    	    $ar_compass_bottom_selected = 'style="background-color:#f37a23 !important"';
                    	}
                    	$ar_compass_left_value = '';
                        $ar_compass_left_selected = '';
                        if (get_post_meta( $model_array['id'], '_ar_compass_left_value', true )){
                    	    $ar_compass_left_value = get_post_meta( $model_array['id'], '_ar_compass_left_value', true );
                    	    $ar_compass_left_selected = 'style="background-color:#f37a23 !important"';
                    	}
                    	$ar_compass_right_value = '';
                        $ar_compass_right_selected = '';
                        if (get_post_meta( $model_array['id'], '_ar_compass_right_value', true )){
                    	    $ar_compass_right_value = get_post_meta( $model_array['id'], '_ar_compass_right_value', true );
                    	    $ar_compass_right_selected = 'style="background-color:#f37a23 !important"';
                    	}
                    	
                        ?>
                        <br>
                        <div id="ar_rotation_limits" class="ar_rotation_limits_containter" style="<?php echo $hide_rotate_limit;?>">
                            <center>
                                <input id="camera_view_button" class="button" type="button" style="margin-top: 10px; margin-left: -200px;" value="<?php _e( 'Set Current Camera View as Initial First', 'ar-for-wordpress' );?>" <?php echo $disabled;?> />
                                <br clear="all">
                                <p><?php _e( 'Then rotate your model to each of your desired limits and click the arrows to apply.', 'ar-for-wordpress' ); ?></p>
                                <div class="ar-compass-container">
                                    <img src="<?php echo esc_url( plugins_url( "assets/images/rotate_up_arrow.png", __FILE__ ) );?>" alt="Compass" id="ar-compass-image" class="ar-compass-image">
                                    <button id = "ar-compass-top" class="ar-compass-button ar-compass-top" <?php echo $ar_compass_top_selected; ?> data-rotate="0" type="button">&UpArrowBar;</button>
                                    <button id = "ar-compass-bottom" class="ar-compass-button ar-compass-bottom" <?php echo $ar_compass_bottom_selected; ?> data-rotate="180" type="button">&DownArrowBar;</button>
                                    <button id = "ar-compass-left" class="ar-compass-button ar-compass-left" <?php echo $ar_compass_left_selected; ?> data-rotate="270" type="button">&LeftArrowBar;</button>
                                    <button id = "ar-compass-right" class="ar-compass-button ar-compass-right" <?php echo $ar_compass_right_selected; ?> data-rotate="90" type="button">&RightArrowBar;</button>
                                </div>
                            </center>
                            <input id="_ar_compass_top_value" name="_ar_compass_top_value" type="hidden" value="<?php echo $ar_compass_top_value;?>" <?php echo $disabled;?>> 
                            <input id="_ar_compass_bottom_value" name="_ar_compass_bottom_value" type="hidden" value="<?php echo $ar_compass_bottom_value;?>" <?php echo $disabled;?>> 
                            <input id="_ar_compass_left_value" name="_ar_compass_left_value" type="hidden" value="<?php echo $ar_compass_left_value;?>" <?php echo $disabled;?>> 
                            <input id="_ar_compass_right_value" name="_ar_compass_right_value" type="hidden" value="<?php echo $ar_compass_right_value;?>" <?php echo $disabled;?>> 
                        </div>
                    </div> <!-- end of Accordian Panel -->
                    <button class="ar_accordian" id="ar_disable_elements_acc" type="button"><?php _e('Disable/Hide Elements', 'ar-for-wordpress' ); if ($disabled!=''){echo ' - '.__('Premium Plans Only', 'ar-for-wordpress');}?></button>
                    <div id="ar_disable_elements_panel" class="ar_accordian_panel">
                        <br>
                    	<?php 
                        //Checkbox Field Array
                        $field_array = array('_ar_view_hide' => 'AR View Button', '_ar_rotate' => 'Auto Rotate', '_ar_hide_dimensions' =>'Dimensions', '_ar_prompt' => 'Interaction Prompt', '_ar_resizing' => 'Resizing in AR', '_ar_qr_hide' => 'QR Code', '_ar_hide_reset' =>'Reset Button', '_ar_disable_zoom' => 'Zoom');
                        foreach ($field_array as $field => $title){
                        ?>
                            <div style="float:left">
                            <div class="ar_admin_label"><label for="<?php echo $field?>"><?php _e( $title, 'ar-for-wordpress' );?> </label> </div>
                    	    <div class="ar_admin_field" style="padding-right:20px"><input type="checkbox" name="<?php echo $field?>" id="<?php echo $field?>" class="ar-ui-toggle" value="1" <?php if (get_post_meta( $model_array['id'], $field, true )=='1'){echo 'checked';} echo $disabled;?>></div>
                            </div>
                        <?php 
                        
                        } 
                    	?>
                    	<div style="clear:both"></div>
                    </div> <!-- end of Accordian Panel -->
                    
                        <?php 
                    $hotspot_count = 0;
                    if ($public != 'y'){ ?>
                        <button class="ar_accordian" id="ar_qr_code_acc" type="button"><?php _e('QR Code Options', 'ar-for-wordpress' ); echo $premium_only;?></button>
                        <div id="ar_qr_code_panel" class="ar_accordian_panel">
                        <br>
                        	<?php $ar_qr_destination = get_post_meta( $model_array['id'], '_ar_qr_destination_mv', true );?>
                        	<div class="ar_admin_label"><label for="_ar_qr_image"><?php _e('QR Code Destination', 'ar-for-wordpress' );?></div>
                            <div class="ar_admin_field"><select id="_ar_qr_destination_mv" name="_ar_qr_destination_mv" class="ar-input ar-input-wide" <?= $disabled;?>>
                              <option value=""><?php _e('Use Global Setting', 'ar-for-wordpress' );?></option>
                              <option value="parent-page" <?php
                                if ($ar_qr_destination=='parent-page'){
                                    echo 'selected';
                                }
                              ?>><?php _e('Parent Page', 'ar-for-wordpress' );?></option>
                              <option value="model-viewer" <?php
                                if ($ar_qr_destination=='model-viewer'){
                                    echo 'selected';
                                }
                              ?>
                              ><?php _e('AR View', 'ar-for-wordpress' );?></option>
                              </select>
                            </div>
                            
                            <div style="clear:both"></div>
                        	<div class="ar_admin_label"><label for="_ar_qr_image"><?php _e( 'Custom QR Code Image', 'ar-for-wordpress' ); echo "<br>"; _e('<span class="ar_label_tip">(JPG or PNG)</span>', 'ar-for-wordpress' );?></label></div>
                            <div class="ar_admin_field"><input type="url" pattern="https?://.+" title="<?php _e('Secure URLs only','ar-for-wordpress'); ?> https://" placeholder="https://" name="_ar_qr_image" id="_ar_qr_image" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_ar_qr_image', true );?>" <?php echo $disabled;?>> <input id="upload_qr_image_button" class="upload_qr_image_button button" type="button" value="<?php _e( 'Upload', 'ar-for-wordpress' );?>" <?php echo $disabled;?>/> <a href="#" onclick="document.getElementById('_ar_qr_image').value = ''"><img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"></a></div>
                        	
                            <div style="clear:both"></div>
                        	<div class="ar_admin_label"><label for="_ar_qr_destination"><?php _e( 'Custom QR Code URL', 'ar-for-wordpress' ); echo "<br>"; _e('<span class="ar_label_tip"></span>', 'ar-for-wordpress' );?></label></div>
                            <div class="ar_admin_field"><input type="url" pattern="https?://.+" title="<?php _e('Secure URLs only','ar-for-wordpress'); ?> https://" placeholder="https://" name="_ar_qr_destination" id="_ar_qr_destination" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_ar_qr_destination', true );?>" <?php echo $disabled;?>>  <a href="#" onclick="document.getElementById('_ar_qr_destination').value = ''"><img src="<?=esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"></a></div>
                        	
                            <div style="clear:both"></div>
                        </div> <!-- end of Accordian Panel -->
                        <button class="ar_accordian" id="ar_additional_interactions_acc" type="button"><?php _e('Additional Interactions', 'ar-for-wordpress' ); echo $premium_only;?></button>
                        <div id="ar_additional_interactions_panel" class="ar_accordian_panel">
                        <br>
                            <div class="ar_admin_label"><label for="_ar_cta"><?php _e( 'Call To Action Button', 'ar-for-wordpress' ); ?></label><br><span class="ar_label_tip"><?php _e( 'Button Displays in 3D Model view and in AR view on Android only', 'ar-for-wordpress' );?></span></div>
                            <div class="ar_admin_field"><input type="text" name="_ar_cta" id="_ar_cta" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_ar_cta', true );?>" <?php echo $disabled;?> placeholder="Click For More"> </div>
                            <div style="clear:both"></div>
                            <div class="ar_admin_label"><label for="_ar_cta_url"><?php _e( 'Call To Action URL', 'ar-for-wordpress' ); ?></label></div>
                            <div class="ar_admin_field"><input type="url" pattern="https?://.+" name="_ar_cta_url" id="_ar_cta_url" class="regular-text" value="<?php echo get_post_meta( $model_array['id'], '_ar_cta_url', true );?>" <?php echo $disabled;?> placeholder="https://"> </div>
                        	<div style="clear:both"></div><hr>
                            <div class="ar_admin_label"><label for="_ar_hotspot_text"><?php _e( 'Hotspots', 'ar-for-wordpress' );?></label><br><span class="ar_label_tip"><?php _e( 'Add your text which can include html and an optional link, click the Add Hotspot button, then click on your model where you would like it placed', 'ar-for-wordpress' );?></span></div>
                        	<div class="ar_admin_field"><input type="text" name="_ar_hotspot_text" id="_ar_hotspot_text" class="ar-input" style="width: 140px;" placeholder="<?php _e( 'Hotspot Text', 'ar-for-wordpress' );?>" <?php echo $disabled;?>> <input type="text" name="_ar_hotspot_link" id="_ar_hotspot_link" class="ar-input" style="width: 140px;" placeholder="<?php _e( 'Hotspot Link', 'ar-for-wordpress' );?>" <?php echo $disabled;?>>
                            	<input type="checkbox" name="_ar_hotspot_check" id="_ar_hotspot_check" class="regular-text" value="y" style="display:none;">
                            	<input type="button" class="button" onclick="enableHotspot()" value="<?php _e( 'Add Hotspot', 'ar-for-wordpress' );?>" <?php echo $disabled;?>>
                            </div>
                            
                        	<div style="clear:both"></div>
                        	<?php 
                        	if (get_post_meta( $model_array['id'], '_ar_hotspots', true )){
                        	    $_ar_hotspots = get_post_meta( $model_array['id'], '_ar_hotspots', true );
                        	    $hotspot_count = count($_ar_hotspots['annotation']);
                        	    $hide_remove_btn = '';
                        	    foreach ($_ar_hotspots['annotation'] as $k => $v){
                        	        if (isset($_ar_hotspots["link"][$k])){
                        	            $link = $_ar_hotspots["link"][$k];
                        	        }else{
                        	            $link ='';
                        	        }
                        	        echo '<div id="_ar_hotspot_container_'.$k.'"><div class="ar_admin_label"><label for="ar_admin_label">Hotspot '.$k.'</label></div><div class="ar_admin_field" id="_ar_hotspot_field_'.$k.'">
                        	        <input hidden="true" id="_ar_hotspots[data-normal]['.$k.']" name="_ar_hotspots[data-normal]['.$k.']" value="'.$_ar_hotspots['data-normal'][$k].'">
                        	        <input hidden="true" id="_ar_hotspots[data-position]['.$k.']" name="_ar_hotspots[data-position]['.$k.']" value="'.$_ar_hotspots['data-position'][$k].'">
                        	        <input type="text" class="regular-text hotspot_annotation" id="_ar_hotspots[annotation]['.$k.']" name="_ar_hotspots[annotation]['.$k.']" hotspot_name="hotspot-'.$k.'" value="'.$v.'">
                        	        <input type="text" class="regular-text hotspot_annotation" id="_ar_hotspots[link]['.$k.']" name="_ar_hotspots[link]['.$k.']" hotspot_link="hotspot-'.$k.'" value="'.$link.'" placeholder="Link">
                        	        </div></div><div style="clear:both"></div>';
                        	    
                        	    }
                        	}else{
                        	    $hide_remove_btn = 'style="display:none;"';
                        	    echo '<div id="_ar_hotspot_container_0"></div>';
                        	}
                        	?>
                        	<div class="ar_admin_label"><label for="_ar_remove_hotspot"></label></div>
                        	<div class="ar_admin_field"><input id="_ar_remove_hotspot" type="button" class="button" <?php echo $hide_remove_btn;?> onclick="removeHotspot()" value="Remove last hotspot" <?php echo $disabled;?>></div>
                        </div> <!-- end of Accordian Panel --> 	
                        <button class="ar_accordian" id="ar_alternative_acc" type="button"><?php _e('Alternative Model For Mobile', 'ar-for-wordpress' ); echo $premium_only; ?></button>
                        <div id="ar_additional_interactions_panel" class="ar_accordian_panel">
                        <br>
                            <div style="clear:both"></div>
                            <div class="ar_admin_label"><label for="_ar_mobile_id"><?php _e( 'Display a different AR model when viewing on mobile devices', 'ar-for-wordpress' );?></label></div>
                            <?php 
                            $temp_post = $post;
                            //Get list of AR Models
                            $args = array(
                                'post_type'=> 'armodels',
                                'orderby'        => 'title',
                                'posts_per_page' => -1,
                                'order'    => 'ASC'
                            );           
                            $ar_id_array = array();
                            $the_query = new WP_Query( $args );
                            if($the_query->have_posts() ) { 
                                while ( $the_query->have_posts() ) { 
                                    $the_query->the_post();
                                    $mob_title = get_the_title();
                                    $mob_id = get_the_ID();
                                    if (($mob_title)){
                                        $ar_id_array[$mob_id] = $mob_title;
                                    }
                                } 
                                wp_reset_postdata(); 
                            }
                            $post = $temp_post;
                            ?>
                            
                        	<div class="ar_admin_field"><select name="_ar_mobile_id" id="_ar_mobile_id" class="ar-input ar-input-wide" <?php echo $disabled;?>>
                        	    <option value=''></option>
                        	    <?php
                        	    foreach ($ar_id_array as $mob_id => $mob_title){
                        	        if ($mob_id != $model_array['id']){
                        	            echo '<option value="'.$mob_id.'" '.selected( get_post_meta( $model_array['id'], '_ar_mobile_id', true ), $mob_id ).'>'.$mob_title.' (#'.$mob_id.')</option>';
                        	        }
                        	    }
                        	    ?>
                        	</select></div>
                            <div style="clear:both"></div>

                            <div class="ar_admin_label"><label for="_ar_alternative_id"><?php _e( 'Display a different AR model when viewing on AR mode', 'ar-for-wordpress' );?></label></div>
                            <div class="ar_admin_field"><select name="_ar_alternative_id" id="_ar_alternative_id" class="ar-input ar-input-wide" <?php echo $disabled;?>>
                                <option value=''></option>
                                <?php
                                foreach ($ar_id_array as $mob_id => $mob_title){
                                    if ($mob_id != $model_array['id']){
                                        echo '<option value="'.$mob_id.'" '.selected( get_post_meta( $model_array['id'], '_ar_alternative_id', true ), $mob_id ).'>'.$mob_title.' (#'.$mob_id.')</option>';
                                    }
                                }
                                ?>
                            </select></div>
                            <div style="clear:both"></div>
                        </div> <!-- end of Accordian Panel -->
                        <button class="ar_accordian" id="ar_element_positions_acc" type="button"><?php _e('Element Positions and CSS Styles', 'ar-for-wordpress' );echo $premium_only;?></button>
                        <div id="ar_additional_interactions_panel" class="ar_accordian_panel">
                        <br>
                            <div style="clear:both"></div>
                            <input type="button" class="button" style="float:right" onclick="importCSS()" value="<?php _e( 'Import Global Settings', 'ar-for-wordpress' );?>" <?php echo $disabled;?>>
                            <div class="ar_admin_label"><label for="_ar_css_override"><strong><?php _e( 'Override Global Settings', 'ar-for-wordpress' );?></strong></label></div>
                        	<div class="ar_admin_field"><input type="checkbox" name="_ar_css_override" id="_ar_css_override" class="ar-ui-toggle" value="1" <?php if (get_post_meta( $model_array['id'], '_ar_css_override', true )=='1'){echo 'checked';$hide_custom_css='';}else{$hide_custom_css='style="display:none;"';} echo $disabled;?>> </div>
                            <div style="clear:both"></div>
                            <div id="ar_custom_css_div" <?php //echo $hide_custom_css;?>>
                            <br>
                            
                                <?php //CSS Positions
                                $ar_css_positions = get_post_meta( $model_array['id'], '_ar_css_positions', true );
                                foreach ($ar_css_names as $k => $v){
                                    ?>
                                    <div style="float:left;padding-right:20px">
                                      <div style="width:160px;float:left;"><strong>
                                          <?php _e($k, 'ar-for-wordpress' );?> </strong></div>
                                      <div style="float:left;"><select id="_ar_css_positions[<?=$k;?>]" name="_ar_css_positions[<?=$k;?>]" class="ar-input" <?= $disabled;?>>
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
                                          
                                    <br  clear="all">
                                    <br>
                                    </div>
                                <?php
                                }
                                ?>
                             <br  clear="all">
                                <div >
                                  <div style="width:160px;float:left;"><strong>
                                      <?php
                                        $ar_css = get_post_meta( $model_array['id'], '_ar_css', true );
                                        $ar_css_import_global='';
                                        if (get_option('ar_css')!=''){
                                            $ar_css_import_global = get_option('ar_css');
                                        }
                                        $ar_css_import=ar_curl(esc_url( plugins_url( "assets/css/ar-display-custom.css", __FILE__ ) ));
                                  
                                	    _e('CSS Styling', 'ar-for-wordpress' );
                                        ?>
                                        </strong>
                                        </div>
                                  <div style="float:left;"><textarea id="_ar_css" name="_ar_css" style="width: 350px; height: 200px;" <?= $disabled;?>><?php echo $ar_css; ?></textarea></div>
                                </div>
                        </div> <!-- end of Accordian Panel --> 
                        <?php } ?>
                    </div>
                </div>
            
                    <?php 
                        /* Display the 3D model if it exists */
                        $hide_ar_view = '';
                        if (get_post_meta($model_array['id'], '_glb_file', true )==''){ $hide_ar_view = 'display:none;';}
                        echo '<div class="ar_admin_viewer" id="ar_admin_model_'.$model_array['id'].'" style="padding: 10px; '.$hide_ar_view.'">';
                            echo '<div style="width: 100%; border: 1px solid #f8f8f8;">'.ar_display_shortcode($model_array).'</div>'; 
                            $ar_camera_orbit = get_post_meta( $model_array['id'], '_ar_camera_orbit', true );
                            if ($public != 'y'){
                            ?>
                        
                            <button id="downloadPosterToBlob" onclick="downloadPosterToDataURL()" class="button" type="button" style="margin-top:10px"><?php _e( 'Set AR Poster Image', 'ar-for-wordpress' );?></button>
                            <input type="hidden" id="_ar_poster_image_field" name="_ar_poster_image_field">
                            
                            <input id="camera_view_button" class="button" type="button" style="float:right;margin-top: 10px" value="<?php _e( 'Set Current Camera View as Initial', 'ar-for-wordpress' );?>" <?php echo $disabled;?> />
                            <div id="_ar_camera_orbit_set" style="float:right;margin: 10px;display:none"><span style="color:green;margin-left: 7px; font-size: 19px;">&#10004;</span></div>
                            <input id="_ar_camera_orbit" name="_ar_camera_orbit" type="text" value="<?php echo $ar_camera_orbit;?>" style="display:none;"><br clear="all" style="float:right;">
                        
                        <?php  
                        }
                        
                        ?>
                    </div>
                
                <div style="clear:both"></div>
                   
                        
                       
                    <?php
                    
                    if($plan_check!='Premium') { 
                	    echo '</div>'; 
                	//close the div that disables mouse clicking 
                	}
                	?>
            
    
        <?php
            /*Set post content to include AR shortcode*/
        	//$post = array('ID'=> $model_array['id'], 'post_content' => '[ardisplay id='.$model_array['id'].']');
            wp_update_post( $post );
            //Output Upload Choose AR Model Files Javascript
            echo ar_upload_button_js($model_array['id']);
        }
        ?>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Check if jQuery is defined
                if (typeof jQuery !== 'undefined') {
                    // Your jQuery code here
                    jQuery(document).ready(function($) {
                        // Click the element with ID "model_files_tab"
                        $("#model_files_tab").click();
                    });
                } else {
                    // If not using jQuery, use pure JavaScript
                    var modelFilesTab = document.getElementById("model_files_tab");
                    if (modelFilesTab) {
                        // Click the element
                        modelFilesTab.click();
                    }
                }
            });
            function ar_open_tab(evt, tabName, target) {
              // Declare all variables
              var i, ar_tabcontent, ar_tablinks;
              // Get all elements with class="tabcontent" and hide them
              ar_tabcontent = document.getElementsByClassName("ar_tabcontent");
              for (i = 0; i < ar_tabcontent.length; i++) {
                ar_tabcontent[i].style.display = "none";
              }
            
              // Get all elements with class="ar_tablinks" and remove the class "active"
              ar_tablinks = document.getElementsByClassName("ar_tablinks");
              for (i = 0; i < ar_tablinks.length; i++) {
                ar_tablinks[i].className = ar_tablinks[i].className.replace(" active", "");
              }
            
              // Show the current tab, and add an "active" class to the button that opened the tab
              document.getElementById(tabName).style.display = "block";
              //evt.currentTarget.className += " active";
              document.getElementById(target).className += " active";
            }
        
            function ar_activeclass(divId) {
              var element = document.getElementById(divId);
              if (element) {
                element.className += " active";
              }
            }
            
            //Accordian Content
            var acc = document.getElementsByClassName("ar_accordian");
            var i;
            
            for (i = 0; i < acc.length; i++) {
              acc[i].addEventListener("click", function() {
                /* Toggle between adding and removing the "active" class,
                to highlight the button that controls the panel */
                this.classList.toggle("ar_active");
            
                /* Toggle between hiding and showing the active panel */
                var panel = this.nextElementSibling;
                /*if (panel.style.display === "block") {
                  panel.style.display = "none";
                } else {
                  panel.style.display = "block";
                }*/
                if (panel.style.maxHeight) {
                  panel.style.maxHeight = null;
                } else {
                  panel.style.maxHeight = panel.scrollHeight + "px";
                }
              });
            }
        
            //Rotation Limits Compass
            
            const modelViewer = document.querySelector('#model_<?php echo $model_array['id']; ?>');
            const ar_compass_buttons = document.getElementsByClassName('ar-compass-button');
            const ar_compass_image = document.getElementById('ar-compass-image');
            document.getElementById('_ar_rotate_limit').addEventListener('change', function() {
                const min_orbit_arr = modelViewer.getAttribute("min-camera-orbit").split(" ");
                const max_orbit_arr = modelViewer.getAttribute("max-camera-orbit").split(" ");
                var element = document.getElementById("ar_rotation_limits");
                if (document.getElementById("_ar_rotate_limit").checked == true){
                    element.style.display = "block";
                    element.style.borderColor = "#49848f";
                }else{
                    element.style.display = "none";
                    modelViewer.setAttribute("min-camera-orbit", 'auto auto '+min_orbit_arr[2]);
                    modelViewer.setAttribute("max-camera-orbit", 'Infinity auto '+max_orbit_arr[2]);
                    document.getElementById("_ar_compass_top_value").value = '';
                    document.getElementById("_ar_compass_bottom_value").value = '';
                    document.getElementById("_ar_compass_left_value").value = '';
                    document.getElementById("_ar_compass_right_value").value = '';
                    document.getElementById("ar-compass-top").style.backgroundColor = '#e2e2e2';
                    document.getElementById("ar-compass-bottom").style.backgroundColor = '#e2e2e2';
                    document.getElementById("ar-compass-left").style.backgroundColor = '#e2e2e2';
                    document.getElementById("ar-compass-right").style.backgroundColor = '#e2e2e2';
                }
            });
            
            // Add a click event listener to each button
            for (let i = 0; i < ar_compass_buttons.length; i++) {
                ar_compass_buttons[i].addEventListener('mouseenter', function() {
                    const id = this.id;
                    if (id == 'ar-compass-top'){
                        ar_compass_image.style.transform = 'rotate(0deg)';
                    }else if (id == 'ar-compass-bottom'){
                        ar_compass_image.style.transform = 'rotate(180deg)';
                    }else if (id == 'ar-compass-right'){
                        ar_compass_image.style.transform = 'rotate(90deg)';
                    }else if (id == 'ar-compass-left'){
                        ar_compass_image.style.transform = 'rotate(270deg)';
                    }
                });
                ar_compass_buttons[i].addEventListener('click', function() {
                    const id = this.id;
                    const orbit = modelViewer.getCameraOrbit();
                    const min_orbit_arr = modelViewer.getAttribute("min-camera-orbit").split(" ");
                    const max_orbit_arr = modelViewer.getAttribute("max-camera-orbit").split(" ");
                    //Set the input field to the axis rotate value and update the model viewer
                    if (id == 'ar-compass-top'){
                        if (document.getElementById("_ar_compass_top_value").value == ''){
                            var orbitString = `${orbit.phi}rad`;
                            document.getElementById("_ar_compass_top_value").value = orbitString;
                            document.getElementById(id).style.backgroundColor = '#f37a23';
                        }else{
                            var orbitString = `auto`;
                            document.getElementById(id).style.backgroundColor = '#e2e2e2';
                            document.getElementById("_ar_compass_top_value").value = '';
                        }
                        modelViewer.setAttribute("min-camera-orbit", min_orbit_arr[0]+' '+orbitString+' '+min_orbit_arr[2]);
                    }else if (id == 'ar-compass-bottom'){
                        if (document.getElementById("_ar_compass_bottom_value").value == ''){
                            var orbitString = `${orbit.phi}rad`;
                            document.getElementById("_ar_compass_bottom_value").value = orbitString;
                            document.getElementById(id).style.backgroundColor = '#f37a23';
                        }else{
                            var orbitString = `auto`;
                            document.getElementById(id).style.backgroundColor = '#e2e2e2';
                            document.getElementById("_ar_compass_bottom_value").value = '';
                        }
                        modelViewer.setAttribute("max-camera-orbit", max_orbit_arr[0]+' '+orbitString+' '+max_orbit_arr[2]);
                    }else if (id == 'ar-compass-right'){
                        if (document.getElementById("_ar_compass_right_value").value == ''){
                            var orbitString = `${orbit.theta}rad`;
                            document.getElementById("_ar_compass_right_value").value = orbitString;
                            document.getElementById(id).style.backgroundColor = '#f37a23';
                        }else{
                            var orbitString = `Infinity`;
                            document.getElementById(id).style.backgroundColor = '#e2e2e2';
                            document.getElementById("_ar_compass_right_value").value = '';
                        }
                        modelViewer.setAttribute("max-camera-orbit", orbitString+' '+max_orbit_arr[1]+' '+max_orbit_arr[2]);
                    }else if (id == 'ar-compass-left'){
                        if (document.getElementById("_ar_compass_left_value").value == ''){
                            var orbitString = `${orbit.theta}rad`;
                            document.getElementById("_ar_compass_left_value").value = orbitString;
                            document.getElementById(id).style.backgroundColor = '#f37a23';
                        }else{
                            var orbitString = `auto`;
                            document.getElementById(id).style.backgroundColor = '#e2e2e2';
                            document.getElementById("_ar_compass_left_value").value = '';
                        }
                        modelViewer.setAttribute("min-camera-orbit", orbitString+' '+min_orbit_arr[1]+' '+min_orbit_arr[2]);
                    }
                    modelViewer.removeAttribute("auto-rotate");
                    document.getElementById("_ar_rotate").checked = true;
                });
            }
            //Animation Selector
            const animationSelector = document.getElementById('_ar_animation_selection');
            const animationDiv = document.getElementById('animationDiv');
            // Load the model and retrieve animation names
            modelViewer.addEventListener('load', () => {
                const names = modelViewer.availableAnimations;
    
                if (names && names.length > 0) {
                    names.forEach((animationName, index) => {
                        const option = document.createElement('option');
                        option.value = animationName;
                        option.text = animationName || `Animation ${index + 1}`;
                        animationSelector.appendChild(option);
                        // Preselect an option if it matches the PHP variable value
                        if (animationName === "<?php echo get_post_meta( $model_array['id'], '_ar_animation_selection', true );?>") {
                            option.selected = true;
                            modelViewer.animationName = animationName;
                        }
                    });
                    // Set the display style to "block" if animations exist
                    animationDiv.style.display = 'block';
                    // Add event listener to change animations
                    animationSelector.addEventListener('change', () => {
                        const selectedAnimationName = animationSelector.value;
                        modelViewer.animationName = selectedAnimationName;
                    });
                }
            });
            document.getElementById('_ar_disable_zoom').addEventListener('change', function() {
                if (document.getElementById("_ar_disable_zoom").checked == true){
                    modelViewer.setAttribute("disable-zoom",true);
                }else{
                    modelViewer.removeAttribute("disable-zoom");
                }
            });
            document.getElementById('_ar_rotate').addEventListener('change', function() {
                if (document.getElementById("_ar_rotate").checked == true){
                    modelViewer.removeAttribute("auto-rotate");
                }else{
                    modelViewer.setAttribute("auto-rotate",true);
                }
            });
            <?php if ($public != 'y'){ ?>
            document.getElementById('_glb_file').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                element.setAttribute("src", this.value);
                var element2 = document.getElementById("ar_admin_model");
                if (element2) {
                    element2.style.display = "block";
                }
            });

            jQuery(document).on('change','#_skybox_file', function(e) {
                //console.log('skybox changed' + jQuery(this).val());
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                element.setAttribute("skybox-image", jQuery(this).val());
            });

           jQuery(document).on('change','#_ar_environment', function(e) {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                element.setAttribute("environment-image", jQuery(this).val());
            });
            
                document.getElementById('_ar_placement').addEventListener('change', function() {
                    var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                    if (this.value == 'floor'){
                        element.setAttribute("ar-placement", '');
                    }else{
                        element.setAttribute("ar-placement", this.value);
                    }
                });
            <?php } ?>
            document.getElementById('_ar_zoom_in').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                if (this.value == 'default'){
                    element.setAttribute("min-camera-orbit", 'auto auto 20%');
                }else{
                    const min_orbit_arr = element.getAttribute("min-camera-orbit").split(" ");
                    element.setAttribute("min-camera-orbit", min_orbit_arr[0]+' '+min_orbit_arr[1]+' '+(100 - this.value) +'%');
                    
                }
            });
            document.getElementById('_ar_zoom_out').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                if (this.value == 'default'){
                    element.setAttribute("max-camera-orbit", 'Infinity auto 300%');
                }else{
                    const max_orbit_arr = element.getAttribute("max-camera-orbit").split(" ");
                    element.setAttribute("max-camera-orbit", max_orbit_arr[0]+' '+max_orbit_arr[1]+' '+(((this.value/100)*400)+100) +'%');
                }
            });
            document.getElementById('_ar_field_of_view').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                if (this.value == 'default'){
                    element.setAttribute("field-of-view", '');
                }else{
                    element.setAttribute("field-of-view", this.value +'deg');
                }
            });
            document.getElementById('_ar_environment_image').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                if (document.getElementById("_ar_environment_image").checked == true){
                    element.setAttribute("environment-image", 'legacy');
                }else{
                    element.setAttribute("environment-image", '');
                }
            });
            document.getElementById('_ar_exposure').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                element.setAttribute("exposure", this.value);
            });
            document.getElementById('_ar_shadow_intensity').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                element.setAttribute("shadow-intensity", this.value);
            });
            document.getElementById('_ar_shadow_softness').addEventListener('change', function() {
                var element = document.getElementById("model_<?php echo $model_array['id']; ?>");
                element.setAttribute("shadow-softness", this.value);
            });
    
            modelViewer.addEventListener('camera-change', () => {
                const orbit = modelViewer.getCameraOrbit();
                const orbitString = `${orbit.theta}rad ${orbit.phi}rad ${orbit.radius}m`;
                jQuery(document).ready(function($){
                    $( "#camera_view_button" ).click(function() {
                        document.getElementById("_ar_camera_orbit_set").style.display='block';
                        document.getElementById("_ar_camera_orbit").value=orbitString;
                    });
                });
            });
            
            document.getElementById('_ar_view_hide').addEventListener('change', function() {
                var element = document.getElementById("ar-button_<?php echo $model_array['id']; ?>");
                if (document.getElementById("_ar_view_hide").checked == true){
                    element.style.display = "none";
                }else{
                    element.style.display = "block";
                }
            });
            
            document.getElementById('_ar_qr_hide').addEventListener('change', function() {
                var element = document.getElementById("ar-qrcode_<?php echo $model_array['id']; ?>");
                if (document.getElementById("_ar_qr_hide").checked == true){
                    element.style.display = "none";
                }else{
                    element.style.display = "block";
                }
            });
            
            document.getElementById('_ar_hide_dimensions').addEventListener('change', function() {
                var element = document.getElementById("controls");
                var element_checkbox = document.getElementById("show-dimensions_<?php echo $model_array['id']; ?>");
                if (document.getElementById("_ar_hide_dimensions").checked == true){
                    element.style.display = "none";
                    element_checkbox.checked = false;
                    const modelViewer = document.querySelector('#model_<?php echo $model_array['id']; ?>');
                    modelViewer.querySelectorAll('button').forEach((hotspot) => {
                      if ((hotspot.classList.contains('dimension'))||(hotspot.classList.contains('dot'))){
                            hotspot.classList.add('nodisplay');
                      }
                    });
                }else{
                    element.style.display = "block";
                }
            });
            
            document.getElementById('_ar_hide_reset').addEventListener('change', function() {
                var element = document.getElementById("ar-reset_<?php echo $model_array['id']; ?>");
                if (document.getElementById("_ar_hide_reset").checked == true){
                    element.style.display = "none";
                }else{
                    element.style.display = "block";
                }
            });
            
            [ _ar_x, _ar_y, _ar_z ].forEach(function(element) {
                element.addEventListener('change', function() {
                    var x = document.getElementById('_ar_x').value;
                    var y = document.getElementById('_ar_y').value;
                    var z = document.getElementById('_ar_z').value;

                    const updateScale = () => {
                      modelViewer.scale = x +' '+ y +' '+ z;
                    };
                    updateScale();
                });
            });
            document.getElementById('_ar_animation').addEventListener('change', function() {
                var element = document.getElementById("ar-button-animation");
                if (document.getElementById("_ar_animation").checked == true){
                    element.style.display = "block";
                }else{
                    element.style.display = "none";
                }
            });
             
            
            document.body.addEventListener( 'keyup', function ( event ) {
                //Hotspots update on change 
                if( event.target.id.startsWith('_ar_hotspots' )) {
                    var hotspot_name = event.target.getAttribute("hotspot_name");
                    var hotspot_link = event.target.getAttribute("hotspot_link");
                    var match = event.target.id.match(/\[([0-9]+)\]/);
                    var index = match ? match[1] : null;
                    if (hotspot_name){
                        var hotspot_content = document.getElementById(event.target.getAttribute("hotspot_name")).innerHTML;
                        // Extract the index from the currentId
                        if (index !== null) {
                            // Replace "annotation" with "link" and construct the new id
                            var newId = event.target.id.replace('annotation', 'link');
                            var inputlink = document.getElementById(newId).value;
                        }
                        var inputtext = event.target.value;
                    }
                    if (hotspot_link){
                        var inputlink = event.target.value;
                        // Replace "link" with "annotation" and construct the new id
                        var newId = event.target.id.replace('link', 'annotation');
                        var inputtext = document.getElementById(newId).value;
                        var hotspot_name = hotspot_link;
                    }
                        
                    if (inputlink){
                        inputtext = '<a href="'+inputlink+'" target="_blank">'+inputtext+'</a>';
                    }
                    document.getElementById(hotspot_name).innerHTML='<div class="annotation">'+inputtext+'</div>';
                
                };
                //CTA update on change 
                if( event.target.id=='_ar_cta') {
                    document.getElementById("ar-cta-button-container").style="display:block";
                    document.getElementById("ar-cta-button").innerHTML=event.target.value;
                };
            });
            <?php if ($public != 'y'){ ?>
                //Custom CSS Importing
                function importCSS(){
                    var css_content = '<?php if ($ar_css_import_global!=''){ echo ar_encodeURIComponent($ar_css_import_global);}else{echo ar_encodeURIComponent($ar_css_import);}?>';
                    document.getElementById('_ar_css').value = decodeURI(css_content);
                    <?php 
                    $ar_css_positions = get_option('ar_css_positions');
                    if (is_array($ar_css_positions)){
                        foreach ($ar_css_positions as $k => $v){
                              echo "document.getElementById('_ar_css_positions[".$k."]').value = '".$v."';
                              ";
                        }
                    }
                    ?>
                }
                
                document.getElementById('_ar_css_override').addEventListener('change', function() {
                    var element = document.getElementById("ar_custom_css_div");
                    if (document.getElementById("_ar_css_override").checked == true){
                        element.style.display = "block";
                    }else{
                        element.style.display = "none";
                    }
                });
            <?php } ?>
    
      jQuery(document).ready(function($){
        // Convert the JSON-encoded PHP array to a JavaScript array
        var arOpenTabsArray = <?php echo $jsArray; ?>;

        // Loop through each button ID and trigger a click
        arOpenTabsArray.forEach(function (buttonId) {
            var ar_tab_button = document.getElementById(buttonId);

            // Check if the button element exists
            if (ar_tab_button) {
                ar_tab_button.click();
            }
        });
      
        var asset_JsonList = {"asset_Table" : 
            [
                    {"modelMakeID" : "1","modelMake" : "1.0"},
                    {"modelMakeID" : "2","modelMake" : "1.4142"},
                    {"modelMakeID" : "3","modelMake" : "1.25"},
            		{"modelMakeID" : "4","modelMake" : "1.5"},
            		{"modelMakeID" : "5","modelMake" : "1.33"}
            ]};
        var modelTypeJsonList = {"1.0" : 
            [
                    {"modelTypeID" : "1","modelType" : "100%"},
                    {"modelTypeID" : "1.5","modelType" : "150%"},
                    {"modelTypeID" : "2","modelType" : "200%"},
                    {"modelTypeID" : "2.5","modelType" : "250%"},
                    {"modelTypeID" : "3","modelType" : "300%"},
                    {"modelTypeID" : "4","modelType" : "400%"},
                    {"modelTypeID" : "5","modelType" : "500%"}
            ],
            "1.4142" : 
            [
                    {"modelTypeID" : "1","modelType" : "A4 21.0 x 29.7cm / 8.3 x 11.7in"},
                    {"modelTypeID" : "1.41","modelType" : "A3 29.7 x 42cm / 11.7 x 16.5in"},
                    {"modelTypeID" : "2","modelType" : "A2 42 x 59.4cm / 16.5 x 23.4in"},
                    {"modelTypeID" : "2.83","modelType" : "A1 59.4 x 84.1cm / 23.4 x 33.1in"}
            ],
            "1.25" : 
            [
                    {"modelTypeID" : "1","modelType" : "20 x 25cm / 8 x 10in"},
                    {"modelTypeID" : "1.5","modelType" : "30.5 x 38.0cm / 12 x 15in"},
                    {"modelTypeID" : "2","modelType" : "41 x 51cm /16 x 20in"},
                    {"modelTypeID" : "2.5","modelType" : "50.8 x 63.5cm /16 x 20in"},
                    {"modelTypeID" : "3","modelType" : "61 x 76cm / 24 x 30in"}
            ],
            "1.5" : 
            [
                    {"modelTypeID" : "1","modelType" : "20 x 30cm / 8 x 12in"},
                    {"modelTypeID" : "1.5","modelType" : "30 x 46cm / 12 x 18in"},
                    {"modelTypeID" : "2","modelType" : "41 x 61cm / 16 x 24in"},
                    {"modelTypeID" : "2.5","modelType" : "51 x 76cm / 20 x 30in"},
                    {"modelTypeID" : "3","modelType" : "61 x 91cm / 24 x 36in"}
            ],
            "1.33" : 
            [
                    {"modelTypeID" : "1","modelType" : "23 x 30cm / 9 x 12in"},
                    {"modelTypeID" : "1.3","modelType" : "30 x 41cm/ 12 x 16in"},
                    {"modelTypeID" : "1.6","modelType" : "38 x 51cm/ 15 x 20in"},
                    {"modelTypeID" : "2","modelType" : "46 x 61cm / 18 x 24in"}
            ]
        };
        var ModelListItems= "";
        for (var i = 0; i < asset_JsonList.asset_Table.length; i++){
            ModelListItems+= "<option value='" + asset_JsonList.asset_Table[i].modelMakeID + "'>" + asset_JsonList.asset_Table[i].modelMake + "</option>";
        }
        $("#makeSelectionBox").html(ModelListItems);
    
    var updatear_asset_size_options = function(ratio) {
        console.log('updating with ', ratio);
        var listItems = "";
        if (ratio in modelTypeJsonList) {
    
        } else {
    
            ratio = '1.0';
        }
        if (ratio in modelTypeJsonList) {
            for (var i = 0; i < modelTypeJsonList[ratio].length; i++) {
                listItems += "<option value='" + modelTypeJsonList[ratio][i].modelTypeID + "'>" + modelTypeJsonList[ratio][i].modelType + "</option>";
            }
            $("select#ar_asset_size").html(listItems);
            $('#ar_asset_size_container').css('display', 'block');
            if ($('#_ar_asset_file').val()) {
                $('#ar_asset_builder_model_done').html('&#10003;');
                $('#ar_asset_builder_submit_container').css('display', 'block');
            }
        }
    }
    
    function ar_update_size_fn(){ 
        var ratio = $('#ar_asset_ratio').val();
        if (ratio === '1') {
            ratio = '1.0';
        }
        $('#ar_asset_ratio_select').val(ratio);
        // Remove " Matches your Image" from all options
        $('#ar_asset_ratio_select option:not([value="' + ratio + '"])').each(function() {
            var currentText = $(this).text();
            $(this).text(currentText.replace(' - Suggested for your Image', ''));
        });
        // Get the original text of the selected option
        var originalText = $('#ar_asset_ratio_select option[value="' + ratio + '"]').text();
        
        // Update the text content of the selected option
        $('#ar_asset_ratio_select option[value="' + ratio + '"]').text(originalText + ' - Suggested for your Image');
        
        
        updatear_asset_size_options(ratio); 
        $('#ar_asset_builder_texture_done').html('&#10003;');
    }  
    ar_update_size_function = ar_update_size_fn;
    $("select#ar_asset_ratio_select").on('change',function(){
        var selectedRatio = $('#ar_asset_ratio_select option:selected').val();
        $('#ar_asset_ratio').val(selectedRatio);
        updatear_asset_size_options(selectedRatio);
    });  
    //Update the scale of the model
    $("select#ar_asset_size").on('change',function(){
        var selectedSize = $('#ar_asset_size option:selected').val();
        $('#_ar_x').val(selectedSize);
        $('#_ar_y').val(selectedSize);
        $('#ar_asset_builder_size_done').html('&#10003;');
        
    });  
});
function calculateImageRatio() {
      var imageUrl = jQuery('#_asset_texture_file_0').val();
      // Create an image element dynamically
      var img = new Image();

      // Set the source URL for the image
      img.src = imageUrl;

      // Wait for the image to load
      img.onload = function() {
        // Determine if the image is landscape or portrait
        var orientation;
        if (img.width > img.height) {
          orientation = 'landscape';
        } else if (img.width < img.height) {
          orientation = 'portrait';
        } else {
          orientation = 'square';
        }

        // Set the longer dimension as width
        var width = (orientation === 'landscape') ? img.width : img.height;
        var height = (orientation === 'landscape') ? img.height : img.width;

        // Update the select field with the orientation
        //jQuery('#ar_asset_orientation').find('option[value="' + orientation + '"]').prop('selected', true);
        jQuery('#ar_asset_orientation').val(orientation);
        // Calculate the width-to-height ratio
        var ratio = width / height;

        // Define the target ratios
        //var targetRatios = [2 / 3, 4 / 5, 3 / 4, 11 / 14, 1.4142]; // A4:A3 paper ratio is approximately 1.4142
        var targetRatios = [1.0, 1.5, 1.25, 1.33, 1.27, 1.4142]; // A4:A3 paper ratio is approximately 1.4142

        // Find the closest ratio
        var closestRatio = findClosestRatio(ratio, targetRatios);

        // Output the result
        jQuery('#ar_asset_ratio').val(closestRatio);
        //alert('Closest Ratio: ' + closestRatio);
         // Execute the ar_update_size_fn function
        ar_update_size_function(closestRatio);
      };
    }

    // Function to find the closest ratio
    function findClosestRatio(actualRatio, targetRatios) {
      var closestRatio = targetRatios[0];
      var minDifference = Math.abs(actualRatio - targetRatios[0]);

      for (var i = 1; i < targetRatios.length; i++) {
        var difference = Math.abs(actualRatio - targetRatios[i]);
        if (difference < minDifference) {
          minDifference = difference;
          closestRatio = targetRatios[i];
        }
      }

      return closestRatio;
    }
    function asset_display_thumb() {
        
        var imageUrl = jQuery('#_asset_texture_file_0').val();
        jQuery('#asset_thumb_img').attr('src', imageUrl);
    }
    
    // Trigger the function when the value of _asset_texture_file_0 changes
    jQuery('#_asset_texture_file_0').on('input', calculateImageRatio);
    jQuery('#_asset_texture_file_0').on('input', asset_display_thumb);
    </script>
        <script nonce="<?php wp_create_nonce('set_ar_featured_image'); ?>">
            
            //Save screenshot of model
            function downloadPosterToDataURL() {
                var btn = document.getElementById("downloadPosterToBlob");
                btn.innerHTML = 'Creating Image';
                btn.disabled = true;
                const url = modelViewer.toDataURL("image/png").replace("image/png", "image/octet-stream");
                const a = document.createElement("a");
                document.getElementById("_ar_poster_image_field").value=url;
                var xhr = new XMLHttpRequest();
                //document.getElementById("nonce").value="<?php wp_create_nonce('set_ar_featured_image'); ?>"
                var data = new FormData();
                data.append('post_ID', document.getElementById("post_ID").value);
                
                if(document.getElementById("original_post_title")){
                    data.append('post_title', document.getElementById("original_post_title").value);
                } else if(document.getElementsByClassName("wp-block-post-title")) {
                    data.append('post_title', document.getElementsByClassName("wp-block-post-title")[0].value);
                } else {
                    data.append('post_title','armodel-' + document.getElementById("post_ID").value);
                }
                data.append('_ar_poster_image_field',document.getElementById("_ar_poster_image_field").value);
                data.append('action',"set_ar_featured_image");
                data.append('nonce',"<?php echo wp_create_nonce('set_ar_featured_image'); ?>");
                //data.nonce = "<?php wp_create_nonce('set_ar_featured_image'); ?>";
               // console.log(data);
                xhr.open("POST", "<?php echo site_url('wp-json/arforwp/v2/set_ar_featured_image/');?>", true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                /*xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var attachmentID = xhr.responseText; 
                    wp.media.featuredImage.set( attachmentID );
                   }
                };*/

                //convert to json
                var object = {};
                data.forEach(function(value, key){
                    object[key] = value;
                });
                var json = JSON.stringify(object);


                xhr.onload = function () { 
                    var attachmentID = xhr.responseText; 
                    wp.media.featuredImage.set( attachmentID );
                    btn.innerHTML = 'Set AR Poster Image';
                    btn.disabled = false;
                }
                
                xhr.send(json);
                return false;
            }
        </script>
        <!-- HOTSPOTS -->
        <!-- The following libraries and polyfills are recommended to maximize browser support -->
        <!-- Web Components polyfill to support Edge and Firefox < 63 -->
        <script src="https://unpkg.com/@webcomponents/webcomponentsjs@2.1.3/webcomponents-loader.js"></script>
        <!-- Intersection Observer polyfill for better performance in Safari and IE11 -->
        <script src="https://unpkg.com/intersection-observer@0.5.1/intersection-observer.js"></script>
        <!-- Resize Observer polyfill improves resize behavior in non-Chrome browsers -->
        <script src="https://unpkg.com/resize-observer-polyfill@1.5.1/dist/ResizeObserver.js"></script>
        <script>
            var hotspotCounter = <?php echo $hotspot_count; ?>;
            function addHotspot(MouseEvent) {
                //var _ar_hotspot_check = document.getElementById('_ar_hotspot_check').value;
                if (document.getElementById("_ar_hotspot_check").checked != true){
                return;
                    
                }
                var inputtext = document.getElementById('_ar_hotspot_text').value;
            
                // if input = nothing then alert error if it isnt then add the hotspot
                if (inputtext == ""){
                    alert("<?php _e( 'Enter hotspot text first, then click the Add Hotspot button.', 'ar-for-wordpress' );?>");
                    return;
                }else{
                    var inputlink = document.getElementById('_ar_hotspot_link').value;
                    if (inputlink){
                        inputtext = '<a href="'+inputlink+'" target="_blank">'+inputtext+'</a>';
                    }
                    const viewer = document.querySelector('#model_<?php echo $model_array['id']; ?>');
                    const x = event.clientX;
                    const y = event.clientY;
                    const positionAndNormal = viewer.positionAndNormalFromPoint(x, y);
                    
                    // if the model is not clicked return the position in the console
                    if (positionAndNormal == null) {
                        console.log('no hit result: mouse = ', x, ', ', y);
                        return;
                    }
                    const {position, normal} = positionAndNormal;
                    
                    // create the hotspot
                    const hotspot = document.createElement('button');
                    hotspot.slot = `hotspot-${hotspotCounter ++}`;
                    hotspot.classList.add('hotspot');
                    hotspot.id = `hotspot-${hotspotCounter}`;
                    hotspot.dataset.position = position.toString();
                    if (normal != null) {
                        hotspot.dataset.normal = normal.toString();
                    }
                    viewer.appendChild(hotspot);
                    
                    // adds the text to last hotspot
                    var element = document.createElement("div");
                    element.classList.add('annotation');
                    element.innerHTML = inputtext;
                    document.getElementById(`hotspot-${hotspotCounter}`).appendChild(element);
                    
                    //Add Hotspot Input fields
                    var hotspot_container = document.getElementById(`_ar_hotspot_container_${hotspotCounter -1}`);
                    hotspot_container.insertAdjacentHTML('afterend', `<div style="clear:both"></div><div id="_ar_hotspot_container_${hotspotCounter}" style="padding-bottom: 10px"><div class="ar_admin_label"><label for="ar_admin_field">Hotspot ${hotspotCounter}</label></div><div class="ar_admin_field" id="_ar_hotspot_field_${hotspotCounter}">`);
                    
                    var hotspot_fields = document.getElementById(`_ar_hotspot_field_${hotspotCounter}`);
                    
                    var inputList = document.createElement("input");
                    inputList.setAttribute('type','text');
                    inputList.setAttribute('class','regular-text hotspot_annotation');
                    inputList.setAttribute('id',`_ar_hotspots[link][${hotspotCounter}]`);
                    inputList.setAttribute('name',`_ar_hotspots[link][${hotspotCounter}]`);
                    inputList.setAttribute('hotspot_name',`hotspot-${hotspotCounter}`);
                    inputList.setAttribute('value',document.getElementById('_ar_hotspot_link').value);
                    inputList.setAttribute('placeholder','Link');
                    hotspot_fields.insertAdjacentElement('afterend', inputList);   
                    
                    var inputList = document.createElement("input");
                    inputList.setAttribute('type','text');
                    inputList.setAttribute('class','regular-text hotspot_annotation');
                    inputList.setAttribute('id',`_ar_hotspots[annotation][${hotspotCounter}]`);
                    inputList.setAttribute('name',`_ar_hotspots[annotation][${hotspotCounter}]`);
                    inputList.setAttribute('hotspot_name',`hotspot-${hotspotCounter}`);
                    inputList.setAttribute('value',document.getElementById('_ar_hotspot_text').value);
                    inputList.setAttribute('placeholder','Annotation');
                    hotspot_fields.insertAdjacentElement('afterend', inputList);
                    
                    var inputList = document.createElement("input");
                    inputList.setAttribute('hidden','true');
                    inputList.setAttribute('id',`_ar_hotspots[data-position][${hotspotCounter}]`);
                    inputList.setAttribute('name',`_ar_hotspots[data-position][${hotspotCounter}]`);
                    inputList.setAttribute('value',hotspot.dataset.position);
                    hotspot_fields.insertAdjacentElement('afterend', inputList);
                    
                    var inputList = document.createElement("input");
                    inputList.setAttribute('hidden','true');
                    inputList.setAttribute('id',`_ar_hotspots[data-normal][${hotspotCounter}]`);
                    inputList.setAttribute('name',`_ar_hotspots[data-normal][${hotspotCounter}]`);
                    inputList.setAttribute('value',hotspot.dataset.normal);
                    hotspot_fields.insertAdjacentElement('afterend', inputList);
                    
                    hotspot_fields.insertAdjacentHTML('afterend', '</div></div>');
                    
                    var additionalPanel = document.getElementById("ar_additional_interactions_panel");

                    // Check if the element exists
                    if (additionalPanel) {
                        // Get the current height and add 100px to it
                        var newHeight = additionalPanel.offsetHeight + 100;
                    
                        // Set the new height to the element
                        //additionalPanel.style.height = newHeight + "px";
                        additionalPanel.style.maxHeight = newHeight + "px";
                    }
                    //Reset hotspot text box and checkbox
                    document.getElementById('_ar_hotspot_text').value = "";
                    document.getElementById('_ar_hotspot_link').value = "";
                    document.getElementById("_ar_hotspot_check").checked = false;
                    
                    //Show Remove Hotspot button
                    document.getElementById('_ar_remove_hotspot').style = "display:block;";
                }
            }
            function enableHotspot(){
                var inputtext = document.getElementById('_ar_hotspot_text').value;
                if (inputtext == ""){
                    alert("<?php _e( 'Enter hotspot text first, then click Add Hotspot button.', 'ar-for-wordpress' );?>");
                    return;
                }else{
                    document.getElementById("_ar_hotspot_check").checked = true;
                }
            }
            function removeHotspot(){
                var el = document.getElementById(`_ar_hotspot_container_${hotspotCounter}`);
                var el2 = document.getElementById(`hotspot-${hotspotCounter}`);
                if (el == null){
                    alert("No hotspots to delete");
                }else{
                    hotspotCounter --;
                    el.remove(); // Removes the last added hotspot fields
                    el2.remove(); // Removes the last added hotspot from model
                }
            }
            document.addEventListener('DOMContentLoaded', function () {
            // Array of button IDs
            var buttonIds = ['ar_display_options_acc', 'ar_rotation_acc', 'ar_disable_elements_acc', 'ar_qr_code_acc', 'ar_additional_interactions_acc', 'ar_alternative_acc', 'ar_element_positions_acc'];
            
            // Text field
            var arOpenTabsTextField = document.getElementById('ar_open_tabs');
            
            // Add click event listeners to buttons
            buttonIds.forEach(function (buttonId) {
            var button = document.getElementById(buttonId);
            
            if (button) {
              button.addEventListener('click', function () {
                // Get the current value of the text field
                var currentText = arOpenTabsTextField.value;
            
                // Check if the button ID is already present in the text field
                var isButtonInText = currentText.includes(buttonId);
            
                // Update the text field based on whether the button ID is already present
                if (isButtonInText) {
                  // Remove the button ID from the text field
                  var newText = currentText.replace(buttonId + ',', '').replace(',' + buttonId, '');
                  arOpenTabsTextField.value = newText;
                } else {
                  // Add the button ID to the text field
                  var newText = currentText + buttonId + ',';
                  arOpenTabsTextField.value = newText;
                }
              });
            }
            });
            });

        </script>
        
        <?php
    }
}

if ((isset($_POST['_glb_file']))or(isset($_POST['_usdz_file']))){
    add_action('save_post', 'save_ar_wp_option_fields'); // Saving the uploaded file details
}

add_filter( 'wp_insert_post_data' , 'filter_post_data' , '99', 2 );

function filter_post_data( $data , $postarr ) {
    global $post;
    if (isset($post)){
        if ($data['post_type']=='armodels'){
            // Add AR Display shortcode to the post content field
            $data['post_content'] = '[ardisplay id='.$post->ID.']';
        }
    }
    return $data;
}
?>