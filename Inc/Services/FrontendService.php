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

	/**
	 * @return void
	 */
	public function get_wc_products(): void {

		check_ajax_referer('labkings-tooltips-ajax-nonce', 'nonce');

		$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
		$products_per_page = 100; // Number of products to load per request
		$offset = ($page - 1) * $products_per_page;

	    if (!isset($_POST['skus'])) {
	        error_log('No SKUs provided in the AJAX call.');
	        wp_send_json_error('No SKUs provided.');
	        return;
	    }

		$skus = $_POST['skus']; // Get the SKUs from the AJAX call

		$products = array();
		foreach ($skus as $sku) {
			$args = array(
//				'limit' => $products_per_page,
//				'offset' => $offset,
				'sku' => $sku,
				'status' => 'publish',
			);
			$product = wc_get_products($args);
			if (!empty($product)) {
				$products[] = $product[0];
			} else {
				error_log('No product found for SKU: ' . $sku);
			}
		}

		if (empty($products)) {
			error_log('No products found for provided SKUs.');
			wp_send_json_error('No products found.');
			return;
		}

		$product_details = array();
		foreach ($products as $product) {
			$product_details[$product->get_sku()] = array(
				'price' => wc_price($product->get_price()),
			);
		}

		if (empty($product_details)) {
			error_log('No product details found.');
			wp_send_json_error('No product details found.');
			return;
		}

		wp_send_json_success($product_details);
//
//		$sku = $_POST['sku'];
//		$id = wc_get_product_id_by_sku( $sku );
//
//		if ( ! $id ) {
//			return;
//		}
//
//		$product = wc_get_product( $id );
//		$price = $product->get_price();
//		echo wc_price($price);
//		die();

//		$args = array(
//			'limit'   => 100,
//			'orderby' => 'date',
//			'order'   => 'DESC',
//		);
//
//
//		$query = new WC_Product_Query( $args );
//		$query->set( 'sku', 'LK1' );
//		$products = $query->get_products();
//
//	    foreach ($products as $product) {
//	        $skus[] = $product->get_sku();
//	    }
//
//	    echo json_encode($skus);
//	    die();

	}
}