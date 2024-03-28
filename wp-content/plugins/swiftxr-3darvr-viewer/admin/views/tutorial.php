<?php

/**
 * Provide a Tutorial area view for the plugin
 *
 * This file is used to markup the tutorial page.
 *
 */
?>

<div class="wrap swiftxr-wrap">

    <div class="swiftxr-header">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Guide', 'swiftxr-3darvr-viewer' ); ?></h1>

        <div>

            <a href="<?php echo admin_url( 'admin.php?page=swiftxr-app-dashboard' ); ?>" class="button"><?php esc_html_e( 'Back', 'swiftxr-3darvr-viewer' ); ?></a>

        </div>
    </div>

    <div class="swiftxr-column">
        <div class="swiftxr-card">
            <h4><?php esc_html_e( "Let's get you started 🚀", 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'Follow these steps to start using 3D/AR/VR on your website quickly', 'swiftxr-3darvr-viewer' ); ?></p>

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Step 1 - Go to the SwiftXR Hub', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'To publish your 3D Model, visit the SwiftXR Hub in the Viewer Dashboard. SwiftXR accepts multiple file formats such as fbx, obj, stl, gltf, and images. In the SwiftXR Editor design tab, under "Dimension" set the viewer height to (100%) and width to (100%).', 'swiftxr-3darvr-viewer' ); ?></p>

            <p class="swiftxr-text-muted"><?php esc_html_e( 'Important: To make sure that only your 3D model is visible remove any other components on the SwiftXR Canvas.', 'swiftxr-3darvr-viewer' ); ?></p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . "../assets/step-2-min.jpg" ); ?>" alt="Step 1 Image">

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Step 2 - Publish your SwiftXR Project', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'To publish your project, click the "Publish" button located at the top right corner of the SwiftXR Editor and then select "Publish Project".', 'swiftxr-3darvr-viewer' ); ?></p>

            <p class="swiftxr-text-muted"><?php esc_html_e( '(Optional) You can give different names to the sites where you publish your projects.', 'swiftxr-3darvr-viewer' ); ?></p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/step-3-min.jpg'); ?>" alt="Step 2 Image">

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Step 3 - Copy the link provided after your project is published on SwiftXR.', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'After publishing your project, you can view it by opening the URL provided and you can also copy the URL to share it with others.', 'swiftxr-3darvr-viewer' ); ?></p>

            <p class="swiftxr-text-muted"><?php esc_html_e( 'Important: Make sure that your project URL has the "swiftxr.app" domain, otherwise our servers will mark it as invalid.', 'swiftxr-3darvr-viewer' ); ?></p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/step-4-min.jpg'); ?>" alt="Step 3 Image">

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Step 4 - Create a 3D Entry on the Admin Dashboard (Website Mode)', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'To create a 3D entry on the admin dashboard, paste the link you copied after publishing your project into the "Published Project Link" field. You will also need to specify the viewer width and height.', 'swiftxr-3darvr-viewer' ); ?></p>

            <p class="swiftxr-text-muted"><?php esc_html_e( 'Note: A SwiftXR shortcode will be generated for every entry. You can use this code to embed your 3D project on your website.', 'swiftxr-3darvr-viewer' ); ?></p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/swiftxr-wordpress-step-4-min.jpg'); ?>" alt="Step 4 Image">

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Step 5 - Create a 3D Entry on the Admin Dashboard (E-Commerce Mode)', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'After publishing your project, copy the link and paste it into the "Published Project Link" field. Then, switch to e-commerce mode and specify the product that you want to link it to.', 'swiftxr-3darvr-viewer' ); ?></p>

            <p class="swiftxr-text-muted"><?php esc_html_e( 'Note: A SwiftXR shortcode will be generated for each entry. You can use this shortcode to embed the entry on your website, including e-commerce entries', 'swiftxr-3darvr-viewer' ); ?></p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/swiftxr-wordpress-step-5-min.jpg'); ?>" alt="Step 5 Image">

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Step 5B - Set the Position and Height of the SwiftXR Viewer on Your Shop Page (E-Commerce Users Only)', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p>
                <p><?php esc_html_e( 'To customize the position and size of the 3D/AR/VR view on your shop product page, follow these steps:', 'swiftxr-3darvr-viewer' ); ?></p>

                <ol class="tutorial-steps">
                    <li><?php esc_html_e( 'Go to the SwiftXR Plugin settings page', 'swiftxr-3darvr-viewer' ); ?></li>
                    <li><?php esc_html_e( 'Adjust the viewer position to your desired position.','swiftxr-3darvr-viewer' ); ?></li>
                    <li><?php esc_html_e( 'Adjust the viewer height to your desired dimension.', 'swiftxr-3darvr-viewer' ); ?></li>
                </ol>

                <p><?php esc_html_e( 'We recommend placing the viewer at the top of the product page for better visibility. Note that the viewer will automatically be hidden if there is no corresponding SwiftXR URL for the product.', 'swiftxr-3darvr-viewer' ); ?></p>
            </p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/swiftxr-wordpress-step-5B-min.jpg'); ?>" alt="Step 5b Image">

        </div>

        <div class="swiftxr-card">
            <h4><?php esc_html_e( 'Congratulations! You have successfully added 3D/AR/VR views to your Shop or Website. Enjoy exploring your products in a whole new way!', 'swiftxr-3darvr-viewer' ); ?></h4>

            <p><?php esc_html_e( 'SwiftXR automatically switches between different views depending on the product and shows the correct link as specified in the product dashboard.', 'swiftxr-3darvr-viewer' ); ?></p>

            <p class="swiftxr-text-muted"><?php esc_html_e( 'Note: Keep adding your 3D models to your store to make it more interactive.', 'swiftxr-3darvr-viewer' ); ?></p>

            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/swiftxr-wordpress-step-6-min.jpg'); ?>" alt="Step 6 Image">

        </div>
    </div>

    
</div>
