<?php

/**
 * Provide a Add Entry admin area view for the plugin
 *
 * This file is used to markup the entry page.
 *
 */
?>

<div class="wrap swiftxr-wrap">

    <div class="swiftxr-header">

        <h1 class="wp-heading-inline"><?php esc_html_e($id !== null ? 'Update 3D Entry' : 'Create 3D Entry', 'swiftxr-3darvr-viewer' ); ?></h1>

        <div>
            <a href="<?php echo admin_url( 'admin.php?page=swiftxr-app-dashboard' ); ?>" class="button"><?php esc_html_e( 'Back', 'swiftxr-3darvr-viewer' ); ?></a>
        </div>

    </div>    
    
    <?php if($message) {?>
        <div class="notice is-dismissible <?php echo esc_attr( isset($message_type) && $message_type == 'success'? 'notice-success':'notice-error' )?>">
            <p><?php esc_html_e( $message, 'swiftxr-3darvr-viewer' );?></p>
        </div>
    <?php } ?>

    <div class="swiftxr-column swiftxr-bigger">

        <div class="swiftxr-admin-form">

            <form method="post">

                <div class="swiftxr-column-odn">

                    <div class="swiftxr-card">
                        <div class="text-wrap">
                            <h5><?php esc_html_e( 'Enter SwiftXR Published Project Link', 'swiftxr-3darvr-viewer' ); ?></h5>

                            <a href="<?php echo esc_url( "https://swiftxr.io/hub" ) ; ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Generate URL?', 'swiftxr-3darvr-viewer' ); ?></a>
                        </div>

                        <input type="url" name="swiftxr-url" id="swiftxr-url" class="regular-text" value="<?php echo esc_attr( isset( $url ) ? $url : '' ); ?>">

                        <p class="swiftxr-text-muted"><?php esc_html_e( 'This is the URL link generated from publishing your project on the SwiftXR Platform', 'swiftxr-3darvr-viewer' ); ?></p>
                    </div>

                    <div class="swiftxr-toggle-parent">
                        <p><?php esc_html_e( 'Website Mode', 'swiftxr-3darvr-viewer' ); ?></p>

                        <label class="swiftxr-toggle">
                            <input type="checkbox" id="swiftxr-mode-toggle" name="swiftxr-mode" value=" <?php echo esc_attr( "1" ) ?>" <?php checked('1', $wc_product? '1': '0'); ?> onchange="RunCheckBoxSelect()">
                            <span class="swiftxr-slider"></span>
                        </label>

                        <p><?php esc_html_e( 'E-Commerce Mode', 'swiftxr-3darvr-viewer' ); ?></p>
                    </div>

                    <div class="swiftxr-column-odn <?php echo esc_attr( $wc_product ? "swiftxr-hide" : '' ); ?>" id="website-mode">

                        <?php if($id) { ?>

                            <div class="swiftxr-card swiftxr-shortcode-copy">
                                <h5><?php esc_html_e('[/] Shortcode', 'swiftxr-3darvr-viewer'); ?></h5>

                                <p class="swiftxr-text-muted"><?php esc_html_e('Copy and paste this shortcode into your posts, pages and widget:', 'swiftxr-3darvr-viewer'); ?></p>

                                <p class="swiftxr-shortcode-content"><?php esc_html_e('[swiftxr id='. $id .']', 'swiftxr-3darvr-viewer'); ?></p>
                            </div>

                        <?php } ?>

                        <div class="swiftxr-card">

                            <h5><?php esc_html_e( 'Viewer Dimension', 'swiftxr-3darvr-viewer' ); ?></h5>

                            <div class="swiftxr-card-dimension">

                                <div>
                                    <h5><?php esc_html_e( 'Width', 'swiftxr-3darvr-viewer' ); ?></h5>

                                    <div class="swiftxr-custom-number-input">
                                        
                                        <input type="number" name="swiftxr-width" id="swiftxr-width" class="regular-text" value="<?php echo isset( $width ) ? esc_attr( str_replace(array('%', 'px'), '', $width) ) : ''; ?>">

                                        <select name="swiftxr-w-unit">
                                            <option value="<?php echo esc_attr("px"); ?>" <?php echo isset($width_unit) && $width_unit === 'px'? esc_attr( 'selected' ): '' ?>><?php esc_html_e( 'px', 'swiftxr-3darvr-viewer' ); ?></option>

                                            <option value="<?php echo esc_attr("%"); ?>" <?php echo isset($width_unit) && $width_unit === '%'? esc_attr( 'selected' ): '' ?>><?php esc_html_e( '%', 'swiftxr-3darvr-viewer' ); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <h5><?php esc_html_e( 'Height', 'swiftxr-3darvr-viewer' ); ?></h5>

                                    <div class="swiftxr-custom-number-input">
                                        <input type="number" name="swiftxr-height" id="swiftxr-height" class="regular-text" value="<?php echo isset( $height ) ? esc_attr( str_replace(array('%', 'px'), '', $height) ) : ''; ?>">

                                        <select name="swiftxr-h-unit">

                                            <option value="<?php echo esc_attr("px"); ?>" <?php echo isset($height_unit) && $height_unit === 'px'? esc_attr( 'selected' ): '' ?>><?php esc_html_e( 'px', 'swiftxr-3darvr-viewer' ); ?></option>

                                            <option value="<?php echo esc_attr("%"); ?>" <?php echo isset($height_unit) && $height_unit === '%'? esc_attr( 'selected' ): '' ?>><?php esc_html_e( '%', 'swiftxr-3darvr-viewer' ); ?></option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="swiftxr-column-odn <?php echo esc_attr( !$wc_product ? 'swiftxr-hide' : '' ); ?>" id="ecommerce-mode">

                        <div class="swiftxr-card">

                            <div class="text-wrap">
                                <h5><?php esc_html_e( 'WooCommerce Product', 'swiftxr-3darvr-viewer' ); ?></h5>

                                <?php if(isset($wc_product)) {?>

                                    <button class="button-link" onclick="OpenProductPicker(event)"><?php esc_html_e( 'Change Product', 'swiftxr-3darvr-viewer' ); ?></button>

                                <?php }else{ ?>

                                    <button class="button-link" onclick="OpenProductPicker(event)"><?php esc_html_e( 'Select Product', 'swiftxr-3darvr-viewer' ); ?></button>

                                <?php } ?>   
                                
                            </div>

                            <input type="text" name="swiftxr-woocommerce-product-id" value="<?php echo esc_attr( isset( $wc_product ) ? $wc_id : '' ); ?>" class="swiftxr-hide">

                            <div class="swiftxr-product-item" id="swiftxr-wc-selected-product">

                                <?php if($wc_product) {?>

                                    <img src="<?php echo esc_attr( wp_get_attachment_image_src( $wc_product->get_image_id(), 'thumbnail' )[0] ); ?>" alt="<?php echo esc_attr( $wc_product->get_name() ); ?>">

                                    <p><?php esc_html_e( $wc_product->get_name(), 'swiftxr-3darvr-viewer' ); ?></p>

                                <?php }else{ ?>

                                    <button class="button button-secondary" onclick="OpenProductPicker(event)"><?php esc_html_e( "Select Product", 'swiftxr-3darvr-viewer' ); ?></button>

                                <?php } ?>

                            </div>

                            <?php if($woo_commerce_products) {?>

                                <p class="swiftxr-text-muted"><?php esc_html_e( 'The SwiftXR project above will be linked to this WooCommerce product', 'swiftxr-3darvr-viewer' ); ?></p>

                            <?php }else{ ?>
                                <p class="swiftxr-text-muted"><?php esc_html_e( 'It appears that you currently do not have any published products or have not installed WooCommerce. To continue, please either install WooCommerce or add a product', 'swiftxr-3darvr-viewer' ); ?></p>
                            <?php } ?>

                        </div>

                    </div>
                    
                </div>

                <?php wp_nonce_field( 'swiftxr-shortcode-form', 'swiftxr-shortcode-form-nonce' ); ?>

                <div class="swiftxr-form-action">
                    
                    <?php if ($id) { ?>

                        <p class="submit">
                            <a 
                                href="<?php echo admin_url('admin.php?page=swiftxr-app-form&action=swiftxr-shortcode-delete&redirect=dashboard&id=' . $id); ?>" 

                                class="button button-secondary swiftxr-button-danger" 

                                onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this Entry?', 'swiftxr-3darvr-viewer' ); ?>')">
                                
                                <?php esc_html_e('Delete', 'swiftxr-3darvr-viewer' ); ?>
                            </a>
                        </p>
                    <?php } ?>

                    <?php submit_button( esc_html__( $id? 'Update Entry': 'Add Entry', 'swiftxr-3darvr-viewer' ), 'primary', 'swiftxr-shortcode-submit' ) ?>
                </div>

            </form>

            <div class="swiftxr-modal swiftxr-hide" id="productModal">
                <div class="modal-content">

                    <div class="modal-header">

                        <h1 class="modal-title fs-5" id="productModalLabel"><?php esc_html_e( "Add WooCommerce Product", 'swiftxr-3darvr-viewer' ); ?></h1>

                        <button class="swiftxr-modal-close" type="button" onclick="CloseProductPicker()"><?php esc_html_e( "X", 'swiftxr-3darvr-viewer' ); ?></button>
                    </div>

                    <div class="modal-body">
                        <div class="input-group">

                            <span><?php esc_html_e( "@", 'swiftxr-3darvr-viewer' ); ?></span>

                            <input type="text" placeholder="Search products" aria-label="Search products" oninput="SearchProducts()" id="swiftxr-search-products">

                            <button class="button button-primary" type="button" onclick="SearchProducts()"><?php esc_html_e( "Search", 'swiftxr-3darvr-viewer' ); ?></button>
                        </div>

                        <div class="modal-body-content" id="swiftxr-product-modal">
                            <?php if($woo_commerce_products){ ?>

                                <?php foreach ( $woo_commerce_products as $woo ) { ?>
                                    <div data-product-id="<?php echo esc_attr( $woo->get_id() ); ?>">
                                    
                                        <label class="swiftxr-product-item">

                                            <input type="radio" id="wc-product-id" name="swiftxr-product" value="<?php echo esc_attr( $woo->get_id() ); ?>" oninput="SelectProduct(this)">

                                            <img src="<?php echo esc_attr( wp_get_attachment_image_src( $woo->get_image_id(), 'thumbnail' )[0] ); ?>" alt="<?php echo esc_attr( $woo->get_name() ); ?>">

                                            <p><?php esc_html_e( $woo->get_name(), 'swiftxr-3darvr-viewer' ); ?></p>

                                        </label>
                                    </div>
                                <?php } ?>

                            <?php }else{ ?>

                                <p><?php esc_html_e( 'No WooCommerce Product Found', 'swiftxr-3darvr-viewer' )?></p>
                            
                            <?php } ?>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="button button-secondary" id="product-picker-picker" onclick="CloseProductPicker()"><?php esc_html_e( "Close", 'swiftxr-3darvr-viewer' ); ?></button>

                        <button class="button button-primary" id="product-picker-select" disabled onclick="AddSelectedProduct()"><?php esc_html_e( "Add", 'swiftxr-3darvr-viewer' ); ?></button>
                    </div>
                    
                </div>
            </div>



            <div class="swiftxr-card">
                <h5 for="swiftxr-iframe"><?php esc_html_e( '3D Object Preview', 'swiftxr-3darvr-viewer' ); ?></h5>

                <iframe name="swiftxr-iframe" id="swiftxr-iframe" class="swiftxr-iframe" width="100%" height="500px" allow="fullscreen; autoplay; vr; camera; midi; encrypted-media; xr-spatial-tracking;" src="<?php echo isset( $url ) ? esc_url( $url ) : ''; ?>"></iframe>
            </div>

        </div>

        
    </div>

    
</div>

