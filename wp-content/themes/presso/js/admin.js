/* -----------------------------------------------------------------------------
 * Page Template Meta-box
 * -------------------------------------------------------------------------- */
;(function( $, window, document, undefined ){
	"use strict";
	
	$( document ).ready( function () {
		$( '#vw_review' ).review_editor();

		/* -----------------------------------------------------------------------------
		 * Post format
		 * -------------------------------------------------------------------------- */
		$('#post-formats-select input[type="radio"]').live( 'change', function() {
			var val = $(this).val();
			if( val == '0' ) {
				val = 'standard';
			}

			$( '#vw_post_format_gallery' ).hide();
			$( '#vw_post_format_audio' ).hide();
			$( '#vw_post_format_video' ).hide();

			if ( 'gallery' == val ) {
				$( '#vw_post_format_gallery' ).show();
			} else if ( 'audio' == val ) {
				$( '#vw_post_format_audio' ).show();
			} else if ( 'video' == val ) {
				$( '#vw_post_format_video' ).show();
			}
		} ).filter(':checked').trigger( 'change' );
		
		/* -----------------------------------------------------------------------------
		 * Page template
		 * -------------------------------------------------------------------------- */
		$( '#page_template' ).change( function() {
			var template = $( '#page_template' ).val();

			// Page Composer Template
			if ( 'page_composer.php' == template || /page_composer.php$/.test( template ) ) {
				
				$.page_composer( 'show' );
				$( '#vw_page_options' ).hide();

			} else {
				$.page_composer( 'hide' );
				$( '#vw_page_options' ).show();
			}
		} ).triggerHandler( 'change' );

		// -----------------------------------------------------------------------------
		// Fitvids - keep video ratio
		// 
		$( '.postbox .embed-code' ).fitVids( { customSelector: "iframe[src*='maps.google.'], iframe[src*='soundcloud.']" });

	} );
})( jQuery, window , document );

/* -----------------------------------------------------------------------------
 * Review Editor
 * -------------------------------------------------------------------------- */
;(function( $, window, document, undefined ){
	var REVIEW_EDITOR = {

		init: function( el ) {
			this.on_click_add_row = $.proxy( this.on_click_add_row, this );
			this.on_click_delete_row = $.proxy( this.on_click_delete_row, this );
			this.on_click_enable_review = $.proxy( this.on_click_enable_review, this );

			this.$metabox = $( '#vw_review' );
			this.$metabox.find( '#add-score' ).click( this.on_click_add_row );
			this.$placeholder = $( '.rwmb-review_score-wrapper .review-scores' );
			this.init_items();
			this.$placeholder.sortable( { handle: ".move-icon" } );

			this.$metabox.find( '#vw_enable_review' ).click( this.on_click_enable_review ).triggerHandler( 'click' );
		},

		init_items: function() {
			var $inputs = $( '#postcustom input[type=text]' );
			for ( var i = 1; i <= 10; i++ ) {
				// Get label value
				var $label = $inputs.filter( '[value=vw_review_score_'+i+'_label]' );
				if ( ! $label.length ) break;
				var meta_id = $label.attr( 'id' ).match(/[0-9]+/);
				var label_value = $( '#postcustom *[name=meta\\['+meta_id+'\\]\\[value\\]]' ).val();

				// Get score value
				var $score = $inputs.filter( '[value=vw_review_score_'+i+'_score]' );
				if ( ! $score.length ) break;
				meta_id = $score.attr( 'id' ).match(/[0-9]+/);
				var score_value = $( '#postcustom *[name=meta\\['+meta_id+'\\]\\[value\\]]' ).val();

				this.add_row( label_value, score_value );
			};
		},

		init_row: function( $row ) {
			$row.find( '.delete-icon' ).click( this.on_click_delete_row );
		},

		add_row: function( label, score ) {
			var id = $.uuid();
			var $row = $(
				'<div id="'+id+'" class="review-score-row">'
					+ '<i class="move-icon icon-entypo-menu"></i>'
					+ '<span class="review-score-label">Label</span>'
					+ '<input type="text" class="rwmb-text" name="vw_review_score_label['+id+']">'
					+ '<span class="review-score-label">Score</span>'
					+ '<input type="number" class="rwmb-number" name="vw_review_score_score['+id+']" step="0.1" min="0" max="10" placeholder="">'
					+ '<i class="delete-icon icon-entypo-cancel"></i>'
				+'</div>'
				);
			$row.find( '.rwmb-text' ).val( label );
			$row.find( '.rwmb-number' ).val( score );
			this.$placeholder.append( $row );
			this.init_row( $row );
		},

		on_click_add_row: function() {
			this.add_row( '', 5 );
		},

		on_click_delete_row: function( e ) {
			var $icon = $( e.target );
			$icon.parents( '.review-score-row' ).remove();
		},

		on_click_enable_review: function( e ) {
			var $checkbox = $( e.target );
			var $fields = this.$metabox.find( '.field-review-summary, .field-review-score' );

			if ( $checkbox.is( ':checked' ) ) {
				$fields.show();
			} else {
				$fields.hide();
			}
		},
	}

	$.fn.review_editor = function() {
		return this.each(function() {
			var review_editor = $.extend( {}, REVIEW_EDITOR );

			review_editor.init( this );
		});
	};
})( jQuery, window , document );


/* -----------------------------------------------------------------------------
 * Media button
 * -------------------------------------------------------------------------- */
;(function( $, window, document, undefined ){
	var MEDIA_BOX_BUTTON = {
		defaults: {
			callback: null
		},

		init: function( el, options ) {
			this.options = $.extend({}, this.defaults, options);

			this.onClickButton = $.proxy( this.onClickButton, this );
			this.onSendToEditor = $.proxy( this.onSendToEditor, this );
			this.$button = $( el );

			this.$button.click( this.onClickButton );
		},

		onClickButton: function( e ) {
			if (e.button == 0) { // Only left click
				this.wp_send_to_editor = window.send_to_editor;
				window.send_to_editor = this.onSendToEditor;

				tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
				return false;
			}
		},

		onSendToEditor: function( html ) {
			var imgurl = $( 'img', '<div>'+html+'</div>' ).attr( 'src' );

			if ( this.options.callback && "function" === typeof(this.options.callback) ) {
				this.options.callback( imgurl, html );
			}

			tb_remove();
			window.send_to_editor = this.wp_send_to_editor;
		},
	}

	$.fn.media_box_button = function( arg1, arg2 ) {
		return this.each(function() {
			var media_box = $.extend({}, MEDIA_BOX_BUTTON);

			if ( "function" === typeof( arg1 ) ) {
				media_box.init( this, $.extend( { callback: arg1 }, arg2 ) );
			} else {
				media_box.init( this, arg1 );
			}
		});
	};
})( jQuery, window , document );

/* -----------------------------------------------------------------------------
 * UUID
 * https://github.com/eburtsev/jquery-uuid/blob/master/jquery-uuid.js
 * -------------------------------------------------------------------------- */
;(function( $, window, document, undefined ){
	$.uuid = function() {
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
			return v.toString(16);
		});
	};
})( jQuery, window , document );

/**
 * ReplaceAll by Fagner Brack (MIT Licensed)
 * Replaces all occurrences of a substring in a string
 * Calling: "".replaceAll(token, newToken, ignoreCase=boolean)
 */
String.prototype.replaceAll=function(c,e,d){var a,b=-1;if((a=this.toString())&&"string"===typeof c)for(d=!0===d?c.toLowerCase():void 0;-1!==(b=void 0!==d?a.toLowerCase().indexOf(d,0<=b?b+e.length:0):a.indexOf(c,0<=b?b+e.length:0));)a=a.substring(0,b).concat(e).concat(a.substring(b+c.length));return a};

/* .stripHTML() */
// String.prototype.stripHTML = function() { return this.replace(/<[^>]+>/ig,""); };