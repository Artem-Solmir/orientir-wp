<?php

if ( ! function_exists( 'vw_notice_regenerate_thumbnail_on_version_2' ) ) {
	function vw_notice_regenerate_thumbnail_on_version_2() {
		$plugin_name = 'force-regenerate-thumbnails';
		$plugin_install_link = '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin_name . '&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="More info about ' . $plugin_name . '">this plugin</a>';

		$class = "update-nag";
		$message = "<strong>PRESSO Theme:</strong> Old theme data detected. Please regenerate all thumbnails by using " . $plugin_install_link . '| <a href="?dismiss_notice_regenerate_thumbnail_on_version_2=true">Hide Notice</a>';
		echo"<div class=\"$class\"> <p>$message</p></div>"; 
	}
}

add_action('admin_init', 'vw_dismiss_notice_regenerate_thumbnail_on_version_2');
if ( ! function_exists( 'vw_dismiss_notice_regenerate_thumbnail_on_version_2' ) ) {
	function vw_dismiss_notice_regenerate_thumbnail_on_version_2() {
		global $current_user;
		$user_id = $current_user->ID;

		// delete_user_meta( $user_id, 'vw_dismiss_regenerate_thumbnail_on_version_2' );
		// var_dump( get_user_meta( $user_id, 'vw_dismiss_regenerate_thumbnail_on_version_2', true ) );
		
		if ( get_user_meta( $user_id, 'vw_dismiss_regenerate_thumbnail_on_version_2', true ) !== 'true' ) {

			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset($_GET['dismiss_notice_regenerate_thumbnail_on_version_2']) && 'true' == $_GET['dismiss_notice_regenerate_thumbnail_on_version_2'] ) {
				add_user_meta( $user_id, 'vw_dismiss_regenerate_thumbnail_on_version_2', 'true', true );

				/* Gets where the user came from after they click Hide Notice */
				if ( wp_get_referer() ) {
					/* Redirects user to where they were before */
					wp_safe_redirect( wp_get_referer() );
				} else {
					/* This will never happen, I can almost gurantee it, but we should still have it just in case*/
					wp_safe_redirect( home_url() );
				}
			}

			add_action( 'admin_notices', 'vw_notice_regenerate_thumbnail_on_version_2' );
		}
	}
}

add_action( 'after_switch_theme', 'vw_force_dismiss_notice_regenerate_thumbnail_on_version_2' );
if ( ! function_exists( 'vw_force_dismiss_notice_regenerate_thumbnail_on_version_2' ) ) {
	function vw_force_dismiss_notice_regenerate_thumbnail_on_version_2() {
		$old_options = get_option( 'vw_presso_options', array() );

		if ( empty( $old_options ) ) {
			global $current_user;
			$user_id = $current_user->ID;
			add_user_meta( $user_id, 'vw_dismiss_regenerate_thumbnail_on_version_2', 'true', true );	
		}
	}
}