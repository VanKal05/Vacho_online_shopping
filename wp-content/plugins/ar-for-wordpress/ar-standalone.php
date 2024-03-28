<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
require_once('../../../wp-load.php');
global $wpdb;
if ($_REQUEST['id']!=''){
    $output = do_shortcode ('[ardisplay id=\''.$_REQUEST['id'].'\']');
}elseif ($_REQUEST['cat']!=''){
    $output = do_shortcode ('[ardisplay cat=\''.$_REQUEST['cat'].'\']');
}
get_header();
echo '<center><span id="ar_standalone_loading">Loading</span></center>
    <div id="ar_standalone_container" style="opacity: 0;">'.$output.'</div>';
//Trigger the AR button to open 
?>
<script>
    const modelViewer = document.getElementById("model_<?php echo $_REQUEST['id'];?>");
    function checkagain() {
        if (modelViewer.modelIsVisible === true) {
            document.getElementById("ar-button_<?php echo $_REQUEST['id']; ?>").click();
        }else {
            checkagain2 = setTimeout(ar_open, 2);
        }
    }

    function ar_open() {
        if (modelViewer.modelIsVisible === true) {
            document.getElementById("ar-button_<?php echo $_REQUEST['id']; ?>").click();
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