<?php
/**
 * Plugin Name: Orientir local dev helpers
 * Description: Disables legacy plugins and rewrites prod URLs on orientir.local.
 */

if ( ( $_SERVER['HTTP_HOST'] ?? '' ) !== 'orientir.local' ) {
	return;
}

const ORIENTIR_LOCAL_URL = 'http://orientir.local';

/**
 * @param string|string[] $plugins
 * @return string[]
 */
function orientir_local_filter_plugins( $plugins ) {
	if ( ! is_array( $plugins ) ) {
		return $plugins;
	}

	$disable = array(
		'dynamic-to-top/dynamic-to-top.php',
		'w3-total-cache/w3-total-cache.php',
		'wp-russian-typograph/wp-russian-typograph.php',
	);

	return array_values( array_diff( $plugins, $disable ) );
}

add_filter( 'option_active_plugins', 'orientir_local_filter_plugins', 1 );

add_filter(
	'site_option_active_sitewide_plugins',
	static function ( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}

		foreach ( array( 'w3-total-cache/w3-total-cache.php', 'wp-russian-typograph/wp-russian-typograph.php' ) as $plugin ) {
			unset( $plugins[ $plugin ] );
		}

		return $plugins;
	},
	1
);

/**
 * @param mixed $value
 * @return mixed
 */
function orientir_local_replace_prod_urls( $value ) {
	if ( is_string( $value ) ) {
		return str_replace(
			array( 'https://orientir.by', 'http://orientir.by' ),
			ORIENTIR_LOCAL_URL,
			$value
		);
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = orientir_local_replace_prod_urls( $item );
		}
	}

	return $value;
}

add_filter( 'option_vw_presso', 'orientir_local_replace_prod_urls' );
add_filter( 'theme_mods_presso', 'orientir_local_replace_prod_urls' );

// CDN enabler pointed uploads to production.
add_filter(
	'option_ossdl_off_cdn_url',
	static function () {
		return ORIENTIR_LOCAL_URL;
	}
);

add_filter( 'wp_get_attachment_url', 'orientir_local_replace_prod_urls' );
add_filter( 'the_content', 'orientir_local_replace_prod_urls', 999 );
add_filter( 'widget_text', 'orientir_local_replace_prod_urls', 999 );
add_filter( 'wp_nav_menu_items', 'orientir_local_replace_prod_urls', 999 );
