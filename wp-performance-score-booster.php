<?php
/*
Plugin Name: WP Performance Score Booster
Plugin URI: https://github.com/dipakcg/wp-performance-score-booster
Description: Helps you to improve your website scores in services like PageSpeed, YSlow, Pingdom and GTmetrix.
Author: Dipak C. Gajjar
Version: 1.1.1
Author URI: http://www.dipakgajjar.com/
*/

// Important: Don't forget to change version number at line line 7, 131 and 200.
// Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
add_action( 'admin_init', 'wppsb_add_stylesheet' );
function wppsb_add_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'wppsb-stylesheet', plugins_url('assets/css/style.css', __FILE__) );
    wp_enqueue_style( 'wppsb-stylesheet' );
}

// Register admin menu
add_action( 'admin_menu', 'wppsb_add_admin_menu' );
function wppsb_add_admin_menu() {
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	add_menu_page( 'WP Performance Score Booster Options', 'WP Performance Score Booster', 'manage_options', 'wp-performance-score-booster', 'wppsb_admin_options', plugins_url('assets/images/wppsb-icon-24x24.png', __FILE__) );
}

function wppsb_admin_options() {
	?>
	<div class="wrap">
	<table width="100%" border="0">
	<tr>
	<td width="75%">
	<h2><?php echo '<img src="' . plugins_url( 'assets/images/wppsb-icon-24x24.png' , __FILE__ ) . '" > ';  ?> WP Performance Score Booster Settings</h2>
	<hr />
	<?php
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	// Variables for the field and option names
	$hidden_field_name = 'wppsb_submit_hidden';
    $remove_query_strings = 'wppsb_remove_query_strings';
    $enable_gzip = 'wppsb_enable_gzip';
    $expire_caching = 'wppsb_expire_caching';

    // Read in existing option value from database
    $remove_query_strings_val = get_option($remove_query_strings);
    $enable_gzip_val = get_option($enable_gzip);
    $expire_caching_val = get_option($expire_caching);

	// See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y' ) {
        // Read their posted value
        $remove_query_strings_val = (isset($_POST[$remove_query_strings]) ? $_POST[$remove_query_strings] : "");
        $enable_gzip_val = (isset($_POST[$enable_gzip]) ? $_POST[$enable_gzip] : "");
        $expire_caching_val = (isset($_POST[$expire_caching]) ? $_POST[$expire_caching] : "");

        // Save the posted value in the database
        update_option( $remove_query_strings, $remove_query_strings_val );
        update_option( $enable_gzip, $enable_gzip_val );
        update_option( $expire_caching, $expire_caching_val );

		// If 'Remove query strings" checkbox ticked, add filter otherwise remove filter
        if ($remove_query_strings_val == 'on') {
	    	add_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
			add_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
			add_filter( 'script_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
			add_filter( 'style_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
	    }
	    else {
			remove_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
			remove_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
			remove_filter( 'script_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
			remove_filter( 'style_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
	    }

		// If 'Enable GZIP" checkbox ticked, add filter otherwise remove filter
        if ($enable_gzip_val == 'on') {
	    	add_filter('mod_rewrite_rules', 'wppsb_enable_gzip_filter');
	    }
	    else {
			remove_filter('mod_rewrite_rules', 'wppsb_enable_gzip_filter');
	    }

		// If 'Expire caching" checkbox ticked, add filter otherwise remove filter
        if ($expire_caching_val == 'on') {
	    	add_filter('mod_rewrite_rules', 'wppsb_expire_caching_filter');
	    }
	    else {
			remove_filter('mod_rewrite_rules', 'wppsb_expire_caching_filter');
	    }

	    flush_rewrite_rules();

        // Put an settings updated message on the screen
   	?>
   	<div class="updated"><p><strong>Settings Saved.</strong></p></div>
	<?php
	}
	?>
	<form method="post" name="options_form">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
	<p>
	<input type="checkbox" name="<?php echo $remove_query_strings; ?>" <?php checked( $remove_query_strings_val == 'on',true); ?> /> &nbsp; <span class="wppsb_settings"> Remove query strings from static content </span>
	</p>
	<p>
	<?php if (function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) { ?>
    	<input type="checkbox" name="<?php echo $enable_gzip; ?>" <?php checked( $enable_gzip_val == 'on',true); ?>  /> &nbsp; <span class="wppsb_settings"> Enable GZIP compression (compress text, html, javascript, css, xml and so on)</span>
    <?php }
    else { ?>
    	<input type="checkbox" name="<?php echo $enable_gzip; ?>" disabled="true" <?php checked( $enable_gzip_val == 'on',true); ?> /> &nbsp; <span class="wppsb_settings"> Enable GZIP compression (compress text, html, javascript, css, xml and so on)</span> <br /> <span class="wppsb_settings" style="margin-left:30px; color:RED;">Your web server does not support GZIP compression. Contact your hosting provider to enable it.</span>
    <?php } ?>
    </p>
    <p>
    <input type="checkbox" name="<?php echo $expire_caching; ?>" <?php checked( $expire_caching_val == 'on',true); ?> /> &nbsp; <span class="wppsb_settings"> Set expire caching (Leverage Browser Caching) </span>
    </p>
    <p><input type="submit" value="<?php esc_attr_e('Save Changes') ?>" class="button button-primary" name="submit" /></p>
    </form>
	</td>
	<td style="text-align: left;">
	<div class="wppsb_admin_dev_sidebar_div">
	<img src="http://www.gravatar.com/avatar/38b380cf488d8f8c4007cf2015dc16ac.jpg" width="100px" height="100px" /> <br />
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . plugins_url( 'assets/images/wppsb-support-this-16x16.png' , __FILE__ ) . '" > ';  ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3S8BRPLWLNQ38" target="_blank"> Support this plugin and donate </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . plugins_url( 'assets/images/wppsb-rate-this-16x16.png' , __FILE__ ) . '" > ';  ?> <a href="http://wordpress.org/support/view/plugin-reviews/wp-performance-score-booster" target="_blank"> Rate this plugin on WordPress.org </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . plugins_url( 'assets/images/wppsb-wordpress-16x16.png' , __FILE__ ) . '" > ';  ?> <a href="http://wordpress.org/support/plugin/wp-performance-score-booster" target="_blank"> Get support on on WordPress.org </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . plugins_url( 'assets/images/wppsb-github-16x16.png' , __FILE__ ) . '" > ';  ?> <a href="https://github.com/dipakcg/wp-performance-score-booster" target="_blank"> Contribute development on GitHub </a> </span>
	<!-- <span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . plugins_url( 'assets/images/wppsb-other-plugins-16x16.png' , __FILE__ ) . '" > ';  ?> <a href="http://profiles.wordpress.org/dipakcg#content-plugins" target="_blank"> Get my other plugins </a> </span> -->
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . plugins_url( 'assets/images/wppsb-twitter-16x16.png' , __FILE__ ) . '" > ';  ?>Follow me on Twitter: <a href="https://twitter.com/dipakcgajjar" target="_blank">@dipakcgajjar</a> </span>
	<br />
	<span class="wppsb_admin_dev_sidebar" style="float: right;"> Version: <strong> 1.1.1 </strong> </span>
	</div>
	</td>
	</tr>
	</table>
	</div>
	<?php
}

// Remove query strings from static content
function wppsb_remove_query_strings_q( $src ) {
	$rqs = explode( '?ver', $src );
	return $rqs[0];
}
function wppsb_remove_query_strings_emp( $src ) {
	$rqs = explode( '&ver', $src );
	return $rqs[0];
}

// Enable GZIP Compression
function wppsb_enable_gzip_filter( $rules ) {
$gzip_htaccess_content = <<<EOD
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
## END : Enable GZIP Compression ##\n\n
EOD;
    return $gzip_htaccess_content . $rules;
}

// Enable expire caching
function wppsb_expire_caching_filter( $rules ) {
$expire_cache_htaccess_content = <<<EOD
\n## Added by WP Performance Score Booster ##
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
    return $expire_cache_htaccess_content . $rules;
}

function wppsb_add_header() {
	$head_comment = <<<EOD
<!-- Performance scores of this site is tuned by WP Performance Score Booster plugin v1.1.1 - http://wordpress.org/plugins/wp-performance-score-booster -->\n
EOD;
	echo $head_comment;
}
add_action('wp_head', 'wppsb_add_header', 1);

// Calling this function will make flush_rules to be called at the end of the PHP execution
function wppsb_activate_plugin() {

    // Save default options value in the database
    update_option( 'wppsb_remove_query_strings', 'on' );
    add_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
	add_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
	add_filter( 'script_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
	add_filter( 'style_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );

	if (function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) {
		update_option( 'wppsb_enable_gzip', 'on' );
		add_filter('mod_rewrite_rules', 'wppsb_enable_gzip_filter');
	}
	else {
		update_option( 'wppsb_enable_gzip', '' );
	}

	update_option( 'wppsb_expire_caching', 'on' );
	add_filter('mod_rewrite_rules', 'wppsb_expire_caching_filter');

    flush_rewrite_rules();
}

// On plugin activation, call the function that will make flush_rules to be called at the end of the PHP execution
register_activation_hook( __FILE__, 'wppsb_activate_plugin' );

function wppsb_deactivate_plugin() {
	// Remove filters on plugin deactivate
	remove_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
	remove_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
	remove_filter( 'script_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
	remove_filter( 'style_loader_src', 'wppsb_remove_query_strings_emp', 15, 1 );
	remove_filter('mod_rewrite_rules', 'wppsb_enable_gzip_filter');
	remove_filter('mod_rewrite_rules', 'wppsb_expire_caching_filter');

	// Delete default options value in the database
	/* delete_option( 'wppsb_remove_query_strings' );
	delete_option( 'wppsb_enable_gzip' );
	delete_option( 'wppsb_expire_caching' ); */

    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wppsb_deactivate_plugin' );
?>
