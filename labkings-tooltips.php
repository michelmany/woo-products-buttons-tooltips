<?php
/**
 * Plugin Name: LabKings Tooltips
 * Description: Add tooltips with price to products buttons
 * Version: 1.3.0
 * Author: Michel Many
 * Author URI: https://michelmany.com
 * Text Domain: labkings-tooltips
 * Domain Path: /languages
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'LABKINGS_TOOLTIPS_VERSION', '1.3.0' );
define( 'LABKINGS_TOOLTIPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include the Composer autoloader
if ( file_exists( LABKINGS_TOOLTIPS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once LABKINGS_TOOLTIPS_PLUGIN_DIR . 'vendor/autoload.php';
} else {
	// Optional: Add an admin notice if composer dependencies are missing
	add_action( 'admin_notices', function () {
		?>
        <div class="notice notice-error">
            <p><?php _e( 'LabKings Tooltips plugin is missing its dependencies. Please run <code>composer install</code> in the plugin directory.',
					'labkings-tooltips' ); ?></p>
        </div>
		<?php
	} );
	error_log( 'LabKings Tooltips: CRITICAL - vendor/autoload.php NOT FOUND. Please run "composer install".' );

	return; // Stop execution if autoloader is not found
}

// Initialize Carbon Fields
add_action( 'after_setup_theme', function () {
	\Carbon_Fields\Carbon_Fields::boot();
	// error_log("LabKings Tooltips: Carbon Fields booted via Composer."); // Optional debug
} );

// Load plugin textdomain for translations
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'labkings-tooltips', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

// No need to require Inc/LabKingsTooltipsServicesContainer.php manually anymore,
// Composer's autoloader will handle it.

use Inc\LabKingsTooltipsServicesContainer;
use Inc\Services\EnqueueService;
use Inc\Services\FrontendService;
use Inc\Services\AdminSettingsService;

// Check if the main container class exists (autoloaded)
if ( ! class_exists( LabKingsTooltipsServicesContainer::class ) ) {
	error_log( 'LabKings Tooltips: CRITICAL - LabKingsTooltipsServicesContainer class not found. Check PSR-4 autoloading setup.' );

	return;
}

$labKingsTooltipsServicesContainer = new LabKingsTooltipsServicesContainer();

$serviceNames = [
	EnqueueService::class,
	FrontendService::class,
	AdminSettingsService::class,
];

foreach ( $serviceNames as $serviceName ) {
	// Autoloader handles finding the class files.
	// We just need to ensure the class exists before instantiating.
	if ( class_exists( $serviceName ) ) {
		$service = new $serviceName();
		$labKingsTooltipsServicesContainer->registerService( strtolower( ( new \ReflectionClass( $service ) )->getShortName() ),
			$service );
		// error_log("LabKings Tooltips: Service '$serviceName' instantiated and registered via autoloader."); // Optional debug
	} else {
		error_log( "LabKings Tooltips: CRITICAL - Service class '$serviceName' NOT FOUND. Check namespace or autoloader." );
	}
}

$labKingsTooltipsServicesContainer->run();
// error_log("LabKings Tooltips: Services run method called."); // Optional debug
