<?php 

if ( defined( 'EWPT_PLUGIN_DIR' ) ) {
	require_once 'class-envato-wordpress-theme-upgrader.php';
	add_action( 'admin_init', 'vw_envato_toolkit_check' );
}

/* -----------------------------------------------------------------------------
 * Display a notice in the admin to remind the user to enter their credentials
 * -------------------------------------------------------------------------- */
function vw_envato_toolkit_credentials_admin_notices() {
	$message = sprintf( __( "To enable theme update notifications, please enter your Envato Marketplace credentials in the %s", "default" ),
	"<a href='" . admin_url() . "admin.php?page=envato-wordpress-toolkit'>Envato WordPress Toolkit Plugin</a>" );
	echo "<div id='message' class='updated below-h2'><p>{$message}</p></div>";
}

/* -----------------------------------------------------------------------------
 * Display a notice in the admin that an update is available
 * -------------------------------------------------------------------------- */
function envato_toolkit_admin_notices() {
	$message = sprintf( __( "An update to the theme is available! Head over to %s to update it now.", "default" ),
		"<a href='" . admin_url() . "admin.php?page=envato-wordpress-toolkit'>Envato WordPress Toolkit Plugin</a>" );
	echo "<div id='message' class='updated below-h2'><p>{$message}</p></div>";
}

/* -----------------------------------------------------------------------------
 * Check for theme update
 * -------------------------------------------------------------------------- */
function vw_envato_toolkit_check() {
	// Use credentials used in toolkit plugin so that we don't have to show our own forms anymore
	$credentials = get_option( 'envato-wordpress-toolkit' );
	if ( empty( $credentials['user_name'] ) || empty( $credentials['api_key'] ) ) {
		add_action( 'admin_notices', 'vw_envato_toolkit_credentials_admin_notices' );
		return;
	}

	// Check updates only after a while
	$lastCheck = get_option( 'toolkit-last-toolkit-check' );
	if ( false === $lastCheck ) {
		update_option( 'toolkit-last-toolkit-check', time() );
		return;
	}

	// Check for an update every 3 hours
	if ( 10800 < ( time() - $lastCheck ) ) {
		return;
	}

	// Update the time we last checked
	update_option( 'toolkit-last-toolkit-check', time() );

	// Check for updates
	$upgrader = new Envato_WordPress_Theme_Upgrader( $credentials['user_name'], $credentials['api_key'] );
	$updates = $upgrader->check_for_theme_update();

	// If $updates->updated_themes_count == true then we have an update!
	
	// Add update alert, to update the theme
	if ( $updates->updated_themes_count ) {
		add_action( 'admin_notices', 'envato_toolkit_admin_notices' );
	}
}