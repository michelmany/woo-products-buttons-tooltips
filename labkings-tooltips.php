<?php

/**
 * Plugin Name: LabKings Tooltips
 * Description: Add tooltips with price to products buttons
 * Version: 1.1.1
 * Author: Michel Many
 * Author URI: https://michelmany.com
 */

define( 'LABKINGS_TOOLTIPS_VERSION', '1.1.1' );

require_once plugin_dir_path( __FILE__ ) . 'Inc/LabKingsTooltipsServicesContainer.php';

use Inc\LabKingsTooltipsServicesContainer;
use Inc\Services\EnqueueService;
use Inc\Services\FrontendService;

$labKingsTooltipsServicesContainer = new LabKingsTooltipsServicesContainer();

$serviceNames = [
	EnqueueService::class,
	FrontendService::class,
];

foreach ( $serviceNames as $serviceName ) {
	$path = str_replace( '\\', '/', $serviceName );
	require_once plugin_dir_path( __FILE__ ) . "$path.php";
	$service = new $serviceName();
	$labKingsTooltipsServicesContainer->registerService( strtolower( ( new \ReflectionClass( $service ) )->getShortName() ),
		$service );
}

$labKingsTooltipsServicesContainer->run();