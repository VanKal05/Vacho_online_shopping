<?php

/**
 * Provide a settings admin area view for the plugin
 *
 * This file is used to markup the settings page.
 *
 */
?>

<div class="wrap swiftxr-wrap">

    <div class="swiftxr-header">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Settings', 'swiftxr-3darvr-viewer' ); ?></h1>

        <a href="<?php echo admin_url( 'admin.php?page=swiftxr-app-dashboard' ); ?>" class="page-title-action"><?php esc_html_e( 'Back', 'swiftxr-3darvr-viewer' ); ?></a>
    </div>

    <?php if($message) {?>
        <div class="notice is-dismissible <?php echo esc_attr( isset($message_type) && $message_type == 'success'? 'notice-success':'notice-error' )?>">
            <p><?php esc_html_e( $message, 'swiftxr-3darvr-viewer' );?></p>
        </div>
    <?php } ?>

    <div class="swiftxr-column swiftxr-left">

        <h5 class="swiftxr-sub-header"><?php esc_html_e( 'E-Commerce  Viewer Settings', 'swiftxr-3darvr-viewer' ); ?></h5>

        <form method="post">

            <div class="swiftxr-card">
                <h5><?php esc_html_e( 'Product Viewer Position', 'swiftxr-3darvr-viewer' ); ?></h5>

                <select name="swiftxr-product-append" onchange="showHelperText()">

                    <option value="woocommerce_before_single_product_summary" <?php echo esc_attr( isset($product_placement) && $product_placement === 'woocommerce_before_single_product_summary'? 'selected': '' ); ?>><?php esc_html_e( 'Top of Product', 'swiftxr-3darvr-viewer' ); ?></option>

                    <option value="woocommerce_product_thumbnails" <?php echo esc_attr( isset($product_placement) && $product_placement === 'woocommerce_product_thumbnails'? 'selected': '' ); ?>><?php esc_html_e( 'Product Gallery', 'swiftxr-3darvr-viewer' ); ?></option>
                    
                    <option value="woocommerce_before_add_to_cart_button" <?php echo esc_attr( isset($product_placement) && $product_placement === 'woocommerce_before_add_to_cart_button'? 'selected': '' ); ?>><?php esc_html_e( 'After Product Description', 'swiftxr-3darvr-viewer' ); ?></option>

                    <option value="woocommerce_after_single_product_summary" <?php echo esc_attr( isset($product_placement) && $product_placement === 'woocommerce_after_single_product_summary'? 'selected': '' ); ?>><?php esc_html_e( 'After Product Summary', 'swiftxr-3darvr-viewer' ); ?></option>
                    
                </select>

                <div id="swiftxr-helper-text" class="swiftxr-text-muted"></div>

            </div>

            <div class="swiftxr-card">
                <h5><?php esc_html_e( 'Product Viewer Height', 'swiftxr-3darvr-viewer' ); ?></h5>

                <div class="swiftxr-custom-number-input">
                    <input type="number" name="swiftxr-height" id="swiftxr-height" class="regular-text" value="<?php echo isset( $height ) ? esc_attr( str_replace(array('%', 'px'), '', $height) ) : ''; ?>">

                    <select name="swiftxr-h-unit">
                        <option value="px" <?php echo esc_attr( isset($height_unit) === 'px'? 'selected': '' ) ?>><?php esc_html_e( 'px', 'swiftxr-3darvr-viewer' ); ?></option>
                    </select>
                </div>
            </div>

            <div class="swiftxr-form-action">
                <?php submit_button( esc_html__('Save', 'swiftxr-3darvr-viewer' ), 'primary', 'swiftxr-settings-submit' ) ?>

            </div>
        </form>

    </div>

</div>
