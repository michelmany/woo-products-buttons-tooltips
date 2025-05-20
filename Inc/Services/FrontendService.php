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
		// Check if Carbon Fields function exists
		if ( ! function_exists( 'carbon_get_theme_option' ) ) {
			return $classes;
		}

		$selected_pages_raw = carbon_get_theme_option( 'crb_tooltip_pages' );
		$selected_page_ids = [];

		if ( is_array( $selected_pages_raw ) && ! empty( $selected_pages_raw ) ) {
			$selected_page_ids = array_map( function ( $page_assoc ) {
				return $page_assoc['id'] ?? null;
			}, $selected_pages_raw );
			$selected_page_ids = array_filter( $selected_page_ids ); // Remove nulls
		}

		$parent_page = get_page_by_path( 'standard-solutions/inorganic-solutionss' );

		if (
			is_page() &&
			(
				in_array( get_the_ID(), $selected_page_ids ) ||
				( $parent_page && $parent_page->ID === wp_get_post_parent_id( get_the_ID() ) )
			)
		) {
			$classes[] = 'apply-labkings-tooltips';
		}

		return $classes;
	}
}
