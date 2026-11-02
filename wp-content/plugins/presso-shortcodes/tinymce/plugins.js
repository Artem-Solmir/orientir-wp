;(function( $, window, document, undefined ){
	"use strict";

	if ( 3 == tinyMCE.majorVersion ) {
		tinymce.create( 'tinymce.plugins.vw_shortcodes', {
			init : function(ed, url) {
				this.editor = ed;
				this.insertShortCode = $.proxy( this.insertShortCode, this );
			},
			createControl : function(n, cm) {
	
				if( 'content' == this.editor.editorId && n=='vw_shortcodes'){
					var mlb = cm.createMenuButton('vw_shortcodes', {
						title : 'Shortcodes',
					});

					var self = this;
					mlb.onRenderMenu.add( function ( button, menu ) {
						$.each( vw_theme_shortcodes, function( i, shortcode_settings ) {
							if ( shortcode_settings.instant_shortcode ) {
								menu.add( { title: shortcode_settings.title, onclick: function() {
									self.insertShortCode( shortcode_settings.instant_shortcode );
								} } );
								
							} else if ( 'undefined' === typeof shortcode_settings.submenu ) {
								// Add single menu
								menu.add( { title: shortcode_settings.title, onclick: function() {
									$.shortcode_editor( shortcode_settings.shortcode, {
										title: shortcode_settings.title,
										callback: self.insertShortCode,
									} );
								} } );

							} else {
								// Add parent menu and submenu
								var parentMenu = menu.addMenu( { title: shortcode_settings.title } );
								$.each( shortcode_settings.submenu, function( j, shortcode_settings ) {

									if ( shortcode_settings.instant_shortcode ) {
										parentMenu.add( { title: shortcode_settings.title, onclick: function() {
											self.insertShortCode( shortcode_settings.instant_shortcode );
										} } );
										
									} else {
										parentMenu.add( { title: shortcode_settings.title, onclick: function() {
											$.shortcode_editor( shortcode_settings.shortcode, {
												title: shortcode_settings.title,
												callback: self.insertShortCode,
											} );
										} } );
									}
								});
							}
						} );
					} ) ;
					return mlb;
				}
				return null;
			},
			insertShortCode: function( shortcode ) {
				if ( shortcode ) this.editor.execCommand( 'mceInsertContent', false, shortcode );
			}
		});

		tinymce.PluginManager.add( 'vw_shortcodes', tinymce.plugins.vw_shortcodes );

	} else { // TinyMCE v4.0+

		tinymce.PluginManager.add( 'vw_shortcodes', function( editor ) {
			var menu = [];
			var insertShortCode = function ( shortcode ) {
				if ( shortcode ) editor.execCommand( 'mceInsertContent', false, shortcode );
			}

			$.each( vw_theme_shortcodes, function( i, shortcode_settings ) {
					if ( shortcode_settings.instant_shortcode ) {
						menu.push( { text: shortcode_settings.title, onclick: function() {
							insertShortCode( shortcode_settings.instant_shortcode );
						} } );
						
					} else if ( 'undefined' === typeof shortcode_settings.submenu ) {
						// Add single menu
						menu.push( { text: shortcode_settings.title, onclick: function() {
							$.shortcode_editor( shortcode_settings.shortcode, {
								title: shortcode_settings.title,
								callback: insertShortCode,
							} );
						} } );

					} else {
						// Add parent menu and submenu
						var parentMenu = { text: shortcode_settings.title, menu: [] };
						// var parentMenu = menu.addMenu( { title: shortcode_settings.title } );
						$.each( shortcode_settings.submenu, function( j, shortcode_settings ) {

							if ( shortcode_settings.instant_shortcode ) {
								parentMenu.menu.push( { text: shortcode_settings.title, onclick: function() {
									insertShortCode( shortcode_settings.instant_shortcode );
								} } );
								
							} else {
								parentMenu.menu.push( { text: shortcode_settings.title, onclick: function() {
									$.shortcode_editor( shortcode_settings.shortcode, {
										title: shortcode_settings.title,
										callback: insertShortCode,
									} );
								} } );
							}
						});

						menu.push( parentMenu );
					}
				} );

			editor.addButton( 'vw_shortcodes', {
				type: 'menubutton',
				tooltip: 'Presso Shortcodes',
				title: 'Presso Shortcodes',
				icon: 'https://pbs.twimg.com/profile_images/1898529068/gravatar_1_normal.jpg',
				classes: 'vw_shortcodes btn widget', // This overwrites all classes on the container!
				menu: menu,
			});

		});
	}


})( jQuery, window , document );