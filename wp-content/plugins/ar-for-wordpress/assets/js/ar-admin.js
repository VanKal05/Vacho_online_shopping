/*window.modelviewer = {    

    admin:{

        model_id: '',
        variation_id: '',
        ModelViewer: '',
        
        init: function(model){
            this.model_id = model;
            this.modelViewer = jQuery('#model_' + model);
        },

        log: function(){
            console.log(this.modelViewer);
        },

        functions: function() {
            jQuery(document).on('change', '.ar_rotate_limit', function($) {

                var variation_id = jQuery(this).data('variation');
                var model_id = jQuery(this).data('model');
                var modelViewer = window.modelviewer.admin.modelViewer;
                var suffix = '';

                console.log(modelViewer);

                var min_orbit_arr = modelViewer.attr("min-camera-orbit").split(" ");
                var max_orbit_arr = modelViewer.attr("max-camera-orbit").split(" ");

                if(variation_id){
                    suffix = '_var_' + variation_id;
                }

                var element = jQuery("#ar_rotation_limits" + suffix);

                console.log("#ar_rotation_limits" + suffix);

                if (jQuery(this).is(':checked')){
                    element.show();
                }else{
                    element.hide();
                    modelViewer.attr("min-camera-orbit", 'auto auto '+min_orbit_arr[2]);
                    modelViewer.attr("max-camera-orbit", 'Infinity auto '+max_orbit_arr[2]);
                    jQuery("#_ar_compass_top_value" + suffix).val('');
                    jQuery("#_ar_compass_bottom_value" + suffix).val('');
                    jQuery("#_ar_compass_left_value" + suffix).val('');
                    jQuery("#_ar_compass_right_value" + suffix).val('');
                    jQuery("#ar-compass-top" + suffix).css('backgroundColor','#e2e2e2');
                    jQuery("#ar-compass-bottom" + suffix).css('backgroundColor','#e2e2e2');
                    jQuery("#ar-compass-left" + suffix).css('backgroundColor','#e2e2e2');
                    jQuery("#ar-compass-right" + suffix).css('backgroundColor','#e2e2e2');
                }

            });

            jQuery(document).on('mouseenter', '.ar-compass-button', function($) {

                var id = jQuery(this).attr('id');
                var variation_id = jQuery(this).data('variation');
                var suffix = '';               

                if(variation_id){
                    suffix = '_var_' + variation_id;
                }

                var ar_compass_image  = jQuery('#ar-compass-image' + suffix);

                if (id == 'ar-compass-top' + suffix){
                    ar_compass_image.css('transform','rotate(0deg)');
                }else if (id == 'ar-compass-bottom' + suffix){
                    ar_compass_image.css('transform','rotate(180deg)');
                }else if (id == 'ar-compass-right' + suffix){
                    ar_compass_image.css('transform','rotate(90deg)');
                }else if (id == 'ar-compass-left' + suffix){
                    ar_compass_image.css('transform','rotate(270deg)');
                }

            });


            jQuery(document).on('click', '.ar-compass-button', function($) {

                var id = jQuery(this).attr('id');
                var variation_id = jQuery(this).data('variation');
                var model_id = jQuery(this).data('model');
                var modelViewer = window.modelviewer.admin.modelViewer;
                var suffix = '';               

                if(variation_id){
                    suffix = '_var_' + variation_id;
                }

                var ar_compass_image  = jQuery('#ar-compass-image' + suffix);
                var min_orbit_arr = modelViewer.attr("min-camera-orbit").split(" ");
                var max_orbit_arr = modelViewer.attr("max-camera-orbit").split(" ");

                if (id == 'ar-compass-top' + suffix){
                    
                    var orbit = modelViewer.getCameraOrbit();
                    if (jQuery("#_ar_compass_top_value" + suffix).val() == ''){
                        var orbitString = `${orbit.phi}rad`;
                        jQuery("#_ar_compass_top_value" + suffix).val(orbitString);
                        jQuery(this).css('backgroundColor','#f37a23');
                    }else{
                        var orbitString = `auto`;
                        jQuery(this).css('backgroundColor','#e2e2e2');
                        jQuery("_ar_compass_top_value" + suffix).val('');
                    }
                    modelViewer.attr("min-camera-orbit", min_orbit_arr[0]+' '+orbitString+' '+min_orbit_arr[2]);

                }else if (id == 'ar-compass-bottom' + suffix){
                    
                    var orbit = modelViewer.getCameraOrbit();
                    if (jQuery("#_ar_compass_bottom_value" + suffix).val() == ''){
                        var orbitString = `${orbit.phi}rad`;
                        jQuery("#_ar_compass_bottom_value" + suffix).val(orbitString);
                        jQuery(this).css('backgroundColor','#f37a23');
                    }else{
                        var orbitString = `auto`;
                        jQuery(this).css('backgroundColor','#e2e2e2');
                        jQuery("#_ar_compass_bottom_value" + suffix).val('');
                    }
                    modelViewer.attr("max-camera-orbit", max_orbit_arr[0]+' '+orbitString+' '+max_orbit_arr[2]);

                }else if (id == 'ar-compass-right' + suffix){
                    
                    var orbit = modelViewer.getCameraOrbit();
                    if (jQuery("#_ar_compass_right_value" + suffix).val() == ''){
                        var orbitString = `${orbit.theta}rad`;
                        jQuery("#_ar_compass_right_value" + suffix).val(orbitString);
                        jQuery(this).css('backgroundColor','#f37a23');
                    }else{
                        var orbitString = `auto`;
                        jQuery(this).css('backgroundColor','#e2e2e2');
                        jQuery("#_ar_compass_right_value" + suffix).val('');
                    }
                    modelViewer.attr("max-camera-orbit", max_orbit_arr[0]+' '+orbitString+' '+max_orbit_arr[2]);

                }else if (id == 'ar-compass-left' + suffix){
                    
                    var orbit = modelViewer.getCameraOrbit();
                    if (jQuery("#_ar_compass_left_value" + suffix).val() == ''){
                        var orbitString = `${orbit.theta}rad`;
                        jQuery("#_ar_compass_left_value" + suffix).val(orbitString);
                        jQuery(this).css('backgroundColor','#f37a23');
                    }else{
                        var orbitString = `auto`;
                        jQuery(this).css('backgroundColor','#e2e2e2');
                        jQuery("#_ar_compass_left_value" + suffix).val('');
                    }
                    modelViewer.attr("min-camera-orbit", min_orbit_arr[0]+' '+orbitString+' '+min_orbit_arr[2]);
                }

                modelViewer.removeAttr("auto-rotate");
                jQuery("#_ar_rotate" + suffix).attr('checked','checked');

            });

            var _modelViewer = document.getElementById('model_' + this.model_id);

            _modelViewer.addEventListener('camera-change', () => {
                var orbit = _modelViewer.getCameraOrbit();
                var orbitString = `${orbit.theta}rad ${orbit.phi}rad ${orbit.radius}m`;
                
                jQuery( "#camera_view_button" ).click(function($) {
                    jQuery("#_ar_camera_orbit_set").css('display','block');
                    jQuery("_ar_camera_orbit").val(orbitString);
                });
                
            });
        },      
    },        
}  */

jQuery(document).ready(function(){

    jQuery(document).on('click','#toggle-model-fields', function(event){
        event.preventDefault();
        console.log(jQuery(this).data('status'));
        if(jQuery(this).data('status') == 'hidden'){
            jQuery('#_usdz_file').attr('type','text');
            jQuery('#_glb_file').attr('type','text');
            jQuery(this).data('status','visible');
            jQuery(this).text('Hide Model Fields');
        } else {
            jQuery('#_usdz_file').attr('type','hidden');
            jQuery('#_glb_file').attr('type','hidden');
            jQuery(this).data('status','hidden');
            jQuery(this).text('Show Model Fields');
        }

    });
});  
