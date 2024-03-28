<?php

/**
 * Provide a Dashboard admin area view for the plugin
 *
 * This file is used to markup the dahboard page.
 *
 */
?>

<div class="wrap swiftxr-wrap">

    <div class="swiftxr-header">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'SwiftXR Viewer', 'swiftxr-3darvr-viewer' ); ?></h1>

        <div>
            
            <a href="<?php echo admin_url( 'admin.php?page=swiftxr-tutorial' ); ?>" class="button"><?php esc_html_e( 'Guide', 'swiftxr-3darvr-viewer' ); ?></a>

            <a href="<?php echo admin_url( 'admin.php?page=swiftxr-settings' ); ?>" class="button"><?php esc_html_e( 'Settings', 'swiftxr-3darvr-viewer' ); ?></a>

            <a href="<?php echo admin_url( 'admin.php?page=swiftxr-app-form' ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Entry', 'swiftxr-3darvr-viewer' ); ?></a>

        </div>
    </div>

    <div class="swiftxr-column">
        <div class="swiftxr-card main-card" >

            <div>
                <h5><?php esc_html_e( 'App Dashboard', 'swiftxr-3darvr-viewer' ); ?></h5>

                <p>
                    <?php esc_html_e( 'Your SwiftXR plugin app is all set to elevate your content to the next level with 3D/AR and VR!', 'swiftxr-3darvr-viewer' ); ?>
                </p>

                <p>
                    <?php esc_html_e( 'Are you ready to see it in action? Start filling your app with entries to view on your website or in your e-commerce store.', 'swiftxr-3darvr-viewer' ); ?>
                </p>


                <a href="<?php echo esc_url( "https://swiftxr.io/hub" ); ?>" target="_blank" rel="noopener" class="button"><?php esc_html_e( 'Go to SwiftXR Hub', 'swiftxr-3darvr-viewer' ); ?></a>

                <p>
                    <?php esc_html_e( 'Learn more about SwiftXR Viewer on Wordpress by checking out this', 'swiftxr-3darvr-viewer' ); ?>

                    <a href="<?php echo esc_url( "https://youtu.be/zJqSmkLvy64" ); ?>" target="_blank" rel="noopener"><?php esc_html_e( ' tutorial 📚', 'swiftxr-3darvr-viewer' ); ?></a>
                </p>
            </div>

            <div>
                <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/home-trophy.png' ); ?>" alt="Welcome Image">
            </div>


        </div>

        <?php if (!empty($shortcodes)){ ?>

            <table class="wp-list-table widefat fixed striped">
                <thead class="swiftxr-table">
                    <tr>
                        <th class="shortcode-id"><?php esc_html_e( 'ID', 'swiftxr-3darvr-viewer' ); ?></th>
                        <th><?php esc_html_e( 'Shortcode', 'swiftxr-3darvr-viewer' ); ?></th>
                        <th><?php esc_html_e( 'Published Project Link', 'swiftxr-3darvr-viewer' ); ?></th>
                        <th><?php esc_html_e( 'Product', 'swiftxr-3darvr-viewer' ); ?></th>
                        <th><?php esc_html_e( 'Width', 'swiftxr-3darvr-viewer' ); ?></th>
                        <th><?php esc_html_e( 'Height', 'swiftxr-3darvr-viewer' ); ?></th>
                        <th><?php esc_html_e( 'Action', 'swiftxr-3darvr-viewer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $shortcodes as $shortcode ) { ?>
                        <tr>
                            <?php $wc_product = $this->get_woocommerce_product_by_id($shortcode['wc_product_id']) ?>

                            <td class="shortcode-id"><?php esc_html_e($shortcode['id'], 'swiftxr-3darvr-viewer'); ?></td>

                            <td><?php esc_html_e( '[swiftxr id='.$shortcode['id'].']', 'swiftxr-3darvr-viewer' ); ?></td>

                            <td><?php esc_html_e( $shortcode['url'], 'swiftxr-3darvr-viewer' ); ?></td>

                            <td><?php esc_html_e( $wc_product? $wc_product->get_name():'-', 'swiftxr-3darvr-viewer' ); ?></td>

                            <td><?php esc_html_e( $shortcode['width'], 'swiftxr-3darvr-viewer' ); ?></td>

                            <td><?php esc_html_e( $shortcode['height'], 'swiftxr-3darvr-viewer'); ?></td>

                            <td>
                                <a href="<?php echo admin_url( 'admin.php?page=swiftxr-app-form&id=' . $shortcode['id'] ); ?>" class="button"><?php esc_html_e( 'Edit', 'swiftxr-3darvr-viewer' ); ?></a>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        <?php }else{ ?>

            <div class="swiftxr-card swiiftxr-center">

                <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../assets/emptystate-files.png' ); ?>" alt="Empty Image" width="250">

                <h4 class="card-title"><?php esc_html_e( 'Create interactive 3D/AR/VR views for your products', 'swiftxr-3darvr-viewer' ); ?></h4>

                <p class="card-text"><?php esc_html_e( "Whether you're showcasing a product from every angle, providing detailed product information, or even allowing customers to try it virtually, the possibilities are endless with SwiftXR.", 'swiftxr-3darvr-viewer' ); ?></p>

                <a href="<?php echo esc_url( "https://swiftxr.io/hub" ); ?>" target="_blank" rel="noopener" class="button button-primary"><?php esc_html_e( 'Publish a 3D project', 'swiftxr-3darvr-viewer' ); ?></a>

            </div>

        <?php } ?>
        
    </div>
</div>
