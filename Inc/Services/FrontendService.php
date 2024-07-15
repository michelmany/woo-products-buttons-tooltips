<?php

namespace Inc\Services;

use WC_Product_Query;

class FrontendService {
	/**
	 * @return void
	 */
	public function register(): void {
		add_filter( 'body_class', array( $this, 'add_custom_body_class' ) );

		add_action( 'wp_ajax_get_wc_products', array( $this, 'get_wc_products' ) );
		add_action( 'wp_ajax_nopriv_get_wc_products', array( $this, 'get_wc_products' ) );
	}

	public function add_custom_body_class( $classes ) {
		$parent_page = get_page_by_path( 'standard-solutions/inorganic-solutionss' );

		if ( $parent_page && is_page() && $parent_page->ID === wp_get_post_parent_id( get_the_ID() ) ) {
			$classes[] = 'apply-labkings-tooltips';
		}

		return $classes;
	}
}