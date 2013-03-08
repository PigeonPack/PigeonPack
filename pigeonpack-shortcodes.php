<?php
/**
 * @package Pigeon Pack
 * @since 0.0.1
 */
 
if ( ! class_exists( 'PigeonPack_Shortcodes' ) ) {
	
	/**
	 * This class defines and returns the shortcodes
	 *
	 * @since 0.0.1
	 */
	class PigeonPack_Shortcodes {
		
		/**
		 * Class Constructor
		 *
		 * @since 0.0.1
		 */
		function PigeonPack_Shortcodes() {
			
			add_shortcode( 'pigeonpack_subscribe_form', array( $this, 'do_subscribe_form' ) );
			add_shortcode( 'pigeonpack_user_optin_form', array( $this, 'do_user_optin_form' ) );
			
		}
		
		/**
		 * Shortcode for displaying a subcription form
		 *
		 * @since 0.0.1
	 	 * @uses apply_filters() Calls 'pigeonpack_subscribe_form' hook on optin form results.
		 *
		 * @param array $atts Agruments pass through shortcode
		 */
		public static function do_subscribe_form( $atts ) {
			
			$message = self::process_pigeonpack_form_submission(); // We need this if JavaScript is disabled or has an error
			
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
				
				if ( $message ) {
					
					$results .= $message;
					
				} else {
				
					$results .= '<form id="pigeonpack-subscribe-' . $list_id . '" class="pigeonpack-subscribe" name="pigeonpack-subscribe-' . $list_id . '" method="post" action="">';
					
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
					$results .= '	<input type="submit" id="pigeonpack-subscribe-button" class="pigeonpack-subscribe-button" name="pigeonpack_subscribe" value="' . __( 'Subscribe', 'pigeonpack' ) . '" />';
					$results .= '</div>';
					
					
					$results .= wp_nonce_field( 'update_pigeonpack_list', 'pigeonpack_list_nonce', true, false );
					
					$results .= '</form>';
					
					$results = apply_filters( 'pigeonpack_subscribe_form', $results, $atts );
					
				}
				
				$results .= '</div>';
				
			}
			
			return $results;
			
		}
		
		/**
		 * Shortcode for displaying a optin checkbox for WordPress Users
		 *
		 * Generally would be used if you have subcsribers but want a front-end interface
		 * for them to control their subcription preference associated with their account
		 *
		 * @since 0.0.1
	 	 * @uses apply_filters() Calls 'pigeonpack_user_optin_form' hook on optin form results.
		 *
		 * @param array $atts Agruments pass through shortcode
		 */
		public static function  do_user_optin_form( $atts ) {
			
			$processed = self::process_pigeonpack_form_submission();
			
			$user = wp_get_current_user();
			
			$defaults = array(
				'label'				=> __( 'Yes, I want to receive email updates', 'pigeonpack' ),
				'desc'				=> __( 'Unchecking this box will stop you from receiving emails based on your user profile with this site, this will not unsubscribe you from any other lists you subscribed to manually.', 'pigeonpack' ),
			);
				
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( shortcode_atts( $defaults, $atts ) );
			
			$results = '';
		
			$results .= '<div class="pigeonpack-user-optin-div">';
			
			$results .= '<form id="pigeonpack-user-optin-form" class="pigeonpack-user-optin" name="pigeonpack-user-optin-form" method="post" action="">';
			
            $results .= '<h3>' . __( 'Subscription Options', 'pigeonpack' ) . '</h3>';
			$results .= '<table class="form-table">';
			$results .= '<tr id="profile-optin">';
			$results .= '	<td>';
            $results .= '    <input type="checkbox" name="pigeonpack_subscription" id="pigeonpack_subscription" ' . checked( 'on' === get_user_meta( $user->ID, '_pigeonpack_subscription', true ), true, false ) . ' /> <label for="pigeonpack_subscription">' . $label . '</label>';
			
			if ( !empty( $desc ) )
	            $results .= '    <p class="description">' . $desc . '</p>';
			
			$results .= '	</td>';
			$results .= '</tr>';
			$results .= '</table>';
		
			$results .= '<div id="pigeonpack_user_optin_button">';
			$results .= '	<input type="submit" id="pigeonpack-user-optin-button" class="pigeonpack-user-optin-button" name="pigeonpack_user_optin" value="' . __( 'Update Subscription Preferences', 'pigeonpack' ) . '" />';
			$results .= '</div>';
			
			
			$results .= wp_nonce_field( 'user_optin_pigeonpack_list', 'pigeonpack_user_optin_nonce', true, false );
			
			$results .= '</form>';
			
			$results .= '</div>';
			
			return apply_filters( 'pigeonpack_user_optin_form', $results, $atts, $user );
			
			
		}
		
		/**
		 * Function for processing shortcode form submissions
		 *
		 * @since 0.0.1
		 */
		function process_pigeonpack_form_submission() {
			
			if ( isset( $_REQUEST['pigeonpack_user_optin'] ) ) {
				// User Optin Form Submission
				
				$user = wp_get_current_user();
				PigeonPack::pigeonpack_profile_update( $user->ID );
				return true;
				
			} else if ( isset( $_REQUEST['pigeonpack_subscribe'] ) ) {
				// Subscribe Form Submission
						
				if ( !isset( $_REQUEST['pigeonpack_list_nonce'] ) 
					|| !wp_verify_nonce( $_REQUEST['pigeonpack_list_nonce'], 'update_pigeonpack_list' ) )
					return;
				
				$subscriber_meta = $_REQUEST;	
				unset( $subscriber_meta['pigeonpack_list_id'] );
				unset( $subscriber_meta['pigeonpack_list_nonce'] );
				unset( $subscriber_meta['_wp_http_referer'] );
				
				list( $subscriber_id, $message, $double_optin ) = add_pigeonpack_subscriber( $_REQUEST['pigeonpack_list_id'], $subscriber_meta );
				
				return $message;
				
			}
			
			return false;
			
		}
	
	}
	
}