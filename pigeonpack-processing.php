<?php
/**
 * @package Pigeon Pack
 * @since 0.0.1
 */

if ( !function_exists( 'process_pigeonpack_subscribe' ) ){
	
	/**
	 * Processes subscriber and outputs results
	 *
	 * @since 0.0.1
	 * @uses apply_filters() Calls 'pigeonpack_processing_invalid_list_id' hook on invalid list ID.
	 * @uses apply_filters() Calls 'pigeonpack_processing_invalid_susbcriber_hash' hook on invalid subscriber hash.
	 * @uses apply_filters() Calls 'pigeonpack_double_optin_success_message' hook on success string.
	 * @uses apply_filters() Calls 'pigeonpack_double_optin_error_message' hook on error string.
	 * @uses apply_filters() Calls 'pigeonpack_double_optin_unknown_error_message' hook on success string.
	 *
	 * @param array $request $_GET array of list ID and subcriber hash to process
	 */
	function process_pigeonpack_subscribe( $request ) {
		
		$home_url = get_home_url();
		$back_to_site = '<p><a href="' . $home_url . '">' . __( 'Continue to our website', 'pigeonpack' ) . '</a></p>';
		$subscribe_error_msg = __( 'Error Processing Subscription Request', 'pigeonpack' );
		
		if ( isset( $request['list_id'] ) && isset( $request['subscriber'] ) ) {
		
			if ( !$list_id = absint( $request['list_id'] )  ) { //verify we get a valid integer

				$error = '<h3>' . __( 'Invalid List ID', 'pigeonpack' ) . '</h3>';
				$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
				$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
				
				wp_die( apply_filters( 'pigeonpack_processing_invalid_list_id', $error ) . $back_to_site, $subscribe_error_msg );

			}
				
			if ( !preg_match( '#^[0-9a-f]{32}$#i', $request['subscriber'] ) ) { //verify we get a valid 32 character md5 hash

				$error = '<h3>' . __( 'Invalid Subscriber Format', 'pigeonpack' ) . '</h3>';
				$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
				$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
				
				wp_die( apply_filters( 'pigeonpack_processing_invalid_susbcriber_hash', $error ) . $back_to_site, $subscribe_error_msg );
				
			}
			
			$subscriber = get_pigeonpack_subscriber_by_list_id_and_hash( $list_id, $request['subscriber'] );
			
			if ( 'subscribed' !== $subscriber['subscriber_status'] ) {
				
				$subscriber = update_pigeonpack_subscriber( $list_id, $subscriber['id'], maybe_unserialize( $subscriber['subscriber_meta'] ), 'subscribed' );
				
				if ( $subscriber ) {
				
					$success = '<h3>' . __( 'Subscription Confirmed', 'pigeonpack' ) . '</h3>';
					$success .= '<p>' . __( 'Your subscription to our list has been confirmed.', 'pigeonpack' ) . '</p>';
					$success .= '<p>' . __( 'Thank you for subscribing!', 'pigeonpack' ) . '</p>';
					
					wp_die( apply_filters( 'pigeonpack_double_optin_success_message', $success ) . $back_to_site, get_the_title( $list_id ) );
					
				} else {
				
					$error = '<h3>' . __( 'Error Processing Subscription', 'pigeonpack' ) . '</h3>';
					$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
					$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
					
					wp_die( apply_filters( 'pigeonpack_double_optin_error_message', $error ) . $back_to_site, get_the_title( $list_id ) );
					
				}
			
			} else { //Already subscribed
	
				$success = '<h3>' . __( 'Subscription Confirmed', 'pigeonpack' ) . '</h3>';
				$success .= '<p>' . __( 'Your subscription to our list has been confirmed.', 'pigeonpack' ) . '</p>';
				$success .= '<p>' . __( 'Thank you for subscribing!', 'pigeonpack' ) . '</p>';
				
				wp_die( apply_filters( 'pigeonpack_double_optin_unknown_error_message', $success ) . $back_to_site, get_the_title( $list_id ) );
				
			}
			
		}
					
	}
	
}

if ( !function_exists( 'process_pigeonpack_unsubscribe' ) ){
	
	/**
	 * Processes and unsubscribes subscriber and outputs results
	 *
	 * @since 0.0.1
	 * @uses apply_filters() Calls 'pigeonpack_processing_invalid_list_id' hook on invalid list ID.
	 * @uses apply_filters() Calls 'pigeonpack_processing_invalid_role_name' hook on invalid role ID.
	 * @uses apply_filters() Calls 'pigeonpack_processing_invalid_susbcriber_hash' hook on invalid subscriber hash.
	 * @uses apply_filters() Calls 'pigeonpack_double_optin_success_message' hook on success string.
	 * @uses apply_filters() Calls 'pigeonpack_double_optin_error_message' hook on error string.
	 * @uses apply_filters() Calls 'pigeonpack_double_optin_unknown_error_message' hook on success string.
	 *
	 * @param array $request $_GET array of list ID and subcriber hash to process
	 */
	function process_pigeonpack_unsubscribe( $request ) {
		
		$home_url = get_home_url();
		$back_to_site = '<p><a href="' . $home_url . '">' . __( 'Continue to our website', 'pigeonpack' ) . '</a></p>';
		$title = '';
		$unsubscribe_error_msg = __( 'Error Processing Unsubscribe Request', 'pigeonpack' );
		
		if ( isset( $request['subscriber'] ) ) {
				
			if ( !preg_match( '#^[0-9a-f]{32}$#i', $request['subscriber'] ) ) { //verify we get a valid 32 character md5 hash

				$error = '<h3>' . __( 'Invalid Subscriber Format', 'pigeonpack' ) . '</h3>';
				$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
				$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
				
				wp_die( apply_filters( 'pigeonpack_processing_invalid_susbcriber_hash', $error ) . $back_to_site, $unsubscribe_error_msg  );
				
			}
			
			if ( isset( $request['list_id'] ) ) {
			
				if ( !$list_id = absint( $request['list_id'] )  ) { //verify we get a valid integer
	
					$error = '<h3>' . __( 'Invalid List ID', 'pigeonpack' ) . '</h3>';
					$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
					$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
					
					wp_die( apply_filters( 'pigeonpack_processing_invalid_list_id', $error ) . $back_to_site, $unsubscribe_error_msg  );
	
				}
				
				$title = get_the_title( $list_id );
				$type = 'list';
				$subscriber = get_pigeonpack_subscriber_by_list_id_and_hash( $list_id, $request['subscriber'] );
				
			} else if ( isset( $request['role_name'] ) ) {
			
				if ( !is_string( $request['role_name'] ) || !get_role( $request['role_name'] ) ) { //verify we get a valid WordPress user role
	
					$error = '<h3>' . __( 'Invalid role name', 'pigeonpack' ) . '</h3>';
					$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
					$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
					
					wp_die( apply_filters( 'pigeonpack_processing_invalid_role_name', $error ) . $back_to_site, $unsubscribe_error_msg );
	
				}
				
				$title = ucfirst( $request['role_name'] );
				$type = 'role';
				$subscriber = get_pigeonpack_wordpress_subscriber_by_hash( $request['subscriber'] );
				
			}
			
			if ( 'subscribed' !== $subscriber['subscriber_status'] ) { //Already unsubscribed

				$success = '<h3>' . __( 'Unsubscribe Successful', 'pigeonpack' ) . '</h3>';
				$success .= '<p>' . __( 'You have been removed from this mailing list.', 'pigeonpack' ) . '</p>';
				
				wp_die( apply_filters( 'pigeonpack_unsubscribed_unknown_error_message', $success ) . $back_to_site, $title );
				
			}
			
			if ( !isset( $request['verify'] ) ) {
			
				wp_die( pigeonpack_unsubcribe_form( $request, $subscriber['email'], $type ) );
				
			}
			
			if ( 'yes' === $request['verify'] ) {
				
				if ( isset( $request['type'] ) && 'list' === $request['type'] )
					$subscriber = update_pigeonpack_subscriber( $list_id, $subscriber['id'], maybe_unserialize( $subscriber['subscriber_meta'] ), 'unsubscribed' );
				else
					$subscriber = update_user_meta( $subscriber['user_id'], '_pigeonpack_subscription', 'off' );
				
				if ( $subscriber ) {
				
					$success = '<h3>' . __( 'Unsubscribe Successful', 'pigeonpack' ) . '</h3>';
					$success .= '<p>' . __( 'You have been removed from this mailing list.', 'pigeonpack' ) . '</p>';
					
					wp_die( apply_filters( 'pigeonpack_unsubscribe_success_message', $success ) . $back_to_site, $title );
					
				} else {
				
					$error = '<h3>' . __( 'Error Processing Subscription', 'pigeonpack' ) . '</h3>';
					$error .= '<p>' . __( 'Please try again.', 'pigeonpack' ) . '</p>';
					$error .= '<p>' . __( 'If you continue to have this problem, contact us immediately.', 'pigeonpack' ) . '</p>';
					
					wp_die( apply_filters( 'pigeonpack_unsubscribe_error_message', $error ) . $back_to_site, $title );
					
				}
				
			}
		
		}
					
	}
	
}

if ( !function_exists( 'pigeonpack_unsubcribe_form' ) ){
	
	/**
	 * Displays unsubscribe form
	 *
	 * @since 0.0.1
	 * @todo double check list id and subscriber -- create helper functions
	 *
	 * @param array $request $_GET array of list ID and subcriber hash to process
	 * @param string $email Email address of subscriber
	 * @param string $type Type of user being unsubscribed: list or role
	 */
	function pigeonpack_unsubcribe_form( $request, $email, $type = 'list' ) {
		
		$home_url = get_home_url();
		$back_to_site = '<p><a href="' . $home_url . '">' . __( 'Continue to our website', 'pigeonpack' ) . '</a></p>';
		
		if ( isset( $request['subscriber'] ) ) {
			
			// double check list id and subscriber -- create helper functions
			
			$form = '<h3>' . __( 'Unsubscribe', 'pigeonpack' ) . '</h3>';
			$form .= '<p>' . sprintf( __( 'Are you sure you want to unsubscribe %s from this mailing list?', 'pigeonpack' ), '<strong>' . $email . '</strong>' ) . '</p>';
			$form .= '<a href="' . add_query_arg( array( 'verify' => 'yes', 'type' => $type ) ) . '">' . __( 'Yes, unsubscribe me!', 'pigeonpack' ) . '</a> | <a href="' . get_home_url() . '">' . __( 'No, get me outta here!', 'pigeonpack' ) . '</a>';
			
			return $form;
			
		}
		
		return __( 'Unable to process unsubscribe form, please try again or contact the site administrator.', 'pigeonpack' );
					
	}
	
}