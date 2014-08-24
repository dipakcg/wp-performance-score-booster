<?php
/*
Plugin Name: WP Performance Score Booster
Plugin URI: https://github.com/dipakcg/wp-performance-score-booster
Description: Helps you to improve your website scores in services like PageSpeed, YSlow, Pingdoom and GTmetrix.
Author: Dipak C. Gajjar
Version: 1.0
Author URI: http://www.dipakgajjar.com/
*/

// Remove query strings from static content
function wppsb_remove_query_strings( $src ) {
	$rqs = explode( '?ver', $src );
        return $rqs[0];
}
add_filter( 'script_loader_src', 'wppsb_remove_query_strings', 15, 1 );
add_filter( 'style_loader_src', 'wppsb_remove_query_strings', 15, 1 );

function wppsb_add_to_htaccess( $rules ) {

// Add the rewrite rules in .htaccess
$htaccess_content = <<<EOD
\n## Added by WP Performance Score Booster ##
## BEGIN : Enable GZIP Compression (compress text, html, javascript, css, xml and so on) ##
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
AddOutputFilterByType DEFLATE application/x-httpd-php
AddOutputFilterByType DEFLATE application/x-httpd-fastphp
AddOutputFilterByType DEFLATE image/svg+xml
SetOutputFilter DEFLATE
</IfModule>
## END : Enable GZIP Compression ##

## Added by WP Performance Score Booster ##
## BEGIN : Expires Caching (Leverage Browser Caching) ##
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType image/jpg "access 2 week"
ExpiresByType image/jpeg "access 2 week"
ExpiresByType image/gif "access 2 week"
ExpiresByType image/png "access 2 week"
ExpiresByType text/css "access 2 week"
ExpiresByType application/pdf "access 2 week"
ExpiresByType text/x-javascript "access 2 week"
ExpiresByType application/x-shockwave-flash "access 2 week"
ExpiresByType image/x-icon "access 2 week"
ExpiresDefault "access 2 week"
</IfModule>
## END : Expires Caching (Leverage Browser Caching) ##\n\n
EOD;
    return $htaccess_content . $rules;
}
add_filter('mod_rewrite_rules', 'wppsb_add_to_htaccess');

// Calling this function will make flush_rules to be called at the end of the PHP execution
function wppsb_enable_flush_rules() {
    global $wp_rewrite;

    // Flush the rewrite rules
    $wp_rewrite->flush_rules();
}

// On plugin activation, call the function that will make flush_rules to be called at the end of the PHP execution
register_activation_hook( __FILE__, 'wppsb_enable_flush_rules' );

function wppsb_deactivate_plugin() {
	// This will remove the rewrite rules
	remove_filter('mod_rewrite_rules', 'wppsb_add_to_htaccess');

	global $wp_rewrite;

    // Flush the rewrite rules
    $wp_rewrite->flush_rules();
}
register_deactivation_hook( __FILE__, 'wppsb_deactivate' );
?>
