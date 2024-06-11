<?php

defined( 'ABSPATH' ) || die( 'Keep Silent' );

if ( ! class_exists( 'Woo_Variation_Swatches_Term_Meta' ) ) :
	class Woo_Variation_Swatches_Term_Meta {

		private $taxonomy;
		private $post_type;
		private $fields = array();

		public function __construct( $taxonomy, $post_type, $fields = array() ) {
			$this->taxonomy  = $taxonomy;
			$this->post_type = $post_type;
			$this->fields    = $fields;

			// Category/term ordering
			// add_action( 'create_term', array( $this, 'create_term' ), 5, 3 );

			add_action( 'delete_term', array( $this, 'delete_term' ), 5, 4 );

			// Add form
			add_action( "{$this->taxonomy}_add_form_fields", array( $this, 'add' ) );
			add_action( "{$this->taxonomy}_edit_form_fields", array( $this, 'edit' ), 10 );
			add_action( 'created_term', array( $this, 'save' ), 10, 3 );
			add_action( 'edited_term', array( $this, 'save' ), 10, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add columns
			add_filter( "manage_edit-{$this->taxonomy}_columns", array( $this, 'taxonomy_columns' ) );
			add_filter( "manage_{$this->taxonomy}_custom_column", array( $this, 'taxonomy_column_preview' ), 10, 3 );
			add_filter( "manage_{$this->taxonomy}_custom_column", array( $this, 'taxonomy_column_group' ), 10, 3 );

			do_action( 'woo_variation_swatches_term_meta_loaded', $this );
		}

		public function preview( $attribute_type, $term_id, $fields ) {
			$meta_key = $fields[0]['id']; // take first key for preview

			$this->color_preview( $attribute_type, $term_id, $meta_key );
			$this->image_preview( $attribute_type, $term_id, $meta_key );
		}

		public function color_preview( $attribute_type, $term_id, $key ) {
			if ( 'color' === $attribute_type ) {
				$primary_color = sanitize_hex_color( get_term_meta( $term_id, $key, true ) );

				$is_dual_color   = wc_string_to_bool( get_term_meta( $term_id, 'is_dual_color', true ) );
				$secondary_color = sanitize_hex_color( get_term_meta( $term_id, 'secondary_color', true ) );

				if ( $is_dual_color && woo_variation_swatches()->is_pro() ) {
					$angle = woo_variation_swatches()->get_frontend()->get_dual_color_gradient_angle();
					printf( '<div class="wvs-preview wvs-color-preview wvs-dual-color-preview" style="background: linear-gradient(%3$s, %1$s 0%%, %1$s 50%%, %2$s 50%%, %2$s 100%%);"></div>', esc_attr( $secondary_color ), esc_attr( $primary_color ), esc_attr( $angle ) );
				} else {
					printf( '<div class="wvs-preview wvs-color-preview" style="background-color:%s;"></div>', esc_attr( $primary_color ) );
				}
			}
		}

		public function group_name( $attribute_type, $term_id ) {
			if ( ! woo_variation_swatches()->is_pro() ) {
				return '';
			}

			$group = sanitize_text_field( get_term_meta( $term_id, 'group_name', true ) );
			if ( $group ) {
				return sanitize_text_field( woo_variation_swatches()->get_backend()->get_group()->get( $group ) );
			}

			return '';
		}

		/**
		 * Create HTML Attributes from given array
		 *
		 * @param array $attributes Attribute array.
		 * @param array $exclude    Exclude attribute. Default array.
		 *
		 * @return string
		 */
		public function get_html_attributes( array $attributes, array $exclude = array() ): string {
			$attrs = array_map(
				function ( $key ) use ( $attributes, $exclude ) {
					// Exclude attribute.
					if ( in_array( $key, $exclude, true ) ) {
						return '';
					}

					$value = $attributes[ $key ];

					// If attribute value is null.
					if ( is_null( $value ) ) {
						return '';
					}

					// If attribute value is boolean.
					if ( is_bool( $value ) ) {
						return $value ? $key : '';
					}

					// If attribute value is array.
					if ( is_array( $value ) ) {
						$value = $this->get_css_classes( $value );
					}

					return sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
				},
				array_keys( $attributes )
			);

			return implode( ' ', $attrs );
		}

		/**
		 * Generate Inline Style from array
		 *
		 * @param array $inline_styles_array Inline style as array.
		 *
		 * @return string
		 * @since      1.0.0
		 */
		public function get_inline_styles( array $inline_styles_array = array() ): string {
			$styles = array();

			foreach ( $inline_styles_array as $property => $value ) {
				if ( is_null( $value ) ) {
					continue;
				}
				$styles[] = sprintf( '%s: %s;', esc_attr( $property ), esc_attr( $value ) );
			}

			return implode( ' ', $styles );
		}

		/**
		 * Array to css class.
		 *
		 * @param array $classes_array css classes array.
		 *
		 * @return string
		 * @since      1.0.0
		 */
		public function get_css_classes( array $classes_array = array() ): string {
			$classes = array();

			foreach ( $classes_array as $class_name => $should_include ) {
				if ( empty( $should_include ) ) {
					continue;
				}

				$classes[] = esc_attr( $class_name );
			}

			return implode( ' ', array_unique( $classes ) );
		}

		public function image_preview( $attribute_type, $term_id, $key ) {
			if ( 'image' === $attribute_type ) {
				$attachment_id = absint( get_term_meta( $term_id, $key, true ) );
				$image         = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

				if ( is_array( $image ) ) {
					printf( '<img src="%s" alt="" width="%d" height="%d" class="wvs-preview wvs-image-preview" />', esc_url( $image[0] ), esc_attr( $image[1] ), esc_attr( $image[2] ) );
				}
			}
		}

		public function taxonomy_columns( $columns ) {
			$new_columns = array();

			if ( isset( $columns['cb'] ) ) {
				$new_columns['cb'] = $columns['cb'];
			}

			$new_columns['wvs-meta-preview'] = '';

			if ( isset( $columns['cb'] ) ) {
				unset( $columns['cb'] );
			}

			if ( woo_variation_swatches()->is_pro() ) {
				$columns['wvs-meta-group'] = esc_html__( 'Group', 'woo-variation-swatches' );
			}

			return array_merge( $new_columns, $columns );
		}

		public function taxonomy_column_preview( $columns, $column, $term_id ) {
			if ( 'wvs-meta-preview' !== $column ) {
				return $columns;
			}

			$attribute      = woo_variation_swatches()->get_backend()->get_attribute_taxonomy( $this->taxonomy );
			$attribute_type = $attribute->attribute_type;
			$this->preview( $attribute_type, $term_id, $this->fields );

			return $columns;
		}

		public function taxonomy_column_group( $columns, $column, $term_id ) {
			if ( 'wvs-meta-group' !== $column ) {
				return $columns;
			}

			$attribute = woo_variation_swatches()->get_backend()->get_attribute_taxonomy( $this->taxonomy );

			$attribute_type = $attribute->attribute_type;

			echo wp_kses_post( $this->group_name( $attribute_type, $term_id ) );

			return $columns;
		}

		public function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {
			global $wpdb;

			$term_id = absint( $term_id );
			if ( $term_id && $taxonomy === $this->taxonomy ) {
				$wpdb->delete( $wpdb->termmeta, array( 'term_id' => $term_id ), array( '%d' ) );
			}
		}

		public function enqueue_scripts() {
			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		public function save( $term_id, $tt_id = '', $taxonomy = '' ) {
			if ( $taxonomy === $this->taxonomy ) {

				check_admin_referer('woo_variation_swatches_term_meta', 'woo_variation_swatches_term_meta_nonce');

				$data = $_POST;
				foreach ( $this->fields as $field ) {
					foreach ( $data as $post_key => $post_value ) {
						if ( $field['id'] === $post_key ) {
							switch ( $field['type'] ) {
								case 'text':
								case 'color':
									$post_value = esc_html( $post_value );
									break;
								case 'url':
									$post_value = esc_url( $post_value );
									break;
								case 'image':
									$post_value = absint( $post_value );
									break;
								case 'textarea':
									$post_value = esc_textarea( $post_value );
									break;
								case 'editor':
									$post_value = wp_kses_post( $post_value );
									break;
								case 'select':
								case 'select2':
									$post_value = sanitize_key( $post_value );
									break;
								case 'checkbox':
									$post_value = sanitize_key( $post_value );
									break;
								default:
									do_action( 'woo_variation_swatches_save_term_meta', $term_id, $field, $post_value, $taxonomy );
									break;
							}
							update_term_meta( $term_id, $field['id'], $post_value );
						}
					}
				}
				do_action( 'woo_variation_swatches_after_term_meta_saved', $term_id, $taxonomy );
			}
		}

		public function add() {
			$this->generate_fields();
		}

		private function generate_fields( $term = false ) {
			$screen           = get_current_screen();
			$screen_post_type = $screen ? $screen->post_type : '';
			$screen_taxonomy  = $screen ? $screen->taxonomy : '';

			if ( ( $screen_post_type === $this->post_type ) && ( $screen_taxonomy === $this->taxonomy ) ) {
				$this->generate_form_fields( $this->fields, $term );
			}
		}

		public function allowed_tags() {

			 $allowed_tags = array_fill_keys( array( 'select', 'option' ), array() );

			 $allowed_tags['select'] = array_fill_keys( array('id', 'multiple', 'type', 'name', 'class', 'size', 'required', 'checked', 'selected', 'value' ), true );
			 $allowed_tags['option'] = array_fill_keys( array( 'class', 'checked', 'selected', 'value' ), true );

			 return $allowed_tags;
		}

		public function generate_form_fields( $fields, $term ) {
			$fields = apply_filters( 'woo_variation_swatches_term_meta_fields', $fields, $term );

			if ( empty( $fields ) ) {
				return;
			}

			foreach ( $fields as $field ) {
				$field = apply_filters( 'woo_variation_swatches_term_meta_field', $field, $term );

				if ( empty( $field['id'] ) ) {
					continue;
				}

				if ( ! $term ) {
					$field['value'] = $field['default'] ?? '';
				} else {
					$field['value'] = get_term_meta( $term->term_id, $field['id'], true );
				}

				$field['size']       = $field['size'] ?? '40';
				$field['desc']       = $field['desc'] ?? '';
				$field['dependency'] = $field['dependency'] ?? array();

				$this->field_start( $field, $term );

				$attributes = array(
					'name'        => $field['id'],
					'id'          => $field['id'],
					'type'        => $field['type'],
					'value'       => $field['value'],
					'size'        => $field['size'],
					'required'    => ! empty( $field['required'] ) ? 'required' : null,
					'placeholder' => $field['placeholder'] ?? null,
				);

				switch ( $field['type'] ) {
					case 'text':
					case 'url':
						printf( '<input %s />', wp_kses_data($this->get_html_attributes( $attributes )) );
						break;

					case 'color':
						$attributes['type']               = 'text';
						$attributes['class']              = array( 'wvs-color-picker' => true );
						$attributes['data-default-color'] = $field['value'];
						printf( '<input %s />', wp_kses_data($this->get_html_attributes( $attributes )) );
						break;

					case 'textarea':
						$attributes['value'] = null;
						$attributes['rows']  = 5;
						$attributes['cols']  = $field['size'];
						$attributes['size']  = null;
						printf( '<textarea %s>%s</textarea>', wp_kses_data($this->get_html_attributes( $attributes )), esc_textarea( $field['value'] ) );
						break;

					case 'editor':
						$field['settings'] = $field['settings'] ?? array(
							'textarea_rows' => 8,
							'quicktags'     => false,
							'media_buttons' => false,
						);
						wp_editor( $field['value'], $field['id'], $field['settings'] );
						break;

					case 'select':
					case 'select2':
						$field['options']       = $field['options'] ?? array();
						$css_class              = ( 'select2' === $field['type'] ) ? 'wc-enhanced-select' : '';
						$attributes['type']     = 'select';
						$attributes['size']     = null;
						$attributes['class']    = $css_class;
						$attributes['multiple'] = ! empty( $field['multiple'] ) ? 'multiple' : null;

						$select_options = array();
						foreach ( $field['options'] as $key => $option ) {
							$select_options[] = sprintf( '<option %s value="%s">%s</option>', selected( $field['value'], $key, false ), esc_attr( $key ), esc_html( $option ) );
						}

						$options = implode( '', $select_options );
						printf( '<select %s>%s</select>', wp_kses_data($this->get_html_attributes( $attributes )), wp_kses( $options, $this->allowed_tags()) );

						break;

					case 'image':
						?>
						<div class="meta-image-field-wrapper">
							<div class="image-preview">
								<img
									data-placeholder="<?php echo esc_url( $this->placeholder_img_src() ); ?>"
									 src="<?php echo esc_url( $this->get_img_src( $field['value'] ) ); ?>"
									 width="60px"
									 height="60px" />
							</div>
							<div class="button-wrapper">
								<input
									type="hidden"
									id="<?php echo esc_attr( $field['id'] ); ?>"
									name="<?php echo esc_attr( $field['id'] ); ?>"
									value="<?php echo esc_attr( $field['value'] ); ?>" />

								<button type="button" class="wvs_upload_image_button button button-primary button-small">
								<?php esc_html_e( 'Upload / Add image', 'woo-variation-swatches' ); ?>
								</button>
								<button
									type="button"
									style="<?php echo( empty( $field['value'] ) ? 'display:none' : '' ); ?>"
									class="wvs_remove_image_button button button-danger button-small">
									<?php esc_html_e( 'Remove image', 'woo-variation-swatches' ); ?>
								</button>
							</div>
						</div>
						<?php
						break;

					case 'checkbox':
						?>
						<label for="<?php echo esc_attr( $field['id'] ); ?>">
							<input name="<?php echo esc_attr( $field['id'] ); ?>"
								   id="<?php echo esc_attr( $field['id'] ); ?>"
								<?php checked( $field['value'], 'yes' ); ?>
								   type="<?php echo esc_attr( $field['type'] ); ?>"
								   value="yes"
								   <?php if ( ! empty( $field['required'] ) ) { ?>
										required="required"
								   <?php } ?> />
							<?php echo esc_html( $field['label'] ); ?></label>
						<?php
						break;

					default:
						do_action( 'woo_variation_swatches_term_meta_field', $field, $term );
						break;
				}

				$this->field_end( $field, $term );
			}

			wp_nonce_field('woo_variation_swatches_term_meta', 'woo_variation_swatches_term_meta_nonce');
		}

		private function field_start( $field, $term ) {
			// Example:
			// http://emranahmed.github.io/Form-Field-Dependency/
			/*'dependency' => array(
				array( '#show_tooltip' => array( 'type' => 'equal', 'value' => 'yes' ) )
			)*/

			$attributes = array(
				'data-gwp_dependency' => ! empty( $field['dependency'] ) ? wc_esc_json( wp_json_encode( $field['dependency'] ) ) : null,
			);

			if ( ! $term ) {
				// Edit mode.
				?>
				<div <?php echo wp_kses_data($this->get_html_attributes( $attributes )); ?>
				class="form-field <?php echo esc_attr( $field['id'] ); ?> <?php echo empty( $field['required'] ) ? '' : 'form-required'; ?>">
				<?php if ( 'checkbox' !== $field['type'] ) : ?>
					<label for="<?php echo esc_attr( $field['id'] ); ?>">
						<?php echo wp_kses_post( $field['label'] ); ?>
					</label>
				<?php
				endif;
			} else {
				?>
				<tr <?php echo wp_kses_data($this->get_html_attributes( $attributes )); ?>
				class="form-field <?php echo esc_attr( $field['id'] ); ?> <?php echo empty( $field['required'] ) ? '' : 'form-required'; ?>">
				<th scope="row">
					<label for="<?php echo esc_attr( $field['id'] ); ?>">
						<?php echo wp_kses_post( $field['label'] ); ?>
					</label>
				</th>
				<td>
				<?php
			}
		}

		private function get_img_src( $thumbnail_id = false ) {
			if ( ! empty( $thumbnail_id ) ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = $this->placeholder_img_src();
			}

			return $image;
		}

		public function placeholder_img_src() {
			return woo_variation_swatches()->images_url( '/placeholder.png' );
		}

		private function field_end( $field, $term ) {
			if ( ! $term ) {
				?>
				<p><?php echo wp_kses_post( $field['desc'] ); ?></p>
				</div>
				<?php
			} else {
				?>
				<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p></td>
				</tr>
				<?php
			}
		}

		public function edit( $term ) {
			$this->generate_fields( $term );
		}
	}
endif;
