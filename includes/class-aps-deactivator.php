<?php
/**
 * Fired during plugin deactivation
 */

if (!defined('ABSPATH')) {
    exit;
}

class APS_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        flush_rewrite_rules();
        // Note: We don't delete tables or data on deactivation
        // Data is only removed via uninstall.php
    }
}
