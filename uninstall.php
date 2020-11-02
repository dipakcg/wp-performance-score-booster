<?php
// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load WP Performance Score Booster - plugin main file.
include_once( 'wp-performance-score-booster.php' );

/** Delete all the Plugin Options */
delete_option( 'wppsb_plugin_version' );
delete_option( 'wppsb_remove_query_strings' );
delete_option( 'wppsb_enable_gzip' );
delete_option( 'wppsb_expire_caching' );
delete_option( 'wppsb_instant_page_preload' );
delete_option( 'wppsb_review_notice' );
delete_option( 'wppsb_activation_date' );


/**********************************
* Delete the directory created for htaccess backup at the time of plugin activation
* location: wp-content/wp-performance-score-booster
**********************************/
wppsb_delete_dir( WPPSB_STORAGE_PATH );


/**********************************
* Delete directory and all the files within
**********************************/
function wppsb_delete_dir( $folderName ) {

    if ( is_dir( $folderName ) ) {
        $folderHandle = opendir( $folderName );
    }
    
    if ( !$folderHandle ) {
        return false;
    }
    
    while( $file = readdir( $folderHandle ) ) {
        if( $file != '.' && $file != '..' ) {
            if( ! is_dir( $folderName. '/' . $file ) ) {
                unlink( $folderName . '/' . $file );
            } else {
                wppsb_delete_dir( $folderName . '/' . $file );
            }
        }
    }
    
    closedir( $folderHandle );
    rmdir( $folderName );
    
    return true;
}