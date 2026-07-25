<?php

namespace AgileStoreLocator;

/**
 * Fired during plugin deactivation
 *
 * @link       https://agilelogix.com
 * @since      1.0.0
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/includes
 * @author     AgileLogix <support@agilelogix.com>
 */
class Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

    //  Clear the cron job
    wp_clear_scheduled_hook( 'asl_import_files' );
	}

	/**
	 * Load the survey assets only on the Plugins screen.
	 *
	 * @param string $hook_suffix Current admin page.
	 */
	public function enqueue_feedback_assets( $hook_suffix ) {

		if ( 'plugins.php' !== $hook_suffix || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		wp_enqueue_style(
			'asl-deactivation-feedback',
			ASL_URL_PATH . 'admin/css/deactivation-feedback.css',
			array(),
			ASL_CVERSION
		);

		wp_enqueue_script(
			'asl-deactivation-feedback',
			ASL_URL_PATH . 'admin/js/deactivation-feedback.js',
			array( 'jquery' ),
			ASL_CVERSION,
			true
		);

		wp_localize_script(
			'asl-deactivation-feedback',
			'ASL_DEACTIVATION_FEEDBACK',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'asl-deactivation-feedback' ),
				'pluginFile' => ASL_BASE_PATH . '/agile-store-locator.php',
				'sending'    => esc_html__( 'Sending feedback…', 'asl_locator' ),
			)
		);
	}

	/**
	 * [feedback_box_html render the feedback box for the plugin]
	 */
	public function feedback_box_html() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$reasons = array(
			'temporary'       => __( 'I am only deactivating temporarily', 'asl_locator' ),
			'not_working'     => __( 'The plugin did not work as expected', 'asl_locator' ),
			'missing_feature' => __( 'I could not find a feature I needed', 'asl_locator' ),
			'difficult'       => __( 'The plugin was difficult to set up or use', 'asl_locator' ),
			'alternative'     => __( 'I found a better plugin', 'asl_locator' ),
			'other'           => __( 'Other', 'asl_locator' ),
		);
		?>
		<div class="asl-deactivate-modal" id="asl-deactivate-modal" aria-hidden="true">
			<div class="asl-deactivate-modal__backdrop"></div>
			<div class="asl-deactivate-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="asl-deactivate-title">
				<div class="asl-deactivate-modal__header">
					<div>
						<h2 id="asl-deactivate-title"><?php esc_html_e( 'Quick Feedback', 'asl_locator' ); ?></h2>
						<p><?php esc_html_e( 'Before you deactivate Agile Store Locator, would you tell us why?', 'asl_locator' ); ?></p>
					</div>
					<button type="button" class="asl-deactivate-modal__close" aria-label="<?php esc_attr_e( 'Close', 'asl_locator' ); ?>">&times;</button>
				</div>
				<form id="asl-deactivate-feedback-form">
					<fieldset>
						<legend class="screen-reader-text"><?php esc_html_e( 'Reason for deactivating', 'asl_locator' ); ?></legend>
						<?php foreach ( $reasons as $value => $label ) : ?>
							<label class="asl-deactivate-reason">
								<input type="radio" name="reason" value="<?php echo esc_attr( $value ); ?>">
								<span><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</fieldset>
					<div class="asl-deactivate-details" hidden>
						<label for="asl-deactivate-details"><?php esc_html_e( 'Could you share a little more?', 'asl_locator' ); ?></label>
						<textarea id="asl-deactivate-details" name="details" rows="3" maxlength="1000" placeholder="<?php esc_attr_e( 'Your feedback helps us improve the plugin.', 'asl_locator' ); ?>"></textarea>
					</div>
					<p class="asl-deactivate-privacy"><?php esc_html_e( 'Submitting sends your response, plugin version, and website URL to AgileLogix. No feedback is sent when you skip.', 'asl_locator' ); ?></p>
					<div class="asl-deactivate-modal__footer">
						<button type="button" class="button-link asl-deactivate-skip"><?php esc_html_e( 'Skip & Deactivate', 'asl_locator' ); ?></button>
						<button type="submit" class="button button-primary asl-deactivate-submit" disabled><?php esc_html_e( 'Submit & Deactivate', 'asl_locator' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Email explicitly submitted deactivation feedback to the support team.
	 */
	public function submit_feedback() {

		check_ajax_referer( 'asl-deactivation-feedback', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to perform this action.', 'asl_locator' ) ), 403 );
		}

		$allowed_reasons = array(
			'temporary'       => 'Temporary deactivation',
			'not_working'     => 'Did not work as expected',
			'missing_feature' => 'Missing feature',
			'difficult'       => 'Difficult to set up or use',
			'alternative'     => 'Found an alternative',
			'other'           => 'Other',
		);
		$reason_key = isset( $_POST['reason'] ) ? sanitize_key( wp_unslash( $_POST['reason'] ) ) : '';

		if ( ! isset( $allowed_reasons[ $reason_key ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Please select a reason.', 'asl_locator' ) ), 400 );
		}

		$details = isset( $_POST['details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['details'] ) ) : '';
		$subject = sprintf( '[ASL] Deactivation feedback: %s', $allowed_reasons[ $reason_key ] );
		$message = implode(
			"\n",
			array(
				'Reason: ' . $allowed_reasons[ $reason_key ],
				'Details: ' . ( $details ? $details : 'Not provided' ),
				'Plugin version: ' . ASL_CVERSION,
				'WordPress version: ' . get_bloginfo( 'version' ),
				'Website: ' . home_url( '/' ),
			)
		);

		$recipient = apply_filters( 'asl_deactivation_feedback_email', 'feedback@agilelogix.com' );
		wp_mail( sanitize_email( $recipient ), $subject, $message );

		// Deactivation should never be held up by a mail configuration problem.
		wp_send_json_success();
	}

}
