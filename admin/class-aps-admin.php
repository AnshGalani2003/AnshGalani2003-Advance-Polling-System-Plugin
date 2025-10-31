<?php
/**
 * Admin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_admin_actions'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Handle admin actions BEFORE any output
     */
    public function handle_admin_actions() {
        // Only run on our plugin pages
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if (empty($page) || strpos($page, 'aps-polls') === false) {
            return;
        }
        
        // Get action
        $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
        
        // Handle delete option
        if ($action === 'delete_option' && isset($_GET['poll_id']) && isset($_GET['option_id'])) {
            
            // Security check - verify nonce
            $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
            if (!wp_verify_nonce($nonce, 'aps_delete_option')) {
                wp_die(esc_html__('Security check failed', 'advance-polling-system'));
            }
            
            // Check permissions
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have sufficient permissions', 'advance-polling-system'));
            }
            
            $poll_id = isset($_GET['poll_id']) ? absint(wp_unslash($_GET['poll_id'])) : 0;
            $option_id = isset($_GET['option_id']) ? absint(wp_unslash($_GET['option_id'])) : 0;
            
            // Load database class
            $db = new APS_Database();
            $db->delete_option($option_id);
            
            // Redirect with success message
            $redirect_url = add_query_arg(
                array(
                    'page' => 'aps-polls-edit',
                    'poll_id' => $poll_id,
                    'deleted' => '1'
                ),
                admin_url('admin.php')
            );
            
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Advance Polling System', 'advance-polling-system'),
            __('Polls', 'advance-polling-system'),
            'manage_options',
            'aps-polls',
            array($this, 'render_polls_list'),
            plugin_dir_url(__FILE__) . 'images/aps-icon.png',
            30
        );
        
        // Visible submenu - Add New
        add_submenu_page(
            'aps-polls',
            __('Add New Poll', 'advance-polling-system'),
            __('Add New', 'advance-polling-system'),
            'manage_options',
            'aps-polls-add',
            array($this, 'render_add_poll')
        );
        
        // Hidden page - Edit Poll
        add_submenu_page(
            null,
            __('Edit Poll', 'advance-polling-system'),
            __('Edit Poll', 'advance-polling-system'),
            'manage_options',
            'aps-polls-edit',
            array($this, 'render_edit_poll')
        );
        
        // Hidden page - Poll Results
        add_submenu_page(
            null,
            __('Poll Results', 'advance-polling-system'),
            __('Poll Results', 'advance-polling-system'),
            'manage_options',
            'aps-polls-results',
            array($this, 'render_poll_results')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'aps-polls') !== false) {
            wp_enqueue_style(
                'aps-admin-style',
                APS_PLUGIN_URL . 'admin/css/aps-admin.css',
                array(),
                APS_VERSION
            );
            
            // Enqueue Chart.js from local file
            wp_enqueue_script(
                'chart-js',
                APS_PLUGIN_URL . 'admin/js/chart.min.js',
                array(),
                '3.9.1',
                true
            );
            
            wp_enqueue_script(
                'aps-admin-js',
                APS_PLUGIN_URL . 'admin/js/aps-admin.js',
                array('jquery', 'chart-js'),
                APS_VERSION,
                true
            );
        }
    }
    
    /**
     * Render methods delegate to APS_Admin_Pages
     */
    public function render_polls_list() {
        $admin_pages = new APS_Admin_Pages();
        $admin_pages->render_polls_list();
    }
    
    public function render_add_poll() {
        $admin_pages = new APS_Admin_Pages();
        $admin_pages->render_add_poll();
    }
    
    public function render_edit_poll() {
        $admin_pages = new APS_Admin_Pages();
        $admin_pages->render_edit_poll();
    }
    
    public function render_poll_results() {
        $admin_pages = new APS_Admin_Pages();
        $admin_pages->render_poll_results();
    }
}
