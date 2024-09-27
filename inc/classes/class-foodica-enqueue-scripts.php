<?php
/**
 * Load scripts & styles
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Foodica
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Foodica_Enqueue_Scripts_Admin' ) ) {

	/**
	 * foodica_Enqueue_Scripts initial setup
	 *
	 * @since 1.0.0
	 */
	class Foodica_Enqueue_Scripts_Admin {

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		}

		/**
		 * Enqueue scripts and styles for all admin pages.
		 */
		public function admin_scripts($hook) {

            wp_enqueue_style( 'foodica-admin', get_template_directory_uri() . '/assets/admin/css/admin.css' );

            if ( 'appearance_page_page-foodica' != $hook ) {

               wp_enqueue_script("jquery-ui");
               wp_enqueue_script("jquery-ui-tabs");

            }
		}


	}

	new Foodica_Enqueue_Scripts_Admin();
}
