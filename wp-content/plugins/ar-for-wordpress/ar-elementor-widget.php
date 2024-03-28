<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Create Elementor Widget
class Elementor_ar_for_wordpress_Widget extends \Elementor\Widget_Base {

	// AR widget name
	public function get_name() {
		return 'ar_for_wordpress_elementor_widget';
	}

	// AR widget title
	public function get_title() {
		return __( 'AR for WordPress', 'ar-for-wordpress-elementor-widget' );
	}

	// AR widget icon
	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	// AR custom help URL
	public function get_custom_help_url() {
		return 'https://augmentedrealityplugins.com/';
	}

	// AR widget categories
	public function get_categories() {
		return [ 'general', 'pro-elements' ];
	}

	// AR widget keywords
	public function get_keywords() {
		return [ 'AR Display', 'augmented reality', 'augmented', 'reality', '3d', '3d model' ];
	}

	// Register AR widget controls
	protected function _register_controls() {
         $args = array(
            'post_type'=> 'armodels',
            'orderby'        => 'title',
            'posts_per_page' => -1,
            'order'    => 'ASC'
        );              
        $the_query = new WP_Query( $args );
        if($the_query->have_posts() ) : 
            while ( $the_query->have_posts() ) : 
               $the_query->the_post();
               $ar_id_array[get_the_ID()] = get_the_title();
            endwhile; 
            wp_reset_postdata(); 
        else: 
        endif;
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'AR for WordPress', 'ar-for-wordpress' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_control(
			'ar_id',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'AR Model', 'ar-for-wordpress' ),
				'options' => $ar_id_array
			]
		);
		$this->end_controls_section();

	}

	// Render AR widget output on the frontend
	protected function render() {
		$settings = $this->get_settings_for_display();
		echo '<div class="oembed-elementor-widget">';
		echo do_shortcode( '[ardisplay id='.$settings['ar_id'].']' );
		echo '</div>';
	}
}