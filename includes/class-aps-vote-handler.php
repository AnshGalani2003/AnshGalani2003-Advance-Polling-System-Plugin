<?php
/**
 * Vote handling functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Vote_Handler {
    
    private $db;
    
    public function __construct() {
        $this->db = new APS_Database();
        add_action('init', array($this, 'handle_vote'), 1);
    }
    
    /**
     * Handle vote submission
     */
    public function handle_vote() {
        // Check if this is a poll vote submission
        if (!isset($_POST['aps_vote_poll']) || !isset($_POST['poll_id']) || !isset($_POST['poll_option'])) {
            return;
        }
        
        // Verify nonce
        $nonce = isset($_POST['aps_nonce']) ? sanitize_text_field(wp_unslash($_POST['aps_nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'aps_vote_action')) {
            wp_die(esc_html__('Security check failed', 'advance-polling-system'));
        }
        
        $poll_id = isset($_POST['poll_id']) ? absint(wp_unslash($_POST['poll_id'])) : 0;
        $option_id = isset($_POST['poll_option']) ? absint(wp_unslash($_POST['poll_option'])) : 0;
        
        // Get poll details
        $poll = $this->db->get_poll($poll_id);
        
        if (!$poll) {
            wp_die(esc_html__('Poll not found', 'advance-polling-system'));
        }
        
        // Check verification method and if user already voted
        $verification_method = $poll->verification_method;
        $already_voted = false;
        
        if ($verification_method === 'ip') {
            // IP-based verification
            $ip_address = $this->get_user_ip();
            $already_voted = $this->db->has_ip_voted($poll_id, $ip_address);
            
            if ($already_voted) {
                wp_safe_redirect(add_query_arg('already_voted', '1', wp_get_referer()));
                exit;
            }
        } else {
            // Cookie-based verification (default)
            $cookie_name = 'aps_poll_voted_' . $poll_id;
            
            if (isset($_COOKIE[$cookie_name])) {
                wp_safe_redirect(add_query_arg('already_voted', '1', wp_get_referer()));
                exit;
            }
        }
        
        // Verify option belongs to poll
        if (!$this->verify_option_belongs_to_poll($option_id, $poll_id)) {
            wp_die(esc_html__('Invalid poll option', 'advance-polling-system'));
        }
        
        // Increment vote count
        $this->db->increment_vote($option_id);
        
        // Record vote based on verification method
        if ($verification_method === 'ip') {
            // Record IP-based vote
            $ip_address = $this->get_user_ip();
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
            $this->db->record_ip_vote($poll_id, $option_id, $ip_address, $user_agent);
        } else {
            // Set cookie for cookie-based verification
            $cookie_name = 'aps_poll_voted_' . $poll_id;
            $cookie_expire = time() + (get_option('aps_cookie_expiry', 365) * DAY_IN_SECONDS);
            $cookie_path = defined('COOKIEPATH') ? COOKIEPATH : '/';
            $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
            
            setcookie($cookie_name, '1', $cookie_expire, $cookie_path, $cookie_domain, is_ssl(), true);
            $_COOKIE[$cookie_name] = '1';
        }
        
        // Redirect
        $redirect_url = remove_query_arg(array('voted', 'already_voted'));
        $redirect_url = add_query_arg('voted', $poll_id, $redirect_url);
        wp_safe_redirect($redirect_url);
        exit;
    }
    
    /**
     * Verify option belongs to poll
     */
    private function verify_option_belongs_to_poll($option_id, $poll_id) {
        global $wpdb;
        $options_table = $wpdb->prefix . 'aps_poll_options';
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$options_table} WHERE id = %d AND poll_id = %d", $option_id, $poll_id));
        
        return $count > 0;
    }
    
    /**
     * Get user IP address
     */
    private function get_user_ip() {
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
