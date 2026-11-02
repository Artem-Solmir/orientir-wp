<?php
/**
 * Copy to wp-config.php and fill in production DB credentials and salts.
 * Local host orientir.local uses DB orientir / root (see $is_local block).
 */

$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$http_host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
$is_local = $http_host === 'orientir.local'
	|| (php_sapi_name() === 'cli' && getenv('APPLICATION_ENV') === 'development');

$db_name = 'your_production_db';
$db_user = 'your_production_db_user';
$db_password = 'your_production_db_password';

if ($is_local) {
	$db_name = 'orientir';
	$db_user = 'root';
	$db_password = 'root';
} else {
	define('WP_CACHE', true);
	define('W3TC_EDGE_MODE', true);
	define('WPCACHEHOME', '/path/to/wp-content/plugins/wp-super-cache/');
}

define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/** Generate at https://api.wordpress.org/secret-key/1.1/salt/ */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

$table_prefix = 'wp_';

if ($is_local) {
	define('WP_DEBUG', true);
	define('WP_DEBUG_DISPLAY', false);
	define('WP_DEBUG_LOG', true);
	error_reporting( E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE & ~E_USER_NOTICE & ~E_WARNING & ~E_USER_WARNING );
	define('WP_HOME', 'http://orientir.local');
	define('WP_SITEURL', 'http://orientir.local');
	define('FORCE_SSL_ADMIN', false);
	define('FS_METHOD', 'direct');
} else {
	define('WP_DEBUG', false);
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

require_once ABSPATH . 'wp-settings.php';
