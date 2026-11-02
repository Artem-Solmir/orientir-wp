+function ( $ ) { "use strict";
	var VWPC_PAGE_COMPOSER = {
		defaults: {
			selector: '#vwpc-container',
		},

		init: function( options ) {
			this.options = $.extend( {}, this.defaults, options );
			this.bind_proxy();

			this.$container = $( this.options.selector );

			this.init_add_section_menu();
			this.init_sections();
		},

		init_sections: function() {
			this.$sections = this.$container.find( '.vwpc-sections' );

			for ( var i = 1; i <= 50; i++ ) {
				var custom_field_name = 'vw_composer_'+i;
				var section_type = this.get_custom_field_value( custom_field_name );
				if ( ! section_type ) break;

				this.add_section( section_type, custom_field_name );
			}

			this.$sections.sortable( { handle: ".vwpc-section-handle, .vwpc-section-label", placeholder: 'vwpc-sortable-placeholder', forcePlaceholderSize: true } );
			this.$sections.find( '.vwpc-section-loading' ).remove();
		},

		init_add_section_menu: function() {
			this.$add_section_button = this.$container.find( '#add-section-button' ).dropdown();
			var $menu_list = this.$container.find( '.vwpc-toolbox .dropdown-menu' );
			
			$.each( vwpc_sections, function( section_type, section_settings ) {
				var $menu_item = $( '<li role="presentation"><a role="menuitem" tabindex="-1" href="#"></a></li>' );
				$menu_item.find( 'a' )
					.data( 'vwpc-section-type', section_type )
					.html( section_settings.title );
				$menu_list.append( $menu_item );
			} );
			$menu_list.find( 'a' ).click( this.on_click_add_section );
		},

		init_section: function( $new_section ) {
			$new_section.find( '.vwpc-section-bar, .vwpc-section-open-option' ).click( this.on_click_open_option );
			$new_section.find( '.vwpc-section-delete-section' ).click( this.on_click_delete_section );
		},

		add_section: function( section_type, custom_field_name ) {
			if ( 'undefined' === typeof vwpc_sections[section_type] ) return;
			
			var uuid = $.uuid();
			var id = 'vwpc_sections['+uuid+']';
			var $new_section = $( $( '#vwpc-template-section' ).html() );
			$new_section.find( '.vwpc-section-type' ).attr( 'name', id+'[_type]' ).val( section_type );
			$new_section.find( '.vwpc-section-order' ).val( uuid );
			$new_section.find( '.vwpc-section-label' ).html( vwpc_sections[section_type].title );

			var $new_option = this.render_section_options( section_type, id, custom_field_name );
			$new_section.find( '.vwpc-section-options' ).append( $new_option );

			this.$sections.append( $new_section );
			this.init_section( $new_section );

			return $new_section;
		},

		get_custom_field_value: function( custom_field_name ) {
			if ( ! custom_field_name ) return false;
			var $custom_field = $( '#postcustom input[value='+custom_field_name+']' );
			if ( ! $custom_field.length ) return false;

			var meta_id = $custom_field.attr( 'id' ).match(/[0-9]+/);
			return $( '#postcustom *[name=meta\\['+meta_id+'\\]\\[value\\]]' ).val();
		},

		render_section_options: function( section_type, id, custom_field_name ) {
			var self = this;
			var section_setting = vwpc_sections[ section_type ];
			var options = [];
			$.each( section_setting.options, function( name, option ) {
				var $new_option = $( $( '#vwpc-template-section-option' ).html() );	
				$new_option.find( '.vwpc-section-option-label' ).html( option.title );
				$new_option.find( '.vwpc-section-option-description' ).html( option.description );
				$new_option.find( '.vwpc-section-option-field-wrapper' ).append(
					self.render_field[ option.field ].call( self, option, id+'['+name+']', self.get_custom_field_value( custom_field_name+'_'+name ) )
				);	
				options.push( $new_option );
			} );

			return options;
		},

		bind_proxy: function() {
			this.on_click_add_section = $.proxy( this.on_click_add_section, this );
			this.on_click_delete_section = $.proxy( this.on_click_delete_section, this );
			this.on_click_open_option = $.proxy( this.on_click_open_option, this );
		},

		on_click_add_section: function( e ) {
			var section_type = $( e.target ).data( 'vwpc-section-type' );
			var $new_section = this.add_section( section_type, false );
			$new_section.find( '.vwpc-section-open-option' ).trigger( 'click' );
			this.$add_section_button.dropdown( 'toggle' );
			return false;
		},

		on_click_delete_section: function( e ) {
			$( e.target ).parents( '.vwpc-section' ).remove();
			return false;
		},

		on_click_open_option: function( e ) {
			var $section_options = $( e.target ).parents( '.vwpc-section' ).find( '.vwpc-section-options' );
			$section_options.slideToggle();
			return false;
		},

		show: function() {
			$( '#postdivrich' ).hide();
			this.$container.show();
		},

		hide: function() {
			$( '#postdivrich' ).show();
			this.$container.hide();
		},

		render_field: {

			select: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-select' ).html() );
				$.each( option.options, function( value, label ) {
					$field.append( $( '<option>' ).attr( 'value', value ).html( label ) );
				} );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},

			number: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-number' ).html() );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},

			text: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-text' ).html() );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},

			html: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-html' ).html() );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},

			category: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-category' ).html() );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},

			category_with_all_option: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-category_with_all_option' ).html() );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},

			categories: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-categories' ).html() );
				if ( ! value ) value = '';

				$field.find( 'input[type=checkbox]' ).each( function( i, el ) {
					$(el).attr( 'name', id+'[]' );
				} );
				
				var selected_checkboxes = value.split( ',' );
				$.each( selected_checkboxes, function( i, el ) {
					if ( ! el ) return;
					$field.find( 'input[value='+el+']' ).prop('checked', true);
				} );

				return $field;
			},

			sidebar: function( option, id, value ) {
				var $field = $( $( '#vwpc-template-field-sidebar' ).html() );
				$field.attr( 'name', id ).val( value || option.default );
				return $field;
			},
		},

	}

	$.extend( {
		page_composer: function( action ) {
			if ( 'show' == action ) {
				VWPC_PAGE_COMPOSER.show();
			} else if ( 'hide' == action ) {
				VWPC_PAGE_COMPOSER.hide();
			}
		},
		// vwsce_on_load: {},
	} );

	/* -----------------------------------------------------------------------------
	 * Document Ready
	 * -------------------------------------------------------------------------- */
	$( document ).ready( function() {
		VWPC_PAGE_COMPOSER.init();
	} );

}( window.jQuery );