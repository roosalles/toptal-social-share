/**
 * Toptal Social Share Plugin JS
 */
jQuery( document ).ready( function( $ ) {

	$( '.tss-share-buttons a.share-button' ).on( 'click', function( e ) {
		
		// For WhatsApp, just follow the link
		if ( $( this ).hasClass( 'whatsapp' ) ) {
			return;
		}
		
		e.preventDefault();
		
		// We don't open popup for pinterest, use their mechanism instead
		if ( $( this ).hasClass( 'pinterest' ) ) {
			return;
		}
		
		var url = $( this ).prop( 'href' );
		tssPopupWindow( url, 'Toptal Social Share', 500, 450);

	});	
	
	// Check if our container for featured image is present on the page
	if ( $( '.tss-featured-image-container' ).length ) {
		
		// Share button on click..
		$( '.tss-featured-image-container .tss-featured-image-toggle' ).click( function( e ) {
			e.preventDefault();
		    
			var shareButtons = $( '.tss-featured-image-container .tss-share-buttons' );
			var shareButtonsHeight =  shareButtons.height();
			
			// Animate elements
			if ( $( this ).hasClass( 'open' ) ) {

				$( this ).text( 'Share' );
				$( this ).animate( 
					{ 
						bottom                  : '0px',
						borderTopLeftRadius     : '4px',
			    		borderTopRightRadius    : '4px',
			    		borderBottomLeftRadius  : '0px',
			    		borderBottomRightRadius : '0px'
					},
					200
				);
				shareButtons.slideUp( 200 );
		
		    } else {
		    	$( this ).text( 'Close' );
		    	$( this ).animate(
		    		{ 
		    			bottom                  : '-' + $( this ).height() + 'px', 
						borderTopLeftRadius     : '0px',
			    		borderTopRightRadius    : '0px',
			    		borderBottomLeftRadius  : '4px',
			    		borderBottomRightRadius : '4px'
		    		}, 
		    		200
		    	);
		    	shareButtons.css( 'display', 'flex' ).hide().slideDown( 200 );
		    }

			// Toggle our buttons
		    $( this ).toggleClass( 'open' );
			
		    return false;
		});		
	}
	
});

// Helper function to open popup centered
function tssPopupWindow( url, title, w, h) {
	var left = ( screen.width/2 ) - ( w/2 );
	var top = ( screen.height/2 ) - ( h/2 );
	return window.open( url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left );
} 