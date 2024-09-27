<?php
/**
 * Theme admin leave review notice
 *
 * @package Foodica
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main PHP class for notice review
 */
class foodica_Notice_Review extends foodica_Notices {

	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'review_notice' ), 20 );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ), 15 );

		$this->current_user_id = get_current_user_id();
	}

	/**
	 * Update option 'foodica_theme_installed_time' if is not exists
	 * Add action if notice wasn't dismissed
	 *
	 * @return void
	 */
	public function review_notice() {
        global $pagenow, $foodica_version;

		if ( ! get_option( 'foodica_theme_installed_time' ) ) {
			update_option( 'foodica_theme_installed_time', time() );
		}

		$this_notice_was_dismissed = $this->get_notice_status( 'review-user-' . $this->current_user_id );

        $current_user_can      = current_user_can( 'edit_theme_options' );

        $should_display_notice = ( $current_user_can && 'index.php' === $pagenow  ) || ( $current_user_can && 'themes.php' === $pagenow && isset( $_GET['activated'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( $should_display_notice ) {

    		if ( ! $this_notice_was_dismissed ) {

                wp_enqueue_style( 'welcome-notice', get_template_directory_uri() . '/assets/admin/css/welcome-notice.css' );

    			add_action( 'admin_notices', array( $this, 'review_notice_markup' ) ); // Display this notice.
    		}

        }
	}

	/**
	 * Show HTML markup if conditions meet
	 *
	 * @return void
	 */
	public function review_notice_markup() {
		$dismiss_url = wp_nonce_url(
			remove_query_arg( array( 'activated' ), add_query_arg( 'foodica-hide-notice', 'review-user-' . $this->current_user_id ) ),
			'foodica_hide_notices_nonce',
			'_foodica_notice_nonce'
		);

		$theme_data   = wp_get_theme();
		$current_user = wp_get_current_user();

		if ( ( get_option( 'foodica_theme_installed_time' ) > strtotime( '-4 day' ) ) ) {
			return;
		}
		?>
		<div id="message" class="notice foodica-notice foodica-review-notice wpz-welcome-notice">
			<a class="foodica-message-close notice-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"></a>

				<div class="wpz-notice-image">
					<img class="foodica-screenshot" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/foodica-top.png" width="233" alt="<?php esc_attr_e( 'Foodica', 'foodica' ); ?>" />
				</div>
				<div class="wpz-notice-text">

                    <h3><?php echo esc_html__( 'Leave us a review!', 'foodica' ); ?></h3>

					<p>
						<?php
						printf(
							/* Translators: %1$s current user display name. */
							esc_html__(
								'We hope you are enjoying the %1$s theme! %2$sIf you can spare a moment, please consider adding a review on WordPress.org.',
								'foodica'
							),
							// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							'<a href="' . esc_url( admin_url( 'themes.php?page=foodica' ) ) . '"><strong>' . esc_html( $theme_data->Name ) . '</strong></a>',
							'<br>'
						);
						?>
					</p>

                    <div class="wpz-welcome-notice-button">

					   <a href="https://wordpress.org/support/theme/foodica/reviews/?rate=5#new-post" class="button button-primary" target="_blank"><span class="dashicons dashicons-star-filled"></span> <?php esc_html_e( 'Leave a Review', 'foodica' ); ?></a>

    					<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary"><?php esc_html_e( 'Hide this notice', 'foodica' ); ?></a>
    					</a>
                    </div>

				</div><!-- .foodica-message-text -->

		</div><!-- #message -->
		<?php
	}
}

new foodica_Notice_Review();
