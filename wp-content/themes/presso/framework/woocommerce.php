<?php
/* -----------------------------------------------------------------------------
 * WooCommerce
 * -------------------------------------------------------------------------- */

// define('WOOCOMMERCE_USE_CSS', false);

if ( ! function_exists( 'vw_setup_woocommerce' ) ) {
	function vw_setup_woocommerce() {
		add_theme_support( 'woocommerce' );

		// remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		// remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

		add_filter( 'woocommerce_show_page_title', '__return_false' );
	}
	add_action( 'after_setup_theme', 'vw_setup_woocommerce' );
}

if ( ! function_exists( 'vw_setup_woocommerce_image_dimensions' ) ) {
	function vw_setup_woocommerce_image_dimensions() {
		$catalog = array( // vw_square_medium size
			'width' 	=> '750',	// px
			'height'	=> '750',	// px
			'crop'		=> 1 		// true
		);
	 
		$single = array(
			'width' 	=> '750',	// px
			'height'	=> '750',	// px
			'crop'		=> 1 		// true
		);
	 
		$thumbnail = array(
			'width' 	=> '360',	// px
			'height'	=> '360',	// px
			'crop'		=> 0 		// false
		);
	 
		// Image sizes
		update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
		update_option( 'shop_single_image_size', $single ); 		// Single product image
		update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
	}
	add_action( 'after_switch_theme', 'vw_setup_woocommerce_image_dimensions' );
}

add_filter( 'woocommerce_breadcrumb_defaults', 'vw_change_breadcrumb_delimiter' );
function vw_change_breadcrumb_delimiter( $defaults ) {
	// Change the breadcrumb delimiter from '/' to '>'
	$defaults['delimiter'] = ' <span class="breadcrumb-delimiter">/</span> ';
	$defaults['wrap_before'] = '<div class="vw-woocommerce-breadcrumb header-font" itemprop="breadcrumb">';
	$defaults['wrap_after'] = '</div>';
	$defaults['home'] = __( 'Shop', 'woocommerce' );
	return $defaults;
}

add_filter( 'woocommerce_breadcrumb_home_url', 'vw_woo_custom_breadrumb_home_url' );
function vw_woo_custom_breadrumb_home_url() {
    return get_permalink( woocommerce_get_page_id( 'shop' ) );
}