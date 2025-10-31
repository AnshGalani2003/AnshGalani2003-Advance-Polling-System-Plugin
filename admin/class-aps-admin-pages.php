<?php
/**
 * Admin page rendering
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Admin_Pages {
    
    private $db;
    
    public function __construct() {
        $this->db = new APS_Database();
    }
    
    /**
     * Render polls list page
     */
    public function render_polls_list() {
        // Security check
        if (!APS_Security::current_user_can()) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'advance-polling-system'));
        }
        
        // Handle delete action
        if (isset($_GET['action']) && sanitize_text_field(wp_unslash($_GET['action'])) === 'delete' && isset($_GET['poll_id'])) {
            // Verify nonce
            $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
            if (!wp_verify_nonce($nonce, 'aps_delete_poll')) {
                wp_die(esc_html__('Security check failed', 'advance-polling-system'));
            }
            
            $poll_id = isset($_GET['poll_id']) ? absint(wp_unslash($_GET['poll_id'])) : 0;
            $this->db->delete_poll($poll_id);
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Poll deleted successfully!', 'advance-polling-system') . '</p></div>';
        }
        
        $polls = $this->db->get_all_polls();
        
        include APS_PLUGIN_DIR . 'admin/views/polls-list.php';
    }
    
    /**
     * Render add new poll page
     */
    public function render_add_poll() {
        if (!APS_Security::current_user_can()) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'advance-polling-system'));
        }
        
        if (isset($_POST['submit_poll'])) {
            // Verify nonce
            $nonce = isset($_POST['aps_nonce']) ? sanitize_text_field(wp_unslash($_POST['aps_nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'aps_add_poll')) {
                wp_die(esc_html__('Security check failed', 'advance-polling-system'));
            }
            
            $poll_title = isset($_POST['poll_title']) ? sanitize_text_field(wp_unslash($_POST['poll_title'])) : '';
            $poll_question = isset($_POST['poll_question']) ? sanitize_textarea_field(wp_unslash($_POST['poll_question'])) : '';
            $verification_method = isset($_POST['verification_method']) ? sanitize_text_field(wp_unslash($_POST['verification_method'])) : 'cookie';
            $results_display = isset($_POST['results_display']) ? sanitize_text_field(wp_unslash($_POST['results_display'])) : 'top3';
            $options = isset($_POST['poll_options']) && is_array($_POST['poll_options']) ? array_map('sanitize_text_field', wp_unslash($_POST['poll_options'])) : array();
            $options = array_filter($options);
            
            if (empty($poll_title) || empty($poll_question) || count($options) < 2) {
                echo '<div class="notice notice-error"><p>' . esc_html__('Please fill all required fields and add at least 2 options.', 'advance-polling-system') . '</p></div>';
            } else {
                $poll_id = $this->db->create_poll($poll_title, $poll_question, $options, $verification_method, $results_display);
                
                if ($poll_id) {
                    $verification_label = $verification_method === 'ip' ? __('IP Address', 'advance-polling-system') : __('Cookie', 'advance-polling-system');
                    $display_labels = array(
                        'top3' => __('Top 3', 'advance-polling-system'),
                        'top5' => __('Top 5', 'advance-polling-system'),
                        'top10' => __('Top 10', 'advance-polling-system'),
                        'all' => __('All', 'advance-polling-system')
                    );
                    $display_label = isset($display_labels[$results_display]) ? $display_labels[$results_display] : __('Top 3', 'advance-polling-system');
                    
                    /* translators: 1: verification method (Cookie or IP Address), 2: display setting (Top 3, Top 5, etc), 3: shortcode */
                    $poll_created_message = __('Poll created with %1$s verification and %2$s results display! Use shortcode: %3$s', 'advance-polling-system');
                    
                    $success_message = sprintf(
                        $poll_created_message,
                        '<strong>' . esc_html($verification_label) . '</strong>',
                        '<strong>' . esc_html($display_label) . '</strong>',
                        '<code>[aps_poll id="' . esc_html($poll_id) . '"]</code>'
                    );
                    
                    echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__('Success!', 'advance-polling-system') . '</strong> ' . wp_kses_post($success_message) . '</p></div>';
                }
            }
        }
        
        include APS_PLUGIN_DIR . 'admin/views/add-poll.php';
    }
    
    /**
     * Render edit poll page
     */
    public function render_edit_poll() {
        if (!APS_Security::current_user_can()) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'advance-polling-system'));
        }
        
        // Verify poll_id exists
        if (!isset($_GET['poll_id'])) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Invalid poll ID', 'advance-polling-system') . '</p></div>';
            return;
        }
        
        $poll_id = isset($_GET['poll_id']) ? absint(wp_unslash($_GET['poll_id'])) : 0;
        $poll = $this->db->get_poll($poll_id);
        
        if (!$poll) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Poll not found', 'advance-polling-system') . '</p></div>';
            return;
        }
        
        $options = $this->db->get_poll_options($poll_id);
        
        // Show delete success message
        if (isset($_GET['deleted']) && sanitize_text_field(wp_unslash($_GET['deleted'])) === '1') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Option deleted successfully!', 'advance-polling-system') . '</p></div>';
        }
        
        // Handle update
        if (isset($_POST['update_poll'])) {
            // Verify nonce
            $nonce = isset($_POST['aps_nonce']) ? sanitize_text_field(wp_unslash($_POST['aps_nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'aps_edit_poll')) {
                wp_die(esc_html__('Security check failed', 'advance-polling-system'));
            }
            
            $poll_title = isset($_POST['poll_title']) ? sanitize_text_field(wp_unslash($_POST['poll_title'])) : '';
            $poll_question = isset($_POST['poll_question']) ? sanitize_textarea_field(wp_unslash($_POST['poll_question'])) : '';
            $verification_method = isset($_POST['verification_method']) ? sanitize_text_field(wp_unslash($_POST['verification_method'])) : null;
            $results_display = isset($_POST['results_display']) ? sanitize_text_field(wp_unslash($_POST['results_display'])) : null;
            
            // Update poll details
            $this->db->update_poll($poll_id, $poll_title, $poll_question, $verification_method, $results_display);
            
            // Update options
            if (isset($_POST['option_id']) && is_array($_POST['option_id']) && 
                isset($_POST['option_text']) && is_array($_POST['option_text'])) {
                
                $option_ids = array_map('absint', wp_unslash($_POST['option_id']));
                $option_texts = array_map('sanitize_text_field', wp_unslash($_POST['option_text']));
                
                $id_count = count($option_ids);
                $text_count = count($option_texts);
                
                if ($id_count === $text_count) {
                    for ($i = 0; $i < $id_count; $i++) {
                        $option_id = isset($option_ids[$i]) ? $option_ids[$i] : 0;
                        $option_text = isset($option_texts[$i]) ? trim($option_texts[$i]) : '';
                        
                        // Skip empty options
                        if (empty($option_text)) {
                            continue;
                        }
                        
                        if ($option_id > 0) {
                            // Update existing option
                            $this->db->update_option($option_id, $option_text);
                        } else {
                            // Add new option
                            $this->db->add_option($poll_id, $option_text);
                        }
                    }
                }
            }
            
            echo '<div class="notice notice-success is-dismissible"><p><strong>' . 
                 esc_html__('Success!', 'advance-polling-system') . '</strong> ' . 
                 esc_html__('Poll updated successfully!', 'advance-polling-system') . '</p></div>';
            
            // Refresh data
            $poll = $this->db->get_poll($poll_id);
            $options = $this->db->get_poll_options($poll_id);
        }
        
        include APS_PLUGIN_DIR . 'admin/views/edit-poll.php';
    }
    
    /**
     * Render poll results page
     */
    public function render_poll_results() {
        if (!APS_Security::current_user_can()) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'advance-polling-system'));
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display, nonce not required
        if (!isset($_GET['poll_id'])) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Invalid poll ID', 'advance-polling-system') . '</p></div>';
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display, nonce not required
        $poll_id = isset($_GET['poll_id']) ? absint(wp_unslash($_GET['poll_id'])) : 0;
        $poll = $this->db->get_poll($poll_id);
        
        if (!$poll) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Poll not found', 'advance-polling-system') . '</p></div>';
            return;
        }
        
        $options = $this->db->get_poll_options($poll_id, 'votes', 'DESC');
        $total_votes = $this->db->get_total_votes($poll_id);
        $option_count = count($options);
        
        $labels = array();
        $votes = array();
        
        foreach ($options as $option) {
            $labels[] = $option->option_text;
            $votes[] = $option->votes;
        }
        
        include APS_PLUGIN_DIR . 'admin/views/poll-results.php';
    }
}
