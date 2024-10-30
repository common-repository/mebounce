<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

global $wpdb;
$table_name = $wpdb->prefix . 'mebounce';
        
//Delete any options thats stored also?
delete_option('mebounce_plugin_options');
delete_option('mebounce_plugin_front_options');

$wpdb->query("DROP TABLE IF EXISTS $table_name");