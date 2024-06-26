<?php

namespace Inc;

class LabKingsTooltipsServicesContainer {
	private array $services;

	public function __construct() {
		$this->services = array();
	}

	/**
	 * @param $name
	 * @param $service
	 *
	 * @return void
	 */
	public function registerService( $name, $service ): void {
		$this->services[ $name ] = $service;
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function getService( $name ) {
		return $this->services[ $name ];
	}

	/**
	 * @return void
	 */
	public function run(): void {
		foreach ( $this->services as $service ) {
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}
}