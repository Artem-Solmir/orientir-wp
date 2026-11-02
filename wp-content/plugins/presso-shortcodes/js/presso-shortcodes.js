/* -----------------------------------------------------------------------------
 * Document ready
 * -------------------------------------------------------------------------- */
;(function( $, window, document, undefined ){
	"use strict";
	
	$( document ).ready( function ($) {

		// -----------------------------------------------------------------------------
		// Accordion
		// 
		$( ".accordion" ).each( function( i, e ) {
			var $this = $( e );
			var options = {
				heightStyle: 'content',
				header: '.accordion-header',
				collapsible: true
			}

			if ( $this.data( 'open' ) == true ) {
				options.active = 0;
			} else {
				options.active = false;
			}

			$this.accordion( options );
		} );

		// -----------------------------------------------------------------------------
		// Tabs
		//
		$( '.tabs' ).each( function( i, el ) {
			var $tabs = $( el );
			var is_tabs_initialed = false;
			$( '.tab-title', $tabs ).click( function( e ) {
				var $tab = $( this );
				var tab_selector = '#tabpanel'+$tab.data( 'tab-id' );
				var $content = $( tab_selector );

				if ( $content.length ) {
					$( '.active', $tabs ).removeClass( 'active' );
					$( '.tab-content', $tabs ).hide();
					$content.show();
					$( '.tab-id-'+$tab.data( 'tab-id' ), $tabs ).addClass( 'active' );
				}

				e.preventDefault();
				if ( is_tabs_initialed ) {
					if(history.pushState) {
				    	history.pushState(null, null, tab_selector);
					} else {
				    	location.hash = tab_selector;
					}
				}
			} );

			$( '.tab-title a', $tabs ).click( function( e ) {
				e.stopPropagation();
				return false;
			} );

			$( '.tab-title', $tabs ).eq( 0 ).trigger( 'click' );

			if ( $( '#'+location.hash.substring( 1 ) ).length ) {
				var tab_id = location.hash.slice( -1 );
				$( '.tab-title.tab-id-'+tab_id ).trigger( 'click' );
			}

			is_tabs_initialed = true;
		} );

	} );
})( jQuery, window , document );