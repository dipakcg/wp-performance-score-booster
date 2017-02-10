<?php
/*
Plugin Name: WP Performance Score Booster
Plugin URI: https://github.com/dipakcg/wp-performance-score-booster
Description: Speed-up page load times and improve website scores in services like PageSpeed, YSlow, Pingdom and GTmetrix.
Version: 1.7.1
Author: Dipak C. Gajjar
Author URI: https://dipakgajjar.com
Text Domain: wp-performance-score-booster
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

// Define plugin version for future releases
if (!defined('WPPSB_PLUGIN_VERSION')) {
    define('WPPSB_PLUGIN_VERSION', 'wppsb_plugin_version');
}
if (!defined('WPPSB_PLUGIN_VERSION_NUM')) {
    define('WPPSB_PLUGIN_VERSION_NUM', '1.7.1');
}
update_option(WPPSB_PLUGIN_VERSION, WPPSB_PLUGIN_VERSION_NUM);

/* Plugin Path */
define( 'WPPSB_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );
/* Plugin File */
define( 'WPPSB_FILE', __FILE__ );
/* Plugin URL */
define( 'WPPSB_URL', plugins_url( '', __FILE__ ) );

require_once 'admin-page.php'; // admin options page.
require_once 'data-processing.php'; // process the data such as remove query strings, enable gzip and leverage browser caching.

// Load plugin textdomain for language trnaslation
function wppsb_load_plugin_textdomain() {

	$domain = 'wp-performance-score-booster';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	// wp-content/languages/plugin-name/plugin-name-de_DE.mo
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

}
add_action( 'init', 'wppsb_load_plugin_textdomain' );

// Add settings link on plugin page
function dcg_settings_link($links) {
	// $settings_link = '<a href="admin.php?page=wp-performance-score-booster">Settings</a>';
	$settings_link = '<a href="options-general.php?page=wp-performance-score-booster">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'dcg_settings_link' );

// Adding WordPress plugin meta links
function wppsb_plugin_meta_links( $links, $file ) {
	$plugin = plugin_basename(__FILE__);
	// Create link
	if ( $file == $plugin ) {
		return array_merge(
			$links,
			array( '<a href="https://dipakgajjar.com/products/wordpress-speed-optimisation-service?utm_source=plugins%20page&utm_medium=text%20link&utm_campaign=wordplress%20plugins" style="color:#FF0000;" target="_blank">Order WordPress Speed Optimisation Service</a>' )
		);
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'wppsb_plugin_meta_links', 10, 2 );

// Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
add_action( 'admin_init', 'wppsb_add_stylesheet' );
function wppsb_add_stylesheet() {
    // Respects SSL, style.css is relative to the current file
    wp_register_style( 'wppsb-stylesheet', WPPSB_URL . '/assets/css/style.min.css' );
    wp_enqueue_style( 'wppsb-stylesheet' );
}

// Add header
function wppsb_add_header() {
	// Get the plugin version from options (in the database)
	$wppsb_plugin_version = get_option('wppsb_plugin_version');
	$head_comment = <<<EOD
<!-- Performance scores of this site is tuned by WP Performance Score Booster plugin v$wppsb_plugin_version - http://wordpress.org/plugins/wp-performance-score-booster -->
EOD;
	$head_comment = $head_comment . PHP_EOL;
	print ($head_comment);
}
add_action('wp_head', 'wppsb_add_header', 1);

// Calling this function will make flush_rules to be called at the end of the PHP execution
function wppsb_activate_plugin() {

    // Save default options value in the database
    update_option( 'wppsb_remove_query_strings', 'on' );
    add_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
	add_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );

	if (function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) {
		update_option( 'wppsb_enable_gzip', 'on' );
	}
	else {
		update_option( 'wppsb_enable_gzip', '' );
	}

	update_option( 'wppsb_expire_caching', 'on' );

    flush_rewrite_rules();
    wppsb_save_mod_rewrite_rules();

    register_uninstall_hook( __FILE__, 'wppsb_uninstall_plugin' );
}
register_activation_hook( __FILE__, 'wppsb_activate_plugin' );

// Remove filters/functions on plugin deactivation
function wppsb_deactivate_plugin() {
	delete_option( 'wppsb_plugin_version' );
	// Clear (off) all the options value (from database)
	update_option( 'wppsb_remove_query_strings', "" );
    update_option( 'wppsb_enable_gzip', "" );
    update_option( 'wppsb_expire_caching', "" );

    flush_rewrite_rules();
    wppsb_save_mod_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wppsb_deactivate_plugin' );

// Delete all the options (from database) on plugin uninstall
function wppsb_uninstall_plugin(){
	delete_option( 'wppsb_remove_query_strings' );
    delete_option( 'wppsb_enable_gzip' );
    delete_option( 'wppsb_expire_caching' );
}
?>
