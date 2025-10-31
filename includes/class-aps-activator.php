<?php
/**
 * Fired during plugin activation
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Polls table - UPDATED with results_display column
        $polls_table = $wpdb->prefix . 'aps_polls';
        $sql_polls = "CREATE TABLE IF NOT EXISTS $polls_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            poll_title varchar(255) NOT NULL,
            poll_question text NOT NULL,
            verification_method varchar(20) DEFAULT 'cookie',
            results_display varchar(20) DEFAULT 'top3',
            status tinyint(1) DEFAULT 1,
            created_by bigint(20) UNSIGNED DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY created_by (created_by),
            KEY verification_method (verification_method)
        ) $charset_collate;";
        
        // Poll options table
        $options_table = $wpdb->prefix . 'aps_poll_options';
        $sql_options = "CREATE TABLE IF NOT EXISTS $options_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            poll_id bigint(20) UNSIGNED NOT NULL,
            option_text varchar(255) NOT NULL,
            votes int(11) DEFAULT 0,
            option_order int(11) DEFAULT 0,
            PRIMARY KEY (id),
            KEY poll_id (poll_id),
            KEY votes (votes)
        ) $charset_collate;";
        
        // Vote tracking table for IP-based verification
        $votes_table = $wpdb->prefix . 'aps_poll_votes';
        $sql_votes = "CREATE TABLE IF NOT EXISTS $votes_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            poll_id bigint(20) UNSIGNED NOT NULL,
            option_id bigint(20) UNSIGNED NOT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent varchar(255) DEFAULT NULL,
            voted_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY poll_id (poll_id),
            KEY ip_address (ip_address),
            KEY voted_at (voted_at),
            UNIQUE KEY unique_vote (poll_id, ip_address)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_polls);
        dbDelta($sql_options);
        dbDelta($sql_votes);
    }
    
    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        add_option('aps_version', APS_VERSION);
        add_option('aps_cookie_expiry', 365);
        add_option('aps_default_verification', 'cookie');
        add_option('aps_default_results_display', 'top3');
    }
}
