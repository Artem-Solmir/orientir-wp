<?php

add_action( 'after_setup_theme', 'vw_setup_assets' );
function vw_setup_assets() {
	add_action( 'wp_enqueue_scripts', 'vw_register_assets', 9 );
	add_action( 'admin_enqueue_scripts', 'vw_register_assets', 9 );
	add_action( 'wp_enqueue_scripts', 'vw_enqueue_front_assets', 11 );
	add_action( 'admin_enqueue_scripts', 'vw_enqueue_admin_assets', 11 );
}

/* -----------------------------------------------------------------------------
 * Register Assets
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_register_assets' ) ) {
	function vw_register_assets() {
		wp_register_script( 'vwjs-fitvids', get_template_directory_uri().'/js/jquery.fitvids.js', array( 'jquery' ), VW_THEME_VERSION, true );
		wp_register_script( 'vwjs-isotope', get_template_directory_uri().'/js/jquery.isotope.min.js', array( 'jquery' ), VW_THEME_VERSION, true );
		
		wp_register_script( 'vwjs-flexslider', get_template_directory_uri().'/framework/flexslider/jquery.flexslider.js', array( 'jquery' ), VW_THEME_VERSION, true );
		wp_register_style( 'vwcss-flexslider', get_template_directory_uri().'/framework/flexslider/flexslider-custom.css', array(), VW_THEME_VERSION );
		
		wp_register_script( 'vwjs-swipebox', get_template_directory_uri().'/framework/swipebox/jquery.swipebox.min.js', array( 'jquery' ), VW_THEME_VERSION, true );
		wp_register_script( 'vwjs-asset', get_template_directory_uri().'/js/asset.js', array( 'jquery' ), VW_THEME_VERSION, true );

		wp_register_script( 'vwjs-bootstrap', get_template_directory_uri().'/framework/bootstrap/js/bootstrap.min.js', array( 'jquery', 'jquery-effects-core' ), VW_THEME_VERSION, true );
		wp_register_style( 'vwcss-bootstrap', get_template_directory_uri().'/framework/bootstrap/css/bootstrap.css', array(), VW_THEME_VERSION );

		wp_register_script( 'vwjs-bootstrap-admin', get_template_directory_uri().'/framework/bootstrap-admin/bootstrap.min.js', array( 'jquery', 'jquery-effects-core' ), VW_THEME_VERSION, true );
		wp_register_style( 'vwcss-bootstrap-admin', get_template_directory_uri().'/framework/bootstrap-admin/bootstrap.css', array(), VW_THEME_VERSION );

		wp_register_style( 'vwcss-swipebox', get_template_directory_uri().'/framework/swipebox/swipebox.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-entypo', get_template_directory_uri().'/framework/font-icons/entypo/css/entypo.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-social', get_template_directory_uri().'/framework/font-icons/social-icons/css/zocial.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-symbol', get_template_directory_uri().'/framework/font-icons/symbol/css/symbol.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-typicons', get_template_directory_uri().'/framework/font-icons/typicons/css/typicons.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-awesome', get_template_directory_uri().'/framework/font-icons/awesome/css/awesome.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-iconic', get_template_directory_uri().'/framework/font-icons/iconic/css/iconic.css', array(), VW_THEME_VERSION );
		wp_register_style( 'vwcss-icon-elusive', get_template_directory_uri().'/framework/font-icons/elusive/css/elusive.css', array(), VW_THEME_VERSION );
	}
}

/* -----------------------------------------------------------------------------
 * Enqueue Front-end Assets
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_enqueue_front_assets' ) ) {
	function vw_enqueue_front_assets() {
		wp_enqueue_script( 'vwjs-main', get_template_directory_uri().'/js/main.js', array(
			'jquery',
			'jquery-effects-fade',
			'vwjs-fitvids',
			'vwjs-isotope',
			'vwjs-flexslider',
			'vwjs-swipebox',
			'vwjs-asset',
		), VW_THEME_VERSION, true );

		if ( vw_get_option( 'icon_elusive' ) ) wp_enqueue_style( 'vwcss-icon-elusive' );
		if ( vw_get_option( 'icon_awesome' ) ) wp_enqueue_style( 'vwcss-icon-awesome' );
		if ( vw_get_option( 'icon_iconic' ) ) wp_enqueue_style( 'vwcss-icon-iconic' );
		if ( vw_get_option( 'icon_typicons' ) ) wp_enqueue_style( 'vwcss-icon-typicons' );

		wp_enqueue_style( 'vwcss-theme', get_template_directory_uri().'/css/theme.css', array(
			'vwcss-flexslider',
			'vwcss-icon-social',
			'vwcss-icon-entypo',
			'vwcss-icon-symbol',
			'vwcss-swipebox',
			'vwcss-bootstrap',
		), VW_THEME_VERSION );
	}
}

/* -----------------------------------------------------------------------------
 * Enqueue Admin Assets
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_enqueue_admin_assets' ) ) {
	function vw_enqueue_admin_assets() {
		wp_enqueue_script( 'vwjs-admin', get_template_directory_uri().'/js/admin.js', array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-effects-fold',
			'wp-color-picker',
			'vwjs-fitvids',
			'vwjs-bootstrap-admin',

		), VW_THEME_VERSION, true );

		// Avoid problem in redux framework
		$screen = get_current_screen();
		if ( $screen->id != 'toplevel_page_vw_theme_options' ) {
			wp_enqueue_style( 'vwcss-bootstrap-admin' );
		}

		wp_enqueue_style( 'vwcss-admin', get_template_directory_uri().'/css/admin.css', array(
			'wp-color-picker',
			'vwcss-icon-entypo',
		), VW_THEME_VERSION );

	}
}