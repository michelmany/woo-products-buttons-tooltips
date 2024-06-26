<?php

namespace Inc\Services;

class EnqueueService {
	/**
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * @return void
	 */
	public function enqueue(): void {
		wp_enqueue_style(
			'labkings-tooltips-style',
			plugins_url( '../../assets/css/style.css', __FILE__ ),
			array(),
			LABKINGS_TOOLTIPS_VERSION,
		);

		wp_enqueue_script(
			'labkings-tooltips-axios',
			'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
			array( ),
			LABKINGS_TOOLTIPS_VERSION,
			true
		);

		wp_enqueue_script(
			'labkings-tooltips-scripts',
			plugins_url( '../../assets/js/scripts.js', __FILE__ ),
			array( 'labkings-tooltips-axios', 'jquery' ),
			LABKINGS_TOOLTIPS_VERSION,
			true
		);

		wp_localize_script( 'labkings-tooltips-scripts', 'ajax_var', array(
			'url'    => admin_url( 'admin-ajax.php' ),
			'nonce'  => wp_create_nonce( 'my-ajax-nonce' ),
			'action' => 'get_wc_products',
		) );
	}
}