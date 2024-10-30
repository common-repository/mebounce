<?php
/**
 * Plugin Name: meBounce
 * Description: This plugin adds bounce popup to convert bounce traffic of the site to leads and also it improves bounce rate of the site. This plugin helps you increase landing page conversion rates.
 * Version: 2.2
 * Author: Medust
 * Author URI: http://medust.com
 * Text Domain: mebounce
 * License: GPL2
 */

if(! defined('ABSPATH')) {
    exit;
}

require_once ( plugin_dir_path( __FILE__ ) . 'classes/db.class.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'classes/popup.class.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'classes/dboperations.class.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'classes/menu.class.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'classes/settings.class.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'classes/csv.class.php' );

add_action('init', mebounce_main_loader_func);

function mebounce_main_loader_func() {
    new Mebounce_popup();
    new Mebounce_csv();
    new Mebounce_db_operations();
    new Mebounce_menu_settings();
}

add_action('admin_init', mebounce_admin_func);
function mebounce_admin_func() {
    new Mebounce_register_settings();
}

$mebounce_db = new Mebounce_db();

//These hooks don' work on any subfolders of plugin directory, so we have created object of the class and then pass methods to these hooks.
register_activation_hook( __FILE__, array( &$mebounce_db, 'mebounce_create_db' ));
//register_deactivation_hook( __FILE__, array( &$mebounce_db, 'mebounce_delete_db' ));
