<?php

defined( 'ABSPATH' ) || die( 'Keep Quit' );

/**
 * Settings Page UI
 *
 * @var $current_section
 * @var $current_tab
 * @var $tabs
 * @var $hide_sidebar
 */

$tab_exists = isset( $tabs[ $current_tab ] );

$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ]['label'] : '';
$current_tab_title = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ]['title'] : '';

if ( empty( $current_section ) ) {

	$action_url_args = array(
		'page' => 'getwooplugins-settings',
		'tab'  => $current_tab
	);

	$reset_url_args = array(
		'action'   => 'reset',
		'_wpnonce' => wp_create_nonce( 'getwooplugins-settings' ),
	);
} else {

	$action_url_args = array(
		'page'    => 'getwooplugins-settings',
		'tab'     => $current_tab,
		'section' => $current_section,
	);

	$reset_url_args = array(
		'action'   => 'reset',
		'_wpnonce' => wp_create_nonce( 'getwooplugins-settings' ),
	);
}


$reset_url  = add_query_arg( wp_parse_args( $reset_url_args, $action_url_args ), admin_url( 'admin.php' ) );
$action_url = add_query_arg( $action_url_args, admin_url( 'admin.php' ) );


if ( ! $tab_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=getwooplugins-settings' ) );
	exit;
}

$sidebar_available     = apply_filters( 'show_getwooplugins_sidebar', has_action( 'getwooplugins_sidebar' ), $current_tab, $current_section );
$save_button_available = apply_filters( 'show_getwooplugins_save_button', true, $current_tab, $current_section );
?>
<div class="wrap woocommerce getwooplugins-settings-wrapper">
	<h1><?php echo esc_html( $current_tab_title ); ?></h1>

	<?php self::show_messages(); ?>
	<?php do_action( 'getwooplugins_before_settings', $current_tab ); ?>


	<?php
	// Don't show form if save button not available
	if ( $save_button_available ) :
		?>
	<form method="post" class="getwooplugins-settings-form" id="mainform" action="<?php echo esc_url( $action_url ); ?>" enctype="multipart/form-data">
		<?php endif; ?>
		<h1 class="screen-reader-text"><?php echo esc_html( $current_tab_title ); ?></h1>
		<?php do_action( 'getwooplugins_sections', $current_tab ); ?>

		<div class="getwooplugins-settings-content-wrapper <?php echo esc_attr( $sidebar_available ? 'has-sidebar' : '' ); ?>">

			<div class="getwooplugins-settings-main"><?php do_action( 'getwooplugins_settings', $current_tab ); ?></div>

			<?php if ( $sidebar_available ) : ?>
				<div class="getwooplugins-settings-sidebar"><?php do_action( 'getwooplugins_sidebar', $current_tab ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( $save_button_available ) : ?>
			<p class="submit submitbox">

				<button name="save" class="button-primary woocommerce-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'woo-variation-swatches' ); ?>"><?php esc_html_e( 'Save changes', 'woo-variation-swatches' ); ?></button>
				<a onclick="return confirm('<?php esc_html_e( 'Are you sure to reset?', 'woo-variation-swatches' ); ?>')" class="submitdelete" href="<?php echo esc_url( $reset_url ); ?>"><?php esc_attr_e( 'Reset all', 'woo-variation-swatches' ); ?></a>

				<?php wp_nonce_field( 'getwooplugins-settings' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $save_button_available ) : ?>
	</form>
<?php endif; ?>

	<?php do_action( 'getwooplugins_after_settings', $current_tab ); ?>

</div>
