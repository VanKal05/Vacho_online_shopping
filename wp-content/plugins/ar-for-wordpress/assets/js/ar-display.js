function copyToClipboard(ID) {
    var copyText = document.getElementById(ID);
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
}


jQuery(document).ready(function(){


    jQuery(document).on('click','button.ar-button', function(event){
        event.preventDefault();
        
        var alt_id = jQuery(this).data('alt');
        var arViewer = jQuery('#model_' + alt_id);
        console.log('#model_' + alt_id);
        if (arViewer[0].canActivateAR) {

            arViewer[0].activateAR();
          
        }
    });


    /*jQuery(document).on('click','button.ar-button', function(event){
        
        event.preventDefault();

        var id = jQuery(this).data('id');        
        var arViewer = jQuery('#model_' + id);
        var alt_glb = jQuery('#alt_glb_file_' + id).val();
        var alt_usdz = jQuery('#alt_usdz_file_' + id).val();

        var orig_glb = jQuery('#alt_glb_file_' + id).data('orig');
        var orig_usdz = jQuery('#alt_usdz_file_' + id).data('orig');

        //console.log('id - ' + id);
        //console.log(arViewer[0]);
        //console.log('alt-glb - ' + alt_glb);
        //console.log('alt-usdz - ' + alt_usdz);        
        //alert("HERE");

        arViewer.attr('src',alt_glb);
        arViewer.attr('ios-src',alt_usdz);

        if (arViewer[0].canActivateAR) {
		    // Programmatically enter AR mode
		    //arViewer.shadowRoot.querySelector('#default-exit-webxr-ar-button').addEventListener("click", function(){ alert("AR CLOSED") });

	    	arViewer[0].activateAR();



	    	//arViewer.attr('src',orig_glb);
        	//arViewer.attr('ios-src',orig_usdz);

	    	
	  	}
        
        return true;
        

    });*/
}); 