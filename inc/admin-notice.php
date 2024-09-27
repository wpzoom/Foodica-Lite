<?php
/**
 * Admin notice after Theme activation
 *
 * @package Foodica
 * @subpackage foodica_Lite
 * @since Foodica 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'foodica_admin_notice' ) ) {
	/**
	 * Welcome Notice after Theme Activation
	 *
	 * @return void
	 */
	function foodica_admin_notice() {
		global $pagenow, $foodica_version;

		$welcome_notice        = get_option( 'foodica_notice_welcome' );
		$current_user_can      = current_user_can( 'edit_theme_options' );
		$should_display_notice = ( $current_user_can && 'index.php' === $pagenow && ! $welcome_notice ) || ( $current_user_can && 'themes.php' === $pagenow && isset( $_GET['activated'] ) && ! $welcome_notice ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $should_display_notice ) {
            wp_enqueue_style( 'welcome-notice', get_template_directory_uri() . '/assets/admin/css/welcome-notice.css' );

			foodica_welcome_notice();
		}
	}
}
add_action( 'admin_notices', 'foodica_admin_notice' );

if ( ! function_exists( 'foodica_hide_notice' ) ) {
	/**
	 * Hide Welcome Notice in WordPress Dashboard
	 *
	 * @return void
	 */
	function foodica_hide_notice() {
		if ( isset( $_GET['foodica-hide-notice'] ) && isset( $_GET['_foodica_notice_nonce'] ) ) {
			if ( ! check_admin_referer( 'foodica_hide_notices_nonce', '_foodica_notice_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'foodica' ) );
			}

			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( esc_html__( 'You do not have the necessary permission to perform this action.', 'foodica' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['foodica-hide-notice'] ) );
			update_option( 'foodica_notice_' . $hide_notice, 1 );
		}
	}
}
add_action( 'wp_loaded', 'foodica_hide_notice' );

if ( ! function_exists( 'foodica_welcome_notice' ) ) {
	/**
	 * Content of Welcome Notice in WordPress Dashboard
	 *
	 * @return void
	 */
	function foodica_welcome_notice() {
		?>
		<div class="notice wpz-welcome-notice">
			<a class="notice-dismiss wpz-welcome-notice-hide" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'foodica-hide-notice', 'welcome' ) ), 'foodica_hide_notices_nonce', '_foodica_notice_nonce' ) ); ?>">
				<span class="screen-reader-text">
					<?php echo esc_html__( 'Dismiss this notice.', 'foodica' ); ?>
				</span>
			</a>

            <div class="wpz-notice-image">
                <a href="https://www.wpzoom.com/themes/foodica/" title="Foodica" target="_blank"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/admin/foodica-top.png' ); ?>" width="233" alt="<?php echo esc_attr__( 'Foodica', 'foodica' ); ?>" /></a>
            </div>

            <div class="wpz-notice-text">

                <h3><?php echo esc_html__( 'Discover Foodica', 'foodica' ); ?></h3>
    			<p>
    			<?php
    			/* translators: %1$s: Foodica theme %2$s: anchor tag open %3$s: anchor tag close */
    			printf( esc_html__( 'Thank you for installing %1$s theme! To get started please make sure you visit the %2$sgetting started page%3$s.', 'foodica' ), 'Foodica', '<a target="_blank" href="' . esc_url( admin_url( 'themes.php?page=foodica' ) ) . '">', '</a>' );
    			?>
    			</p>
    			<div class="wpz-welcome-notice-button">
    				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=foodica' ) ); ?>">
    					<?php
    					/* translators: %s: Foodica theme */
    					printf( esc_html__( '%s Dashboard &rarr;', 'foodica' ), 'Foodica' );
    					?>
    				</a>
    				<a class="button button-secondary" href="<?php echo esc_url( __( 'https://www.wpzoom.com/themes/foodica/', 'foodica' ) ); ?>" target="_blank">
    					<?php esc_html_e( 'Discover Foodica PRO &rarr;', 'foodica' ); ?>
    				</a>
    			</div>
            </div>
		</div>
		<?php
	}
}
