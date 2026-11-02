<?php

defined( 'VW_DEMO_IMPORT_THEME_OPTION_PAGE' ) || define( 'VW_DEMO_IMPORT_THEME_OPTION_PAGE', 'toplevel_page_vw_theme_options' );

if ( ! function_exists( 'vwmigrate_theme_options' ) ) {
	function vwmigrate_theme_options() {
		$old_options = get_option( 'vw_presso_options', array() );
		$new_options = get_option( 'vw_presso', array() );

		if ( empty( $old_options ) ) {
			echo 'There is no existing theme options from old theme version. Importing is aborted.';
			die();
		}

		$new_options[ "site_enable_open_graph" ] = $old_options[ "site_enable_open_graph" ];
		$new_options[ "site_enable_rtl" ] = $old_options[ "site_enable_rtl" ];
		$new_options[ "site_enable_post_box_effects" ] = $old_options[ "site_enable_post_box_effects" ];
		$new_options[ "site_enable_meta_description" ] = $old_options[ "site_enable_meta_description" ];
		$new_options[ "tracking_code" ] = $old_options[ "tracking_code" ];
		$new_options[ "site_layout" ] = $old_options[ "site_layout" ];
		$new_options[ "header_layout" ] = $old_options[ "header_layout" ];
		$new_options[ "header_ads_code" ] = $old_options[ "header_ads_code" ];
		$new_options[ "copyright_text" ] = $old_options[ "copyright_text" ];
		$new_options[ "blog_excerpt_length" ] = $old_options[ "blog_excerpt_length" ];
		$new_options[ "blog_layout" ] = $old_options[ "blog_layout" ];
		$new_options[ "blog_sidebar" ] = $old_options[ "blog_sidebar" ];
		$new_options[ "archive_enable_post_slider" ] = $old_options[ "archive_enable_post_slider" ];
		$new_options[ "blog_show_featured_image_single_post" ] = $old_options[ "blog_show_featured_image_single_post" ];
		$new_options[ "blog_show_video_player_for_thumbnail" ] = $old_options[ "blog_show_video_player_for_thumbnail" ];
		$new_options[ "blog_show_posts_at_top" ] = $old_options[ "blog_show_posts_at_top" ];
		$new_options[ "blog_enable_author_info" ] = $old_options[ "blog_enable_author_info" ];
		$new_options[ "blog_enable_related_post" ] = $old_options[ "blog_enable_related_post" ];
		$new_options[ "blog_enable_custom_gallery" ] = $old_options[ "blog_enable_custom_gallery" ];
		$new_options[ "blog_custom_gallery_layout" ] = $old_options[ "blog_custom_gallery_layout" ];
		$new_options[ "blog_enable_sharebox" ] = $old_options[ "blog_enable_sharebox" ];
		$new_options[ "sharebox_delicious" ] = $old_options[ "sharebox_delicious" ];
		$new_options[ "sharebox_digg" ] = $old_options[ "sharebox_digg" ];
		$new_options[ "sharebox_email" ] = $old_options[ "sharebox_email" ];
		$new_options[ "sharebox_facebook" ] = $old_options[ "sharebox_facebook" ];
		$new_options[ "sharebox_googleplus" ] = $old_options[ "sharebox_googleplus" ];
		$new_options[ "sharebox_linkedin" ] = $old_options[ "sharebox_linkedin" ];
		$new_options[ "sharebox_pinterest" ] = $old_options[ "sharebox_pinterest" ];
		$new_options[ "sharebox_reddit" ] = $old_options[ "sharebox_reddit" ];
		$new_options[ "sharebox_tumblr" ] = $old_options[ "sharebox_tumblr" ];
		$new_options[ "sharebox_twitter" ] = $old_options[ "sharebox_twitter" ];

		$new_options[ "h1" ][ "color" ] = $old_options[ "h1" ][ "color" ];
		$new_options[ "h1" ][ "font-family" ] = $old_options[ "h1" ][ "family" ];
		$new_options[ "h1" ][ "font-weight" ] = $old_options[ "h1" ][ "weight" ];
		// $new_options[ "h1" ][ "font-backup" ] = "";
		// $new_options[ "h1" ][ "font-options" ] = "";
		// $new_options[ "h1" ][ "font-style" ] = "";
		// $new_options[ "h1" ][ "google" ] = "";
		// $new_options[ "h1" ][ "subsets" ] = "";

		$new_options[ "body" ][ "color" ] = $old_options[ "body" ][ "color" ];
		$new_options[ "body" ][ "font-family" ] = $old_options[ "body" ][ "family" ];
		$new_options[ "body" ][ "font-size" ] = $old_options[ "body" ][ "size" ];
		$new_options[ "body" ][ "font-weight" ] = $old_options[ "body" ][ "weight" ];
		// $new_options[ "body" ][ "font-backup" ] = "";
		// $new_options[ "body" ][ "font-options" ] = "";
		// $new_options[ "body" ][ "font-style" ] = "";
		// $new_options[ "body" ][ "google" ] = "";
		// $new_options[ "body" ][ "subsets" ] = "";

		$new_options[ "custom_font1_ttf" ] = $old_options[ "custom_font1_ttf" ];
		$new_options[ "custom_font1_woff" ] = $old_options[ "custom_font1_woff" ];
		$new_options[ "custom_font1_svg" ] = $old_options[ "custom_font1_svg" ];
		$new_options[ "custom_font1_eot" ] = $old_options[ "custom_font1_eot" ];
		$new_options[ "custom_font2_ttf" ] = $old_options[ "custom_font2_ttf" ];
		$new_options[ "custom_font2_woff" ] = $old_options[ "custom_font2_woff" ];
		$new_options[ "custom_font2_svg" ] = $old_options[ "custom_font2_svg" ];
		$new_options[ "custom_font2_eot" ] = $old_options[ "custom_font2_eot" ];
		// $new_options[ "logo" ] = $old_options[ "logo" ];
		// $new_options[ "logo_2x" ] = $old_options[ "logo_2x" ];
		// $new_options[ "fav_icon" ] = $old_options[ "fav_icon" ];
		// $new_options[ "fav_icon_iphone" ] = $old_options[ "fav_icon_iphone" ];
		// $new_options[ "fav_icon_iphone_retina" ] = $old_options[ "fav_icon_iphone_retina" ];
		// $new_options[ "fav_icon_ipad" ] = $old_options[ "fav_icon_ipad" ];
		// $new_options[ "fav_icon_ipad_retina" ] = $old_options[ "fav_icon_ipad_retina" ];
		$new_options[ "icon_iconic" ] = $old_options[ "icon_iconic" ];
		$new_options[ "icon_elusive" ] = $old_options[ "icon_elusive" ];
		$new_options[ "icon_awesome" ] = $old_options[ "icon_awesome" ];
		$new_options[ "icon_typicons" ] = $old_options[ "icon_typicons" ];
		$new_options[ "social_delicious" ] = $old_options[ "social_delicious" ];
		$new_options[ "social_digg" ] = $old_options[ "social_digg" ];
		$new_options[ "social_dribbble" ] = $old_options[ "social_dribbble" ];
		$new_options[ "social_facebook" ] = $old_options[ "social_facebook" ];
		$new_options[ "social_flickr" ] = $old_options[ "social_flickr" ];
		$new_options[ "social_forrst" ] = $old_options[ "social_forrst" ];
		$new_options[ "social_github" ] = $old_options[ "social_github" ];
		$new_options[ "social_googleplus" ] = $old_options[ "social_googleplus" ];
		$new_options[ "social_instagram" ] = $old_options[ "social_instagram" ];
		$new_options[ "social_linkedin" ] = $old_options[ "social_linkedin" ];
		$new_options[ "social_pinterest" ] = $old_options[ "social_pinterest" ];
		$new_options[ "social_rss" ] = $old_options[ "social_rss" ];
		$new_options[ "social_skype" ] = $old_options[ "social_skype" ];
		$new_options[ "social_tumblr" ] = $old_options[ "social_tumblr" ];
		$new_options[ "social_twitter" ] = $old_options[ "social_twitter" ];
		$new_options[ "social_vimeo" ] = $old_options[ "social_vimeo" ];
		$new_options[ "social_yahoo" ] = $old_options[ "social_yahoo" ];
		$new_options[ "social_youtube" ] = $old_options[ "social_youtube" ];
		$new_options[ "social_vk" ] = $old_options[ "social_vk" ];
		$new_options[ "social_ok" ] = $old_options[ "social_ok" ];
		$new_options[ "flexslider_slideshow" ] = $old_options[ "flexslider_slideshow" ];
		$new_options[ "flexslider_randomize" ] = $old_options[ "flexslider_randomize" ];
		$new_options[ "flexslider_pauseonhover" ] = $old_options[ "flexslider_pauseonhover" ];
		$new_options[ "flexslider_animation" ] = $old_options[ "flexslider_animation" ];
		$new_options[ "flexslider_easing" ] = $old_options[ "flexslider_easing" ];
		$new_options[ "flexslider_slideshowspeed" ] = $old_options[ "flexslider_slideshowspeed" ];
		$new_options[ "flexslider_animationspeed" ] = $old_options[ "flexslider_animationspeed" ];
		$new_options[ "color_primary" ] = $old_options[ "color_primary" ];
		$new_options[ "color_topbar_bg" ] = $old_options[ "color_topbar_bg" ];
		$new_options[ "color_topbar_font" ] = $old_options[ "color_topbar_font" ];
		$new_options[ "color_topbar_highlight" ] = $old_options[ "color_topbar_highlight" ];
		$new_options[ "color_topbar_highlight_font" ] = $old_options[ "color_topbar_highlight_font" ];
		$new_options[ "color_header_bg" ] = $old_options[ "color_header_bg" ];
		$new_options[ "color_header_font" ] = $old_options[ "color_header_font" ];
		$new_options[ "color_nav_bg" ] = $old_options[ "color_nav_bg" ];
		$new_options[ "color_nav_font" ] = $old_options[ "color_nav_font" ];
		$new_options[ "color_nav_highlight" ] = $old_options[ "color_nav_highlight" ];
		$new_options[ "color_nav_highlight_font" ] = $old_options[ "color_nav_highlight_font" ];
		$new_options[ "color_footer_bg" ] = $old_options[ "color_footer_bg" ];
		$new_options[ "color_footer_font" ] = $old_options[ "color_footer_font" ];
		$new_options[ "color_footer_heading" ] = $old_options[ "color_footer_heading" ];
		$new_options[ "color_copyright_bg" ] = $old_options[ "color_copyright_bg" ];
		$new_options[ "color_copyright_font" ] = $old_options[ "color_copyright_font" ];
		$new_options[ "bbpress_enable_sidebar" ] = $old_options[ "bbpress_enable_sidebar" ];
		$new_options[ "bbpress_sidebar" ] = $old_options[ "bbpress_sidebar" ];
		$new_options[ "custom_css" ] = $old_options[ "custom_css" ];
		$new_options[ "custom_js" ] = $old_options[ "custom_js" ];

		update_option( 'vw_presso', $new_options );

		echo '<p>Successful. Please refresh the page.</p><p><i>Old theme options has been imported. Some option may not be imported. Please review all options again.</i></p>';
		die();
	}
	add_action( 'wp_ajax_vwmigrate_theme_options', 'vwmigrate_theme_options' );
}

if ( ! function_exists( 'vwmigrate_js' ) ) {
	function vwmigrate_js( $hook_suffix ) {
		// var_dump($hook_suffix);
		// appearance_page_redux_options
		if( $hook_suffix == VW_DEMO_IMPORT_THEME_OPTION_PAGE ) {
			$old_options = get_option( 'vw_presso_options', array() );

			if ( empty( $old_options ) ) { return; }

			wp_enqueue_script('jquery');
    		wp_enqueue_script('thickbox');
    		wp_enqueue_style('thickbox');

			add_action('admin_footer', 'vwmigrate_footer_script');
		}
	}

	add_action('admin_enqueue_scripts', 'vwmigrate_js');
}

if ( ! function_exists( 'vwmigrate_footer_script' ) ) {
	function vwmigrate_footer_script() {
		?>
		<script type="text/javascript">
		//<![CDATA[
		;(function( $, window, document, undefined ){
			$( ' <span>&nbsp;</span> <a id="vw_migrate_theme_options" class="thickbox button" title="Migrate theme options" href="<?php echo admin_url( 'admin-ajax.php?action=vwmigrate_theme_options&amp;width=full&amp;height=full' ) ?>">Migrate old options</a>' ).appendTo( $( '.redux-container .redux-action_bar' ) );
			
			$( document ).ready( function ($) {
				// var template_directory = '<?php echo get_template_directory_uri() ?>';
				$( '#vw_migrate_theme_options' ).click( function( e ) {
					if ( ! confirm( 'Are you sure to import old theme options? The existing theme options will be replaced.' ) ) {
						e.stopPropagation();
						return false;
					}
				} )
			} );
		})( jQuery, window , document );
		//]]>
		</script>
		<?php
	}
}