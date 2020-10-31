<?php
// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load WP Performance Score Booster - plugin main file.
include_once( 'wp-performance-score-booster.php' );

/** Delete all the Plugin Options */
$options = array(
    'wppsb_plugin_version',
    'wppsb_remove_query_strings',
    'wppsb_enable_gzip',
    'wppsb_expire_caching',
    'wppsb_review_notice',
    'wppsb_activation_date',
    'wppsb_instant_page_preload'
);

foreach ( $options as $option ) {
    if ( get_option( $option ) ) {
        delete_option( $option );
    }
}

/**********************************
* Delete the directory created for htaccess backup at the time of plugin activation
* location: wp-content/wp-performance-score-booster
**********************************/
wppsb_delete_dir( WPPSB_STORAGE_PATH );

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
