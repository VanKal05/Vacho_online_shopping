=== AR For WordPress ===
Contributors: webandprint
Donate link: https://webandprint.design
Tags: Augmented Reality, AR, 3D, Model Viewer, 3D Model, 3D Model Viewer, 3D Model Display
Requires at least: 4.6
Tested up to: 6.4.2
Stable tag: 5.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Augmented Reality for WordPress plugin is an all in one solution to allow you to present your 3D models in an interactive 3D model viewer and AR view directly in your browser on both iOS and Android devices without the need for your users to download any apps.

== Description ==

Experience the Future with Augmented Reality for WordPress Plugin!

Transform your website into an immersive 3D wonderland with our all-in-one solution. Now, you can captivate your audience by showcasing your 3D models in an interactive 3D viewer and Augmented Reality (AR) right within their web browsers, available on both iOS and Android devices. No app downloads needed!

**3D Gallery Builder:** Hang your artwork in Augmented Reality with just a photo!

**AR Magic:** Use GLB, GLTF, USDZ and Reality model files for an exceptional 3D model viewing experience in the browser and Augmented Reality.

**Try Before You Buy:**
Give your users the power to visualize your products in 3D within their own environment. This immersive experience lets them 'try before they buy,' resulting in higher conversion rates, reduced returns, and increased profitability for your business.

**Easy to Use and Feature-Rich:**
Our plugin is designed with you in mind! Enjoy a hassle-free feature rich experience with seamless integration into popular builders like Gutenberg and Elementor. Plus, tap into custom API capabilities for sending and receiving JSON data for your models.

Elevate your website to the next level and embrace the future of online shopping with Augmented Reality for WordPress Plugin!

**Features**

* Users can view your models in both 3D and AR views with **no dedicated app required**
* Users can zoom in and out on your models
* Simple easy to use interface
* Responsive design - Desktop view allows for 3D view, Mobile and Tablet view allows both 3D and AR view
* Model placement on the floor (horizontal surfaces) or the wall (vertical surfaces)
* QR Code display on desktop view to allow users to scan QR code with their phone or tablet to easily switch to an AR capable viewing device. 
* Free version restricted to 1 model only

**Premium Subscription**
https://augmentedrealityplugins.com

* Unlimited 3D Models
* Dynamically adjust settings in the admin view such as exposure, shadow softness and intensity, scale, field of view, zoom restraints, legacy lighting.
* Supports models with Variants
* Supports Background and Environment Images
* Disable resizing in AR so the user can only view your model at 100% size - avoids confusion when users wish to see how the model fits in their environment.
* Restrict the rotation of the model
* Add hotspot annotations to your models
* Animation play pause button and autoplay options
* Display thumbnails for multiple models in a single viewer window (User can only view one model at a time in 3D or AR view)
* API to send and receieve json data for your models
* Option to hide the AR button and restrict users to 3D view only
* Option to hide the QR code
* Show the AR View Button image or text links to open AR and 3D models.
* Open the AR model view directly from a QR code
* Automatically generate Featured Images of the current model view
* CTA Button on Model Viewer and Androis AR view
* Custom element positioning within Model Viewer - Global Settings
* CSS Style Editing - Global Settings
* Custom element positioning within Model Viewer - Applicable to individual models
* CSS Style Editing - Applicable to individual models
* Gutenberg Blocks
* WordPress Widget
* Elementor Widget

**How To Use The Plugins**
* https://www.youtube.com/watch?v=jO7wR-meeGI
* https://augmentedrealityplugins.com/support/#getstarted

**See it in action**
* https://augmentedrealityplugins.com

**Sample 3D files and Resources**
* https://augmentedrealityplugins.com/support/

== Installation ==

1. Upload `ar-for-wordpress.zip` to the `/wp-content/plugins/` directory and expand it
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the settings page to get started

== Frequently Asked Questions ==

= What 3D model formats are supported? =

AR for WordPress and AR for WooCommerce display your 3D models using an iOS App built using Apple’s ARkit software. This system supports the use of GLb/GLTF and USDZ/REALITY file formats. You can also use DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped versions of these files which will be automatically converted to GLB format.

= Does it support Android and iOS? =

The plugin uses the model-viewer scripts which supports most iOS and Android devices. Viewing of the models in 3D is done directly in the browser and launches the native Android WebXR and iOS Quick Look apps for AR viewing. For optimum performance it requires your site to have an SSL certificate (https://).

= Are there any additional costs? =
If you use the free version of the plugins then there are no costs involved, however you will have limitations to the number of 3D models you can have on your site and the features for manipulating your model display are limited.

If you use a premium version of the plugins then there is a monthly subscription fee for the plugin to support unlimited models and to access the full feature set for the duration of your subscription. https://augmentedrealityplugins.com

= What if I don't have any 3D models? =

If you don’t have 3D models there are a number of solutions available to you, including downloading existing models from online libraries, creating your own models using your mobile phone or tablet’s cameras or commissioning a 3D model to be created. Please visit our 3D Model Resources section for more details. https://augmentedrealityplugins.com/support/#3d

== Screenshots ==

1. Blender Model in AR

2. Blender Model in 3D

3. Leather Chair in AR

4. Wooden Chair in AR

5. Canoe in 3D

6. Add New AR Models

7. List of AR Models

8. Settings Page

9. Widgets

10. Elementor Elements

11. Gutenberg Blocks - Side Panel

12. Gutenberg Blocks

13. 3d Gallery Builder

== Changelog ==

=5.7=
* Fixed incompatibility issues with QR code generation if imagick not enabled

=5.6=
* Custom View in AR and View in 3D text options
* 3D Gallery Uploader bug fixes
* Reality File Mime Type update

=5.5=
* Fix 3D Gallery image upload issue
* QR code script adjustment

=5.4=
* QR Code implementation of php QR Code library to replace Google API
* Alternative model for AR display option
* Show Model Fields Toggle Option
* Saving model bug fixes

=5.3=
* Right-to-left (RTL) language display issues fixed

=5.2=
* Javascript improvements
* improved compatibility with AR for Woocommerce plugin

=5.1=
* CSS update to fix AR button clickabilitiy
* Fixed issue where 3D Gallery Builder would apply default GLB file when no image was chosen
* Added script to reload Gutenburg post editing page when post is published or updated
* Added option to link Hotspots to URLs
* Restriction to ensure the chosen subscription domain matches the domain of the site

=5.0=
* Interface redesign
* Addition of 3D Gallery Builder - Create a 3D model of your image file in a pircture frame
* Improved loading and user friendliness

=4.8=
* Documentation Links

=4.7=
* Addition of Animation Selector in model editor

=4.6=
* Fix of blocks_categories deprecated error
* Fix for erros when directly accessing files

=4.5=
* Firefox fallback options
* Addition of Alternative Model For Mobile option

=4.4.1=
* Fix to Javascript
* Addition of areditor shortcode to show a simplified model editor on the front end of site.

=4.4=
* Fix to Elementor Pro issue
* Improvements to opening AR models directly from QR codes
* Improvements to arview shortcode

=4.3=
* Improvement of QR Code Destination to open the model in AR view with friendly urls

=4.2=
* Addition of Rotation Limits - Restrict the amount the user can rotate the model in the viewer
* Addition of Disable Zoom option
* Addition of QR Code Destination option on a per model basis
* Improvements of AR Model Editing page layout

=4.1=
* Addition of Loading and hidden model viewer whilst loading on QR code stand alone model links
* Improvements to category option in ardisplay shortcode
* Further Android intent URL improvements

=4.0=
* Admin Interface styling improvements
* Admin - Auto update of model when new file uploaded or chosen from Media Library
* Open AR automatically with QR code when global setting is Model Viewer
* Zoom constraints improvements
* Popup close button improvements when using ar-view shortcode
* Set Featured AR image now using REST API
* Android intent URL improvements

=3.9=
* AR View shortcode improvement to fix Android AR loading issue

=3.8=
* Reset to Initial View Option Globally and per model
* Improvements to ar-view shortcode

=3.7=
* Custom QR Code Image override generated QR Code on each product
* Custom QR Code Destination override the URL used for generated QR Code on each product
* Disable Prompt on each model
* Improved JS handling

=3.6=
* Global setting to choose units for Dimensions - m, cm, mm, in
* Disable model interaction - no rotating and zooming in browser view only
* ar-view shortcode addition of the buttons attribute to display links as html buttons
* Fail safe in place if qr code cannot be generated

=3.5=
* Improved licence key feedback on settings page
* Elementor Fix

=3.4=
* Addition of Gutenberg Blocks
* Addition of text option for ar-view shortcode

=3.3=
* Asset Builder improvements
* Shortcode display improvements
* API Additions - Delete and Featured Image options

=3.2=
* Hide Dimensions option added to API
* cURL fallback using file_get_contents

=3.1=
* Hide Dimensions on a per model basis

=3.0=
* Fullscreen popup improvements
* Set Featured Image improvements
* Replaced file_get_contents with cURL
* Display of AR not supported message for desktops

=2.9=
* Fixed issue with Disable Fullscreen and Dimensions conflict
* Improved Set Current Camera View as Initial button response

=2.8=
* Minor bug fixes

=2.7=
* Endpoint API functionality
* Improvements to Licence Key system
* Improvements to AR standalone Button - Shows 3D model and note if clicked on Desktop
* Settings Page Layout Improvements
* Fixed Exposure, Shadow Intensity and Shadow Softness set to 0 issue
* Added global QR Code Destination option to settings page, which take mobile users directly to the model-viewer or to the parent page the model is displayed on

=2.6=
* Custom Play and Pause buttons and positioning for animated models
* Option for Dimensions to be displayed in inches
* Link to view Model Post
* Improved Licence key checking

=2.5=
* WordPress Widget
* Elementor Widget

=2.4=
* Custom element positioning within Model Viewer - Applicable to individual models
* CSS Style Editing - Applicable to individual models
* Code Improvements

=2.3.8=
* Global custom element positioning within Model Viewer
* Global CSS Style Editing

=2.3.7=
* Call To Action Button - Displays on 3D Model view and in AR view on Android

=2.3.6=
* Set Featured Image button - creates a PNG file of the current model view, adds it to the media library and sets it as the featured image

=2.3.5=
* Improved licence key check
* Option to prioritise Scene Viewer over WebXR on Android devices

=2.3.4=
* Option to prioritise Scene Viewer over WebXR on Android devices

=2.3.3=
* Option to disable the interaction prompt and model rotation/wriggle

=2.3.2=
* Improved AR model admin layout
* Hotspot functionality - add hotspot annotations to your models
* Editing of placement, QR button, AR Button, animation button and scale settings dynamically update in model view in admin editing pages

= 2.3.1=
* Legacy Lighting option
* Option to set initial camera view
* Editing of field of view, exposure, shadow and zoom settings dynamically update in model view in admin editing pages

= 2.3.0=
* Removed loading icon when clicking AR button

= 2.2.9=
* Fixed Android loading issue when scene viewer crashes. Prioritised webxr

= 2.2.8=
* Improved Internationalization of plugin

= 2.2.7=
* Internationalization of plugin
* Updated scaling inputs to default to 1 and include increment stepper
* Validation of AR model urls to be secure - replace http:// with https://
* Restricted optional settings to Premium Plans only
* Premium upgrade banner improvements

= 2.2.6=
* Added support for .REALITY file formats to display on iOS

= 2.2.5=
* Fixed imagedestroy issue
* Fixed AR thumbnails opening models in 1st viewer on page only

= 2.2.4=
* Improved Licence Key check system

= 2.2.3=
* AR Button changes to loading image when tapped to show model is loading into AR viewer

= 2.2.2=
* Added Environment Image Upload ability
* Improved Skybox image handling

= 2.2.1=
* Improved Licence System
* Added Field of View setting
* Added Zoom in and out contraints settings

= 2.2.0=
* Added support for GLB/GLTF animation display and play/pause controls in browser view 

= 2.1.9=
* Added Fullscreen disable option to settings page
* Added support for Poster images when loading 3D models
* Moved dimensions to top left to avoid conflict with Thumbnails when viewing multiple models in one shortcode
* Fixed issue with dimensions checkbox hiding thumbnail slides
* Fixed issue with multiple model thumbnails and QR code button
* Improved CSS cursor pointers on 3D model viewer elements
* Added Copy function to AR Shortcode column in Model/Product list
* Improved admin page layout

= 2.1.8=
* Fixed conflict issue with Revolution Slider

= 2.1.7=
* Fixed issue with AR buttons conflicting with some themes

= 2.1.6=
* Added Upgrade Ribbon to Settings page
* Fixed issue with Licence key saving
* Improved shortcode examples display

= 2.1.5=
* Fixed issue with thumbnail slider changing ios src

= 2.1.4=
* Fixed issue with ar-view shortcode and AR View Hide setting

= 2.1.3=
* Prioritised Scene-viewer over WebXR to improve Android compatibility

= 2.1.2=
* QR Code fully functioning

= 2.1.1=
* QR Code showing blank issue fixed

= 2.1.0=
* QR Code image issue fixed to be loaded inline
* GLTF uploading issue resolved

= 2.0.9=
* Settings Saving issues fixed
* JS issues fixed

= 2.0.8=
* Display model dimensions options
* Multiple models in the one viewer
* Show/hide QR Code
* Show/Hide AR View button
* Shortcode to display QR Code anywhere on page
* Shortcode to display AR View Button anywhere on page
* Custom AR View button image file
* Custom QR Code logo image file
* Improved Settings page

= 2.0.7=
* Asset Builder - Improvements and additional models

= 2.0.6=
* Asset Builder - Improvements and additional models
* Function Consolidation

= 2.0.5=
* Asset Builder - Choose from ready made 3D models and add your own texture file to create a GLTF model on the fly

= 2.0.4=
* Support for zipped GLTF files Added
* Model conversion for DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped versions of these files

= 2.0.3=
* Scaling Options Added

= 2.0.2=
* Fixed FullScreen Issues
* Skybox/Background Image support on Fullscreen mode
* Streamlined Licencing system

= 2.0.1=
* Improved Model Viewer display
* QR Code Implementation
* Fullscreen popup
* Variant Support

= 2.0.0=
*Total overhaul of plugin to include iOS and Android support directly in the browser
* No need for an app
* Use of Model Viewer with USDZ and GLB 3D model files

= 1.0.0=
* New Enhancement
* Product Description is added for mobile app

= 1.0.4=
* New Enhancement
* You can put AR thumbnail icon anywhere in post/page

= 1.0.3=
* New Enhancement
* Allow to select model type of "2D Model" or "3D Model"
* If 2D model then Allow to generate 3D box(3D Model) automatically of given width, height, depth, format
* If 3D model then Allow to upload 3D model

= 1.0.1=
* Bug Fixes
* Minor CSS + JS improvements

= 1.0.0=
* First Official Launch Version
