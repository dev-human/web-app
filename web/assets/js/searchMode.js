/**
* Search mode with the transparent overlay over the whole site
* @return void
*/

define( [ 'jquery' ], function ( $ ) {
	$( '.js--toggle-search-mode' ).on( 'click', function ( ev ) {
		ev.preventDefault();

		$( 'body' ).toggleClass( 'search-mode' );

		if ( $( 'body' ).hasClass( 'search-mode' ) ) {
			// set focus to the text field
			setTimeout(function () {
				$('.js--search-panel-text').focus();
			}, 10);

			// on escape key leave the search mode
			$( document ).on( 'keyup.searchMode', function( ev ) {
				ev.preventDefault();
				if ( ev.keyCode === 27 ){
					$( 'body' ).toggleClass( 'search-mode' );
					$( document ).off( 'keyup.searchMode' );
				}
			} );
		} else {
			$( document ).off( 'keyup.searchMode' );
		}
	} );
} );