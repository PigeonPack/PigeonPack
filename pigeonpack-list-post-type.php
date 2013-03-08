<?php
/**
 * @package Pigeon Pack
 * @since 0.0.1
 */

if ( !function_exists ( 'create_list_post_type' ) ) {
	
	/**
	 * Creates Pigeon Pack subscriber list post type
	 *
	 * Called on 'init' action hook
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_post_type
	 *
	 * @since 0.0.1
	 * @uses register_post_type() to register campaign post type
	 */
	function create_list_post_type()  {
		
		$labels = array(    
			'name' 					=> __( 'Pigeon Pack Lists', 'pigeonpack' ),
			'singular_name' 		=> __( 'List', 'pigeonpack' ),
			'add_new' 				=> __( 'Add New List', 'pigeonpack' ),
			'add_new_item' 			=> __( 'Add New List', 'pigeonpack' ),
			'edit_item' 			=> __( 'Edit List', 'pigeonpack' ),
			'new_item' 				=> __( 'New List', 'pigeonpack' ),
			'view_item' 			=> __( 'View List', 'pigeonpack' ),
			'search_items' 			=> __( 'Search Lists', 'pigeonpack' ),
			'not_found' 			=> __( 'No lists found', 'pigeonpack' ),
			'not_found_in_trash' 	=> __( 'No lists found in trash', 'pigeonpack' ), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __( 'Lists', 'pigeonpack' )
		);
		
		$args = array(
			'label' 				=> 'list',
			'labels' 				=> $labels,
			'description' 			=> __( 'Email Lists', 'pigeonpack' ),
			'public'				=> false,
			'publicly_queryable' 	=> false,
			'exclude_fromsearch' 	=> true,
			'show_ui' 				=> true,
			'show_in_nav_menus'		=> false,
			'show_in_menu' 			=> 'pigeon-pack', //include in the pigeon-pack menu, not it's own menu
			'menu_position'			=> 100, //below second separator 
			'capability_type' 		=> array( 'pigeonpack_list', 'pigeonpack_lists' ),
			'map_meta_cap' 			=> true,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title' ),
			'register_meta_box_cb' 	=> 'add_pigeonpack_list_metaboxes',
			'has_archive' 			=> true,
			'rewrite' 				=> array( 'slug' => 'list' ),
			'menu_icon'				=> PIGEON_PACK_PLUGIN_URL . '/images/lists-16x16.png',
			);
	
		register_post_type( 'pigeonpack_list', $args );
		
	}
	add_action( 'init', 'create_list_post_type' );
	
}

if ( !function_exists ( 'add_pigeonpack_list_metaboxes' ) ) {
	
	/**
	 * Called by 'add_pigeonpack_list_metaboxes' hook from register_meta_box_cb during register post
	 *
	 * Adds metabox for Pigeon Pack list
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'add_pigeonpack_list_metaboxes' for future addons
	 */	
	function add_pigeonpack_list_metaboxes() {
		
		add_meta_box( 'pigeonpack_list_new_subscriber_box', __( 'Add New Subscriber', 'pigeonpack' ), 'pigeonpack_list_new_subscriber_box', 'pigeonpack_list', 'normal', 'high' );
		add_meta_box( 'pigeonpack_list_subscriber_box', __( 'Pigeon Pack Subscribers', 'pigeonpack' ), 'pigeonpack_list_subscriber_box', 'pigeonpack_list', 'normal', 'high' );
		add_meta_box( 'pigeonpack_list_fields_box', __( 'Pigeon Pack Subscriber Fields & {{MERGE}} Tags', 'pigeonpack' ), 'pigeonpack_list_fields_box', 'pigeonpack_list', 'normal', 'high' );
		add_meta_box( 'pigeonpack_list_options_box', __( 'Pigeon Pack List Options', 'pigeonpack' ), 'pigeonpack_list_options_box', 'pigeonpack_list', 'normal', 'high' );
		add_meta_box( 'pigeonpack_list_options_box', __( 'Required Email Footer Content', 'pigeonpack' ), 'pigeonpack_required_footer_settings', 'pigeonpack_list', 'normal', 'high' );
		add_meta_box( 'pigeonpack_double_optin_box', __( 'Double Opt-In Options', 'pigeonpack' ), 'pigeonpack_double_optin_box', 'pigeonpack_list', 'normal', 'high' );
		
		do_action( 'add_pigeonpack_list_metaboxes' );
		
	}

}

if ( !function_exists ( 'pigeonpack_list_new_subscriber_box' ) ) {
		
	/**
	 * Called by add_meta_box function call
	 *
	 * Outputs metabox for adding new subscribers to list
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 */	
	function pigeonpack_list_new_subscriber_box( $post ) {
	
		?>
		
		<div id="pigeonpack_list_new_subscriber_metabox">
			
			<table id="pigeonpack_list_new_subscriber_table" class="pigeonpack_table">
					
				<?php
				
				$list_fields = get_pigeonpack_list_fields( $post->ID );
				
				foreach( $list_fields as $list_field ) {
					
					if ( in_array( $list_field['require'], array( 'on', 'always' ) ) )
						$required = 'required';
					else
						$required = '';
					
					?>
					
					<tr>
						<th class="<?php echo $required; ?>"><?php echo( $list_field['label'] ); ?></th>
						<td>
						
							<?php
							
							switch( $list_field['type'] ) {
								
								case 'radio button':
									echo '<div class="radiofield">';
									$count = 0;
									foreach ( $list_field['choices'] as $choice ) {
									
										echo '<span class="subfield radiochoice">';
										echo '<input type="radio" id="' . $list_field['merge'] . '-' . $count . '" name="M' . $list_field['static_merge'] . '" value="' . $choice . '" ' . ( ( !empty( $required ) && 0 === $count ) ? 'checked="checked"' : '' ) . ' /><label for="' . $list_field['merge'] . '-' . $count . '">' . $choice . '</label>';
										echo '</span>';
										
										$count++;
										
									}
									echo '</div>';
									break;
									
								case 'drop down':
									echo '<div class="dropdownfield">';
									echo '<select id="' . $list_field['merge'] . '-dropdown" name="M' . $list_field['static_merge'] . '">';
									foreach ( $list_field['choices'] as $choice ) {
									
										echo '<option value="' . $choice . '" />' . $choice . '</option>';
										
									}
									echo '</select>';
									echo '</div>';
									break;
									
								case 'address':
									echo '<div class="addressfield">';
									echo '<span class="subfield addr1field"><label for="' . $list_field['merge'] . '-addr1">' . __( 'Street Address', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-addr1" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-addr1" value="" /></span>';
									echo '<span class="subfield addr2field"><label for="' . $list_field['merge'] . '-addr2">' . __( 'Address Line 2', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-addr2" class="field-type-' . $list_field['type'] . '" name="M' . $list_field['static_merge'] . '-addr2" value="" /></span>';
									echo '<span class="subfield cityfield"><label for="' . $list_field['merge'] . '-city">' . __( 'City', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-city" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-city" value="" /></span>';
									echo '<span class="subfield statefield"><label for="' . $list_field['merge'] . '-state">' . __( 'State/Province/Region', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-state" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-state" value="" /></span>';
									echo '<span class="subfield zipfield"><label for="' . $list_field['merge'] . '-zip">' . __( 'Postal / Zip Code', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-zip" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '-zip" value="" /></span>';
									echo '<span class="subfield countryfield"><label for="' . $list_field['merge'] . '-country">' . __( 'Country', 'pigeonpack' ) . '</label><input type="text" id="' . $list_field['merge'] . '-country" class="field-type-' . $list_field['type'] . '" name="M' . $list_field['static_merge'] . '-country" value="" /></span>';
									echo '</div>';
									break;
									
								default: //covers text, number, email, date, zip code, phone, website
									echo '<input type="text" class="field-type-' . $list_field['type'] . ' ' . $required . '" name="M' . $list_field['static_merge'] . '" value="" />&nbsp;';
									break;
								
							}
								
							?>
						</td>
					</tr>
					
					<?php
					
				}
	
				echo '<tr>';
				echo '	<th>' . __( 'Email Format', 'pigeonpack' ) . '</th>';
				echo '	<td>';
				echo '	<div class="dropdownfield">';
				echo '	<select id="email-format-dropdown" name="pigeonpack_email_format">';
				echo '		<option value="html" />HTML</option>';
				echo '		<option value="plain" />' . __( 'Plain Text', 'pigeonpack' ) . '</option>';
				echo '	</select>';
				echo '	</div>';
				echo '	</td>';
				echo '</tr>';
				
				?>
			
			</table>
			
			<div id="add_new_subscriber_button">
				<input type="button" id="add_pigeonpack_subscriber" class="add-new-subscriber button button-secondary button-large" name="add_pigeonpack_subscriber" value="<?php _e( 'Add New Subscriber', 'pigeonpack' ); ?>" />
			</div>
			
			<div id="update_subscriber_button">
				<input type="button" id="update_pigeonpack_subscriber" class="update-subscriber button button-secondary button-large" name="update_subscriber_button" value="<?php _e( 'Update Subscriber', 'pigeonpack' ); ?>" /> &nbsp; 
				<input type="button" id="cancel_update_pigeonpack_subscriber" class="cancel-update-subscriber button button-secondary button-large" name="cancel_update_subscriber" value="<?php _e( 'Cancel', 'pigeonpack' ); ?>" /> 
				<input type="hidden" id="update_pigeonpack_subscriber_id" name="update_pigeonpack_subscriber_id" value="" />
			</div>
			
			<?php wp_nonce_field( 'update_pigeonpack_list', 'pigeonpack_list_nonce' ); ?>
		
		</div>
		
		<?php	
		
	}

}

if ( !function_exists ( 'pigeonpack_list_subscriber_box' ) ) {
		
	/**
	 * Called by add_meta_box function call
	 *
	 * Outputs metabox for displaying existing subscribers in list
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 */	
	function pigeonpack_list_subscriber_box( $post ) {
	
		?>
	   
		<div id="pigeonpack_list_subscriber_metabox">
			
			<table id="pigeonpack_list_subscriber_table" class="pigeonpack_table">
					
				<?php
				
				$list_fields = get_pigeonpack_list_fields( $post->ID );
				
				echo '<thead>';
				echo '<tr>';
			
				echo '<th>' . __( 'Delete', 'pigeonpack' ) . '</th>';
				echo '<th>&nbsp;</th>';
				
				foreach( $list_fields as $list_field ) {
					
					echo '<th>' . $list_field['label'] . '</th>';
					
				}
			
				echo '<th>' . __( 'Date Added', 'pigeonpack' ) . '</th>';
				echo '<th>' . __( 'Last Modified', 'pigeonpack' ) . '</th>';
				echo '<th>' . __( 'Status', 'pigeonpack' ) . '</th>';
				
				echo '</tr>';
				echo '</thead>';
					
				delete_post_meta( $post->ID, '_current_subscribers' );
				
				if ( isset( $_REQUEST['pp_limit'] ) )
					$limit = $_REQUEST['pp_limit'];
				else
					$limit = 20;
					
				if ( isset( $_REQUEST['pp_paged'] ) )
					$page = $_REQUEST['pp_paged'];
				else
					$page = 1;
					
				$subscribers = get_pigeonpack_subscribers( $post->ID, $limit, ( ( $page - 1 ) * $limit ) );
				$subscriber_count = get_pigeonpack_subscriber_count( $post->ID );
				
				echo '<tbody>';
				
				if ( !empty( $subscribers ) ) {
						
					$count = 0;
					foreach ( $subscribers as $subscriber ) {
						
						$oddeven = ( 0 === $count % 2 ) ? 'even' : 'odd';
					
						echo '<tr id="subscriber-' . $subscriber['id'] . '" class="' . $oddeven . '">';
						
						echo subscriber_row( $subscriber, $list_fields );
						
						echo '</tr>';
						
						$count++;
						
					}
					
				} else {
						
					echo '<tr id="no_subscribers">';
					echo '<td colspan="' . ( count( $list_fields ) + 4 ) . '">' . __( 'No Subscribers for this list', 'pigeonpack' ) . '</td>';
					echo '</tr>';	
				
				}
				
				if ( $limit < $subscriber_count ) {
					
					echo '<tfoot>';
					echo '<tr>';
				
					echo '<tr>';
					echo '<td colspan="' . ( count( $list_fields ) + 5 ). '">';
					
					if ( $page >= 2 ) {
							
						echo '<div id="prev_pigeonpack_subscribers">';
						echo '<a href="' . add_query_arg( array( 'pp_limit' => $limit, 'pp_paged' => ( $page - 1 ) ) ) . '">' . __( 'Previous', 'pigeonpack' ) . '</a>';
						echo '</div>';
						
					}
					
					if ( $page < ( $subscriber_count / $limit ) ) {
						
						echo '<div id="next_pigeonpack_subscribers">';
						echo '<a href="' . add_query_arg( array( 'pp_limit' => $limit, 'pp_paged' => ( $page + 1 ) ) ) . '">' . __( 'Next', 'pigeonpack' ) . '</a>';
						echo '</div>';
					
					}
					
					echo '</td>';
					echo '</tr>';
					
					echo '</tfoot>';
				
				}
				
				?>
				
				</tbody>
			
			</table>
			
			<div id="delete_subscribers_button">
				<input type="button" id="delete_pigeonpack_subscribers" class="delete-subscribers button button-secondary button-large" name="delete_pigeonpack_subscribers_button" value="<?php _e( 'Delete Subscriber(s)', 'pigeonpack' ); ?>" />
			</div>
			
		</div>
		
		<?php	
		
	}

}

if ( !function_exists( 'pigeonpack_list_fields_box' ) ) {
		
	/**
	 * Called by add_meta_box function call
	 *
	 * Outputs metabox for managing possible list fields
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 */	
	function pigeonpack_list_fields_box( $post ) {
	
		?>
		
		<div id="pigeonpack_list_fields_metabox">
		
			<table id="pigeonpack_list_fields_table" class="pigeonpack_table">
				<thead>
				<tr>
					<th><?php _e( 'Field Label', 'pigeonpack' ); ?></th>
					<th><?php _e( 'Field Type', 'pigeonpack' ); ?></th>
					<th><?php _e( 'Require?', 'pigeonpack' ); ?></th>
					<th><?php _e( 'Merge Tag', 'pigeonpack' ); ?></th>
					<th><?php _e( 'Delete', 'pigeonpack' ); ?></th>
				</tr>
				</thead>
				
				<tbody>
				<?php
				
				$list_fields = get_pigeonpack_list_fields( $post->ID );
				$last_field = end( $list_fields );
				update_post_meta( $post->ID, '_last_merge_id', $last_field['static_merge'] );
				
				$count = 0;
				$altcount = 0;
				foreach( $list_fields as $list_field ) {
					
					$oddeven = ( 0 === $count % 2 ) ? 'even' : 'odd';
					
					?>
					
					<tr class="<?php echo $oddeven; ?>">
						<td>
							<input type="text" name="pigeonpack_field_label[<?php echo $count; ?>]" value="<?php echo $list_field['label']; ?>" />
							
							<?php
							
							if ( in_array( $list_field['type'], array( 'radio button', 'drop down' ) ) ) {
								
								echo '<ul>';
								
								foreach( $list_field['choices'] as $choice ) {
								
									echo '<li><input type="text" name="pigeonpack_field_choice[' . $count . '][]" value="' . $choice . '" /> <img class="add_option" src="' . PIGEON_PACK_PLUGIN_URL . '/images/plus-16x16.png' . '" /><img class="remove_option" src="'  . PIGEON_PACK_PLUGIN_URL . '/images/minus-16x16.png' . '" /></li>';
									
								}
								
								echo '</ul>';
								
							}
							
							?>
							
						</td>
						<td>
							<?php echo $list_field['type']; ?>
							<input type="hidden" name="pigeonpack_field_type[<?php echo $count; ?>]" value="<?php echo $list_field['type']; ?>" />
						</td>
						<td>
						<?php
						if ( 'always' === $list_field['require'] ) {
							
							_e( 'ALWAYS', 'pigeonpack' );
							echo '<input type="hidden" name="pigeonpack_field_require[' . $count . ']" value="always" />';
						
						} else {
							
							echo '<input type="checkbox" name="pigeonpack_field_require[' . $count . ']" ' . checked( 'on' === $list_field['require'], true, false ) . ' />';
						
						}
						?>
						</td>
						<td>
						<?php
						
						$altcount = !empty( $list_field['static_merge'] ) ? $list_field['static_merge'] : $altcount++;
						
						if ( 'always' === $list_field['require'] ) {
							
							echo '{{<input type="text" readonly="readonly" disabled="disabled" class="pigeonpack-medium-text" name="pigeonpack_field_merge[' . $count . ']" value="' . ( ( !empty( $list_field['merge'] ) ) ? $list_field['merge'] : 'MERGE' . $altcount ) . '" />}} or {{MERGE' . $altcount . '}}';
							echo '<input type="hidden" name="pigeonpack_field_merge[' . $count . ']" value="' . $list_field['merge'] . '" />';
							echo '<input type="hidden" class="pigeonpack_field_static_merge" name="pigeonpack_field_static_merge[' . $count . ']" value="' . $altcount . '" />';
						
						} else {
							
							echo '{{<input type="text" class="pigeonpack-medium-text" name="pigeonpack_field_merge[' . $count . ']" value="' . ( ( !empty( $list_field['merge'] ) ) ? $list_field['merge'] : 'MERGE' . $altcount ) . '" />}} or {{MERGE' . $altcount . '}}';
							echo '<input type="hidden" class="pigeonpack_field_static_merge" name="pigeonpack_field_static_merge[' . $count . ']" value="' . $altcount . '" />';
						
						}
						?>
						</td>
						<td>
						<?php
						if ( isset( $list_field['nodelete'] ) && 'true' === $list_field['nodelete'] )
							echo '<input type="hidden" name="pigeonpack_field_nodelete[' . $count . ']" value="true" />';
						else
							echo '<img class="pigeonpack_delete_field" src="' . PIGEON_PACK_PLUGIN_URL . '/images/delete-16x16.png' . '" />';
						?>
						</td>
					</tr>
					
					<?php
					
					$count++;
					$altcount++;
					
				}
				
				?>
				</tbody>
			
			</table>
			
			<div id="add_list_field">
				<img class="pigeonpack_add_field" src="<?php echo PIGEON_PACK_PLUGIN_URL . '/images/add-16x16.png'; ?>" /> <?php _e( 'add a field', 'pigeonpack' ); ?>
			</div>
			
			<div id="list_field_types">
			
				<?php
				
				$field_types = get_pigeonpack_default_field_types();
				
				foreach( $field_types as $field_type ) {
					
					?>
					
					<input type="button" class="add-field-type button button-primary button-small" name="<?php echo $field_type['type']; ?>" value="<?php echo $field_type['label']; ?>" />
	
					<?php
					
				}
				
				?>
			
			</div>
		
		</div>
		
		<?php	
		
	}

}

if ( !function_exists( 'pigeonpack_list_options_box' ) ) {
		
	/**
	 * Display Pigeon Pack list options
	 *
	 * @todo Send a final welcome email, send unsubscribe confirmation notifications to my subscribers, end-of-the-day summary of subscribe and unsubscribe activity on your list, receive quick email alerts when subscribers join or leave this list
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 **/
	function pigeonpack_list_options_box( $post ) {
		
		$pigeonpack_settings = get_pigeonpack_settings();
		
		$allow_user_format = get_post_meta( $post->ID, '_pigeonpack_allow_user_format', true );
		$allow_user_format = ( $allow_user_format ) ? $allow_user_format : $pigeonpack_settings['allow_user_format'];
		
		?>
		
		<div id="pigeonpack_list_options_metabox">
		
			<table id="pigeonpack_list_options_table" class="pigeonpack_table">
			
				<tbody>
				
				<tr>
					<th><?php _e( 'Subscribers can set their preferred email format.', 'pigeonpack' ); ?></th>
					<td><input type="checkbox" name="pigeonpack_allow_user_format" <?php checked( 'on' === $allow_user_format, true ); ?> /></td>
				</tr>
				
				</tbody>
				
			</table>
		
		</div>
		
		<?php
		
	}

}

if ( !function_exists( 'pigeonpack_double_optin_box' ) ) {
		
	/**
	 * Display Pigeon Pack Double Opt-in options
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 **/
	function pigeonpack_double_optin_box( $post ) {
		
		$pigeonpack_settings = get_pigeonpack_settings();
		
		$default_double_optin = array(
									'enabled'		=> 'on',
									'from_name'		=> $pigeonpack_settings['from_name'],
									'from_email'	=> $pigeonpack_settings['from_email'],
									'subject'		=> __( 'Please validate your email address', 'pigeonpack' ),
									'message'		=> sprintf( __( "Please click the link below to validate your email and activate your email subscription to %s.\n\n%s\n\nThank you.", 'pigeonpack' ), '{{LIST_NAME}}', '{{OPTIN_URL}}' ),
								);
		
		$double_optin = get_post_meta( $post->ID, '_pigeonpack_double_optin_settings', true );
		$double_optin = wp_parse_args( $double_optin, $default_double_optin );
		
		?>

		
		<div id="pigeonpack_double_optin_metabox">
		
			<table id="pigeonpack_double_optin_table" class="pigeonpack_table">
            
                <tr>
                    <th><?php _e( 'Enable Double Opt-In', 'pigeonpack' ); ?></th>
                    <td>
                    <input type="checkbox" id="pigeonpack_double_optin_enabled" name="pigeonpack_double_optin_enabled" <?php checked( 'on' === $double_optin['enabled'], true ); ?> />
                    </td>
                </tr>
            
                <tr>
                    <th><?php _e( 'From Name', 'pigeonpack' ); ?></th>
                    <td>
                    <input type="text" id="pigeonpack_double_optin_from_name" class="regular-text" name="pigeonpack_double_optin_from_name" value="<?php echo htmlspecialchars( stripcslashes( $double_optin['from_name'] ) ); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><?php _e( 'From Email', 'pigeonpack' ); ?></th>
                    <td>
                    <input type="text" id="pigeonpack_double_optin_from_email" class="regular-text" name="pigeonpack_double_optin_from_email" value="<?php echo htmlspecialchars( stripcslashes( $double_optin['from_email'] ) ); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><?php _e( 'Subject', 'pigeonpack' ); ?></th>
                    <td>
                    <input type="text" id="pigeonpack_double_optin_subject" class="regular-text" name="pigeonpack_double_optin_subject" value="<?php echo htmlspecialchars( stripcslashes( $double_optin['subject'] ) ); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><?php _e( 'Message', 'pigeonpack' ); ?></th>
                    <td>
                    <textarea id="pigeonpack_double_optin_message" class="large-text" name="pigeonpack_double_optin_message" cols="50" rows="6"><?php echo htmlspecialchars( stripslashes( $double_optin['message'] ) ); ?></textarea>
                    </td>
                </tr>
                
            </table>
        
        </div>
                    
	<?php
		
	}
	
}

if ( !function_exists( 'pigeonpack_required_footer_settings' ) ) {
		
	/**
	 * Display Pigeon Pack Required Email Footer options
	 * These are required to comply with SPAM laws
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 **/
	function pigeonpack_required_footer_settings( $post ) {
		
		$pigeonpack_settings = get_pigeonpack_settings();
		
		$default_email_footer = array(
									'company'		=> $pigeonpack_settings['company'],
									'address'		=> $pigeonpack_settings['address'],
									'reminder'		=> $pigeonpack_settings['reminder'],
								);
		
		$required_email_footer = get_post_meta( $post->ID, '_pigeonpack_required_footer_settings', true );
		$required_email_footer = wp_parse_args( $required_email_footer, $default_email_footer );
		
		?>

		<div id="pigeonpack_required_email_footer_metabox">
    
            <p><?php _e( "Enter the contact information and physical mailing address for the owner of this list. It's required by law.", 'pigeonpack' ); ?></p>
                        
			<table id="pigeonpack_required_email_footer_table" class="pigeonpack_table">
            
                <tr>
                    <th><?php _e( 'Company/Organization', 'pigeonpack' ); ?></th>
                    <td>
                    <input type="text" id="pigeonpack_footer_company" name="pigeonpack_footer_company" value="<?php echo htmlspecialchars( stripcslashes( $required_email_footer['company'] ) ); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th><?php _e( 'Address', 'pigeonpack' ); ?></th>
                    <td>
                    <textarea id="pigeonpack_footer_address" class="large-text" name="pigeonpack_footer_address" cols="50" rows="3"><?php echo htmlspecialchars( stripslashes( $required_email_footer['address'] ) ); ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th><?php _e( 'Permission Reminder', 'pigeonpack' ); ?></th>
                    <td>
                    <textarea id="pigeonpack_footer_reminder" class="large-text" name="pigeonpack_footer_reminder" cols="50" rows="3"><?php echo htmlspecialchars( stripslashes( $required_email_footer['reminder'] ) ); ?></textarea>
                    <p class="description">
                    <?php _e( "Recipients forget signing up to lists all the time. To prevent false spam reports, let's briefly remind your recipients how they got on your list.", 'pigeonpack' ); ?>
                    </p>
                    </td>
                </tr>
                
            </table>
        
        </div>
                    
	<?php
		
	}
	
}


if ( !function_exists( 'get_pigeonpack_list_fields' ) ) {
	
	/**
	 * Output Pigeon Pack list fields
	 *
	 * @since 0.0.1
	 *
	 * @param int $post_id WordPress post object ID
	 * @return array Associative array of list fields for this list
	 **/
	function get_pigeonpack_list_fields( $list_id ) {
		
		$list_fields = get_post_meta( $list_id, '_pigeonpack_list_fields', true );
		
		if ( empty( $list_fields ) )
			$list_fields = get_pigeonpack_list_fields_defaults();
			
		return $list_fields;
		
	}

}

if ( !function_exists( 'get_pigeonpack_list_fields_defaults' ) ) {
		
	/**
	 * Returns required and default list fields
	 *
	 * @since 0.0.1
	 * @uses apply_filters() Calls 'pigeonpack_list_fields_defaults' hook on default list fields array
	 *
	 * @return array Associative array of required and default list fields
	 **/
	function get_pigeonpack_list_fields_defaults() {
	
		$required[] = array(
							'label'			=> __( 'Email Address', 'pigeonpack' ),
							'type'			=> 'email',
							'require'		=> 'always',
							'merge'			=> 'EMAIL',
							'static_merge'	=> '0',
							'nodelete'		=> 'true',
							);
		$defaults[] = array(
							'label'			=> __( 'First Name', 'pigeonpack' ),
							'type'			=> 'text',
							'require'		=> 'off',
							'merge'			=> 'FNAME',
							'static_merge'	=> '1',
							);
		$defaults[] = array(
							'label'			=> __( 'Last Name', 'pigeonpack' ),
							'type'			=> 'text',
							'require'		=> 'off',
							'merge'			=> 'LNAME',
							'static_merge'	=> '2',
							);
		
		return array_merge( $required, apply_filters( 'pigeonpack_list_fields_defaults', $defaults ) );
		
	}

}

if ( !function_exists( 'get_pigeonpack_default_field_types' ) ) {
			
	/**
	 * Returns default field types
	 *
	 * @since 0.0.1
	 * @uses apply_filters() Calls 'pigeonpack_default_field_types' hook on default field types array
	 *
	 * @return array Associative array of default field types
	 **/
	function get_pigeonpack_default_field_types() {
	
		$defaults[] = array(
							'label'		=> __( 'Text', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Number', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Radio Button', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Drop Down', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Date', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Address', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Zip Code (US Only)', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Phone', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		$defaults[] = array(
							'label'		=> __( 'Website', 'pigeonpack' ),
							'type'		=> __( 'text', 'pigeonpack' )
							);
		
		return apply_filters( 'pigeonpack_default_field_types', $defaults );
		
	}

}

if ( !function_exists( 'save_pigeonpack_list_meta' ) ) {
			
	/**
	 * Called by save_post action
	 *
	 * Verifies we're working with a pigeonpack_list
	 * Deletes and adds subscribers
	 * Deletes and adds list fields\
	 *
	 * jQuery is used to provide feedback for whether required fields are set but
	 * this function will not add a subscriber unless all the required fields 
	 * are being used. No real feedback without jQuery due to how WordPress saves
	 * posts.
	 *
	 * @since 0.0.1
	 *
	 * @param int $list_id WordPress post ID
	 */		
	function save_pigeonpack_list_meta( $list_id ) {
		
		if ( isset( $_REQUEST['post_type'] ) && 'pigeonpack_list' !== $_REQUEST['post_type'] )
			return;
			
		if ( !current_user_can( 'edit_list', $list_id ) )
			return;
			
		if ( !isset( $_REQUEST['pigeonpack_list_nonce'] ) || !wp_verify_nonce( $_REQUEST['pigeonpack_list_nonce'], 'update_pigeonpack_list' ) )
			return;
			
		if ( isset( $_REQUEST['pigeonpack_list_delete'] ) )
			delete_pigeonpack_subscribers( $list_id, $_REQUEST['pigeonpack_list_delete'] );
			
		if ( isset( $_REQUEST['pigeonpack_allow_user_format'] ) )
			update_post_meta( $list_id, '_pigeonpack_allow_user_format', $_REQUEST['pigeonpack_allow_user_format'] );
			
		// Begin Required Footer Email Content //
		if ( isset( $_REQUEST['pigeonpack_footer_company'] ) )
			$required_footer['company'] = $_REQUEST['pigeonpack_footer_company'];
			
		if ( isset( $_REQUEST['pigeonpack_footer_address'] ) )
			$required_footer['address'] = $_REQUEST['pigeonpack_footer_address'];
			
		if ( isset( $_REQUEST['pigeonpack_footer_reminder'] ) )
			$required_footer['reminder'] = $_REQUEST['pigeonpack_footer_reminder'];
		
		update_post_meta( $list_id, '_pigeonpack_required_footer_settings', $required_footer );	
		
		unset( $required_footer );
		// End Required Footer Email Content //
							
		// Begin Double Opt-in Settings //
		if ( isset( $_REQUEST['pigeonpack_double_optin_enabled'] ) )
			$double_optin['enabled'] = $_REQUEST['pigeonpack_double_optin_enabled'];
		else
			$double_optin['enabled'] = 'off'; //checkboxes aren't set if they aren't checked...
								
		if ( isset( $_REQUEST['pigeonpack_double_optin_from_name'] ) )
			$double_optin['from_name'] = $_REQUEST['pigeonpack_double_optin_from_name'];
								
		if ( isset( $_REQUEST['pigeonpack_double_optin_from_email'] ) )
			$double_optin['from_email'] = $_REQUEST['pigeonpack_double_optin_from_email'];
								
		if ( isset( $_REQUEST['pigeonpack_double_optin_subject'] ) )
			$double_optin['subject'] = $_REQUEST['pigeonpack_double_optin_subject'];
			
		if ( isset( $_REQUEST['pigeonpack_double_optin_message'] ) )
			$double_optin['message'] = $_REQUEST['pigeonpack_double_optin_message'];
			
		update_post_meta( $list_id, '_pigeonpack_double_optin_settings', $double_optin );	
			
		unset( $double_optin );
		// End Double Opt-in Settings //
		
		// Begin Subscriber Fields //
		$new_fields		= array();
		$fieldlabels 	= isset( $_REQUEST['pigeonpack_field_label'] ) 			? $_REQUEST['pigeonpack_field_label'] 		: array() ;
		$types 			= isset( $_REQUEST['pigeonpack_field_type'] ) 			? $_REQUEST['pigeonpack_field_type'] 		: array() ;
		$requires 		= isset( $_REQUEST['pigeonpack_field_require'] ) 		? $_REQUEST['pigeonpack_field_require'] 	: array() ;
		$merges 		= isset( $_REQUEST['pigeonpack_field_merge'] ) 			? $_REQUEST['pigeonpack_field_merge'] 		: array() ;
		$static_merges 	= isset( $_REQUEST['pigeonpack_field_static_merge'] )	? $_REQUEST['pigeonpack_field_static_merge'] : array() ;
		$nodeletes 		= isset( $_REQUEST['pigeonpack_field_nodelete'] ) 		? $_REQUEST['pigeonpack_field_nodelete'] 	: array() ;
		$choices 		= isset( $_REQUEST['pigeonpack_field_choice'] ) 		? $_REQUEST['pigeonpack_field_choice'] 		: array() ;
		
		foreach( $fieldlabels as $key => $value ) {
			
			$field = array(
						'label'			=> $value,
						'type'			=> $types[$key],
						'require'		=> isset( $requires[$key] ) ? $requires[$key] : '',
						'merge'			=> strtoupper( $merges[$key] ),
						'static_merge'	=> $static_merges[$key],
						'nodelete'		=> isset( $nodeletes[$key] ) ? $nodeletes[$key] : '',
						);
						
			$last_merge_id = $static_merges[$key];
		
			if ( in_array( $types[$key], array( 'radio button', 'drop down' ) ) )
				$field['choices'] = $choices[$key];
			
			if ( !empty( $field['require'] ) )
				$required_fields[] = $field;
				
			$new_fields[] = $field;
			
		}
		
		update_post_meta( $list_id, '_pigeonpack_list_fields', $new_fields );
		// End Subcriber Fields //
		
		if ( isset( $last_merge_id ) )
			update_post_meta( $list_id, '_last_merge_id', $last_merge_id );
		
		// Begin New Subscriber //
		$subscriber_meta = array();
		
		foreach ( $required_fields as $field ) {
			
			$merge = $field['static_merge'];
		
			if ( 'address' !== $field['type'] ) {
				
				if ( !isset( $_REQUEST['M' . $merge] ) || empty( $_REQUEST['M' . $merge] ) )
					return;
				
				$subscriber_meta['M' . $merge] = $_REQUEST['M' . $merge];
					
			} else {
			
				if ( !isset( $_REQUEST['M' . $merge . '-addr1'] ) || empty( $_REQUEST['M' . $merge . '-addr1'] ) )
					return;
			
				if ( !isset( $_REQUEST['M' . $merge . '-city'] ) || empty( $_REQUEST['M' . $merge . '-city'] ) )
					return;
			
				if ( !isset( $_REQUEST['M' . $merge . '-state'] ) || empty( $_REQUEST['M' . $merge . '-state'] ) )
					return;
			
				if ( !isset( $_REQUEST['M' . $merge . '-zip'] ) || empty( $_REQUEST['M' . $merge . '-zip'] ) )
					return;	
					
				$subscriber_meta['M' . $merge . '-addr1'] 	= $_REQUEST['M' . $merge . '-addr1'];
				$subscriber_meta['M' . $merge . '-city'] 	= $_REQUEST['M' . $merge . '-city'];
				$subscriber_meta['M' . $merge . '-state'] 	= $_REQUEST['M' . $merge . '-state'];
				$subscriber_meta['M' . $merge . '-zip'] 	= $_REQUEST['M' . $merge . '-zip'];
				
				if ( isset( $_REQUEST['M' . $merge . '-addr2'] ) && !empty( $_REQUEST['M' . $merge . '-addr2'] ) )
					$subscriber_meta['M' . $merge . '-addr2'] 	= $_REQUEST['M' . $merge . '-addr2'];
					
				if ( isset( $_REQUEST['M' . $merge . '-country'] ) && !empty( $_REQUEST['M' . $merge . '-country'] ) )
					$subscriber_meta['M' . $merge . '-country'] = $_REQUEST['M' . $merge . '-country'];
				
			}
			
		}
		
		foreach ( $new_fields as $field ) {
		
			$merge = $field['static_merge'];
			
			if ( isset( $subscriber_meta['M' . $merge] ) ) //skip any required fields that have already been set
				continue;
		
			if ( 'address' !== $field['type'] ) {
				
				if ( isset( $_REQUEST['M' . $merge] ) && !empty( $_REQUEST['M' . $merge] ) )
					$subscriber_meta['M' . $merge] = $_REQUEST['M' . $merge];
					
			} else {
			
				if ( isset( $_REQUEST['M' . $merge . '-addr1'] ) && !empty( $_REQUEST['M' . $merge . '-addr1'] ) )
					$subscriber_meta['M' . $merge . '-addr1'] = $_REQUEST['M' . $merge . '-addr1'];
			
				if ( isset( $_REQUEST['M' . $merge . '-city'] ) && !empty( $_REQUEST['M' . $merge . '-city'] ) )
					$subscriber_meta['M' . $merge . '-city'] = $_REQUEST['M' . $merge . '-city'];
			
				if ( isset( $_REQUEST['M' . $merge . '-state'] ) && !empty( $_REQUEST['M' . $merge . '-state'] ) )
					$subscriber_meta['M' . $merge . '-state'] = $_REQUEST['M' . $merge . '-state'];
			
				if ( isset( $_REQUEST['M' . $merge . '-zip'] ) && !empty( $_REQUEST['M' . $merge . '-zip'] ) )
					$subscriber_meta['M' . $merge . '-zip'] = $_REQUEST['M' . $merge . '-zip'];
				
				if ( isset( $_REQUEST['M' . $merge . '-addr2'] ) && !empty( $_REQUEST['M' . $merge . '-addr2'] ) )
					$subscriber_meta['M' . $merge . '-addr2'] = $_REQUEST['M' . $merge . '-addr2'];
					
				if ( isset( $_REQUEST['M' . $merge . '-country'] ) && !empty( $_REQUEST['M' . $merge . '-country'] ) )
					$subscriber_meta['M' . $merge . '-country'] = $_REQUEST['M' . $merge . '-country'];
				
			}
			
		}
		
		if ( !empty( $subscriber_meta ) )
			add_pigeonpack_subscriber( $list_id, $subscriber_meta, 'subscribed' );
		// End New Susbcriber //
	
	}
	add_action( 'save_post', 'save_pigeonpack_list_meta' );

}

if ( !function_exists( 'get_pigeonpack_subscribers' ) ) {
		
	/**
	 * Returns list of Pigeon Pack subscribers
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $list_id Pigeon Pack list ID (e.g. WordPress post ID)
	 * @param int $limit optional To limit the number of subscribers returned
	 * @param int $limit optional To offset the number of subscribers polled
	 */					
	function get_pigeonpack_subscribers( $list_id, $limit = '', $offset = 0, $status = '' ) {
	
		global $wpdb;
		
		$where = 'WHERE list_id = %d';
		
		if ( !empty( $status ) )
			$where .= ' AND subscriber_status = "' . $status . '"';
			
		$query = 'SELECT * FROM ' . $wpdb->prefix . 'pigeonpack_subscribers ' . $where;
		
		if ( !empty( $limit ) )
			$query .= ' LIMIT ' . $offset . ',' . $limit;
	
		return $wpdb->get_results( $wpdb->prepare( $query, $list_id ), ARRAY_A );
		
	}

}

if ( !function_exists( 'get_pigeonpack_subscriber_count' ) ) {
					
	/**
	 * Returns count of Pigeon Pack subscribers
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $list_id Pigeon Pack list ID (e.g. WordPress post ID)
	 * @return int Value of current subscribers for given list ID
	 */				
	function get_pigeonpack_subscriber_count( $list_id ) {
	
		global $wpdb;
		
		$where = 'WHERE list_id = %d';
		$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'pigeonpack_subscribers ' . $where;
		
		return $wpdb->get_var( $wpdb->prepare( $query, $list_id ) );
		
	}

}

if ( !function_exists( 'get_pigeonpack_subscriber' ) ) {
	
	/**
	 * Returns specific Pigeon Pack subscriber by subscriber ID
	 *
	 * Used in AJAX call to edit/update existing subscribers
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $subscriber_id Pigeon Pack subscriber ID
	 * @return array Associative array of subscriber for given subscriber ID
	 */				
	function get_pigeonpack_subscriber( $subscriber_id ) {
	
		global $wpdb;
	
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE id = %d', $subscriber_id ), ARRAY_A );
		
	}

}

if ( !function_exists( 'get_pigeonpack_subscriber_by_list_id_and_hash' ) ) {
	
	/**
	 * Returns specific Pigeon Pack subscriber by list id and hash
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $list_id Pigeon Pack list ID
	 * @param string|md5 $subscriber_hash Subscriber's unique hash from double opt-in email
	 * @return array Associative array of subscriber for given subscriber ID
	 */				
	function get_pigeonpack_subscriber_by_list_id_and_hash( $list_id, $subscriber_hash ) {
	
		global $wpdb;
	
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE list_id = %d and subscriber_hash = %s', $list_id, $subscriber_hash ), ARRAY_A );
		
	}

}
if ( !function_exists( 'get_pigeonpack_wordpress_subscriber_by_hash' ) ) {
	
	/**
	 * Returns specific Pigeon Pack subscriber by hash
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param string|md5 $subscriber_hash Subscriber's unique hash from double opt-in email
	 * @return mixed Associative array of subscriber for given subscriber ID
	 */				
	function get_pigeonpack_wordpress_subscriber_by_hash( $subscriber_hash ) {
	
		global $wpdb;
	
		$user_id = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key = %s AND meta_value = %s', '_pigeonpack_subscriber_hash', $subscriber_hash ) ); //get the user id
		
		if ( !$user_id )
			return false;
			
		$user = get_userdata( $user_id );
			
		$subscriber = array(
						'user_id'			=> $user_id,
						'email' 			=> $user->user_email,
						'subscriber_status' => ( 'on' === get_user_meta( $user_id, '_pigeonpack_subscription', true ) ) ? 'subscribed' : 'unsubscribed',
						);
						
		return $subscriber;
		
	}

}

if ( !function_exists( 'add_pigeonpack_subscriber' ) ) {
		
	/**
	 * Adds new subscriber to list
	 *
	 * Also used in AJAX call to add new subscribers
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $list_id Pigeon Pack list ID (e.g. WordPress post ID)
	 * @param array $subscriber_meta Associative array of subcriber values
	 * @param string $status pending, subscribed, unsubscribed, or bounced
	 * @return array|bool Associated array of new subscriber, message, and double optin setting or FALSE if failed
	 */		
	function add_pigeonpack_subscriber( $list_id, $subscriber_meta, $status = 'pending', $format = 'html' ) {
	
		global $wpdb;
		
		$message = '';
		$new_subscriber = true;
		
		if ( 'pending' === $status ) {
			
			$double_optin = get_post_meta( $list_id, '_pigeonpack_double_optin_settings', true );
				
			if ( isset( $double_optin['enabled'] ) && 'on' === $double_optin['enabled'] ) {
				
				$double_optin = true;
			
			} else {
				
				$status = 'subscribed'; //not double opt-in, default is 'subscribed'
				$double_optin = false;
				
			}
		
		}
	
		if ( !$list_id = absint( $list_id )  )
			return false;
			
		$result = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE list_id = %d AND email = %s', $list_id, $subscriber_meta['M0'] ), ARRAY_A ); //M0 (aka MERGE0) should ALWAYS be email
			
		if ( !empty( $result ) ) { //Do not add duplicate subscribers
		
			$subscriber_id = update_pigeonpack_subscriber( $list_id, $result['id'], $subscriber_meta, $status ); //Update
			$new_subscriber = false;
			
		}
		
		if ( $new_subscriber ) {
			
			if ( isset( $subscriber_meta['pigeonpack_email_format'] ) ) {
				
				$format = $subscriber_meta['pigeonpack_email_format'];
				unset( $subscriber_meta['pigeonpack_email_format'] ); // We don't want to include this setting in the subscriber_meta column
				
			}
			
			$new_subscriber = array(
								'list_id'				=> $list_id,
								'email'					=> $subscriber_meta['M0'], //M0 (aka MERGE0) should ALWAYS be email
								'subscriber_meta'		=> maybe_serialize( $subscriber_meta ),
								'subscriber_added'		=> date_i18n( 'Y-m-d H:i:s' ),
								'subscriber_modified'	=> date_i18n( 'Y-m-d H:i:s' ),
								'subscriber_status'		=> $status,
								'subscriber_hash'		=> pigeonpack_hash( $subscriber_meta['M0'] ), //Hash the email address
								'message_preference'	=> $format,
								);
			
			$return = $wpdb->insert( $wpdb->prefix . 'pigeonpack_subscribers', $new_subscriber );
			
			if ( $return )
				$subscriber_id = $wpdb->insert_id;
			
		}
		
		if ( $subscriber_id && $double_optin ) {
			
			pigeonpack_double_optin_scheduler( $list_id, $subscriber_id );
			
			$message = '<h3>' . __( 'Almost finished...' , 'pigeonpack' ) . '</h3>';
			$message .= '<p>' . __( 'We need to confirm your email address.' , 'pigeonpack' ) . '</p>';
			$message .= '<p>' . __( 'To complete the subscription process, please click the link in the email we just sent you.' , 'pigeonpack' ) . '</p>';
			$message = apply_filters( 'double_optin_almost_message', $message );
			
		} else if ( $subscriber_id ) {
		
			$message = '<h3>' . __( 'Subscription Confirmed', 'pigeonpack' ) . '</h3>';
			$message .= '<p>' . __( 'Your subscription to our list has been confirmed.', 'pigeonpack' ) . '</p>';
			$message .= '<p>' . __( 'Thank you for subscribing!', 'pigeonpack' ) . '</p>';
			$message = apply_filters( 'new_subscriber_success_message', $message );
			
		} else {
		
			$message = '<h3>' . __( 'Error Processing Subscription', 'pigeonpack' ) . '</h3>';
			$message .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
			$message .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
			$message = apply_filters( 'new_subscriber_error_message', $message );
			
		}
		
		if ( $subscriber_id )
			return array( $subscriber_id, $message, $double_optin );
		
		return false;
		
	}

}

if ( !function_exists( 'update_pigeonpack_subscriber' ) ) {
			
	/**
	 * Updates existing subscriber
	 *
	 * Also used in AJAX call to edit/update existing subscribers
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $list_id Pigeon Pack list ID (e.g. WordPress post ID)
	 * @param int $subscriber_id Existing subscriber ID
	 * @param array $subscriber_meta Associative array of new subscriber values
	 * @param string $status Optional string to update susbcribers subscription status
	 * @return int|bool Subscriber ID or FALSE if failed
	 */		
	function update_pigeonpack_subscriber( $list_id, $subscriber_id, $subscriber_meta, $status = false, $format = 'html' ) {
	
		global $wpdb;
	
		if ( !$list_id = absint( $list_id )  )
			return false;
			
		$result = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE list_id = %d AND id = %d', $list_id, $subscriber_id ), ARRAY_A );
			
		if ( empty( $result ) ) //subscriber doesn't exist
			return false;
			
		if ( isset( $subscriber_meta['pigeonpack_email_format'] ) ) {
			
			$format = $subscriber_meta['pigeonpack_email_format'];
			unset( $subscriber_meta['pigeonpack_email_format'] ); // We don't want to include this setting in the subscriber_meta column
			
		}
			
		$subscriber_meta = wp_parse_args( $subscriber_meta, maybe_unserialize( $result['subscriber_meta'] ) );
		
		$update_subscriber = array(
								'email'					=> $subscriber_meta['M0'], //M0 (aka MERGE0) should ALWAYS be email
								'subscriber_meta'		=> maybe_serialize( $subscriber_meta ),
								'subscriber_modified'	=> date_i18n( 'Y-m-d H:i:s' ),
								'subscriber_status'		=> $status ? $status : $result['subscriber_status'], //only update if $status is not false otehrwise use current setting
								'message_preference'	=> $format,
							);
		
		$return = $wpdb->update( $wpdb->prefix . 'pigeonpack_subscribers', $update_subscriber, array( 'id' => $subscriber_id ) );
					
		if ( $return )
			return $subscriber_id;
		else
			return false;
		
	}

}

if ( !function_exists( 'update_pigeonpack_subscriber_hash' ) ) {
			
	/**
	 * Updates existing subscriber's hash
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $subscriber_id Existing subscriber ID
	 * @param string $subscriber_hash Susbcriber's hash
	 * @return int|bool Subscriber ID or FALSE if failed
	 */		
	function update_pigeonpack_subscriber_hash( $subscriber_id, $subscriber_hash ) {
	
		global $wpdb;
	
		if ( !$subscriber_id = absint( $subscriber_id )  )
			return false;
			
		$result = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE id = %d', $subscriber_id ), ARRAY_A );
			
		if ( empty( $result ) ) //subscriber doesn't exist
			return false;
		
		$update_subscriber = array(
								'subscriber_modified'	=> date_i18n( 'Y-m-d H:i:s' ),
								'subscriber_hash'		=> $subscriber_hash,
							);
		
		$return = $wpdb->update( $wpdb->prefix . 'pigeonpack_subscribers', $update_subscriber, array( 'id' => $subscriber_id ) );
					
		if ( $return )
			return $subscriber_id;
		else
			return false;
		
	}

}

if ( !function_exists( 'delete_pigeonpack_subscribers' ) ) {
				
	/**
	 * Delete existing subscribers
	 *
	 * Also used in AJAX call to delete existing subscribers
	 *
	 * @since 0.0.1
	 * @uses $wpdb WordPress datbase API
	 * @see http://codex.wordpress.org/Class_Reference/wpdb
	 *
	 * @param int $list_id Pigeon Pack list ID (e.g. WordPress post ID)
	 * @param array $subscriber_ids Existing subscriber ID
	 * @return int|bool Number of rows affected or FALSE if failure
	 */	
	function delete_pigeonpack_subscribers( $list_id, $subscriber_ids ) {
	
		global $wpdb;
	
		if ( !is_array( $subscriber_ids ) || !( $list_id = absint( $list_id ) )  )
			return false;
		
		return $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE id IN (' . "'" . implode( "','", $subscriber_ids ) . "'" . ') AND list_id = %d', $list_id ) );
		
	}

}

if ( !function_exists( 'subscriber_row' ) ) {
					
	/**
	 * Output table row of subscriber data
	 *
	 * Also used in AJAX call
	 *
	 * @since 0.0.1
	 *
	 * @param int $subscriber Pigeon Pack subscriber ID
	 * @param array $list_fields Current list fields
	 * @return string Table row for subscriber
	 */	
	function subscriber_row( $subscriber, $list_fields ) {
	
		$output = '';
		
		if ( !empty( $subscriber ) ) {
	
			$subscriber_meta = maybe_unserialize( $subscriber['subscriber_meta'] );
		
			$output .= '<td><input type="checkbox" name="pigeonpack_list_delete[]" value="' . $subscriber['id'] . '" /></td>';
			$output .= '<td><input type="button" name="edit_subscriber" subscriber_id="' . $subscriber['id'] . '" class="edit-subscriber button button-primary button-small" value="' . __( 'Edit', 'pigeonpack' ) . '" /></td>';
			
			foreach( $list_fields as $list_field ) {
				
				$merge = $list_field['static_merge'];
				
				if ( 'address' !== $list_field['type'] ) {
					
					$output .= '<td>' . ( isset( $subscriber_meta['M' . $merge] ) ? $subscriber_meta['M' . $merge] : '&nbsp;' ) . '</td>';
					
				} else {
				
					$output .= '<td>';
					$output .= ( ( isset( $subscriber_meta['M' . $merge . '-addr1'] ) 
									&& !empty( $subscriber_meta['M' . $merge . '-addr1'] ) ) ? $subscriber_meta['M' . $merge. '-addr1'] . '<br />' : '' );
					$output .= ( ( isset( $subscriber_meta['M' . $merge . '-addr2'] )
									&& !empty( $subscriber_meta['M' . $merge . '-addr2'] ) ) ? $subscriber_meta['M' . $merge. '-addr2'] . '<br />' : '' );
					$output .= ( ( isset( $subscriber_meta['M' . $merge . '-city'] )
									&& !empty( $subscriber_meta['M' . $merge . '-city'] ) ) ? $subscriber_meta['M' . $merge. '-city'] . ', ' : '' );
					$output .= ( ( isset( $subscriber_meta['M' . $merge . '-state'] )
									&& !empty( $subscriber_meta['M' . $merge . '-state'] ) ) ? $subscriber_meta['M' . $merge. '-state'] . ' ' : '' );
					$output .= ( ( isset( $subscriber_meta['M' . $merge . '-zip'] )
									&& !empty( $subscriber_meta['M' . $merge . '-zip'] ) ) ? $subscriber_meta['M' . $merge. '-zip'] . '<br />' : '' );
					$output .= ( ( isset( $subscriber_meta['M' . $merge . '-country'] )
									&& !empty( $subscriber_meta['M' . $merge . '-country'] ) ) ? $subscriber_meta['M' . $merge. '-country'] : '' );
					$output .= '</td>';
					
				}
				
			}
			
			$output .= '<td>' . $subscriber['subscriber_added'] . '</td>';
			$output .= '<td>' . $subscriber['subscriber_modified'] . '</td>';
			$output .= '<td>' . $subscriber['subscriber_status'] . '</td>';
		
		}
		
		return $output;
		
	}

}

if ( !function_exists( 'wp_ajax_add_pigeonpack_subscriber' ) ) {
	
	/**
	 * Called by 'wp_ajax_add_pigeonpack_subscriber' action hook AJAX to add new subscriber
	 *
	 * @todo If user exists and tries to subscribe again but is "pending" and double opt-in is enabled, resend opt-in email
	 *
	 * @since 0.0.1
	 */	
	function wp_ajax_add_pigeonpack_subscriber() {
		
		check_ajax_referer( 'update_pigeonpack_list' );
		
		$new_subscriber = array();
		
		foreach ( $_REQUEST['data'] as $data ) {
		
			$subscriber_meta[$data['name']] = $data['value'];
			
		}
		
		if ( isset( $_REQUEST['subscriber_status'] ) )
			$status = $_REQUEST['subscriber_status'];
		else
			$status = 'pending';
		
		list( $subscriber_id, $message, $double_optin ) = add_pigeonpack_subscriber( $_REQUEST['list_id'], $subscriber_meta, $status );
		
		$list_fields = get_pigeonpack_list_fields( $_REQUEST['list_id'] );
		
		die( json_encode( array( $subscriber_id, subscriber_row( get_pigeonpack_subscriber( $subscriber_id ), $list_fields ), $double_optin, $message ) ) );
		
	}
	add_action( 'wp_ajax_add_pigeonpack_subscriber', 'wp_ajax_add_pigeonpack_subscriber' );
	add_action( 'wp_ajax_nopriv_add_pigeonpack_subscriber', 'wp_ajax_add_pigeonpack_subscriber');

}

if ( !function_exists( 'wp_ajax_edit_pigeonpack_subscriber' ) ) {
		
	/**
	 * Called by 'wp_ajax_edit_pigeonpack_subscriber' action hook AJAX to edit existing subscriber
	 *
	 * @since 0.0.1
	 */	
	function wp_ajax_edit_pigeonpack_subscriber() {
		
		check_ajax_referer( 'update_pigeonpack_list' );
		
		if ( isset( $_REQUEST['subscriber_id'] ) && $subscriber_id = absint( $_REQUEST['subscriber_id'] ) )
			$subscriber = get_pigeonpack_subscriber( $subscriber_id );
		else
			die( false );
		
		die( json_encode( array_merge( maybe_unserialize( $subscriber['subscriber_meta'] ), array( 'email_format' => $subscriber['message_preference'] ) ) ) );
		
	}
	add_action( 'wp_ajax_edit_pigeonpack_subscriber', 'wp_ajax_edit_pigeonpack_subscriber' );

}

if ( !function_exists( 'wp_ajax_update_pigeonpack_subscriber' ) ) {
	
	/**
	 * Called by 'wp_ajax_update_pigeonpack_subscriber' action hook AJAX to update existing subscriber
	 *
	 * @since 0.0.1
	 */	
	function wp_ajax_update_pigeonpack_subscriber() {
		
		check_ajax_referer( 'update_pigeonpack_list' );
		
		if ( !isset( $_REQUEST['subscriber_id'] ) || !$subscriber_id = absint( $_REQUEST['subscriber_id'] ) )
			die();
			
		if ( !isset( $_REQUEST['list_id'] ) || !$list_id = absint( $_REQUEST['list_id'] ) )
			die();
		
		$subscriber_meta = array();
		
		foreach ( $_REQUEST['data'] as $data ) {
		
			$subscriber_meta[$data['name']] = $data['value'];
			
		}
		
		$updated_subscriber = update_pigeonpack_subscriber( $list_id, $subscriber_id, $subscriber_meta );
		
		$list_fields = get_pigeonpack_list_fields( $list_id );
		
		die( subscriber_row( get_pigeonpack_subscriber( $updated_subscriber ), $list_fields ) );
		
	}
	add_action( 'wp_ajax_update_pigeonpack_subscriber', 'wp_ajax_update_pigeonpack_subscriber' );

}

if ( !function_exists( 'wp_ajax_delete_pigeonpack_subscribers' ) ) {
		
	/**
	 * Called by 'wp_ajax_delete_pigeonpack_subscribers' action hook AJAX to delete existing subscriber(s)
	 *
	 * @since 0.0.1
	 */
	function wp_ajax_delete_pigeonpack_subscribers() {
		
		check_ajax_referer( 'update_pigeonpack_list' );
		
		if ( isset( $_REQUEST['subscriber_ids'] ) && isset( $_REQUEST['list_id'] ) )
			$affected_rows = delete_pigeonpack_subscribers( $_REQUEST['list_id'], $_REQUEST['subscriber_ids'] );
		
		die( strval( $affected_rows ) );
		
	}
	add_action( 'wp_ajax_delete_pigeonpack_subscribers', 'wp_ajax_delete_pigeonpack_subscribers' );

}

if ( !function_exists( 'delete_subscribers_after_delete_post' ) ) {
		
	/**
	 * Called by 'after_delete_post' action hook to delete subcribers from table if list is deleted in WordPress UI
	 *
	 * @since 0.0.1
	 */
	function delete_subscribers_after_delete_post( $list_id ) {
		
		global $wpdb;
	
		if ( !( $list_id = absint( $list_id ) )  )
			return false;
		
		return $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'pigeonpack_subscribers WHERE list_id = %d', $list_id ) );
		
	}
	add_action( 'after_delete_post', 'delete_subscribers_after_delete_post' );

}