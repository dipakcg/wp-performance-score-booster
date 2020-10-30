<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly.

delete_option( 'wppsb_plugin_version' );
delete_option( 'wppsb_remove_query_strings' );
delete_option( 'wppsb_enable_gzip' );
delete_option( 'wppsb_expire_caching' );
delete_option( 'wppsb_review_notice' );
delete_option( 'wppsb_activation_date' );

$wppsb_backup_dir = get_home_path() . 'wp-content/wp-performance-score-booster'

if( chmod( $wppsb_backup_dir , 0777 ) ) {
   delete_storage_dir( $wppsb_backup_dir );
}

function delete_storage_dir( $dirPath ) {
    if ( ! is_dir( $dirPath ) ) {
        throw new InvalidArgumentException( "$dirPath must be a directory" );
    }
    
    if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {
        $dirPath .= '/';
    }
    
    $files = glob( $dirPath . '*', GLOB_MARK);
    foreach ( $files as $file ) {
        if ( is_dir( $file ) ) {
            self::deleteDir( $file );
        } else {
            unlink( $file );
        }
    }
    rmdir( $dirPath );
}