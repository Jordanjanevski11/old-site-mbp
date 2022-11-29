<?php
require 'plugin-updates/plugin-update-checker.php';
$MyUpdateChecker = new PluginUpdateChecker(
    'http://minabastapolare.se/external-wp-plugins/mbp-hide-update/metadata.json',
    __FILE__,
    'mbp-hide-update'
);
/**
* Plugin Name: Mbp updates
* Plugin URI: http://minabastapolare.se/wp-plugins/mbp-hide-update
* Description: Tar bort uppdateringsnotifieringar och hindrar auto-updates
* Version: 1.0
* Author: Johannes Mattsson
*/
add_filter( 'automatic_updater_disabled', '__return_true' ); //Disables auto-update
add_filter( 'auto_update_core', '__return_false' ); //Disables auto-update
add_action('admin_menu','wphidenag'); //Removes update flash-message
function wphidenag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}
add_action( 'admin_enqueue_scripts', 'mbp_hide_updates_scripts' ); //Include script that removes update DOM elements
function mbp_hide_updates_scripts() { 
	wp_enqueue_script( 'hide-update-name', '/wp-content/plugins/mbp-hide-update/js/hide-update.js', array('jquery'), '1.0.0', true );
}
?>