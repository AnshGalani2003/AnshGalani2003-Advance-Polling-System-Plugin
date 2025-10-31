<?php
/**
 * Security and validation functions
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Security {
    
    /**
     * Check if current user can manage polls
     */
    public static function current_user_can() {
        return current_user_can('manage_options');
    }
    
    /**
     * Validate and sanitize integer
     */
    public static function validate_int($value) {
        return absint($value);
    }
    
    /**
     * Sanitize text field
     */
    public static function sanitize_text($value) {
        return sanitize_text_field($value);
    }
    
    /**
     * Sanitize textarea
     */
    public static function sanitize_textarea($value) {
        return sanitize_textarea_field($value);
    }
    
    /**
     * Verify nonce
     */
    public static function verify_nonce($nonce, $action) {
        return wp_verify_nonce($nonce, $action);
    }
    
    /**
     * Verify option belongs to poll (security check)
     */
    public static function verify_option_belongs_to_poll($option_id, $poll_id) {
        global $wpdb;
        $options_table = $wpdb->prefix . 'aps_poll_options';
        
        $option_id = self::validate_int($option_id);
        $poll_id = self::validate_int($poll_id);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$options_table} WHERE id = %d AND poll_id = %d", $option_id, $poll_id));
        
        return $count > 0;
    }
}
