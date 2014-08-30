<?php
/*
Plugin Name: WP Performance Score Booster
Plugin URI: https://github.com/dipakcg/wp-performance-score-booster
Description: Helps you to improve your website scores in services like PageSpeed, YSlow, Pingdoom and GTmetrix.
Author: Dipak C. Gajjar
Version: 1.1
Author URI: http://www.dipakgajjar.com/
*/

// Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
add_action( 'admin_init', 'wppsb_add_stylesheet' );
function wppsb_add_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'wppsb-stylesheet', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'wppsb-stylesheet' );

    wp_register_style( 'wppsb-gfont', 'http://fonts.googleapis.com/css?family=Roboto' );
    wp_enqueue_style( 'wppsb-gfont' );
}

// Register admin menu
add_action( 'admin_menu', 'wppsb_add_admin_menu' );
function wppsb_add_admin_menu() {
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	add_menu_page( 'WP Performance Score Booster Options', 'WP Performance Score Booster', 'manage_options', 'wp-performance-score-booster', 'wppsb_admin_options', 'https://cdn1.iconfinder.com/data/icons/free-dark-blue-cloud-icons/24/Dashboard.png' );
}

function wppsb_admin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
	<div class="wrap">
	<table width="100%" border="0">
	<tr>
	<td width="70%">
	<h2><img src="https://cdn1.iconfinder.com/data/icons/free-dark-blue-cloud-icons/24/Dashboard.png" />  WP Performance Score Booster Settings</h2>
	<form method="post" action="options.php">
	<p>
	<input type="checkbox" name="remove_query_strings" checked='checked' /> &nbsp; <span class="wppsb_settings"> Remove query strings from static content </span>
	</p>
	<p>
	<?php if (function_exists('ob_gzhandler') && ini_get('zlib.output_compression')) { ?>
    <input type="checkbox" name="enable_gzip" checked='checked' /> &nbsp; <span class="wppsb_settings"> Enable GZIP compression (compress text, html, javascript, css, xml and so on)</span>
    <?php }
    else { ?>
    <input type="checkbox" name="enable_gzip" disabled="false" /> &nbsp; <span class="wppsb_settings"> Enable GZIP compression (compress text, html, javascript, css, xml and so on)</span> <br /> <span class="wppsb_settings" style="margin-left:30px; color:RED;">Your web server does not support GZIP compression. Contact your hosting provider to enable it.</span>
    <?php } ?>
    </p>
    <p>
    <input type="checkbox" name="expire_caching" checked='checked' /> &nbsp; <span class="wppsb_settings"> Set expire caching (Leverage Browser Caching) </span>
    </p>
    <p><input type="submit" value="Save" class="button button-primary" name="submit" /></p>
    </form>
	</td>
	<td style="text-align: center;">
	<img src="http://www.gravatar.com/avatar/38b380cf488d8f8c4007cf2015dc16ac.jpg" width="125px" height="125px" /> <br />
	<strong> Get in touch with me on Twitter: <a href="https://twitter.com/dipakcgajjar" target="_blank">@dipakcgajjar</a></strong>
	</td>
	</tr>
	</table>
	</div>
	<?php
}

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
register_deactivation_hook( __FILE__, 'wppsb_deactivate_plugin' );
?>
