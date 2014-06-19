var $pigeonpack_list = jQuery.noConflict();

$pigeonpack_list(document).ready(function($) {

	var plus_icon = '<img class="add_option" src="' + pigeonpack_list_object.plugin_url + '/images/plus-16x16.png" />';
	var plus_and_minus_icon = plus_icon + ' <img class="remove_option" src="' + pigeonpack_list_object.plugin_url + '/images/minus-16x16.png" />';
	
	$( 'div#add_list_field' ).live( 'click', function() {
		
		$( 'div#list_field_types' ).toggle();
		
	});
	
	$( 'input#add_pigeonpack_subscriber' ).live( 'click', function() {
		
		var error = false;
		
		$( 'table#pigeonpack_list_new_subscriber_table input.required' ).each( function() {
			
			if ( '' === $( this ).val() ) {
				
				$( this ).addClass( 'error' );
				error = true;
				
			}			
			
		});
		
		if ( error )
			return;
	
		var data = {
			'action':				'add_pigeonpack_subscriber',
			'data':					$( 'table#pigeonpack_list_new_subscriber_table input' ).serializeArray(),
			'list_id':				$( 'input#post_ID' ).val(),
			'subscriber_status':	'subscribed',
			'_wpnonce': 			$( 'input#pigeonpack_list_nonce' ).val()
		};
		
		$.post( ajaxurl, data, function(response) {
		
			console.log( response );
			
			results = $.parseJSON( response );
			
			$( 'tr#no_susbcribers' ).remove();
			
			var count = $( 'table#pigeonpack_list_subscriber_table > tbody > tr' ).length;
			var oddeven = ( 0 === count % 2 ) ? 'even' : 'odd';
			
			$( 'table#pigeonpack_list_subscriber_table > tbody:last' ).append( '<tr id="subscriber-' + results[0] + '" class="' + oddeven + '">' + results[1] + '</tr>' );
			
		});
		
		clear_subscriber_fields();
		
	});
	
	$( 'table#pigeonpack_list_new_subscriber_table input.required' ).focus( function() {
		
		$( this ).removeClass( 'error' );
		
	});
	
	$( 'input.add-field-type' ).live( 'click', function() {
		
		var count = $( 'table#pigeonpack_list_fields_table > tbody > tr' ).length;
		var altcount = $( 'input[type=hidden].pigeonpack_field_static_merge' ).last().val();
		altcount++;
		
		var oddeven = ( 0 === count % 2 ) ? 'even' : 'odd';
		var field_label = '<input type="text" name="pigeonpack_field_label[' + count + ']" value="Untitled" />';
		
		if ( 'Radio Button' === $( this ).val() || 'Drop Down' === $( this ).val() ) {
			
			field_label = field_label + '<br />' //i18n First Choice, Second Choice, Third Choice
						+ '<ul>'
						+ '<li><input type="text" name="pigeonpack_field_choice[' + count + '][]" value="First Choice" /> ' + plus_and_minus_icon + '</li>'
						+ '<li><input type="text" name="pigeonpack_field_choice[' + count + '][]" value="Second Choice" /> ' + plus_and_minus_icon + '</li>'
						+ '<li><input type="text" name="pigeonpack_field_choice[' + count + '][]" value="Third Choice" /> ' + plus_and_minus_icon + '</li>'
						+ '</ul>';
			
		}
		
		var row = '<tr class="' + oddeven + '">'
				+ '<td>' + field_label + '</td>'
				+ '<td>' 
					+ $( this ).val().toLowerCase() 
					+ '<input type="hidden" name="pigeonpack_field_type[' + count + ']" value="' + $( this ).val().toLowerCase() + '" />'
				+ '</td>'
				+ '<td><input type="checkbox" name="pigeonpack_field_require[' + count + ']" /></td>'
				+ '<td>'
					+ '{{<input type="text" class="pigeonpack-medium-text" name="pigeonpack_field_merge[' + count + ']" value="MERGE' + altcount + '" />}}'
					+ ' or {{MERGE' + altcount + '}}'
					+ '<input type="hidden" class="pigeonpack_field_static_merge" name="pigeonpack_field_static_merge[' + count + ']" value="' + altcount + '" />'
				+ '</td>'
				+ '<td><img class="pigeonpack_delete_field" src="' + pigeonpack_list_object.plugin_url + '/images/delete-16x16.png" /></td>'
				+ '</tr>';
				
		$( 'table#pigeonpack_list_fields_table > tbody:last' ).append( row );
		
	});
	
	$( 'img.pigeonpack_delete_field' ).live( 'click', function() {
	
		var agree = confirm( 'Are you sure you want to delete this field?' ); //i18n
		
		if ( agree )
			$( this ).closest( 'tr' ).remove();
		
	});
	
	$( 'img.add_option' ).live( 'click', function() {
		
		var me = $( this );
		var clone = me.closest( 'li' ).clone();
		clone.insertAfter( me.closest( 'li' ) );
		
	});
	
	$( 'img.remove_option' ).live( 'click', function() {
	
		var count = $( this ).parent().parents( 'ul' ).children( 'li' ).length;
		
		if ( 1 < count )
			$( this ).closest( 'li' ).remove();
		else
			alert( 'Error: you must have at least one option.' ); //i18n
		
	});
	
	$( 'input.field-type-date' ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "c-120:+10"
	});
	
	$( 'input.edit-subscriber' ).live( 'click', function() {
		
		$( 'div#add_new_subscriber_button' ).hide();
		$( 'div#update_subscriber_button' ).show();
	
		var data = {
			'action':			'edit_pigeonpack_subscriber',
			'subscriber_id':	$( this ).attr( 'subscriber_id' ),
			'_wpnonce':			$( 'input#pigeonpack_list_nonce' ).val()
		};
		
		$( 'input#update_pigeonpack_subscriber_id' ).val( data['subscriber_id'] );
		
		$.post( ajaxurl, data, function(response) {
			
			results = $.parseJSON( response );
			
			clear_subscriber_fields();
			
			$.each( results, function(key,val) {
				
				$( 'input[name="' + key + '"]' ).val( val );
				
			});
			
			$( 'select[name=pigeonpack_email_format]' ).val( results['email_format'] );
			
		});
		
	});
	
	$( 'input#update_pigeonpack_subscriber' ).live( 'click', function() {
	
		var data = {
			'action':			'update_pigeonpack_subscriber',
			'data':				$( 'table#pigeonpack_list_new_subscriber_table input' ).serializeArray(),
			'list_id':			$( 'input#post_ID' ).val(),
			'subscriber_id':	$( 'input#update_pigeonpack_subscriber_id' ).val(),
			'_wpnonce':			$( 'input#pigeonpack_list_nonce' ).val()
		};
		
		$.post( ajaxurl, data, function(response) {
			
			$( 'div#add_new_subscriber_button' ).show();
			$( 'div#update_subscriber_button' ).hide();
			
			$( 'table#pigeonpack_list_subscriber_table tr#subscriber-' + data['subscriber_id'] ).html( response );
			
		});
		
		clear_subscriber_fields();
		
	});
	
	$( 'input#cancel_update_pigeonpack_subscriber' ).live( 'click', function() {
		
		$( 'div#add_new_subscriber_button' ).show();
		$( 'div#update_subscriber_button' ).hide();
		
		clear_subscriber_fields();
		
	});
	
	$( 'input#delete_pigeonpack_subscribers' ).live( 'click', function() {
	
		var checked = $( 'input[name="pigeonpack_list_delete[]"]:checked' ).length;
		
		if ( 1 === checked ) {
		
			var thisthese = 'this';
			var plural = '';
		
		} else {
		
			var thisthese = 'these';
			var plural = 's';
		
		}
		
		var agree = confirm( 'Are you sure you want to delete ' + thisthese + ' subscriber' + plural + '?' ); //i18n
		
		if ( agree ) {
			
			var subscriber_ids = new Array();
			
			$( 'input[name="pigeonpack_list_delete[]"]:checked' ).each( function() {
				
				subscriber_ids[ subscriber_ids.length ] = $( this ).val();
				
			});
			
			var data = {
				'action':			'delete_pigeonpack_subscribers',
				'subscriber_ids':	subscriber_ids,
				'list_id':			$( 'input#post_ID' ).val(),
				'_wpnonce':			$( 'input#pigeonpack_list_nonce' ).val()
			};
			
			$.post( ajaxurl, data, function(response) {
				
				if ( 0 < parseInt( response ) ) {
						
					$.each( subscriber_ids, function( i, subscriber_id ) {
						
						$( 'tr#subscriber-' + subscriber_id ).remove();
						
					});

				}
				
			});
			
		}
		
	});
	
	function clear_subscriber_fields() {
	
		$( 'table#pigeonpack_list_new_subscriber_table input[type="text"]' ).each( function() {
			
			$( this ).val( '' );
			
		});	
	}
	
});