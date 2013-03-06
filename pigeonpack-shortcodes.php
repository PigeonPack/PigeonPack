<?php
/**
 * @package Pigeon Pack
 * @since 0.0.1
 */
 
if ( ! class_exists( 'PigeonPack_Shortcodes' ) ) {
	
	/**
	 * This class defines and returns the shortcodes
	 *
	 * @since 1.0
	 */
	class PigeonPack_Shortcodes {
		
		/**
		 * Class Constructor
		 *
		 * @since 0.3
		 */
		function PigeonPack_Shortcodes() {
			
			add_action( 'wp_enqueue_scripts', array( $this, 'pigeonpack_shortcode_wp_enqueue_scripts' ) );
			
			add_shortcode( 'pigeonpack_subscribe_form', array( &$this, 'do_subscribe_form' ) );
			
		}
		
		function pigeonpack_shortcode_wp_enqueue_scripts() {
		
			$pigeonpack_settings = get_pigeonpack_settings();
		
			switch( $pigeonpack_settings['css_style'] ) {
				
				case 'none' :
					break;
				
				case 'default' :
				default : 
					wp_enqueue_style( 'pigeonpack_style', PIGEON_PACK_PLUGIN_URL . '/css/pigeonpack.css', '', PIGEON_PACK_VERSION );
					break;
					
			}
			
			wp_enqueue_script( 'pigeonpack_script', PIGEON_PACK_PLUGIN_URL . '/js/pigeonpack.js', array( 'jquery' ), PIGEON_PACK_VERSION );
			$args = array(
						'ajax_url'	=> admin_url( 'admin-ajax.php' ),
						);
			wp_localize_script( 'pigeonpack_script', 'pigeonpack_ajax_object', $args );
			
		}
		
		/**
		 * Shortcode for displaying a subcription form
		 *
		 * @since 1.0
		 *
		 * @param array $atts Agruments pass through shortcode
		 */
		public static function do_subscribe_form( $atts ) {
			
			global $post;
			
			$pigeonpack_settings = get_pigeonpack_settings();
			$results = '';
			
			$defaults = array(
				'list_id'			=> false,
				'title'				=> '',
				'desc'				=> '',
				'required_only'		=> false,
			);
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( shortcode_atts( $defaults, $atts ) );
			
			if ( $list_id ) {
			
				$results .= '<div class="pigeonpack-subscribe-div">';
				
				$results .= '<form id="pigeonpack-subscribe-' . $list_id . '" class="pigeonpack-subscribe" name="pigeonpack-subscribe-' . $list_id . '">';
				$results .= '<input type="hidden" class="pigeonpack_list_id" name="pigeonpack_list_id" value="' . $list_id . '">';
				$results .= '<table class="pigeonpack-subscribe-table">';

				$list_fields = get_pigeonpack_list_fields( $list_id );
				
				foreach( $list_fields as $list_field ) {
					
					if ( in_array( $list_field['require'], array( 'on', 'always' ) ) )
						$required = 'pigeonpack-required';
					else
						$required = '';
						
					if ( ( 'true' === $required_only || 'on' === $required_only ) && empty( $required ) )
						continue;
					
					$results .= '<tr>';
					$results .= '	<th class="' . $required . '">' . $list_field['label'] . '</th>';
					$results .= '	<td>';
							
							switch( $list_field['type'] ) {
								
								case 'radio button':
									$results .= '<div class="radiofield">';
									$count = 0;
									foreach ( $list_field['choices'] as $choice ) {
									
										$results .= '<span class="subfield radiochoice">';
										$results .= '<input type="radio" id="' . $list_field['merge'] . '-' . $count . '" name="M' . $list_field['static_merge'] . '" value="' . $choice . '" ' . ( ( !empty( $required ) && 0 === $count ) ? 'checked="checked"' : '' ) . ' /><label for="' . $list_field['merge'] . '-' . $count . '">' . $choice . '</label>';
										$results .= '</span>';
										
										$count++;
										
									}
									$results .= '</div>';
									break;
									
								case 'drop down':
									$results .= '<div class="dropdownfield">';
									$results .= '<select id="' . $list_field['merge'] . '-dropdown" name="M' . $list_field['static_merge'] . '">';
									foreach ( $list_field['choices'] as $choice ) {
									
										$results .= '<option value="' . $choice . '" />' . $choice . '</option>';
										
									}
									$results .= '</select>';
									$results .= '</div>';
									break;
									
								case 'address':
									$results .= '<div class="addressfield">';
									$results .= '<span class="subfield addr1field"><label for="' . $list_field['merge'] . '-addr1">' . __( 'Street Address', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-addr1" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-addr1" value="" /></span>';
									$results .= '<span class="subfield addr2field"><label for="' . $list_field['merge'] . '-addr2">' . __( 'Address Line 2', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-addr2" class="field-type-' . $list_field['type'] . '" name="M' . $list_field['static_merge'] . '-addr2" value="" /></span>';
									$results .= '<span class="subfield cityfield"><label for="' . $list_field['merge'] . '-city">' . __( 'City', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-city" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-city" value="" /></span>';
									$results .= '<span class="subfield statefield"><label for="' . $list_field['merge'] . '-state">' . __( 'State/Province/Region', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-state" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-state" value="" /></span>';
									$results .= '<span class="subfield zipfield"><label for="' . $list_field['merge'] . '-zip">' . __( 'Postal / Zip Code', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-zip" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-zip" value="" /></span>';
									$results .= '<span class="subfield countryfield"><label for="' . $list_field['merge'] . '-country">' . __( 'Country', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-country" class="field-type-' . $list_field['type'] . '" name="M' . $list_field['static_merge'] . '-country" value="" /></span>';
									$results .= '</div>';
									break;
									
								default: //covers text, number, email, date, zip code, phone, website
									$results .= '<input type="text" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '" value="" />&nbsp;';
									break;
								
							}
							
					$results .= '	</td>';
					$results .= '</tr>';
					
				}
	
				$results .= '<tr>';
				$results .= '	<th>' . __( 'Email Format', 'pigeonpack' ) . '</th>';
				$results .= '	<td>';
				$results .= '	<div class="dropdownfield">';
				$results .= '	<select id="email-format-dropdown" name="pigeonpack_email_format">';
				$results .= '		<option value="html" />HTML</option>';
				$results .= '		<option value="plain" />' . __( 'Plain Text', 'pigeonpack' ) . '</option>';
				$results .= '	</select>';
				$results .= '	</div>';
				$results .= '	</td>';
				$results .= '</tr>';
					

				$results .= '</table>';
				
				
				$results .= '<div id="pigeonpack_subscribe_button">';
				$results .= '	<input type="submit" id="subscriber" class="pigeonpack-subscribe-button" name="pigeonpack_subscribe" value="' . __( 'Subscribe', 'pigeonpack' ) . '" />';
				$results .= '</div>';
				
				
				$results .= wp_nonce_field( 'update_pigeonpack_list', 'pigeonpack_list_nonce', true, false );
				
				$results .= '</form>';
				
				$results .= '</div>';
				
			}
			
			return $results;
			
		}
	
	}
	
}