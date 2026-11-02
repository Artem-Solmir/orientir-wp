<?php
/* -----------------------------------------------------------------------------
 * RTL
 * -------------------------------------------------------------------------- */
add_action( 'init', 'vw_setup_rtl' );
if ( ! function_exists( 'vw_setup_rtl' ) ) {
	function vw_setup_rtl() {
		$site_enable_rtl = vw_get_option( 'site_enable_rtl' );

		if ( $site_enable_rtl ) {
			wp_enqueue_style( 'vwcss-bootstrap-rtl', get_template_directory_uri().'/css/bootstrap-rtl.css', array( 'vwcss-theme' ), VW_THEME_VERSION );
			wp_enqueue_style( 'vwcss-rtl', get_template_directory_uri().'/css/rtl.css', array( 'vwcss-theme' ), VW_THEME_VERSION );
			add_filter( 'body_class', 'vw_body_class_rtl' );
		}
	}
}

/* -----------------------------------------------------------------------------
 * Add Body Classes
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_body_class_rtl' ) ) {
	function vw_body_class_rtl( $classes ) {
		$classes[] = 'rtl';

		return $classes;
	}
}