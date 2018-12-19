<?php
/*
Plugin Name: WP Performance Score Booster
Plugin URI: https://github.com/dipakcg/wp-performance-score-booster
Description: Speed-up page load times and improve website scores in services like PageSpeed, YSlow, Pingdom and GTmetrix.
Version: 1.9.2.2
Author: Dipak C. Gajjar
Author URI: https://dipakgajjar.com
Text Domain: wp-performance-score-booster
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

include_once ( ABSPATH . 'wp-admin/includes/file.php' ); // to get get_home_path() function work
include_once ( ABSPATH . 'wp-admin/includes/plugin.php' ); // to is_plugin_active()() function work

// Define plugin version for future releases
if (!defined('WPPSB_PLUGIN_VERSION')) {
    define('WPPSB_PLUGIN_VERSION', '1.9.2.2');
}

define( 'WPPSB_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) ); // plugin Path
define( 'WPPSB_FILE', __FILE__ ); // plugin file
define( 'WPPSB_URL', plugins_url( '', __FILE__ ) ); // plugin url
define( 'WPPSB_STORAGE_PATH', get_home_path() . 'wp-content/wp-performance-score-booster' ); // storage path to store .htaccess backups

include_once 'admin-page.php'; // admin options page.
include_once 'data-processing.php'; // process the data such as remove query strings, enable gzip and leverage browser caching.

global $wppsb_plugin_version, $wppsb_remove_query_strings, $wppsb_enable_gzip, $wppsb_expire_caching;
$wppsb_plugin_version = ( get_option('wppsb_plugin_version') ? get_option('wppsb_plugin_version') : WPPSB_PLUGIN_VERSION );
$wppsb_remove_query_strings = ( FALSE !== get_option('wppsb_remove_query_strings') ? get_option('wppsb_remove_query_strings') : 'on'  );
$wppsb_enable_gzip = ( FALSE !== get_option('wppsb_enable_gzip') ? get_option('wppsb_enable_gzip') : 'on' );
$wppsb_expire_caching = ( FALSE !== get_option('wppsb_expire_caching') ? get_option('wppsb_expire_caching') : 'on'  );

function wppsb_load_plugin_textdomain() {

	$domain = 'wp-performance-score-booster';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	// wp-content/languages/plugin-name/plugin-name-de_DE.mo
	load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
	load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

}

// Call all the functions together to hook with init()
function wppsb_master_init () {
    wppsb_load_plugin_textdomain(); // load plugin textdomain for language trnaslation
    wppsb_update_checker(); // Check if plugin updated
}
add_action( 'init', 'wppsb_master_init' );

// Call all the functions together to hook with admin_init()
function wppsb_master_admin_init () {
    wppsb_add_stylesheet(); // adds plugin stylesheet
    wppsb_update_processor(); // Reload the config (rewrite rules) after applying plugin updates

    /* BEGIN : Rate this plugin on wordpress */
    // check for admin notice dismissal
    if ( isset( $_POST['wppsb-already-reviewed'] ) ) {
        update_option( 'wppsb_review_notice', "" );
        if ( get_option( 'wppsb_activation_date' ) ) {
            delete_option( 'wppsb_activation_date' );
        }
    }

    // display admin notice after 30 days if clicked 'May be later'
    if ( isset( $_POST['wppsb-review-later'] ) ) {
        update_option( 'wppsb_review_notice', "" );
        update_option( 'wppsb_activation_date', strtotime( "now" ) );
    }

    $install_date = get_option( 'wppsb_activation_date' );
    $past_date = strtotime( '-30 days' );

    if ( FALSE !== get_option( 'wppsb_activation_date' ) && $past_date >= $install_date ) {
        update_option( 'wppsb_review_notice', "on" );
        delete_option( 'wppsb_activation_date' );
    }
    /* END : Rate this plugin on wordpress */
}
add_action( 'admin_init', 'wppsb_master_admin_init' );

// Add settings link on plugin page
function dcg_settings_link($links) {
	// $settings_link = '<a href="admin.php?page=wp-performance-score-booster">Settings</a>';
	$wppsb_links = array( '<a href="options-general.php?page=wp-performance-score-booster">Settings</a>' );
	// array_unshift($links, $settings_link);
	$links = array_merge($wppsb_links, $links);
	return $links;
}
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'dcg_settings_link' );

// Adding WordPress plugin meta links
function wppsb_plugin_meta_links( $links, $file ) {
	// Create link
	if ( $file == plugin_basename(__FILE__) ) {
		return array_merge(
			$links,
			array( '<a href="https://dipakgajjar.com/products/wordpress-speed-optimization-service?utm_source=plugins%20page&utm_medium=text%20link&utm_campaign=wordplress%20plugins" style="color:#FF0000;" target="_blank">Order WordPress Speed Optimisation Service</a>' )
		);
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'wppsb_plugin_meta_links', 10, 2 );

// Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
function wppsb_add_stylesheet() {
    // Respects SSL, style.css is relative to the current file
    wp_register_style( 'wppsb-stylesheet', WPPSB_URL . '/assets/css/style.min.css' );
    wp_enqueue_style( 'wppsb-stylesheet' );
}

// Add header
function wppsb_add_header() {
	// Get the plugin version from options (in the database)
	global $wppsb_plugin_version;
	$head_comment = <<<EOD
<!-- Speed of this site is optimised by WP Performance Score Booster plugin v$wppsb_plugin_version - https://dipakgajjar.com/wp-performance-score-booster/ -->
EOD;
	$head_comment = $head_comment . PHP_EOL;
	print ($head_comment);
}
add_action('wp_head', 'wppsb_add_header', 1);

// If 'Remove query strings" checkbox ticked, add filter otherwise remove filter
if ( $wppsb_remove_query_strings == 'on') {
	// Disable the functionality under Admin to avoid any conflicts
	if ( ! is_admin() ) {
		add_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
		add_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
	}
}
else {
	remove_filter( 'script_loader_src', 'wppsb_remove_query_strings_q');
	remove_filter( 'style_loader_src', 'wppsb_remove_query_strings_q');
}

// Display admin notice to apply changes in the occurrence of update (when plugin updated)
function wppsb_apply_updates_notice() {
    global $wppsb_plugin_version;
    $notice_contents = "<p style=\"font-size: 15px; color: #FF9900;\">WP Performance Score Booster has been updated to version: " . WPPSB_PLUGIN_VERSION . "</p>";
    $notice_contents .= "<p><a href=\"options-general.php?page=wp-performance-score-booster&apply-updates=true\" class=\"button button-primary\" style=\"font-size: 15px; color: #FFFFFF; font-weight: bold;\">Apply Changes</a></p>";
    ?>
	<div class="notice notice-success"><p><strong><?php _e($notice_contents, 'wp-performance-score-booster'); ?></strong></p></div>
    <?php
}

// Check if plugin updated
function wppsb_update_checker() {
    global $wppsb_plugin_version;
    if ($wppsb_plugin_version != WPPSB_PLUGIN_VERSION) {
        if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
            add_action( 'admin_notices', 'wppsb_apply_updates_notice' );
        }
    }
}

// If 'Apply Updates' clicked under admin notice (in case of plugin updated)
function wppsb_update_processor() {
    global $wppsb_plugin_version , $wppsb_enable_gzip, $wppsb_expire_caching;
    if ( isset($_GET['apply-updates']) && $_GET['apply-updates'] == 'true' ) {
        if ($wppsb_plugin_version != WPPSB_PLUGIN_VERSION) {
            update_option('wppsb_plugin_version', WPPSB_PLUGIN_VERSION);
            flush_rewrite_rules();
            wppsb_save_mod_rewrite_rules($wppsb_enable_gzip, $wppsb_expire_caching);
            exit ( wp_redirect( admin_url( 'options-general.php?page=wp-performance-score-booster&update-applied=true' ) ) );
    	}
    }
}

// Calling this function will make flush_rules to be called at the end of the PHP execution
function wppsb_activate_plugin() {

    global $wppsb_remove_query_strings, $wppsb_enable_gzip, $wppsb_expire_caching;
    wppsb_htaccess_bakup(); // Backup .htacces before appending any rules

    /* Create transient data for submit review admin notice */
	// set_transient( 'wppsb_submit_review_transient', true, 360 );

	// Rate this plugin on wordpress - check for admin notice dismissal
    if ( FALSE === get_option( 'wppsb_review_notice' ) ) {
        add_option( 'wppsb_review_notice', "on" );
    }

    // Save default options value in the database
    add_option( 'wppsb_plugin_version', WPPSB_PLUGIN_VERSION );
    add_option( 'wppsb_remove_query_strings', $wppsb_remove_query_strings );

    if ( $wppsb_remove_query_strings == 'on' ) {
        add_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
        add_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
    }

	if (function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) {
		add_option( 'wppsb_enable_gzip', $wppsb_enable_gzip );
	}
	else {
		add_option( 'wppsb_enable_gzip', '' );
	}

	add_option( 'wppsb_expire_caching', $wppsb_expire_caching );

    flush_rewrite_rules();
    wppsb_save_mod_rewrite_rules($wppsb_enable_gzip, $wppsb_expire_caching);
}
register_activation_hook( __FILE__, 'wppsb_activate_plugin' );


/* Add admin notice for requesting plugin review */
add_action( 'admin_notices', 'wppsb_submit_review_notice' );
function wppsb_submit_review_notice() {
    global $wppsb_plugin_version;

    /* Display review plugin notice if plugin updated */
    // only applies to older versions of the plugin (older than 1.9.1) where option isn't set
    if ( isset( $_GET['update-applied'] ) && $_GET['update-applied'] == 'true' ) {
        if ( FALSE === get_option('wppsb_review_notice' ) ) {
            update_option( 'wppsb_review_notice', "on" );
        }
    }

	/* Check transient that's been set on plugin activation or check if user has already submitted review */
	// if( get_transient( 'wppsb_submit_review_transient' ) || !get_user_meta( $user_id, 'wppsb_submit_review_dismissed' ) ) {
	if( get_option( 'wppsb_review_notice') && get_option( 'wppsb_review_notice' ) == "on"  ) {

    	$notice_contents = "<p> Thank you for using <strong>WP Performance Score Booster</strong>. </p>";
    	$notice_contents .= "<p> Could you please do me a BIG favour and give this plugin a 5-star rating on WordPress? It will help me spread the word and boost my motivation. — Dipak C. Gajjar </p>";
    	$notice_contents .= "<p> <a href=\"#\"id=\"letMeReview\" class=\"button button-primary\">Yes, you deserve it</a> &nbsp; <a href=\"#\" id=\"willReviewLater\" class=\"button button-primary\">Maybe later</a> &nbsp; <a href=\"#\" id=\"alredyReviewed\" class=\"button button-primary\">I already did it</a> </p>";
		?>
		<div class="notice notice-info is-dismissible" id="wppsb-review-notice"> <?php _e($notice_contents, 'wp-performance-score-booster'); ?> </div>
		<script type="text/javascript">
    		// set jQuery in noConflict mode. This helps to mitigate conflicts between jQuery scripts. jQuery conflicts are all too common with themes and plugins.
    		var $j = jQuery.noConflict();
            $j(document).ready( function() {
                var loc = location.href;
                // loc += loc.indexOf("?") === -1 ? "?" : "&";

                $j("#letMeReview").on('click', function() {
                    /*jQuery('.notice').slideUp("slow", function(){;
                        window.open("//wordpress.org/support/plugin/wp-performance-score-booster/reviews/?rate=5#new-post", "_blank");
                    });*/
                    $j('.notice').slideUp();
                    $j.ajax({
                        url: loc,
                        type: 'POST',
                        data: {
                            "wppsb-review-later": ''
                        },
                        success: function(msg) {
                            window.open("//wordpress.org/support/plugin/wp-performance-score-booster/reviews/?rate=5#new-post", "_blank");
                        }
                    });
                });
                $j("#willReviewLater").on('click', function() {
                    $j('.notice').slideUp();
                    $j.ajax({
                        url: loc,
                        type: 'POST',
                        data: {
                            "wppsb-review-later": ''
                        }/* ,
                        success: function(msg) {
                            console.log("WPPSB DEBUG: Review the Plugin Later.");
                        } */
                    });
                });
                $j("#alredyReviewed").on('click', function() {
                    $j('.notice').slideUp();
                    $j.ajax({
                        url: loc,
                        type: 'POST',
                        data: {
                            "wppsb-already-reviewed": ''
                        }
                    });
                });
                /* If top-right X button clicked */
                $j(document).on('click', '#wppsb-review-notice .notice-dismiss', function() {
                    $j.ajax({
                        url: loc,
                        type: 'POST',
                        data: {
                            "wppsb-already-reviewed": ''
                        }
                    });
                });
            });
        </script>
		<?php
        // delete_transient( 'wppsb_submit_review_transient' );
    }
}

function wppsb_htaccess_bakup() {
    if (!file_exists( WPPSB_STORAGE_PATH )) {
        mkdir( WPPSB_STORAGE_PATH, 0777, true );
    }

    $htaccess_file = get_home_path() . '.htaccess'; // original .htaccess file
    $htaccess_bak = WPPSB_STORAGE_PATH . '/.htaccess.wppsb';

    copy($htaccess_file, $htaccess_bak);
}

// Remove filters/functions on plugin deactivation
function wppsb_deactivate_plugin() {

	// Clear (off) all the options value (from database)
	/* update_option( 'wppsb_remove_query_strings', "" );
    update_option( 'wppsb_enable_gzip', "" );
    update_option( 'wppsb_expire_caching', "" ); */

    flush_rewrite_rules();
    wppsb_save_mod_rewrite_rules('', '');
}
register_deactivation_hook( __FILE__, 'wppsb_deactivate_plugin' );
?>
