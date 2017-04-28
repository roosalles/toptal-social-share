/**
 * Toptal Social Share Plugin Admin JS
 */
jQuery( document ).ready( function( $ ) {

	// Function to update our hidden field (ordered networks)
	var updateOrder = function( event, ui ) {
		
		var $network = $( '#tss-sortable-networks' ).sortable( 'toArray', { attribute: 'data-network' } )
		$( '#tss-ordered-networks' ).val( $network );
	}	

	// Make list of networks sortable, update field when order is changed.
	$( '#tss-sortable-networks' ).sortable( {
		create: updateOrder,		
		update: updateOrder
	});
	
	// Execute wpColorPicker for all color picker fields
	$( '#tss-color-picker' ).wpColorPicker();
	
	// Show/Hide Color picker on user selection/click
	$( '#tss-use-custom-color-field' ).on( 'click', function() {
		$( '#tss-color-picker-wrapper' ).toggle();
	});
	
});