<?php
/**
 * Registers Pigeon Pack "campaign" post type in WordPress and related functions
 *
 * @package Pigeon Pack
 * @since 0.0.1
 */

if ( !function_exists( 'create_campaign_post_type' ) ) {
		
	/**
	 * Creates Pigeon Pack campaign post type
	 *
	 * Called on 'init' action hook
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 *
	 * @since 0.0.1
	 * @uses register_post_type() to register campaign post type
	 */
	function create_campaign_post_type()  {
		
		$labels = array(    
			'name' 					=> __( 'Pigeon Pack Campaigns', 'pigeonpack' ),
			'singular_name' 		=> __( 'Campaign', 'pigeonpack' ),
			'add_new' 				=> __( 'Add New Campaign', 'pigeonpack' ),
			'add_new_item' 			=> __( 'Add New Campaign', 'pigeonpack' ),
			'edit_item' 			=> __( 'Edit Campaign', 'pigeonpack' ),
			'new_item' 				=> __( 'New Campaign', 'pigeonpack' ),
			'view_item' 			=> __( 'View Campaign', 'pigeonpack' ),
			'search_items' 			=> __( 'Search Campaigns', 'pigeonpack' ),
			'not_found' 			=> __( 'No campaigns found', 'pigeonpack' ),
			'not_found_in_trash' 	=> __( 'No campaigns found in trash', 'pigeonpack' ), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __( 'Campaigns', 'pigeonpack' )
		);
		
		$args = array(
			'label' 				=> 'campaign',
			'labels' 				=> $labels,
			'description' 			=> __( 'Pigeon Pack Campaigns', 'pigeonpack' ),
			'public'				=> true,
			'publicly_queryable' 	=> false,
			'exclude_fromsearch' 	=> true,
			'show_ui' 				=> true,
			'show_in_nav_menus'		=> false,
			'show_in_menu' 			=> 'pigeon-pack', //include in the pigeon-pack menu, not it's own menu
			'menu_position'			=> 100, //below second separator 
			'capability_type' 		=> array( 'pigeonpack_campaign', 'pigeonpack_campaigns' ),
			'map_meta_cap' 			=> true,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title', 'editor', 'revisions' ),
			'register_meta_box_cb' 	=> 'add_pigeonpack_campaigns_metaboxes',
			'has_archive' 			=> true,
			'rewrite' 				=> array( 'slug' => 'campaign' ),
			'menu_icon'				=> PIGEON_PACK_PLUGIN_URL . '/images/campaigns-16x16.png',
			);
	
		register_post_type( 'pigeonpack_campaign', $args );
		
	}
	add_action( 'init', 'create_campaign_post_type' );
	
}

if ( !function_exists( 'pigeonpack_campaign_columns' ) ) {
		
	/**
	 * Called by 'manage_edit-pigeonpack_campaign_columns' filter for setting custom columns
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'pigeonpack_human_readable_campaign_type' for future addons
	 *
	 * @param array $columns Current columns
	 * @return array Modified columns
	 */	
	function pigeonpack_campaign_columns( $columns ) {
		
		$columns['campaign_type'] = __( 'Campaign Type', 'pigeonpack' );
		$columns['recipients'] = __( 'Recipients', 'pigeonpack' );
	
		return $columns;
		
	}
	add_filter( 'manage_edit-pigeonpack_campaign_columns', 'pigeonpack_campaign_columns', 10, 1 );

}

if ( !function_exists( 'manage_pigeonpack_campaign_posts_custom_column' ) ) {
		
	/**
	 * Called by 'manage_pigeonpack_campaign_posts_custom_column' filter for displaying custom column data
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'pigeonpack_human_readable_campaign_type' for future addons
	 *
	 * @param string $column_name Column name from 'manage_edit-pigeonpack_campaign_columns' filter
	 * @param string $post_id Post ID
	 */	
	function manage_pigeonpack_campaign_posts_custom_column( $column_name, $post_id ) {
		
		switch( $column_name ) {
		
			case 'campaign_type':
				echo pigeonpack_human_readable_campaign_type( get_post_meta( $post_id, '_pigeonpack_campaign_type', true ) );
				break;
			
			case 'recipients':
				echo pigeonpack_human_readable_recipients( get_post_meta( $post_id, '_pigeonpack_recipients', true ) );
				break;
			
		}
	
	}
	add_filter( 'manage_pigeonpack_campaign_posts_custom_column', 'manage_pigeonpack_campaign_posts_custom_column', 10, 2 );
	
}

if ( !function_exists( 'pigeonpack_human_readable_campaign_type' ) ) {
		
	/**
	 * Returns the human readable campaign type for display in the campaign custom columns
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'pigeonpack_human_readable_campaign_type' for future addons
	 *
	 * @param string $campaign_type Campaign type
	 * @return string Human readable version of the campaign type
	 */	
	function pigeonpack_human_readable_campaign_type( $campaign_type ) {
	
		switch( $campaign_type ) {
		
			case( 'single_campaign' ) :
				return __( 'Single Campaign', 'pigeonpack' );
				
			case( 'wp_post' ) :
				return __( 'WordPress Post', 'pigeonpack' );
			
		}
		
		do_action( 'pigeonpack_human_readable_campaign_type', $campaign_type );
		
	}

}

if ( !function_exists( 'pigeonpack_human_readable_recipients' ) ) {
		
	/**
	 * Returns the role name or list title for display in the campaign custom columns
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'pigeonpack_human_readable_recipients' for future addons
	 *
	 * @param string $recipients Recipient type Role or List
	 * @return string Human readable version of the recipient
	 */	
	function pigeonpack_human_readable_recipients( $recipients ) {
	
		if ( 'R' === substr( $recipients, 0, 1 ) ) {
			
			return substr( $recipients, 1 );
			
		} else if ( 'L' === substr( $recipients, 0, 1 ) ) {
			
			return get_the_title( substr( $recipients, 1 ) );
			
		}
		
		do_action( 'pigeonpack_human_readable_recipients', $recipients );
		
	}

}

if ( !function_exists( 'add_pigeonpack_campaigns_metaboxes' ) ) {
		
	/**
	 * Called by 'add_campaign_metaboxes' hook from register_meta_box_cb during register post
	 *
	 * Adds metabox for campaign
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'add_pigeonpack_campaigns_metaboxes' for future addons
	 */	
	function add_pigeonpack_campaigns_metaboxes() {
		
		add_meta_box( 'pigeonpack_campaign_meta_box', __( 'Pigeon Pack Campaign Options', 'pigeonpack' ), 'pigeonpack_campaign_meta_box', 'pigeonpack_campaign', 'normal', 'high' );
		
		do_action( 'add_pigeonpack_campaigns_metaboxes' );
		
	}

}

if ( !function_exists( 'pigeonpack_campaign_meta_box' ) ) {
		
	/**
	 * Called by add_meta_box function call
	 *
	 * Outputs metabox for campaign
	 *
	 * @since 0.0.1
	 *
	 * @param object $post WordPress post object
	 */			
	function pigeonpack_campaign_meta_box( $post ) {
		
		/**
		 * WordPress time format
		 */
		$timeformat = get_option( 'time_format' );
		
		$pigeonpack_settings = get_pigeonpack_settings();
		
		$campaign_type 	= get_post_meta( $post->ID, '_pigeonpack_campaign_type', true );
		$recipients 	= get_post_meta( $post->ID, '_pigeonpack_recipients', true );
		$wp_post_type 	= get_post_meta( $post->ID, '_pigeonpack_wp_post_type', true );
		$clude			= ( $var = get_post_meta( $post->ID, '_pigeonpack_clude_cat', true ) ) ? $var : 'in';
		$clude_cats		= ( $var = get_post_meta( $post->ID, '_pigeonpack_clude_cats', true ) ) ? $var : array( 0 );
		$days 			= array( 'sun', 'mon', 'tues', 'wed', 'thurs', 'fri', 'sat' );
		
		$default_wp_post_digest = array(
									'freq'	=> 'daily',
									'day'	=> 0,
									'date'	=> 1,
									'time'	=> date_i18n( 'H:00' ),
									'days'	=> $days,
									);
		$wp_post_digest = get_post_meta( $post->ID, '_pigeonpack_wp_post_digest', true );
		$wp_post_digest = wp_parse_args( $wp_post_digest, $default_wp_post_digest );
		
		$from_name = get_post_meta( $post->ID, '_pigeonpack_from_name', true );
		$from_name = ( $from_name ) ? $from_name : $pigeonpack_settings['from_name'];
		
		$from_email = get_post_meta( $post->ID, '_pigeonpack_from_email', true );
		$from_email = ( $from_email ) ? $from_email : $pigeonpack_settings['from_email'];
		
		?>
		
		<div id="pigeonpack_campaign_options_metabox">
		
			<table id="pigeonpack_campaign_options_table" class="pigeonpack_table">
				
				<tbody>
					
					<tr>
						<th><label for="campaign_type"><?php _e( 'Campaign Type', 'pigeonpack' ); ?></label></th>
						<td>
							<select id="campaign_type" name="campaign_type">
								<option value="single_campaign" <?php selected( 'single_campaign', $campaign_type ); ?> ><?php _e( 'Single Campaign', 'pigeonpack' ); ?></option>
								<option value="wp_post" <?php selected( 'wp_post', $campaign_type ); ?> ><?php _e( 'WordPress Post Campaign', 'pigeonpack' ); ?></option>
							</select>
						</td>
					</tr>
					
					<?php 
					if ( 'wp_post' !== $campaign_type ) 
						$wp_post_hidden = 'style="display: none;"';
					else
						$wp_post_hidden = '';
					?>
					
					<tr class="wp_post_options" <?php echo $wp_post_hidden; ?>>
						<th><label for="wp_post_type"><?php _e( 'WordPress Post Campaign Type', 'pigeonpack' ); ?></label></th>
						<td>
							<select id="wp_post_type" name="wp_post_type">
								<option value="individual" <?php selected( 'individual', $wp_post_type ); ?> ><?php _e( 'Each Post', 'pigeonpack' ); ?></option>
								<option value="digest" <?php selected( 'digest', $wp_post_type ); ?> ><?php _e( 'Digest', 'pigeonpack' ); ?></option>
							</select>
						</td>
					</tr>
					
					<?php 
					if ( 'digest' !== $wp_post_type ) 
						$digest_hidden = 'style="display: none;"';
					else
						$digest_hidden = '';
					?>
					
					<tr class="wp_post_digest_options" <?php echo $digest_hidden; ?>>
						<th><label for="wp_post_digest_frequency"><?php _e( 'WordPress Post Digest Frequency', 'pigeonpack' ); ?></label></th>
						<td>
							<div id="pigeonpack_digest_options">
								
								<select id="wp_post_digest_frequency" name="wp_post_digest_frequency">
									<option value="daily" <?php selected( 'daily', $wp_post_digest['freq'] ); ?> ><?php _e( 'Daily', 'pigeonpack' ); ?></option>
									<option value="weekly" <?php selected( 'weekly', $wp_post_digest['freq'] ); ?> ><?php _e( 'Weekly', 'pigeonpack' ); ?></option>
									<option value="monthly" <?php selected( 'monthly', $wp_post_digest['freq'] ); ?> ><?php _e( 'Monthly', 'pigeonpack' ); ?></option>
								</select>
								
								<?php
								switch ( $wp_post_digest['freq'] ) {
								
									case'monthly':
										$monthly_hidden = '';
										$weekly_hidden = 'style="display: none;"';
										$daily_hidden = 'style="display: none;"';
										break;
										
									case 'weekly':
										$monthly_hidden = 'style="display: none;"';
										$weekly_hidden = '';
										$daily_hidden = 'style="display: none;"';
										break;
										
									case 'daily':
									default:
										$monthly_hidden = 'style="display: none;"';
										$weekly_hidden = 'style="display: none;"';
										$daily_hidden = '';
										break;
									
								}
								?>
								
								<div class="weekly_options" <?php echo $weekly_hidden; ?>>
								&nbsp;<?php _e( 'on', 'pigeonpack' ); ?>&nbsp;
								
								<select id='wp_post_digest_day' name='wp_post_digest_day'>
									<?php
									for ( $i = 0; $i < 7; $i++ ) {
										
										echo '<option value="' . $i . '" ' . selected( $i, $wp_post_digest['day'], false ) . '>' . ucfirst( $days[$i] ) . '</option>';
										
									}
									?>
								</select>
								</div>
								
								<div class="monthly_options" <?php echo $monthly_hidden; ?>>
								&nbsp;<?php _e( 'on the', 'pigeonpack' ); ?>&nbsp;
								
								<select id='wp_post_digest_date' name='wp_post_digest_date'>
									<?php
									for ( $i = 1; $i < 28; $i++ ) {
										
										echo '<option value="' . $i . '" ' . selected( $i, $wp_post_digest['date'], false ) . '>' . ordinal_suffix( $i ) . '</option>';
										
									}
									?>
									<option value="last_day" <?php selected( 'last_day', $wp_post_digest['date'] ); ?> ><?php _e( 'last', 'pigeonpack' ); ?></option>
								</select>
								
								&nbsp;<?php _e( 'day of the month', 'pigeonpack' ); ?>&nbsp;
								</div>
								
								<div class="time_options">
								&nbsp;<?php _e( 'at', 'pigeonpack' ); ?>&nbsp;
								
								<select id='wp_post_digest_time' name='wp_post_digest_time'>
									<?php
									$loop_time = strtotime( '00:00' );
									$end_time = strtotime( '23:00' );
									
									while( $loop_time <= $end_time ) {
										
										$hour = date_i18n( 'H:00', $loop_time );
										
										echo '<option value="' . $hour . '" ' . selected( $hour, $wp_post_digest['time'], false ) . '>' . date_i18n( $timeformat, $loop_time ) . '</option>';
										
										$loop_time += 3600; //+1 hour
										
									}
									?>
								</select>
								</div>
								
								<br />
								
								<div class="wp_post_digest_days" <?php echo $daily_hidden; ?>>
								<?php
								for ( $i = 0; $i < 7; $i++ ) {
				
									echo '<input type="checkbox" id="' . $days[$i] . '" name="wp_post_digest_days[]" value="' . $i . '" ' . checked( in_array( $i, $wp_post_digest['days'] ), true, false ) . '><label for="' . $days[$i] . '"> ' . ucfirst( $days[$i] ) . '</label> &nbsp;';	
					
								}
								?>
								</div>
								
							</div>
							
						</td>
					</tr>
					
					<tr class="clude_cats" <?php echo $wp_post_hidden; ?>>
						<th><label for="clude_cats"><?php _e( 'Allowed Categories', 'pigeonpack' ); ?></label></th>
						<td>
                            <input type='radio' name='clude' id='pigeonpack_include_cat' value="in" <?php checked( 'in', $clude ); ?> /><label for='pigeonpack_include_cat'> <?php _e( 'Include', 'pigeonpack' ); ?></label> &nbsp; &nbsp; <input type='radio' name='clude' id='pigeonpack_exclude_cat' value='ex' <?php checked( 'ex', $clude ); ?> /><label for='pigeonpack_exclude_cat'> <?php _e( 'Exclude', 'pigeonpack' ); ?></label>
                            <br />
                            
							<select id="clude_cats" name="clude_cats[]" multiple="multiple" size="5">
								<option value="0" <?php selected( in_array( '0', $clude_cats ) ); ?>><?php _e( 'All Categories', 'pigeonpack' ); ?></option>
								<?php 
								$categories = get_categories( array( 'hide_empty' => 0, 'orderby' => 'name' ) );
								foreach ( (array)$categories as $category ) {
									?>
									
									<option value="<?php echo $category->term_id; ?>" <?php selected( in_array( $category->term_id, (array)$clude_cats ) ); ?>><?php echo $category->name; ?></option>
				
				
									<?php
								}
								?>
							</select>
                            
                            <p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'To "deselect" hold the SHIFT key on your keyboard while you click the category.', 'pigeonpack' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th><label for="recipients"><?php _e( 'Campaign Recipients', 'pigeonpack' ); ?></label></th>
						<td>
							<select id='recipients' name='recipients'>
								<?php pigeonpack_dropdown_roles( $recipients ); ?>
								<?php pigeonpack_dropdown_lists( $recipients ); ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<th><?php _e( 'From Name', 'pigeonpack' ); ?></th>
						<td><input type="text" name="pigeonpack_from_name" value="<?php echo $from_name; ?>" /></td>
					</tr>
                    
					<tr>
						<th><?php _e( 'Reply-to Email', 'pigeonpack' ); ?></th>
						<td><input type="text" name="pigeonpack_from_email" value="<?php echo $from_email; ?>" /></td>
					</tr>
				
				</tbody>
			
			</table>
			
			<?php wp_nonce_field( plugin_basename( __FILE__ ), 'pigeonpack_edit_nonce' ); ?>
		
		</div>
		
		<?php	
		
	}
	
}

if ( !function_exists( 'save_pigeonpack_campaign_meta' ) ) {
		
	/**
	 * Called by save_post action
	 *
	 * Verifies we're working with a pigeonpack_campaign, parses and saves meta values
	 *
	 * @since 0.0.1
	 * @uses do_action() To call 'pigeonpack_campaign_type_change' for future addons
	 *
	 * @param int $campaign_id WordPress post ID
	 */			
	function save_pigeonpack_campaign_meta( $campaign_id ) {
	
		if ( isset( $_REQUEST['post_type'] ) && 'pigeonpack_campaign' !== $_REQUEST['post_type'] )
			return;
			
		if ( !current_user_can( 'edit_pigeonpack_campaign', $campaign_id ) )
			return;
			
		if ( !isset( $_REQUEST['pigeonpack_edit_nonce'] ) || !wp_verify_nonce( $_REQUEST['pigeonpack_edit_nonce'], plugin_basename( __FILE__ ) ) )
			return;
			
		if ( isset( $_REQUEST['pigeonpack_text_email'] ) && !empty( $_REQUEST['pigeonpack_text_email'] ) )
			update_post_meta( $campaign_id, '_pigeonpack_campaign_text_format', $_REQUEST['pigeonpack_text_email'] );
			
		if ( isset( $_REQUEST['campaign_type'] ) ) {
			
			if ( ( $current_campaign_type = get_post_meta( $campaign_id, '_pigeonpack_campaign_type', true ) )
				&& $current_campaign_type !== $_REQUEST['campaign_type'] ) {

				if ( 'wp_post' === $current_campaign_type )
					remove_pigeonpack_wp_post_campaign( $campaign_id );
				
				// If new campaign types are added, we'll need to extend this functionality with this action
				do_action( 'pigeonpack_campaign_type_change', $campaign_id );
				
			}
			
			update_post_meta( $campaign_id, '_pigeonpack_campaign_type', $_REQUEST['campaign_type'] );
			
		}
			
		if ( isset( $_REQUEST['recipients'] ) )
			update_post_meta( $campaign_id, '_pigeonpack_recipients', $_REQUEST['recipients'] );
			
		if ( isset( $_REQUEST['wp_post_type'] ) ) {
			
			if ( ( $current_wp_post_type = get_post_meta( $campaign_id, '_pigeonpack_wp_post_type', true ) )
				&& $current_wp_post_type !== $_REQUEST['wp_post_type'] ) {

				// We've already done what we need to do to remove the 'individual' wp_post campaigns
				// now we just need to remove the next digest schedule.
				if ( 'digest' === $current_wp_post_type ) 
					remove_pigeonpack_wp_post_digest_schedule( $campaign_id );
				
			}
			
			update_post_meta( $campaign_id, '_pigeonpack_wp_post_type', $_REQUEST['wp_post_type'] );
			
		}
			
		if ( isset( $_REQUEST['clude_cat'] ) )
			update_post_meta( $campaign_id, '_pigeonpack_clude_cat', $_REQUEST['clude_cat'] );
			
		if ( isset( $_REQUEST['clude_cats'] ) )
			update_post_meta( $campaign_id, '_pigeonpack_clude_cats', $_REQUEST['clude_cats'] );
			
		if ( isset( $_REQUEST['wp_post_digest_frequency'] ) )
			$wp_post_digest['freq'] = $_REQUEST['wp_post_digest_frequency'];
			
		if ( isset( $_REQUEST['wp_post_digest_day'] ) )
			$wp_post_digest['day'] = $_REQUEST['wp_post_digest_day'];
			
		if ( isset( $_REQUEST['wp_post_digest_date'] ) )
			$wp_post_digest['date'] = $_REQUEST['wp_post_digest_date'];
			
		if ( isset( $_REQUEST['wp_post_digest_time'] ) )
			$wp_post_digest['time'] = $_REQUEST['wp_post_digest_time'];
			
		if ( isset( $_REQUEST['wp_post_digest_days'] ) )
			$wp_post_digest['days'] = $_REQUEST['wp_post_digest_days'];
			
		if ( isset( $wp_post_digest) && !empty( $wp_post_digest ) )
			update_post_meta( $campaign_id, '_pigeonpack_wp_post_digest', $wp_post_digest );
		else
			delete_post_meta( $campaign_id, '_pigeonpack_wp_post_digest' );
		
		if ( isset( $_REQUEST['pigeonpack_from_name'] ) )
			update_post_meta( $campaign_id, '_pigeonpack_from_name', $_REQUEST['pigeonpack_from_name'] );
			
		if ( isset( $_REQUEST['pigeonpack_from_email'] ) )
			update_post_meta( $campaign_id, '_pigeonpack_from_email', $_REQUEST['pigeonpack_from_email'] );
			
	}
	add_action( 'save_post', 'save_pigeonpack_campaign_meta' );
	
}

if ( !function_exists( 'pigeonpack_dropdown_roles' ) ) { 
	
	/**
	 * Helper Function for listing roles (modified version of WordPress wp_dropdown_roles)
	 *
	 * @since 0.0.1
	 *
	 * @param int|string ID of currently selected list option
	 * @return string HTML formated <option> block of WordPress roles
	 */
    function pigeonpack_dropdown_roles( $selected = false ) { 
		
		$p = '';
		$r = '';
	
		$editable_roles = get_editable_roles();
	
		foreach ( $editable_roles as $role => $details ) {
			
			$name = translate_user_role( $details['name'] );
			
			if ( 'R' . $role === $selected ) // preselect specified role
				$p = "\n\t<option selected='selected' value='R" . esc_attr( $role ) . "'>$name (" . __( 'Role', 'pigeonpack' ) . ")</option>";
			else
				$r .= "\n\t<option value='R" . esc_attr( $role ) . "'>$name (" . __( 'Role', 'pigeonpack' ) . ")</option>";
				
		}
		
		echo $p . $r;
		
    }   
	
}

if ( !function_exists( 'pigeonpack_dropdown_lists' ) ) { 
	
	/**
	 * Helper Function for listing Pigeon Pack Lists
	 *
	 * @since 0.0.1
	 *
	 * @param int|string ID of currently selected list option
	 * @return string HTML formated <option> block of Pigedon Pack lists
	 */
    function pigeonpack_dropdown_lists( $selected = false ) { 
		
		$p = '';
		$r = '';
	
		$args = array(
					'posts_per_page'	=> -1,
					'post_type'			=> 'pigeonpack_list'
				);
		$lists = get_posts( $args );
	
		foreach ( $lists as $list ) {
			
			if ( 'L' . $list->ID === $selected ) // preselect specified role
				$p = "\n\t<option selected='selected' value='L" . esc_attr( $list->ID ) . "'>" . $list->post_title . " (" . __( 'List', 'pigeonpack' ) . ")</option>";
			else
				$r .= "\n\t<option value='L" . esc_attr( $list->ID ) . "'>" . $list->post_title . " (" . __( 'List', 'pigeonpack' ) . ")</option>";
			
		}
		
		echo $p . $r;
	
    }   
	
}

if ( !function_exists( 'unset_pigeonpack_wp_post_campaigns_option_after_delete_post' ) ) {
	
	/**
	 * Called by after_delete_post action
	 *
	 * Removes deleted campaign from pigeonpack_wp_post_campaigns option in options table
	 * and removes digest schedule
	 *
	 * @since 0.0.1
	 *
	 * @param int $campaign_id WordPress Post ID
	 */	
	function pigeonpack_campaign_deleted( $campaign_id ) {
		
		if ( !( $campaign_id = absint( $campaign_id ) )  )
			return false;

		$campaign_type = get_post_meta( $post->ID, '_pigeonpack_campaign_type', true );
		
		if ( 'wp_post' === $campaign_type ) {
			
			remove_pigeonpack_wp_post_campaign( $post->ID );
			
			$current_wp_post_type = get_post_meta( $post->ID, '_pigeonpack_wp_post_type', true );
			
			if ( 'digest' === $current_wp_post_type )
				remove_pigeonpack_wp_post_digest_schedule( $post->ID );
				
		}
				
		do_action( 'pigeonpack_campaign_deleted', $post->ID );
		
	}
	add_action( 'after_delete_post', 'pigeonpack_campaign_deleted' );

}