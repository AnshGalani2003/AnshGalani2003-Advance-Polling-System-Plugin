<?php
/**
 * Database operations
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Database {
    
    private $wpdb;
    private $polls_table;
    private $options_table;
    private $votes_table;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->polls_table = $wpdb->prefix . 'aps_polls';
        $this->options_table = $wpdb->prefix . 'aps_poll_options';
        $this->votes_table = $wpdb->prefix . 'aps_poll_votes';
    }
    
    /**
     * Get all polls
     */
    public function get_all_polls() {
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $this->wpdb->get_results("SELECT * FROM {$this->polls_table} ORDER BY id DESC");
    }
    
    /**
     * Get poll by ID
     */
    public function get_poll($poll_id) {
        $poll_id = APS_Security::validate_int($poll_id);
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->polls_table} WHERE id = %d", $poll_id));
    }
    
    /**
     * Get poll options
     */
    public function get_poll_options($poll_id, $order_by = 'id', $order = 'ASC', $limit = null) {
        $poll_id = APS_Security::validate_int($poll_id);
        
        // Validate order_by and order for security
        $allowed_orders = array('id', 'votes', 'option_order');
        $order_by = in_array($order_by, $allowed_orders, true) ? $order_by : 'id';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $query = $this->wpdb->prepare("SELECT * FROM {$this->options_table} WHERE poll_id = %d ORDER BY {$order_by} {$order}", $poll_id);
        
        if ($limit) {
            $query .= $this->wpdb->prepare(" LIMIT %d", absint($limit));
        }
        
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $this->wpdb->get_results($query);
    }
    
    /**
     * Create new poll
     */
    public function create_poll($title, $question, $options, $verification_method = 'cookie', $results_display = 'top3') {
        $title = APS_Security::sanitize_text($title);
        $question = APS_Security::sanitize_textarea($question);
        $verification_method = in_array($verification_method, array('cookie', 'ip'), true) ? $verification_method : 'cookie';
        $results_display = in_array($results_display, array('top3', 'top5', 'top10', 'all'), true) ? $results_display : 'top3';
        $user_id = get_current_user_id();
        
        $result = $this->wpdb->insert(
            $this->polls_table,
            array(
                'poll_title' => $title,
                'poll_question' => $question,
                'verification_method' => $verification_method,
                'results_display' => $results_display,
                'created_by' => $user_id,
                'status' => 1
            ),
            array('%s', '%s', '%s', '%s', '%d', '%d')
        );
        
        if ($result === false) {
            return false;
        }
        
        $poll_id = $this->wpdb->insert_id;
        
        // Insert options
        foreach ($options as $index => $option) {
            $option_text = APS_Security::sanitize_text($option);
            if (!empty($option_text)) {
                $this->wpdb->insert(
                    $this->options_table,
                    array(
                        'poll_id' => $poll_id,
                        'option_text' => $option_text,
                        'option_order' => $index,
                        'votes' => 0
                    ),
                    array('%d', '%s', '%d', '%d')
                );
            }
        }
        
        return $poll_id;
    }
    
    /**
     * Update poll
     */
    public function update_poll($poll_id, $title, $question, $verification_method = null, $results_display = null) {
        $poll_id = APS_Security::validate_int($poll_id);
        $title = APS_Security::sanitize_text($title);
        $question = APS_Security::sanitize_textarea($question);
        
        $data = array(
            'poll_title' => $title,
            'poll_question' => $question
        );
        $format = array('%s', '%s');
        
        if ($verification_method !== null) {
            $verification_method = in_array($verification_method, array('cookie', 'ip'), true) ? $verification_method : 'cookie';
            $data['verification_method'] = $verification_method;
            $format[] = '%s';
        }
        
        if ($results_display !== null) {
            $results_display = in_array($results_display, array('top3', 'top5', 'top10', 'all'), true) ? $results_display : 'top3';
            $data['results_display'] = $results_display;
            $format[] = '%s';
        }
        
        return $this->wpdb->update(
            $this->polls_table,
            $data,
            array('id' => $poll_id),
            $format,
            array('%d')
        );
    }
    
    /**
     * Update poll option
     */
    public function update_option($option_id, $option_text) {
        $option_id = APS_Security::validate_int($option_id);
        $option_text = APS_Security::sanitize_text($option_text);
        
        return $this->wpdb->update(
            $this->options_table,
            array('option_text' => $option_text),
            array('id' => $option_id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Add new option to poll
     */
    public function add_option($poll_id, $option_text) {
        $poll_id = APS_Security::validate_int($poll_id);
        $option_text = APS_Security::sanitize_text($option_text);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $max_order = $this->wpdb->get_var($this->wpdb->prepare("SELECT MAX(option_order) FROM {$this->options_table} WHERE poll_id = %d", $poll_id));
        
        $next_order = $max_order !== null ? intval($max_order) + 1 : 0;
        
        $result = $this->wpdb->insert(
            $this->options_table,
            array(
                'poll_id' => $poll_id,
                'option_text' => $option_text,
                'option_order' => $next_order,
                'votes' => 0
            ),
            array('%d', '%s', '%d', '%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Delete poll
     */
    public function delete_poll($poll_id) {
        $poll_id = APS_Security::validate_int($poll_id);
        
        $this->wpdb->delete($this->options_table, array('poll_id' => $poll_id), array('%d'));
        $this->wpdb->delete($this->votes_table, array('poll_id' => $poll_id), array('%d'));
        
        return $this->wpdb->delete($this->polls_table, array('id' => $poll_id), array('%d'));
    }
    
    /**
     * Delete option
     */
    public function delete_option($option_id) {
        $option_id = APS_Security::validate_int($option_id);
        
        return $this->wpdb->delete($this->options_table, array('id' => $option_id), array('%d'));
    }
    
    /**
     * Increment vote count
     */
    public function increment_vote($option_id) {
        $option_id = APS_Security::validate_int($option_id);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $this->wpdb->query($this->wpdb->prepare("UPDATE {$this->options_table} SET votes = votes + 1 WHERE id = %d", $option_id));
    }
    
    /**
     * Get total votes for poll
     */
    public function get_total_votes($poll_id) {
        $poll_id = APS_Security::validate_int($poll_id);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $this->wpdb->get_var($this->wpdb->prepare("SELECT SUM(votes) FROM {$this->options_table} WHERE poll_id = %d", $poll_id));
        
        return $result ? intval($result) : 0;
    }
    
    /**
     * Check if IP has voted
     */
    public function has_ip_voted($poll_id, $ip_address) {
        $poll_id = APS_Security::validate_int($poll_id);
        $ip_address = sanitize_text_field($ip_address);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $count = $this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(*) FROM {$this->votes_table} WHERE poll_id = %d AND ip_address = %s", $poll_id, $ip_address));
        
        return $count > 0;
    }
    
    /**
     * Record IP vote
     */
    public function record_ip_vote($poll_id, $option_id, $ip_address, $user_agent = '') {
        $poll_id = APS_Security::validate_int($poll_id);
        $option_id = APS_Security::validate_int($option_id);
        $ip_address = sanitize_text_field($ip_address);
        $user_agent = sanitize_text_field(substr($user_agent, 0, 255));
        
        return $this->wpdb->insert(
            $this->votes_table,
            array(
                'poll_id' => $poll_id,
                'option_id' => $option_id,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent
            ),
            array('%d', '%d', '%s', '%s')
        );
    }
    
    /**
     * Get vote count by IP for a poll
     */
    public function get_poll_ip_vote_count($poll_id) {
        $poll_id = APS_Security::validate_int($poll_id);
        
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(DISTINCT ip_address) FROM {$this->votes_table} WHERE poll_id = %d", $poll_id));
    }
}
