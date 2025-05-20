<?php

namespace Inc\Services;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class AdminSettingsService {
	/**
	 * @return void
	 */
	public function register(): void {
		add_action( 'carbon_fields_register_fields', array( $this, 'create_plugin_options_page' ) );
	}

	/**
	 * @return void
	 */
	public function create_plugin_options_page(): void {
		Container::make( 'theme_options', __( 'LabKings Tooltips Settings', 'labkings-tooltips' ) )
		         ->set_page_menu_title( __( 'LabKings Tooltips', 'labkings-tooltips' ) ) // Title for the top-level menu
		         ->set_icon( 'dashicons-admin-comments' ) // You can choose any dashicon
		         ->add_fields( array(
				Field::make( 'association', 'crb_tooltip_pages', __( 'Enable Tooltips on Pages', 'labkings-tooltips' ) )
				     ->set_help_text( __( 'Select the pages where the product tooltips functionality should be active.',
					     'labkings-tooltips' ) )
				     ->set_types( array(
					     array(
						     'type'      => 'post',
						     'post_type' => 'page',
					     ),
				     ) ),
			) );
	}
}
