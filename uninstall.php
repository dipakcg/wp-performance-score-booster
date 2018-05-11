<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly.

delete_option( 'wppsb_plugin_version' );
delete_option( 'wppsb_remove_query_strings' );
delete_option( 'wppsb_enable_gzip' );
delete_option( 'wppsb_expire_caching' );
delete_option( 'wppsb_review_notice' );
delete_option( 'wppsb_activation_date' );

// Delete .htaccess backups (including backup storage page directory)
delete_storage_dir( get_home_path() . 'wp-content/wp-performance-score-booster');

function delete_storage_dir($dir) {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($dir);
}

?>