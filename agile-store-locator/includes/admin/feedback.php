<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/** Voluntary feedback from the dashboard and settings pages. */
class Feedback {

    public static function enqueue($library_handle) {
        wp_enqueue_script('asl-feedback', ASL_URL_PATH . 'admin/js/feedback.js', ['jquery', $library_handle], filemtime(ASL_PLUGIN_PATH . 'admin/js/feedback.js'), true);
        wp_enqueue_style('asl-feedback', ASL_URL_PATH . 'admin/css/feedback.css', [], filemtime(ASL_PLUGIN_PATH . 'admin/css/feedback.css'));
        wp_localize_script('asl-feedback', 'ASL_FEEDBACK', [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asl-nounce'),
            'siteUrl' => home_url('/'),
            'title' => __('Send Feedback', 'asl_locator'),
            'intro' => __('Have an idea or something we could improve?', 'asl_locator'),
            'type' => __('Feedback type', 'asl_locator'),
            'suggestion' => __('Suggestion', 'asl_locator'),
            'issue' => __('Report an issue', 'asl_locator'),
            'general' => __('General feedback', 'asl_locator'),
            'message' => __('Message (required)', 'asl_locator'),
            'email' => __('Email (optional)', 'asl_locator'),
            'emailHelp' => __('Provide your email if you would like a reply.', 'asl_locator'),
            'includeSite' => __('Include my site URL', 'asl_locator'),
            'cancel' => __('Cancel', 'asl_locator'),
            'close' => __('Close', 'asl_locator'),
            'required' => __('Please enter your feedback (up to 5,000 characters).', 'asl_locator'),
            'invalidEmail' => __('Please enter a valid email address or leave it blank.', 'asl_locator'),
            'failed' => __('Unable to send feedback. Your message is still here; please try again.', 'asl_locator'),
            'thanks' => __('Thank you for your feedback!', 'asl_locator'),
        ]);
    }

    public function send() {
        // The AJAX router also checks access and nonce; keep this action protected independently.
        if (!current_user_can(ASL_PERMISSION)) {
            return ['success' => false, 'message' => __('You do not have permission to send feedback.', 'asl_locator')];
        }
        $nonce = isset($_POST['asl-nounce']) && is_string($_POST['asl-nounce']) ? sanitize_key($_POST['asl-nounce']) : '';
        if (!wp_verify_nonce($nonce, 'asl-nounce')) {
            return ['success' => false, 'message' => __('Please reload the page and try again.', 'asl_locator')];
        }
        foreach (['feedback_type', 'message', 'email', 'include_site'] as $field) {
            if (isset($_POST[$field]) && !is_string($_POST[$field])) {
                return ['success' => false, 'message' => __('Invalid feedback submission.', 'asl_locator')];
            }
        }
        $types = ['suggestion' => 'Suggestion', 'issue' => 'Report an issue', 'general' => 'General feedback'];
        $type = isset($_POST['feedback_type']) ? sanitize_key($_POST['feedback_type']) : '';
        $message = isset($_POST['message']) ? trim(sanitize_textarea_field(wp_unslash($_POST['message']))) : '';
        $email = isset($_POST['email']) ? trim(wp_unslash($_POST['email'])) : '';
        if (!isset($types[$type]) || $message === '' || strlen($message) > 20000 || preg_match_all('/./us', $message) > 5000) {
            return ['success' => false, 'message' => __('Choose a feedback type and enter a message of up to 5,000 characters.', 'asl_locator')];
        }
        if ($email !== '' && (!is_email($email) || preg_match('/[\r\n]/', $email))) {
            return ['success' => false, 'message' => __('Please enter a valid email address or leave it blank.', 'asl_locator')];
        }
        $rate_key = 'asl_feedback_sent_' . get_current_user_id();
        if (get_transient($rate_key)) {
            return ['success' => false, 'message' => __('Please wait a minute before sending more feedback.', 'asl_locator')];
        }
        $body = "Feedback type: " . $types[$type] . "\nPlugin version: " . ASL_CVERSION . "\n";
        if ($email !== '') {
            $body .= "Reply email: " . $email . "\n";
        }
        if (isset($_POST['include_site']) && $_POST['include_site'] === '1') {
            $body .= "Site URL: " . home_url('/') . "\n";
        }
        $body .= "\nMessage:\n" . $message;
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        if ($email !== '') {
            $headers[] = 'Reply-To: ' . sanitize_email($email);
        }
        $sent = wp_mail('feedback@agilelogix.com', '[Agile Store Locator] ' . $types[$type], $body, $headers);
        if (!$sent) {
            return ['success' => false, 'message' => __('Your site could not send the email. Please check WordPress email delivery and try again.', 'asl_locator')];
        }
        set_transient($rate_key, 1, MINUTE_IN_SECONDS);
        return ['success' => true];
    }
}
