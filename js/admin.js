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
	
});