<?php
/*
Plugin Name: Presso Shortcodes
Plugin URI: http://envirra.com
Description: Shortcodes for Presso
Version: 1.0.3
Author: Envirra
Author URI: http://envirra.com
*/

define( 'VWSC_ROOT', plugin_dir_path( __FILE__ ) );

// Get the plugin path without resolve the symbolic link
if ( false === strpos( __FILE__, WP_CONTENT_DIR ) ) {

	if ( isset( $mu_plugin ) ) {
		$vwsc_plugins_url = $mu_plugin;
	} else if ( isset( $network_plugin ) ) {
		$vwsc_plugins_url = $network_plugin;
	} else if ( isset( $plugin ) ) {
		$vwsc_plugins_url = $plugin;
	}

} else {
	$vwsc_plugins_url = __FILE__;
}

define( 'VWSC_URI', plugins_url( '' , $vwsc_plugins_url ) );

require_once VWSC_ROOT.'/tinymce/plugins.php';
require_once VWSC_ROOT.'/shortcode-editor/shortcode-editor.php';
require_once VWSC_ROOT.'/shortcodes.php';

/* -----------------------------------------------------------------------------
 * Enqueue Front-end Assets
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwsc_enqueue_front_assets' ) ) {
	function vwsc_enqueue_front_assets() {
		wp_enqueue_script( 'vwscjs-main', VWSC_URI.'/js/presso-shortcodes.js', array(
			'jquery',
			'jquery-effects-fade',
			'jquery-ui-accordion',
			'jquery-ui-tabs',
		), VW_THEME_VERSION, true );
	}
	add_action( 'wp_enqueue_scripts', 'vwsc_enqueue_front_assets', 11 );
}