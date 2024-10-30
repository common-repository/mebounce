<?php
class Mebounce_db {
    
    public function __construct() {
        
    }
    
    public function mebounce_create_db() {
        global $wpdb;
        $version = get_option( 'mebounce_version', '1.0' );
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'mebounce';

        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            mobile bigint(20),
            date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        if ( version_compare( $version, '2.0' ) < 0 ) {
            $sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                email varchar(100) NOT NULL,
                mobile bigint(20),
                date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                view_status int(11) DEFAULT 0 NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
            
            update_option( 'mebounce_version', '2.0' );
        }
    }
    
    public function mebounce_delete_db() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mebounce';
        
        //Delete any options thats stored also?
        delete_option('mebounce_plugin_options');
        delete_option('mebounce_plugin_front_options');

        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}