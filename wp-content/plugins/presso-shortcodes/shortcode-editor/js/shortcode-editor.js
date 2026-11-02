+function ( $ ) { "use strict";
	var _vwsce_shortcode_editor;
	var VWSCE_SHORTCODE_EDITOR = {
		defaultOptions: {
			title: 'Shortcode Editor',
			saveButton: 'Insert Shortcode',
			callback: undefined,
		},
		init: function() {
			$( '.switch-tmce' ).trigger( 'click' );
			this.bindProxy();
			this.init_modal();
			$( '.vwsce-save-shortcode', this.$modal ).click( this.onClickSaveEditor );
		},

		init_modal: function() {
			var title = '<h4 class="vwsce-modal-title">Modal title</h4>';
			var html = '<div class="vwsce-modal fade">';
			html += '<div class="vwsce-modal-dialog">';
			html += '<div class="vwsce-modal-content">';
			html += '<div class="vwsce-modal-header">'+title+'</div>';
			
			html += '<div class="vwsce-modal-body">'+'Loading...'+'</div>';
			
			html += '<div class="vwsce-modal-footer">'
						+'<button type="button" class="vwsce-save-shortcode button button-primary">Save changes</button>'
					+'</div>';

			html += '</div>'; // modal-content
			html += '</div>'; // modal-dialog
			html += '</div>'; // modal

			this.$modal = $( html );
			$( 'body' ).append( this.$modal );
			this.$modal.on( 'hidden.bs.modal', this.onModalHidden );
		},

		bindProxy: function() {
			this.onClickSaveEditor = $.proxy( this.onClickSaveEditor, this );
			this.saveShortCode = $.proxy( this.saveShortCode, this );
			this.onModalHidden = $.proxy( this.onModalHidden, this );
		},

		onClickSaveEditor: function() {
			this.$modal.modal( 'hide' );
		},

		saveShortCode: function() {
			$( '.switch-html', this.$modal ).trigger( 'click' );
			
			var main_tag = $( '#main_tag', this.$modal ).val();
			var atts = '';
			var shortcode_content = '';
			var shortcode_obj = {};
			var self = this;
			$( '.vwsce-attr', this.$modal ).each( function( i, el ) {
				var $field = $( this );
				var attr_name = $field.attr( 'name' );

				// Skip if it is the default value
				if ( ( ! $field.is( 'img' ) && $field.val() == String( $field.data( 'vwsce-attr-default' ) ) )
					|| $field.is( '.vwsce-render-as-content' ) ) {
					return;
				}

				if ( $field.is( 'input[type="text"]' ) ) {
					atts += ' '+self.createAttribute( attr_name, $field.val() );
					shortcode_obj[attr_name] = $field.val();

				} else if ( $field.is( 'textarea.wp-editor-area' ) ) {
					atts += ' '+self.createAttribute( attr_name, $field.val() );
					shortcode_obj[attr_name] = $field.val();

				} else if ( $field.is( 'img' ) ) {
					if ( $field.attr( 'src' ) != $field.data( 'vwsce-attr-default' ) ) {
						atts += ' '+self.createAttribute( attr_name, $field.attr( 'src' ) );
						shortcode_obj[attr_name] = $field.attr( 'src' );
					}

				} else if ( $field.is( 'select' ) ) {
					atts += ' '+self.createAttribute( attr_name, $field.val() );
					shortcode_obj[attr_name] = $field.val();

				} else {
					atts += ' '+self.createAttribute( attr_name, $field.val() );
					shortcode_obj[attr_name] = $field.val();

				}
			} );

			$( '.vwsce-attr.vwsce-render-as-content', this.$modal ).each( function( i, el ) {
				var $field = $( this );
				shortcode_content += $field.val();
				if ( ! shortcode_obj['content'] ) shortcode_obj['content'] = '';
				shortcode_obj['content'] += $field.val();
			} );
			
			if ( this.callback && "function" === typeof(this.callback) ) {
				this.callback( '['+main_tag+atts+']'+shortcode_content+'[/'+main_tag+']', shortcode_obj );
			}
		},

		onModalHidden: function() {
			this.saveShortCode();
			this.$modal.remove();
		},

		onLoad: function() {
			var self = this;
			$.each( $.vwsce_on_load, function( slug, callback ) {
				if ( typeof callback == 'function' ) {
					callback.call( self );
				}
			} );
			this.$modal.find('select, input[type=text], input[type=checkbox], textarea, radio').filter(':eq(0)').focus();
		},

		open: function( content, options ) {
			options = $.extend( this.defaultOptions, options );
			this.callback = options.callback;

			var self = this;
			$.ajax( vwsce_ajax.ajaxurl, {
				type: 'POST',
				data: { content: content, action: 'vwsce_build_editor' },
				success: function( data ) {
					$( '.vwsce-modal-body', self.$modal ).html( data );
					self.onLoad();
				}
			} );

			$( '.vwsce-modal-title', this.$modal ).html( options.title );
			$( '.vwsce-save-shortcode', this.$modal ).html( options.saveButton );
			this.$modal.modal( 'show' );
		},

		createAttribute: function( attr, val ) {
			return attr+"='"+val.replace(/\'/g, "\u2019")+"'";
		},

	}

	$.extend( {
		shortcode_editor: function( content, options ) {
			var _vwsce_shortcode_editor = $.extend( {}, VWSCE_SHORTCODE_EDITOR );
			_vwsce_shortcode_editor.init();
			_vwsce_shortcode_editor.open( content, options );
		},
		vwsce_on_load: {},
	} );

	$.vwsce_on_load.init_media_button = function() {
		var $placeholder = $( 'img.placeholder', this.$modal );
		var $select_button = $( '.button.vwsce-insert-media', this.$modal );
		var $remove_button = $( '.button.vwsce-remove-media', this.$modal );

		if ( $placeholder.attr( 'src' ) ) {
			$placeholder.show();
		} else {
			$placeholder.hide();
		}

		$select_button.click( function( e ) {
			event.preventDefault();
			var file_frame;
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}
		
			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: jQuery( this ).data( 'uploader_title' ) || 'Select Image',
				button: {
				text: jQuery( this ).data( 'uploader_button_text' ) || 'Select',
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});
		
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				var attachment = file_frame.state().get('selection').first().toJSON();
				$placeholder.attr( 'src', attachment.url ).show();
			});
		
			// Finally, open the modal
			file_frame.open();
			
		} );

		$remove_button.click( function( e ) {
			e.preventDefault();
			$placeholder.attr( 'src', '' ).hide();
		} );
	};

	$.vwsce_on_load.init_tinymce = function() {
		var textarea = $( 'textarea.wp-editor-area', this.$modal );

		if ( ! textarea.length ) return;
		
		var textfield_id = textarea.attr("id");
		var settings	= {id: textfield_id , buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close"};
		quicktags(settings);
		QTags._buttonsInit(); //workaround since dom ready was triggered already and there would be no initialization

		if ( 3 == tinyMCE.majorVersion ) {
			tinymce.EditorManager.execCommand( 'mceRemoveControl', true, textfield_id );
			tinymce.EditorManager.execCommand( 'mceAddControl', true, textfield_id );
		} else {
			tinymce.EditorManager.execCommand( 'mceRemoveEditor', true, textfield_id );
			tinymce.EditorManager.execCommand( 'mceAddEditor', true, textfield_id );
		}

		tinymce.get( textfield_id ).setContent( window.switchEditors.wpautop( textarea.val() ), {format : 'raw'});
		$( '.switch-html', this.$modal ).trigger( 'click' );
		$( '.switch-tmce', this.$modal ).trigger( 'click' );
	};

	$.vwsce_on_load.init_color_picker = function() {
		$( '.color-picker', this.$modal ).wpColorPicker();
	};

	$.vwsce_on_load.init_icon_fields = function() {
		$( '.vwsce-field-icon .vwsce-available-icons', this.$modal ).each( function ( i, el ) {
			$( el ).find( 'i' ).click( function () {
				var $this = $( this );
				var icon_name = $this.data( 'vwsce-icon-name' ).replace(/^icon-/, '' );
				$this.parent().find( 'i' ).removeClass( 'selected-icon' );
				$this.closest( '.vwsce-field-icon' ).find( 'input.vwsce-attr' ).val( icon_name );
				$this.addClass( 'selected-icon' );
			} );
		} );
	}

}( window.jQuery );