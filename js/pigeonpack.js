var $pigeonpack_frontend = jQuery.noConflict();

$pigeonpack_frontend(document).ready(function($) {
	
	$( 'input.pigeonpack-subscribe-button' ).live( 'click', function( e ) {
		
		e.preventDefault();
		
		var error = false;
		var form_parent = $( this ).closest( 'form.pigeonpack-subscribe' );
		
		$( 'input.pigeonpack-required', form_parent ).each( function() {
			
			if ( '' === $( this ).val() ) {
				
				$( this ).addClass( 'pigeonpack-error' );
				error = true;
				
			}			
			
		});
		
		if ( error )
			return;
		
		var data = {
			'action':	'add_pigeonpack_subscriber',
			'data':		$( 'table.pigeonpack-subscribe-table input, table.pigeonpack-subscribe-table select', form_parent ).serializeArray(),
			'list_id':	$( 'input.pigeonpack_list_id', form_parent ).val(),
			'_wpnonce': $( 'input#pigeonpack_list_nonce', form_parent ).val()
		};
		
		$.post( pigeonpack_ajax_object.ajax_url, data, function(response) {
			
			results = $.parseJSON( response );
			
			if ( 0 < parseInt( results[0] ) ) { //Valid Int List ID
				
				if ( true === results[2] ) { //Is this Double Opt-In?
				
					$( form_parent ).html( results[3] ); //Double Opt-In Results Message
					
				} else {
					
					$( form_parent ).html( results[3] ); //New Subscriber Results Message
					
				}
				
			}
			
		});
		
		clear_subscriber_fields( form_parent );
		
	});
	
	$( 'table.pigeonpack-subscribe-table input.pigeonpack-required' ).focus( function() {
		
		$( this ).removeClass( 'pigeonpack-error' );
		
	});
	
	function clear_subscriber_fields( form ) {
	
		$( 'input[type="text"]', form ).each( function() {
			
			$( this ).val( '' );
			
		});	
	}
	
});