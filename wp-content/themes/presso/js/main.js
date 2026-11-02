/* -----------------------------------------------------------------------------
 * Document ready
 * -------------------------------------------------------------------------- */
;(function( $, window, document, undefined ){
	"use strict";
	
	$( document ).ready( function ($) {
		
		// Add swipe box into gallery
		$(".swipebox, .custom-gallery a, iv[id^=attachment_] a[href$=jpg], div[id^=attachment_] a[href$=jpeg], div[id^=attachment_] a[href$=png], .post-content a[href$=jpg], .post-content a[href$=png], .post-content a[href$=jpeg]").each( function( i, el ) {
			var $this = $( el );

			if ( ! $this.attr( 'title' ) ) {
				$this.attr( 'title', $this.find( 'img' ).attr( 'alt' ) );
			}
		}).swipebox();

		// Wordpress gallery grid
		$( '.custom-gallery' ).each( function ( i, el ) {
			var $gallery =  $( el );
			var layout = $gallery.attr( 'data-gallery-layout' );
			if ( ! ( parseInt( layout, 10 ) > 0 ) ) {
				layout = '213'; // Default layout
			}

			layout = layout.split('');
			var columnLayout = [];
			for (var i in layout ) {
				var columnCount = parseInt( layout[i], 10 );
				var columnWidth = 100.0 / columnCount;
				for ( var j = 1; j <= columnCount; j++ ) {
					columnLayout.push( columnWidth );
				}
			}

			$gallery.find( '> figure' ).each( function( i, el ) {
				var $el = $( el );
				var layoutIndex = i % columnLayout.length;
				$el.css( 'width', columnLayout[ layoutIndex ] - 1 + '%' );
			} );
		} );

		// Fit images
		$( '.vw-imgliquid' ).imgLiquid();

		// Show circle graph for review box
		$(".dial").knob();

		//  Instant search
		$(".instant-search-icon").instant_search();

		// Sticky top bar
		$(".top-bar").sticky();

		// carouFredSel
		$(".top-posts .top-posts-inner").carouFredSel({
			auto: true,
			swipe: true,
			mousewheel: true,
			align: 'left',
			width: '100%',
			items: {
				start: -1,
				width: 'auto',
				visible: function( visibleItems ) {
					var viewportWidth  = document.documentElement.clientWidth;

					/* md device */ if ( viewportWidth >= 992 ) return 4;
					/* sm device */if ( viewportWidth >= 768 ) return 3;
					return 2;
				},
			},
			scroll: {
				items: 1,
				easing: "quadratic",
				duration: 500,
				pauseOnHover: true,
				onAfter: function() {
					$.force_appear();
				}
			},
			prev: ".carousel-nav-prev",
			next: ".carousel-nav-next",
			onCreate: function( data ) {
				var $this = $(this)
				setTimeout( function() {
					$this.trigger("slideTo", 0);
				}, 250 );
			}
		});

		$(window).resize(function() {
			$(".top-posts .top-posts-inner").trigger('configuration', ['debug', false, true]);
		});

		// -----------------------------------------------------------------------------
		// Mobile navigation
		// 
		var $body = $('body');

		$('#open-mobile-nav').click( function( e ) {
			$body.toggleClass('mobile-nav-open');
			$( 'body,html' ).scrollTop( 0 );
			return false;
		} );

		var defaultWindowWidth = $(window).width();
		$(window).resize(function() {
			if ( defaultWindowWidth != $(window).width() ) {
				$body.removeClass('mobile-nav-open');
			}
		});

		var $mobile_nav = $('#mobile-nav-wrapper');

		var $clone_main_nav = $('#main-nav-wrapper').children().clone();
		$clone_main_nav.find( '.sub-posts' ).remove();
		$clone_main_nav = $clone_main_nav.removeAttr('id').removeClass('main-nav').addClass('mobile-nav');

		var $clone_top_nav = $('#top-nav-wrapper').children().clone();
		$clone_top_nav.find( '.sub-posts' ).remove();
		$clone_top_nav = $clone_top_nav.removeAttr('id').removeClass('top-nav').addClass('mobile-nav');

		$mobile_nav.append( $clone_main_nav, $( '<hr>' ), $clone_top_nav );

		// -----------------------------------------------------------------------------
		// Navigation Sub-menu
		// 
		$( '.main-nav .menu-item' ).hover(
			function() {
				$( '> .sub-menu' , this )
					.stop( true, true )
					.fadeIn( { duration: 250 } );

			}, function() {
				$( '> .sub-menu' , this )
					.stop( true, true )
					.fadeOut( { duration: 250 } );
		} );

		// -----------------------------------------------------------------------------
		// Fitvids - keep video ratio
		// 
		$( '.post-audio-wrapper, .post-video-wrapper, .flxmap-container, .comment-body, .post-content, #footer' ).fitVids( { customSelector: "iframe[src*='maps.google.']", ignore: '.vw-ignore-fitvids' });

		// -----------------------------------------------------------------------------
		// Isotope - Mansonry grid
		// 
		var $isotope_list = $('.vw-isotope');
		$isotope_list.imagesLoaded( function () {
			var options = {};
			if ( $( 'body' ).hasClass( 'rtl' ) ) {
				options.isOriginLeft = false;
			}

			$isotope_list.isotope( options );

			$(window).resize(function() {
				$isotope_list.isotope('layout');
			});
		});

		// -----------------------------------------------------------------------------
		// Back to top
		//
		$( '.back-to-top' ).click( function() {
			$( 'body,html' ).animate( {	scrollTop: 0 } );
			return false;
		} );

		// -----------------------------------------------------------------------------
		// Appear
		//
		$( 'body.site-enable-post-box-effects .post-box.animated-content' ).data( 'appear-top-offset', 100 ).on( 'appear', function( event, $all_appeared_elements ) {
			$all_appeared_elements.removeClass( 'animated-content' ).addClass( 'appeared' );
		} ).appear( { force_process: false } );

		if ( $( 'html' ).hasClass( 'no-touch' ) ) {
			$( document ).scrollTop( 1 );
		}

	} );
})( jQuery, window , document );

/**
 * No-touch detection
 */
if (!("ontouchstart" in document.documentElement)){ 
    document.documentElement.className += " no-touch"; 
}