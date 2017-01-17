<?php
/**
 * Installs WordPress for running the tests and loads WordPress and the test libraries
 */
$_tests_dir   = getenv( 'WP_TESTS_DIR' );
$_wp_core_dir = getenv( 'WP_CORE_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

if ( ! $_wp_core_dir ) {
    $_wp_core_dir = '/tmp/wordpress';
}


$config_file_path = $_tests_dir;
if ( ! file_exists( $_tests_dir . '/wp-tests-config.php' ) ) {
	// Support the config file from the root of the develop repository.
	if ( basename( $config_file_path ) === 'phpunit' && basename( dirname( $config_file_path ) ) === 'tests' )
		$config_file_path = dirname( dirname( $config_file_path ) );
}
$config_file_path .= '/wp-tests-config.php';

/*
 * Globalize some WordPress variables, because PHPUnit loads this file inside a function
 * See: https://github.com/sebastianbergmann/phpunit/issues/325
 */
global $wpdb, $current_site, $current_blog, $wp_rewrite, $shortcode_tags, $wp, $phpmailer;

if ( !is_readable( $config_file_path ) ) {
	die( "ERROR: wp-tests-config.php is missing! Please use wp-tests-config-sample.php to create a config file.\n" );
}
require_once $config_file_path;

define( 'WP_TESTS_TABLE_PREFIX', $table_prefix );
define( 'DIR_TESTDATA', dirname( __FILE__ ) . '/../data' );

if ( ! defined( 'WP_TESTS_FORCE_KNOWN_BUGS' ) )
	define( 'WP_TESTS_FORCE_KNOWN_BUGS', false );

// Cron tries to make an HTTP request to the blog, which always fails, because tests are run in CLI mode only
define( 'DISABLE_WP_CRON', true );

define( 'WP_MEMORY_LIMIT', -1 );
define( 'WP_MAX_MEMORY_LIMIT', -1 );

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = WP_TESTS_DOMAIN;
$_SERVER['SERVER_NAME'] = WP_TESTS_DOMAIN;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

if ( "1" == getenv( 'WP_MULTISITE' ) ||
	( defined( 'WP_TESTS_MULTISITE') && WP_TESTS_MULTISITE ) ) {
	$multisite = true;
} else {
	$multisite = false;
}

// Override the PHPMailer
require_once( $_tests_dir. '/includes/mock-mailer.php' );
$phpmailer = new MockPHPMailer();
system( WP_PHP_BINARY . ' ' . escapeshellarg( $_tests_dir . '/includes/install.php' ) . ' ' . escapeshellarg( $config_file_path ) . ' ' . $multisite );
//shell_exec("./vendor/wp-cli/wp-cli/bin/wp plugin install woocommerce --path='$_wp_core_dir' --activate");
