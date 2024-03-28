<?php
if ( ! class_exists( 'LVCA_InfoBox_Param' ) ) {

	class LVCA_InfoBox_Param {

		public function __construct() {
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'lvca_infobox', array( $this, 'infobox_field' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'lvca_infobox', array( $this, 'infobox_field' ) );
				}
			}
		}

		public function infobox_field( $settings, $value ) {
			$dependency = '';
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			$text       = isset( $settings['text'] ) ? $settings['text'] : '';
			$output     = '<h4 ' . $dependency . ' class="wpb_vc_param_value ' . esc_attr( $class ) . '">' . $text . '</h4>';
			$output    .= '<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value lvca-infobox ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . '/>';
			return $output;
		}

	}
}

if ( class_exists( 'LVCA_InfoBox_Param' ) ) {
	$ultimate_paramheading_param = new LVCA_InfoBox_Param();
}
