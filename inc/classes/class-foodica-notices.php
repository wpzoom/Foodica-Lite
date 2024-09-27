<?php
/**
 * Theme admin notices
 *
 * @package Foodica
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main PHP class for admin notices
 */
class foodica_Notices {
	/**
	 * Notice name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Notice type
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Notice permanent dismiss URL
	 *
	 * @var string
	 */
	public $dismiss_url;

	/**
	 * Current user ID
	 *
	 * @var int
	 */
	public $current_user_id;

	/**
	 * The constructor.
	 *
	 * @param string $name Notice Name.
	 * @param string $type Notice type.
	 * @param string $dismiss_url Notice permanent dismiss URL.
	 */
	public function __construct( $name, $type, $dismiss_url ) {
		$this->name            = $name;
		$this->type            = $type;
		$this->dismiss_url     = $dismiss_url;
		$this->current_user_id = get_current_user_id();
	}

	/**
	 * Display notice
	 *
	 * @return void
	 */
	public function notice() {
		if ( ! $this->is_dismiss_notice() ) {
			$this->notice_markup();
		}
	}

	/**
	 * Receive notice dismiss
	 * Provided filter 'foodica_{$notice_name}_notice_dismiss' to allow developers to hook into it
	 *
	 * @return boolean
	 */
	private function is_dismiss_notice() {
		return apply_filters( 'foodica_' . $this->name . '_notice_dismiss', true );
	}

	/**
	 * Notice markup
	 *
	 * @return void
	 */
	public function notice_markup() {
		echo '';
	}

	/**
	 * Get theme admin notices stored to option 'foodica_admin_notices'
	 *
	 * @return array Theme admin notices
	 */
	public function get_notices() {
		$foodica_theme_admin_notices = get_option( 'foodica_admin_notices' );
		return $foodica_theme_admin_notices;
	}

	/**
	 * Receive notice dismiss status
	 *
	 * @param int $notice_id Notice ID.
	 * @return boolean
	 */
	public function get_notice_status( $notice_id ) {
		$theme_admin_notices = $this->get_notices();

		if ( is_array( $theme_admin_notices ) && in_array( $notice_id, $theme_admin_notices ) ) {
			$this_notice_was_dismissed = true;
		} else {
			$this_notice_was_dismissed = false;
		}

		return $this_notice_was_dismissed;
	}

	/**
	 * Hide a notice if the GET variable is set
	 *
	 * @return void
	 */
	public function hide_notices() {
		if ( isset( $_GET['foodica-hide-notice'] ) && isset( $_GET['_foodica_notice_nonce'] ) ) {
			if ( ! check_admin_referer( 'foodica_hide_notices_nonce', '_foodica_notice_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'foodica' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Cheatin&#8217; huh?', 'foodica' ) );
			}

			if ( wp_unslash( $_GET['foodica-hide-notice'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$hide_notice_id = sanitize_text_field( wp_unslash( $_GET['foodica-hide-notice'] ) );

				$theme_admin_notices = $this->get_notices();

				if ( is_array( $theme_admin_notices ) ) {
					if ( ! in_array( $hide_notice_id, $theme_admin_notices ) ) {
						// this notice has never been dismissed before.
						$theme_admin_notices[] = $hide_notice_id;
						$run_update            = true;
					}
				} else {
					// This is the first time a theme admin notice is being dismissed.
					$theme_admin_notices   = array();
					$theme_admin_notices[] = $hide_notice_id;
					$run_update            = true;
				}

				if ( isset( $run_update ) ) {
					update_option( 'foodica_admin_notices', $theme_admin_notices );
				}
			}
		}
	}

}
