<?php
/**
 * Uninstall script
 * Fires when the plugin is uninstalled
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Access global wpdb
global $wpdb;

// Define table names
$tables = array(
    $wpdb->prefix . 'aps_poll_votes',
    $wpdb->prefix . 'aps_poll_options',
    $wpdb->prefix . 'aps_polls'
);

// Drop all plugin tables
foreach ($tables as $table) {
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
    $wpdb->query("DROP TABLE IF EXISTS {$table}");
}

// Delete plugin options
$options = array(
    'aps_version',
    'aps_cookie_expiry',
    'aps_default_verification'
);

foreach ($options as $option) {
    delete_option($option);
}

// Clear transients
delete_transient('aps_cache');

// Clear any cached data
wp_cache_flush();
