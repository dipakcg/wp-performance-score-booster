<?php
/*
Plugin Name: WP Performance Score Booster
Plugin URI: https://github.com/dipakcg/wp-performance-score-booster
Description: Helps you to improve your website scores in services like PageSpeed, YSlow, Pingdoom and GTmetrix.
Author: Dipak C. Gajjar
Version: 1.0
Author URI: http://www.dipakgajjar.com/
*/

/* Remove query strings from static content */
function dcg_remove_query_strings( $src ){
	$rqs = explode( '?ver', $src );
        return $rqs[0];
}

add_filter( 'script_loader_src', 'dcg_remove_query_strings', 15, 1 );
add_filter( 'style_loader_src', 'dcg_remove_query_strings', 15, 1 );

?>