<?php

/**
 * Plugin Name: Advance Polling System
 * Description: A comprehensive polling system with beautiful UI, real-time results, and advanced analytics for WordPress.
 * Version: 1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Ansh Galani
 * Author URI: https://profiles.wordpress.org/anshgalani003/
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: advance-polling-system
 * Domain Path: /languages
 * 
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('APS_VERSION', '1.0');
define('APS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('APS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('APS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_advance_polling_system()
{
    require_once APS_PLUGIN_DIR . 'includes/class-aps-activator.php';
    APS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_advance_polling_system()
{
    require_once APS_PLUGIN_DIR . 'includes/class-aps-deactivator.php';
    APS_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_advance_polling_system');
register_deactivation_hook(__FILE__, 'deactivate_advance_polling_system');

/**
 * Begins execution of the plugin.
 */
function run_advance_polling_system()
{
    // Load dependencies
    require_once APS_PLUGIN_DIR . 'includes/class-aps-security.php';
    require_once APS_PLUGIN_DIR . 'includes/class-aps-database.php';
    require_once APS_PLUGIN_DIR . 'includes/class-aps-vote-handler.php';
    require_once APS_PLUGIN_DIR . 'admin/class-aps-admin.php';
    require_once APS_PLUGIN_DIR . 'admin/class-aps-admin-pages.php';
    require_once APS_PLUGIN_DIR . 'public/class-aps-shortcode.php';

    // Initialize components
    new APS_Vote_Handler();
    new APS_Admin();
    new APS_Admin_Pages();
    new APS_Shortcode();
}

run_advance_polling_system();

// Enqueue frontend styles
function aps_enqueue_frontend_styles()
{
    wp_enqueue_style(
        'aps-poll-style',
        APS_PLUGIN_URL . 'public/css/aps-public.css',
        array(),
        APS_VERSION
    );
}
add_action('wp_enqueue_scripts', 'aps_enqueue_frontend_styles');
