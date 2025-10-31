<?php

/**
 * Shortcode handling
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Shortcode
{

    private $db;

    public function __construct()
    {
        $this->db = new APS_Database();
        add_shortcode('aps_poll', array($this, 'render_poll'));
    }
    /**
     * Enqueue frontend styles
     */
    public function enqueue_poll_styles()
    {
        wp_enqueue_style(
            'aps-poll-style',
            APS_PLUGIN_URL . 'public/css/aps-public.css',
            array(),
            APS_VERSION
        );
    }

    /**
     * Render poll shortcode
     */
    public function render_poll($atts)
    {
        $atts = shortcode_atts(
            array(
                'id' => 0,
            ),
            $atts,
            'aps_poll'
        );

        $poll_id = absint($atts['id']);

        if (!$poll_id) {
            return '<p>' . esc_html__('Invalid poll ID', 'advance-polling-system') . '</p>';
        }

        $poll = $this->db->get_poll($poll_id);

        if (!$poll) {
            return '<p>' . esc_html__('Poll not found', 'advance-polling-system') . '</p>';
        }

        // Get results display setting
        $results_display = $poll->results_display;
        $results_limit = null;

        switch ($results_display) {
            case 'top3':
                $results_limit = 3;
                break;
            case 'top5':
                $results_limit = 5;
                break;
            case 'top10':
                $results_limit = 10;
                break;
            case 'all':
            default:
                $results_limit = null;
                break;
        }

        // Get all options for vote form
        $options_for_form = $this->db->get_poll_options($poll_id);

        // Get options for results (sorted by votes, limited if needed)
        $options_for_results = $this->db->get_poll_options($poll_id, 'votes', 'DESC', $results_limit);

        $total_votes = $this->db->get_total_votes($poll_id);

        // Check if user already voted
        $user_voted = false;
        $just_voted = false;
        $already_voted = false;

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display parameters set by vote handler redirect
        $voted_param = isset($_GET['voted']) ? absint(wp_unslash($_GET['voted'])) : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display parameters set by vote handler redirect
        $already_voted_param = isset($_GET['already_voted']) ? absint(wp_unslash($_GET['already_voted'])) : 0;

        if ($voted_param === $poll_id) {
            $user_voted = true;
            $just_voted = true;
        } elseif ($already_voted_param === 1) {
            $user_voted = true;
            $already_voted = true;
        } else {
            // Check based on verification method
            $verification_method = $poll->verification_method;

            if ($verification_method === 'ip') {
                $ip_address = $this->get_user_ip();
                $user_voted = $this->db->has_ip_voted($poll_id, $ip_address);
            } else {
                $cookie_name = 'aps_poll_voted_' . $poll_id;
                $user_voted = isset($_COOKIE[$cookie_name]);
            }
        }

        ob_start();
        include APS_PLUGIN_DIR . 'public/views/poll-display.php';
        return ob_get_clean();
    }

    /**
     * Get user IP address
     */
    private function get_user_ip()
    {
        $ip = '';

        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }

        // Validate IP address
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        return $ip ? $ip : '0.0.0.0';
    }
}
