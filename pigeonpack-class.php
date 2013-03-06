<?php
/**
 * @package Pigeon Pack
 */

/**
 * This class registers the main pigeonpack functionality
 *
 * @since 0.0.1
 */
if ( !class_exists( 'PigeonPack' ) ) {
	
	class PigeonPack {
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @todo add pigeon notifications, API, pigeonpack_deactivation hook, etc.
		 *
		 * @since 0.0.1
		 * @uses add_action() Calls 'admin_init' hook on $this->upgrade
		 * @uses add_action() Calls 'admin_enqueue_scripts' hook on $this->pigeonpack_admin_wp_enqueue_scripts
		 * @uses add_action() Calls 'admin_print_styles' hook on $this->pigeonpack_admin_wp_print_styles
		 * @uses add_action() Calls 'admin_menu' hook on $this->pigeonpack_admin_menu
		 * @uses add_action() Calls 'wp_ajax_verify' hook on $this->pigeonpack_api_ajax_verify
		 * @uses add_action() Calls 'transition_post_status' hook on $this->pigeonpack_transition_post_status
		 */
		function PigeonPack() {
			
			$pigeonpack_settings = get_option( 'pigeonpack' );
			
			add_action( 'admin_init', array( $this, 'upgrade' ) );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'pigeonpack_admin_wp_enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'pigeonpack_admin_wp_print_styles' ) );
					
			add_action( 'admin_menu', array( $this, 'pigeonpack_admin_menu' ) );
			
			add_action( 'wp_ajax_verify', array( $this, 'pigeonpack_api_ajax_verify' ) );
			
			add_action( 'transition_post_status', array( $this, 'pigeonpack_transition_post_status' ), 100, 3 );
			
			add_action( 'wp', array( $this, 'process_pigeonpack_requests' ) );
	
			//Add opt-in/opt-out options to profile.php
			add_action( 'show_user_profile', array( $this, 'pigeonpack_show_user_profile' ) );
			add_action( 'edit_user_profile', array( $this, 'pigeonpack_show_user_profile' ) );
			add_action( 'personal_options_update', array( $this, 'pigeonpack_profile_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'pigeonpack_profile_update' ) );
			
			/*
			add_action( 'admin_notices', array( $this, 'pigeonpack_notification' ) );

			//Premium Plugin Filters
			add_filter( 'plugins_api', array( $this, 'pigeonpack_plugins_api' ), 10, 3 );
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pigeonpack_update_plugins' ) );
			
			if ( empty( $pigeonpack_settings['api_key'] ) ) {
			
				update_option( 'pigeonpack_api_error_received', true );
				update_option( 'pigeonpack_api_error_message', __( 'Please enter your pigeonpack API key in the <a href="/wp-admin/edit.php?post_type=blast&page=pigeonpack">pigeonpack Settings</a> to continue to get support and upgrades.', 'pigeonpack' ) );
				
			} else {
				
				delete_option( 'pigeonpack_api_error_received' );
				delete_option( 'pigeonpack_api_error_message' );
				
			}
				
			register_deactivation_hook( __FILE__, array( $this, 'pigeonpack_deactivation' ) );
			
			//add_filter( 'views_edit-blast', array( $this, 'display_pigeonpack_dot_com_rss_item' ) );
			*/
			
		}
		
		/**
		 * Initialize pigeonpack Admin Menu
		 *
		 * @since 0.0.1
		 * @uses add_menu_page() Creates Pigeon Pack menu
		 * @uses add_submenu_page() Creates Settings submenu to Pigeon Pack menu
		 * @uses add_submenu_page() Creates Help submenu to Pigeon Pack menu
		 */
		function pigeonpack_admin_menu() {
			
			$pigeonpack_settings = $this->get_pigeonpack_settings();
			
			add_menu_page( __( 'Pigeon Pack', 'pigeonpack' ), __( 'Pigeon Pack', 'pidgenpack' ), apply_filters( 'manage_pigeonpack_settings', 'manage_pigeonpack_settings' ), 'pigeon-pack', array( $this, 'pigeonpack_settings_page' ), PIGEON_PACK_PLUGIN_URL . '/images/pigeon-16x16.png' );
			
			add_submenu_page( 'pigeon-pack', __( 'Settings', 'pigeonpack' ), __( 'Settings', 'pigeonpack' ), apply_filters( 'manage_pigeonpack_settings', 'manage_pigeonpack_settings' ), 'pigeonpack-settings', array( $this, 'pigeonpack_settings_page' ) );
			
			add_submenu_page( 'pigeon-pack', __( 'Help', 'pigeonpack' ), __( 'Help', 'pigeonpack' ), apply_filters( 'manage_pigeonpack_settings', 'manage_pigeonpack_settings' ), 'pigeonpack-help', array( $this, 'pigeonpack_help_page' ) );
			
		}
		
		/**
		 * Deactivation hook
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_deactivation() {
			
		}
		
		/* NOT USED
		function display_pigeonpack_dot_com_rss_item( $views ) {
		
			if ( $last_rss_item = get_option( 'last_pigeonpack_dot_com_rss_item' ) ) {
				
				echo '<div id="pigeonpack_rss_item">';
				echo $last_rss_item;
				echo '</div>';
				
			}
			
			return $views;
			
		}
		*/
		
		/**
		 * Prints backend pigeonpack styles
		 *
		 * @since 0.0.1
		 * @uses $hook_suffix to determine which page we are looking at, so we only load the CSS on the proper page(s)
		 * @uses wp_enqueue_style to enqueue the necessary pigeon pack style sheets
		 */
		function pigeonpack_admin_wp_print_styles() {
		
			global $hook_suffix;
			
			//wp_print_r( $hook_suffix, false );
				
			if ( isset( $_REQUEST['post_type'] ) ) {
				
				$post_type = $_REQUEST['post_type'];
				
			} else {
				
				if ( isset( $_REQUEST['post'] ) )
					$post_id = (int) $_REQUEST['post'];
				elseif ( isset( $_REQUEST['post_ID'] ) )
					$post_id = (int) $_REQUEST['post_ID'];
				else
					$post_id = 0;
				
				if ( $post_id )
					$post = get_post( $post_id );
				
				if ( isset( $post ) && !empty( $post ) )
					$post_type = $post->post_type;
				
			}
			
			if ( in_array( $hook_suffix, array( 'pigeon-pack_page_pigeonpack-settings', 'pigeon-pack_page_pigeonpack-help' ) )
				|| ( isset( $post_type ) && in_array( $post_type, array( 'pigeonpack_campaign', 'pigeonpack_list' ) ) ) ) {
					
				wp_enqueue_style( 'pigeonpack_admin_style', PIGEON_PACK_PLUGIN_URL . '/css/pigeonpack-admin.css', false, PIGEON_PACK_VERSION );
				wp_enqueue_style( 'jquery-ui-smoothness', PIGEON_PACK_PLUGIN_URL . '/css/smoothness/jquery-ui-1.10.0.custom.min.css', false, PIGEON_PACK_VERSION );
			
			}
			
		}
		
		/**
		 * Enqueues backend pigeonpack scripts
		 *
		 * @since 0.0.1
		 * @uses wp_enqueue_script to enqueue the necessary pigeon pack javascripts
		 * 
		 * @param $hook_suffix passed through by filter used to determine which page we are looking at
		 *        so we only load the CSS on the proper page(s)
		 */
		function pigeonpack_admin_wp_enqueue_scripts( $hook_suffix ) {
			
			//wp_print_r( $hook_suffix, false );
			
			if ( isset( $_REQUEST['post_type'] ) ) {
				
				$post_type = $_REQUEST['post_type'];
				
			} else {
				
				if ( isset( $_REQUEST['post'] ) )
					$post_id = (int) $_REQUEST['post'];
				elseif ( isset( $_REQUEST['post_ID'] ) )
					$post_id = (int) $_REQUEST['post_ID'];
				else
					$post_id = 0;
				
				if ( $post_id )
					$post = get_post( $post_id );
				
				if ( isset( $post ) && !empty( $post ) )
					$post_type = $post->post_type;
				
			}
			
			if ( isset( $post_type ) && 'pigeonpack_list' === $post_type ) {
				
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'pigeonpack_list_script', PIGEON_PACK_PLUGIN_URL . '/js/pigeonpack-list.js', array( 'jquery' ), PIGEON_PACK_VERSION );
				$args = array(
							'plugin_url' => PIGEON_PACK_PLUGIN_URL,
						);
				wp_localize_script( 'pigeonpack_list_script', 'pigeonpack_list_object', $args );
				
			} else if ( isset( $post_type ) && 'pigeonpack_campaign' === $post_type ) {
				
				wp_enqueue_script( 'pigeonpack_campaign_script', PIGEON_PACK_PLUGIN_URL . '/js/pigeonpack-campaign.js', array( 'jquery' ), PIGEON_PACK_VERSION );
				
			} else if ( 'pigeon-pack_page_pigeonpack-settings' == $hook_suffix ) {
			
				wp_enqueue_script( 'pigeonpack_settings_script', PIGEON_PACK_PLUGIN_URL . '/js/pigeonpack-settings.js', array( 'jquery' ), PIGEON_PACK_VERSION );
				
			}
			
		}
		
		/**
		 * Enqueues frontend pigeonpack scripts
		 *
		 * @todo get this working :)
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_frontend_js() {
		
		}
		
		/**
		 * Get pigeonpack options set in options table
		 *
		 * @since 0.0.1
		 * @uses wp_parse_args function to merge default with stored options
		 *
		 * return array Pigeon Pack settings
		 */
		function get_pigeonpack_settings() {
			
			$defaults = array( 
								'api_key' 					=> '', 
								'from_name'							=> get_option( 'blogname' ),
								'from_email'						=> get_option( 'admin_email' ),
								'email_format'						=> 'html',
								'allow_user_format'					=> 'yes',
								'page_for_subscription_settings'	=> 0,
								'css_style'							=> 'default',
								'smtp_enable'						=> 'mail',
								'smtp_server'						=> 'localhost',
								'smtp_port'							=> '25',
								'smtp_encryption'					=> 'none',
								'smtp_authentication'				=> 'none',
								'smtp_username'						=> '',
								'smtp_password'						=> '',
								'emails_per_cycle'					=> 100,
								'email_cycle'						=> '1',
								
							);
		
			$pigeonpack_settings = get_option( 'pigeonpack' );
			
			return wp_parse_args( $pigeonpack_settings, $defaults );
			
		}
		
		/**
		 * Output Pigeon Pack's settings page and saves new settings on form submit
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_settings_page() {
			
			// Get the user options
			$pigeonpack_settings = $this->get_pigeonpack_settings();
			
			if ( isset( $_REQUEST['update_pigeonpack_settings'] ) ) {
				
				if ( isset( $_REQUEST['api_key'] ) )
					$pigeonpack_settings['api_key'] = $_REQUEST['api_key'];
					
				if ( isset( $_REQUEST['page_for_subscription_settings'] ) )
					$pigeonpack_settings['page_for_subscription_settings'] = $_REQUEST['page_for_subscription_settings'];
					
				if ( isset( $_REQUEST['css_style'] ) )
					$pigeonpack_settings['css_style'] = $_REQUEST['css_style'];
					
				if ( isset( $_REQUEST['from_name'] ) )
					$pigeonpack_settings['from_name'] = $_REQUEST['from_name'];
					
				if ( isset( $_REQUEST['from_email'] ) )
					$pigeonpack_settings['from_email'] = $_REQUEST['from_email'];
					
				if ( isset( $_REQUEST['page_for_subscription_settings'] ) )
					$pigeonpack_settings['page_for_subscription_settings'] = $_REQUEST['page_for_subscription_settings'];
					
				if ( isset( $_REQUEST['smtp_enable'] ) )
					$pigeonpack_settings['smtp_enable'] = $_REQUEST['smtp_enable'];
					
				if ( isset( $_REQUEST['smtp_server'] ) )
					$pigeonpack_settings['smtp_server'] = $_REQUEST['smtp_server'];
					
				if ( isset( $_REQUEST['smtp_port'] ) )
					$pigeonpack_settings['smtp_port'] = $_REQUEST['smtp_port'];
					
				if ( isset( $_REQUEST['smtp_encryption'] ) )
					$pigeonpack_settings['smtp_encryption'] = $_REQUEST['smtp_encryption'];
					
				if ( isset( $_REQUEST['smtp_authentication'] ) )
					$pigeonpack_settings['smtp_authentication'] = $_REQUEST['smtp_authentication'];
					
				if ( isset( $_REQUEST['smtp_username'] ) )
					$pigeonpack_settings['smtp_username'] = $_REQUEST['smtp_username'];
					
				if ( isset( $_REQUEST['smtp_password'] ) )
					$pigeonpack_settings['smtp_password'] = $_REQUEST['smtp_password'];
					
				if ( isset( $_REQUEST['emails_per_cycle'] ) )
					$pigeonpack_settings['emails_per_cycle'] = $_REQUEST['emails_per_cycle'];
					
				if ( isset( $_REQUEST['email_cycle'] ) )
					$pigeonpack_settings['email_cycle'] = $_REQUEST['email_cycle'];
				
				update_option( 'pigeonpack', $pigeonpack_settings );
					
				// It's not pretty, but the easiest way to get the menu to refresh after save...
				?>
					<script type="text/javascript">
					<!--
					window.location = "<?php add_query_arg( array(  'page'				=> 'pigeonpack-settings',
																	'settings_saved' 	=> '' ) ); ?>"
					//-->
					</script>
				<?php
				
			}
			
			if ( isset( $_REQUEST['update_pigeonpack_settings'] ) || isset( $_REQUEST['settings_saved'] ) ) {
				
				// update settings notification ?>
				<div class="updated"><p><strong><?php _e( 'Pigeon Pack Settings Updated.', 'pigeonpack' );?></strong></p></div>
				<?php
				
			}
			
			// Display HTML form for the options below
			?>
			<div id="pigeonpack_administrator_options" class=wrap>
            
            <div class="icon32 icon32-pigeonpack_settings" id="icon-edit"><br></div>
            
            <h2><?php _e( 'Pigeon Pack Settings', 'pigeonpack' ); ?></h2>

            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-sortable">
            
                <form id="pigeonpack" method="post" action="" enctype="multipart/form-data" encoding="multipart/form-data">
                    
                    <div id="api-key" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'Pigeon Pack API Key', 'pigeonpack' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="pigeonpack_api_key">
                        
                        	<tr>
                                <th> <?php _e( 'API Key', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="api" class="regular-text" name="api_key" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['api_key'] ) ); ?>" />
                                
                                <input type="button" class="button" name="verify_pigeonpack_api" id="verify" value="<?php _e( 'Verify Pigeon Pack API', 'pigeonpack' ) ?>" />
                                <?php wp_nonce_field( 'verify', 'pigeonpack_verify_wpnonce' ); ?>
                                </td>
                            </tr>
                            
                        </table>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_pigeonpack_settings" value="<?php _e( 'Save Settings', 'pigeonpack' ) ?>" />
                        </p>
                        
                        </div>
                        
                    </div>
                    
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'Pigeon Pack General Options', 'pigeonpack' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="pigeonpack_administrator_options">
                        
                        	<tr>
                                <th> <?php _e( 'From Name', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="from_name" class="regular-text" name="from_name" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['from_name'] ) ); ?>" />
                                </td>
                            </tr>
                            
                        	<tr>
                                <th> <?php _e( 'From Email', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="from_email" class="regular-text" name="from_email" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['from_email'] ) ); ?>" />
                                </td>
                            </tr>
                        
                        	<tr>
                                <th> <?php _e( 'Subscription Settings Page', 'pigeonpack' ); ?></th>
                                <td><?php echo wp_dropdown_pages( array( 'name' => 'page_for_subscription_settings', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => $pigeonpack_settings['page_for_subscription_settings'] ) ); ?></td>
                            </tr>
                        
                        	<tr>
                                <th> <?php _e( 'CSS Style', 'pigeonpack' ); ?></th>
                                <td>
								<select id='css_style' name='css_style'>
									<option value='default' <?php selected( 'default', $pigeonpack_settings['css_style'] ); ?> ><?php _e( 'Default', 'pigeonpack' ); ?></option>
									<option value='none' <?php selected( 'none', $pigeonpack_settings['css_style'] ); ?> ><?php _e( 'None', 'pigeonpack' ); ?></option>
								</select>
                                </td>
                            </tr>
                        
                        	<tr>
                                <th> <?php _e( 'Show a "follow blog" option in the comment form', 'pigeonpack' ); ?></th>
                                <td>
                                </td>
                            </tr>
                        
                        	<tr>
                                <th> <?php _e( 'Show a "follow comments" option in the comment form', 'pigeonpack' ); ?></th>
                                <td>
                                </td>
                            </tr>
                            
                        </table>
                        
                        <?php wp_nonce_field( 'pigeonpack_general_options', 'pigeonpack_general_options_nonce' ); ?>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_pigeonpack_settings" value="<?php _e( 'Save Settings', 'pigeonpack' ) ?>" />
                        </p>

                        </div>
                        
                    </div>
                    
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'SMTP Options', 'pigeonpack' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="pigeonpack_smtp_settings">
                        
                        	<tr>
                                <th> <?php _e( 'Use SMTP Server?', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="radio" id="mail_checkbox" name="smtp_enable" value="mail" <?php checked( 'mail', $pigeonpack_settings['smtp_enable'] ); ?> /> <label for="mail_checkbox"><?php _e( 'Use built-in wp_mail() function to send emails.' , 'pigeonpack' ); ?></label>
                                <br />
                                <input type="radio" id="smtp_checkbox" name="smtp_enable" value="smtp" <?php checked( 'smtp', $pigeonpack_settings['smtp_enable'] ); ?> /> <label for="smtp_checkbox"><?php _e( 'Use SMTP server to send emails.' , 'pigeonpack' ); ?></label>
                                </td>
                            </tr>
                            
                            <?php
							if ( 'mail' === $pigeonpack_settings['smtp_enable'] )
								$hidden = 'style="display: none;"';
							else
								$hidden = '';
							?>
                        
                        	<tr class="smtp_options" <?php echo $hidden; ?>>
                                <th> <?php _e( 'SMTP Server', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="smtp_server" class="regular-text" name="smtp_server" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['smtp_server'] ) ); ?>" />
                                </td>
                            </tr>
                            
                        	<tr class="smtp_options" <?php echo $hidden; ?>>
                                <th> <?php _e( 'SMTP Port', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="smtp_port" class="regular-text" name="smtp_port" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['smtp_port'] ) ); ?>" />
                                </td>
                            </tr>
                            
                        	<tr class="smtp_options" <?php echo $hidden; ?>>
                                <th> <?php _e( 'Encryption', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="radio" id="smtp_ssl_none" class="smtp_encryption" class="regular-text" name="smtp_encryption" value="none" <?php checked( 'none' === $pigeonpack_settings['smtp_encryption'] ); ?> /> <label for="smtp_ssl_none"><?php _e( 'No encryption', 'pigeonpack' ); ?></label> <br />
                                <input type="radio" id="smtp_ssl_ssl" class="smtp_encryption" class="regular-text" name="smtp_encryption" value="ssl" <?php checked( 'ssl' === $pigeonpack_settings['smtp_encryption'] ); ?> /> <label for="smtp_ssl_ssl"><?php _e( 'Use SSL encryption', 'pigeonpack' ); ?></label> <br />
                                <input type="radio" id="smtp_ssl_tls" class="smtp_encryption" class="regular-text" name="smtp_encryption" value="tls" <?php checked( 'tls' === $pigeonpack_settings['smtp_encryption'] ); ?> /> <label for="smtp_ssl_tls"><?php _e( 'Use TLS encryption', 'pigeonpack' ); ?></label> <br />
                                </td>
                            </tr>
                            
                        	<tr class="smtp_options" <?php echo $hidden; ?>>
                                <th> <?php _e( 'Authentication', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="radio" id="smtp_auth_none" class="smtp_authentication" class="regular-text" name="smtp_authentication" value="none" <?php checked( 'none' === $pigeonpack_settings['smtp_authentication'] ); ?> /> <label for="smtp_auth_none"><?php _e( 'No authentication', 'pigeonpack' ); ?></label> <br />
                                <input type="radio" id="smtp_auth_true" class="smtp_authentication" class="regular-text" name="smtp_authentication" value="none" <?php checked( 'true' === $pigeonpack_settings['smtp_authentication'] ); ?> /> <label for="smtp_auth_true"><?php _e( 'Yes, use SMTP authentication', 'pigeonpack' ); ?></label> <br />
                                </td>
                            </tr>
                            
                        	<tr class="smtp_options" <?php echo $hidden; ?>>
                                <th> <?php _e( 'SMTP Username', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="smtp_username" class="regular-text" name="smtp_username" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['smtp_username'] ) ); ?>" />
                                </td>
                            </tr>
                            
                        	<tr class="smtp_options" <?php echo $hidden; ?>>
                                <th> <?php _e( 'SMTP Password', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="smtp_password" class="regular-text" name="smtp_password" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['smtp_password'] ) ); ?>" />
                                </td>
                            </tr>
                            
                        	<tr>
                                <th> <?php _e( 'Emails per cycle', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="emails_per_cycle" class="small-text" name="emails_per_cycle" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['emails_per_cycle'] ) ); ?>" /> <?php _e( 'emails', 'pigeonpack' ); ?>.
                                </td>
                            </tr>
                            
                        	<tr>
                                <th> <?php _e( 'Email cycle', 'pigeonpack' ); ?></th>
                                <td>
                                <input type="text" id="email_cycle" class="small-text" name="email_cycle" value="<?php echo htmlspecialchars( stripcslashes( $pigeonpack_settings['email_cycle'] ) ); ?>" /> <?php _e( 'minutes', 'pigeonpack' ); ?>.
                                </td>
                            </tr>
                            
                        </table>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_pigeonpack_settings" value="<?php _e( 'Save Settings', 'pigeonpack' ) ?>" />
                        </p>
                        
                        </div>
                        
                    </div>
                    
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'SPAM Laws', 'pigeonpack' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <p>
                        <?php printf( __( '%s enables you to own and operate your own email campaign manager. You have full control and ownership over your email lists, campaigns, autoresponders, and more. Due to this, you are also required to follow the SPAM laws, guidelines and recommendations for your country. The plugin is setup to meet compliance with current laws, however, you have the responsibility to know the laws and make sure you are using the plugin appropriately. For more information about the SPAM laws in your country, see the list below or google "SPAM LAWS" for your country.', 'pigeonpack' ), 'Pigeon Pack' ); ?>
                        </p>
                        
						<ol>
                            
                            <li><a href="http://www.business.ftc.gov/documents/bus61-can-spam-act-compliance-guide-business" target="_blank"><?php _e( 'United States - CAN-SPAM Act', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www2.parl.gc.ca/HousePublications/Publication.aspx?Language=E&Parl=40&Ses=3&Mode=1&Pub=Bill&Doc=C-28_3" target="_blank"><?php _e( 'Canada - C-28', 'pigeonpack' ); ?></a></li>
                            <li><?php _e( 'Australia', 'pigeonpack' ); ?> - <?php _e( 'Spam Act 2003, Act No. 129 of 2003 as amended..', 'pigeonpack' ); ?></li>
                            <li><a href="http://ec.europa.eu/information_society/policy/ecomm/todays_framework/privacy_protection/spam/index_en.htm" target="_blank"><?php _e( 'EU - Article 13 of DIRECTIVE 2002/58/EC OF THE EUROPEAN PARLIAMENT AND OF THE COUNCIL of 12 July 2002', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.legislation.gov.uk/uksi?title=The%20Privacy%20and%20Electronic%20Communication" target="_blank"><?php _e( 'UK - The Privacy and Electronic Communications Regulations', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.rtr.at/en/tk/TKG2003" target="_blank"><?php _e( 'Austria - Telecommunications Act 2003', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.privacy.fgov.be/publications/spam_4-7-03_fr.pdf" target="_blank"><?php _e( 'Belgium - Etat des lieux en juillet 2003, July 4, 2003', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.dataprotection.gov.cy/dataprotection/dataprotection.nsf/index_en/index_en?opendocument" target="_blank"><?php _e( 'Cyprus - Section 06 of the Regulation of Electronic Communications and Postal Services Law of 2004', 'pigeonpack' ); ?></a></li>
                            <li><?php _e( 'Czech Republic', 'pigeonpack' ); ?> - <?php _e( 'Act No. 480/2004 Coll., on Certain Information Society Services', 'pigeonpack' ); ?></li>
                            <li><a href="https://www.riigiteataja.ee/akt/780289" target="_blank"><?php _e( 'Estonia - Information Society Service Act', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.cnil.fr/dossiers/conso-pub-spam/fiches-pratiques/article/la-prospection-commerciale-par-courrier-electronique/" target="_blank"><?php _e( 'France - CNIL Guidelines on email marketing.', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.iuscomp.org/gla/statutes/BDSG.htm" target="_blank"><?php _e( 'Germany - Art. 7 German Unfair Competition Law (Gesetz gegen Unlauteren Wettbewerb)', 'pigeonpack' ); ?></a></li>
                            <li><a href="http://www.garanteprivacy.it/garante/document?ID=311066" target="_blank"><?php _e( 'Italy - Personal Data Protection Code (legislative decree no. 196/2003)', 'pigeonpack' ); ?></a></li>
                            <li><?php _e( 'Netherlands', 'pigeonpack' ); ?> - <?php _e( 'Article 11.7 of the Dutch Telecommunications Act and Dutch Data Protection Act.', 'pigeonpack' ); ?></li>
                            <li><?php _e( 'Sweden', 'pigeonpack' ); ?> - <?php _e( 'Swedish Code of Statutes, SFS 1995:450 & Swedish Code of Statutes, SFS 1998:204.', 'pigeonpack' ); ?></li>
                        
                        </ul>
                        
                        </div>
                        
                    </div>
                    
                </form>
                
            </div>
            </div>
            </div>
			</div>
			<?php
			
		}
		
		/**
		 * Output Pigeon Pack's help page
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_help_page() {
			
			// Display HTML
			?>
			<div id="pigeonpack_help_page" class=wrap>
            
            <div class="icon32 icon32-pigeonpack_help" id="icon-edit"><br></div>
    
            <h2><?php _e( 'Pigeon Pack Help', 'pigeonpack' ); ?></h2>
            
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-sortable">
                
                <div id="pigeonpack-campaigns" class="postbox">
                
                    <div class="handlediv" title="Click to toggle"><br /></div>
    
                    <h3 class="hndle"><span><?php _e( '[pigeonpack_sub_pref] - Subscription Preferences Shortcode', 'pigeonpack' ); ?></span></h3>
                    
                    <div class="inside">
                                    
                        <table class="form-table">
                    
                            <tr>
                            
                                <td>
                                	
                                    Pigeon Pack pack Subscription Preferences: <code style="font-size: 1.2em; background: #ffffe0;">[pigeonpack_sub_pref]</code>
                                    
                                    <p>Blah Blah Blah</p>
                                    
                                    <pre>
                                                    
Default Variables:

orderby => 'term_id'
order => 'DESC'
limit => 0
pdf_title => pigeonpack Setting "PDF Title"
default_image => pigeonpack Setting "Default Cover Image"

Accepted Arguments:

orderby => 'term_id', 'issue_order', 'name' (for Issue ID Number, Issue Order, or Issue Name)
order => 'DESC' or 'ASC' (for Descending or Ascending)
limit => Any number 0 and greater
pdf_title => 'Text'
default_image => 'Image URL'

Examples:

[pigeonpack_archives orderby="issue_order"]
[pigeonpack_archives orderby="name" order="ASC" limit=5 pdf_title="Download Now" default_image="http://yoursite.com/yourimage.jpg"]

                                    </pre>
                                    
                                </td>
                                
                            </tr>
                            
                        </table>
                    
                    </div>
                    
                </div>
                
            </div>
            </div>
            </div>
			</div>
			<?php
			
		}
		
		/**
		 * Checks if plugin is being ugpraded to newer version and runs necessary upgrade functions
		 *
		 * @since 0.0.1
		 */
		function upgrade() {
			
			$pigeonpack_settings = $this->get_pigeonpack_settings();
			
			/* Plugin Version Changes */
			//if ( isset( $pigeonpack_settings['version'] ) )
			//	$old_version = $pigeonpack_settings['version'];
			//else
				$old_version = 0;
			
			if ( version_compare( $old_version, '0.0.1', '<' ) )
				$this->upgrade_to_0_0_1();
			
			$pigeonpack_settings['version'] = PIGEON_PACK_VERSION;
			
			/* Table Version Changes */
			//if ( isset( $pigeonpack_settings['db_version'] ) )
			//	$old_db_version = $pigeonpack_settings['db_version'];
			//else
				$old_db_version = 0;
			
			if ( version_compare( $old_db_version, PIGEON_PACK_DB_VERSION, '<' ) )
				$this->init_db_table();
				
			$pigeonpack_settings['db_version'] = PIGEON_PACK_DB_VERSION;
				
			update_option( 'pigeonpack', $pigeonpack_settings );
			
		}
		
		/**
		 * Upgrade to version 0.0.1, sets default permissions
		 *
		 * @since 0.0.1
		 */
		function upgrade_to_0_0_1() {
			
			$role = get_role('administrator');
			if ($role !== NULL)
				// Blasts
				$role->add_cap('edit_pigeonpack_campaign');
				$role->add_cap('read_pigeonpack_campaign');
				$role->add_cap('delete_pigeonpack_campaign');
				$role->add_cap('edit_pigeonpack_campaigns');
				$role->add_cap('edit_others_pigeonpack_campaigns');
				$role->add_cap('publish_pigeonpack_campaigns');
				$role->add_cap('read_private_pigeonpack_campaigns');
				$role->add_cap('delete_pigeonpack_campaigns');
				$role->add_cap('delete_private_pigeonpack_campaigns');
				$role->add_cap('delete_published_pigeonpack_campaigns');
				$role->add_cap('delete_others_pigeonpack_campaigns');
				$role->add_cap('edit_private_pigeonpack_campaigns');
				$role->add_cap('edit_published_pigeonpack_campaigns');
				// Lists
				$role->add_cap('edit_pigeonpack_list');
				$role->add_cap('read_pigeonpack_list');
				$role->add_cap('delete_pigeonpack_list');
				$role->add_cap('edit_pigeonpack_lists');
				$role->add_cap('edit_others_pigeonpack_lists');
				$role->add_cap('publish_pigeonpack_lists');
				$role->add_cap('read_private_pigeonpack_lists');
				$role->add_cap('delete_pigeonpack_lists');
				$role->add_cap('delete_private_pigeonpack_lists');
				$role->add_cap('delete_published_pigeonpack_lists');
				$role->add_cap('delete_others_pigeonpack_lists');
				$role->add_cap('edit_private_pigeonpack_lists');
				$role->add_cap('edit_published_pigeonpack_lists');
				$role->add_cap('manage_pigeonpack_settings');
	
			$role = get_role('editor');
			if ($role !== NULL) {}
				// Blasts
				$role->add_cap('edit_pigeonpack_campaign');
				$role->add_cap('edit_others_pigeonpack_campaigns');
				$role->add_cap('delete_published_pigeonpack_campaigns');
				$role->add_cap('publish_pigeonpack_campaigns');
				// Lists
				$role->add_cap('edit_pigeonpack_list');
				$role->add_cap('edit_others_pigeonpack_lists');
				$role->add_cap('delete_published_pigeonpack_lists');
				$role->add_cap('publish_pigeonpack_lists');
	
			$role = get_role('author');
			if ($role !== NULL) {}
				// Blasts
				$role->add_cap('edit_pigeonpack_campaign');
				$role->add_cap('delete_published_pigeonpack_campaigns');
				$role->add_cap('publish_pigeonpack_campaigns');
				// Lists
				$role->add_cap('edit_pigeonpack_list');
				$role->add_cap('delete_published_pigeonpack_lists');
				$role->add_cap('publish_pigeonpack_lists');
	
			$role = get_role('contributor');
			if ($role !== NULL) {}
				$role->add_cap('edit_pigeonpack_campaign');
				
		}
		
		/**
		 * Initialized & Upgrade Pigeon Pack Database Table
		 *
		 * @see http://codex.wordpress.org/Creating_Tables_with_Plugins
		 *
		 * @since 0.0.1
		 */
		function init_db_table() {
			
			global $wpdb;
			
			$table_name = $wpdb->prefix . "pigeonpack_subscribers";

			//available subscriber status = pending, unsubscribed, subscribed, bounced
			$sql = "CREATE TABLE $table_name (
				id 					mediumint(9) 	NOT NULL AUTO_INCREMENT,
				list_id 			bigint(20) 		DEFAULT 0 NOT NULL,
				email 				VARCHAR(100) 	NOT NULL,
				subscriber_meta 	longtext 		DEFAULT NULL,
				subscriber_added 	datetime 		DEFAULT '0000-00-00 00:00:00' NOT NULL,
				subscriber_modified datetime 		DEFAULT '0000-00-00 00:00:00' NOT NULL,
				subscriber_status 	VARCHAR(100)	DEFAULT 'pending' NOT NULL,
				subscriber_hash 	VARCHAR(64) 	DEFAULT NULL,
				UNIQUE KEY id (id)
			);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
		}
		
		/**
		 * AJAX call to verify API key with Pigeon Pack servers
		 *
		 * @todo get this working!
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_api_ajax_verify() {
		
			check_ajax_referer( 'verify' );
			
			if ( isset( $_REQUEST['api_key'] ) ) {
						
				// POST data to send to your API
				$args = array(
					'action' 	=> 'verify-api',
					'api'		=> $_REQUEST['api_key']
				);
					
				// Send request for detailed information
				$response = $this->pigeonpack_api_request( $args );
				
				die( $response->response );
		
			} else {
		
				die( __( 'Please fill in your API key.', 'pigeonpack' ) );
		
			}
		
		}
		
		/**
		 * Filtering 'plugins_api' hook to get plugin information from Pigeon Pack servers
		 *
		 * @todo get this working!
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_plugins_api( $false, $action, $args ) {
		
			$plugin_slug = 'pigeonpack';
			
			// Check if this plugins API is about this plugin
			if( $args->slug !== $plugin_slug )
				return false;
				
			// POST data to send to your API
			$args = array(
				'action' 	=> 'get-plugin-information'
			);
				
			// Send request for detailed information
			$response = $this->pigeonpack_api_request( $args );
				
			return $response;
			
		}
		
		/**
		 * Filtering 'pre_set_site_transient_update_plugins' hook to get plugin latest version from Pigeon Pack servers
		 *
		 * @todo get this working!
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_update_plugins( $transient ) {
			
			// Check if the transient contains the 'checked' information
    		// If no, just return its value without hacking it
			if ( empty( $transient->checked ) )
				return $transient;
		
			// The transient contains the 'checked' information
			// Now append to it information form your own API
			$plugin_path = plugin_basename( __FILE__ );
				
			// POST data to send to your API
			$args = array(
				'action' 	=> 'check-latest-version'
			);
			
			// Send request checking for an update
			$response = $this->pigeonpack_api_request( $args );
				
			// If there is a new version, modify the transient
			if( version_compare( $response->new_version, $transient->checked[$plugin_path], '>' ) )
				 $transient->response[$plugin_path] = $response;
				
			return $transient;
			
		}
		
		/**
		 * Normalize Pigeon Pack API request
		 *
		 * @todo get this working!
		 *
		 * @since 0.0.1
		 * @uses wp_remote_post
		 */
		function pigeonpack_api_request( $args ) {
			
			$pigeonpack_settings = get_pigeonpack_settings();
			
			$args['site'] = network_site_url();
			
			if ( !isset( $args['api'] ) )
				$args['api'] = apply_filters( 'pigeonpack_api_key', $pigeonpack_settings['api_key'] );
			
			// Send request									
			$request = wp_remote_post( PIGEONPACK_API_URL, array( 'body' => $args ) );
			
			if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) )
				return false;
				
			$response = unserialize( wp_remote_retrieve_body( $request ) );
			
			$this->api_status( $response );
			
			if ( is_object( $response ) )
				return $response;
			else
				return false;

		}
		
		/**
		 * Determine API status and set/remove notifications
		 *
		 * @todo get this working!
		 *
		 * @since 0.0.1
		 */
		function api_status( $response ) {
		
			if ( 1 < $response->account_status ) {
				
				update_option( 'pigeonpack_api_error_received', true );
				update_option( 'pigeonpack_api_error_message', $response->response );
				
			} else {
			
				delete_option( 'pigeonpack_api_error_received' );
				delete_option( 'pigeonpack_api_error_message' );
				delete_option( 'pigeonpack_api_error_message_version_dismissed' );
				
			}
			
		}
		
		/**
		 * Added Pigeon Pack API error messages
		 *
		 * @todo get this working!
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_notification() {
			
			if ( isset( $_REQUEST['remove_pigeonpack_api_error_message'] ) ) {
				
				delete_option( 'pigeonpack_api_error_message' );
				update_option( 'pigeonpack_api_error_message_version_dismissed', PIGEON_PACK_VERSION );
				
			}
		
			if ( ( $notification = get_option( 'pigeonpack_api_error_message' ) ) && version_compare( get_option( 'pigeonpack_api_error_message_version_dismissed' ), PIGEON_PACK_VERSION, '<' ) )
				echo '<div class="update-nag">' . $notification . '<br /><a href="' . add_query_arg( 'remove_pigeonpack_api_error_message', true ) . '">' . __( 'Dismiss', 'pigeonpack' ) . '</a></div>';
		 
		}
		
		/**
		 * Action from 'transition_post_status' to determine if new post has been published.
		 *
		 * If post status is 'publish' determine post type and process as necessary
		 * Campaigns get scheduled
		 * Posts get added to digests (if digest campaigns exist)
		 *
		 * @since 0.0.1
		 */
		function pigeonpack_transition_post_status( $new_status, $old_status, $post ) {
		
			if ( 'publish' === $new_status ) {
				
				if ( 'pigeonpack_campaign' === $post->post_type ) {
					
					$campaign_type = get_post_meta( $post->ID, '_pigeonpack_campaign_type', true );
					
					switch ( $campaign_type ) {
						
						case ( 'wp_post' ):
							pigeonpack_wp_post_campaign_init( $post->ID );
							break;
					
						case ( 'single_campaign' ):
						default:
							pigeonpack_campaign_scheduler( $post->ID );
							break;
							
						
					}
					
				} else if ( 'post' === $post->post_type ) {
				
					do_pigeonpack_wp_post_campaigns( $post->ID );
					
				}
				
			}
			
		}
		
		/**
		 * Action from 'wp' to check if a pigeonpack _REQUEST was sent
		 *
		 * If post status is 'publish' determine post type and process as necessary
		 * Campaigns get scheduled
		 * Posts get added to digests (if digest campaigns exist)
		 *
		 * @since 0.0.1
		 */
		function process_pigeonpack_requests() {
		
			if ( isset( $_REQUEST['pigeonpack'] ) ) {
				
				require_once( 'pigeonpack-processing.php' );
			
				switch( $_REQUEST['pigeonpack'] ) {
				
					case 'subscribe':
						process_pigeonpack_subscribe( $_REQUEST );
						break;
						
					case 'unsubscribe':
						process_pigeonpack_unsubscribe( $_REQUEST );
						break;
					
					
				}
				
			}
			
		}	
			
		/**
		 * Action from 'show_user_profile' and 'edit_user_profile' to 
		 * display Pigeon Pack profile field options.
		 *
		 * @since 0.0.1
		 *
		 * @param object $user User object passed through action hook
		 */
		function pigeonpack_show_user_profile( $user ) {
			
			?>
            
            <h3><?php _e( 'Subscription Options', 'pigeonpack' ); ?></h3>
            
			<table class="form-table">
			<tr id="profile-optin">
				<th><label for="pigeonpack_subscription"><?php _e('Yes, I want to receive email updates'); ?></label></th>
				<td>
                <input type="checkbox" name="pigeonpack_subscription" id="pigeonpack_subscription" <?php checked( 'on' === get_user_meta( $user->ID, '_pigeonpack_subscription', true ) ); ?> />
                <p class="description">
                <?php _e( 'Unchecking this box will stop you from receiving emails based on your user profile with this site, this will not unsubscribe you from any other lists you subscribed to manually.', 'pigeonpack' ); ?>
                </p>
				</td>
			</tr>
			</table>
            
			<?php
			
		}
		
		/**
		 * Action from 'personal_options_update' and 'edit_edit_user_profile_update' to 
		 * update Pigeon Pack profile field options.
		 *
		 * @since 0.0.1
		 *
		 * @param int $user_id User ID passed through action hook
		 */
		function pigeonpack_profile_update( $user_id ) {
			
			if ( !current_user_can( 'edit_user', $user_id ) )
				return false;
			
			if ( isset( $_POST['pigeonpack_subscription'] ) )
				update_user_meta( $user_id, '_pigeonpack_subscription', 'on' );
			else
				delete_user_meta( $user_id, '_pigeonpack_subscription' );
			
		}
		
	}
	
}