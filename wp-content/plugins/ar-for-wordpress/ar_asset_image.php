<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
function ar_image_create($file) {
    $size = getimagesize($file);
    if(!$size) {
        return false;
    }
    $type = $size[2];
    $valid_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);

    if(!in_array($type,  $valid_types)) {
        return false;
    }
    switch ($type) {
        case 1 :
            $im['image'] = imageCreateFromGif($file);
        break;
        case 2 :
            $im['image'] = imageCreateFromJpeg($file);
        break;
        case 3 :
            $im['image'] = imageCreateFromPng($file);
        break;
        case 4 :
            $im['image'] = imageCreateFromBmp($file);
        break;
    } 
     $im['type'] = $type;
    
    return $im; 
}

// Load image file 
$image = ar_image_create(urldecode($_GET['file']));
// Flip the image 
imageflip($image['image'], IMG_FLIP_BOTH); 
//echo $image['type'];exit;
switch ($image['type']) {
    case 1 :
        header('Content-type: image/gif'); 
        imagegif($image['image']); 
    break;
    case 2 :
        header('Content-type: image/jpeg'); 
        imagejpeg($image['image']); 
    break;
    case 3 :
        header('Content-type: image/png'); 
        imagepng($image['image']); 
    break;
    case 4 :
        header('Content-type: image/bmp'); 
        imagebmp($image['image']); 
    break;
} 
