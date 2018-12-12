<?php
function wppsb_admin_options() {
	?>
	<div class="wrap">
	<table width="100%" border="0">
	<tr>
	<td width="75%">
	<h2><?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-icon-24x24.png' . '" > ';  ?> <?php _e('WP Performance Score Booster Settings', 'wp-performance-score-booster'); ?></h2>
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
    global $wppsb_plugin_version, $wppsb_remove_query_strings, $wppsb_enable_gzip, $wppsb_expire_caching;
    $remove_query_strings_val = $wppsb_remove_query_strings;
    $enable_gzip_val = $wppsb_enable_gzip;
    $expire_caching_val = $wppsb_expire_caching;

    // Display notice if clicked 'Apply Updates' button (applicable to plugin updates)
    if ( isset($_GET['update-applied']) && $_GET['update-applied'] == 'true' ) {
	?>
	    <div class="updated"><p><strong><?php _e('<strong>Update applied successfully!</strong>', 'wp-performance-score-booster'); ?></strong></p></div>
	<?php
    }

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

	    flush_rewrite_rules();
	    wppsb_save_mod_rewrite_rules($enable_gzip_val, $expire_caching_val);

        // Put the settings updated message on the screen
   	    ?>
   	    <div class="updated"><p><strong><?php _e('<strong>Settings Saved.</strong>', 'wp-performance-score-booster'); ?></strong></p></div>
	<?php
	}
    ?>

	<form method="post" name="options_form">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
	<table>
	<!-- Remove Query String -->
	<tr> <td class="wppsb_onoff">
	<div class="wppsb_onoffswitch">
	<input type="checkbox" name="<?php echo $remove_query_strings; ?>" <?php checked( $remove_query_strings_val == 'on',true); ?> class="wppsb_onoffswitch-checkbox" id="<?php echo $remove_query_strings; ?>" />
	<label class="wppsb_onoffswitch-label" for="<?php echo $remove_query_strings; ?>">
		<span class="wppsb_onoffswitch-inner"></span>
		<span class="wppsb_onoffswitch-switch"></span>
	</label>
	</div>
	</td> <td>
	<label for="<?php echo $remove_query_strings; ?>" class="wppsb_settings" style="display: inline;"> <?php _e('Remove query strings from static content', 'wp-performance-score-booster'); ?> </label>
	</td> </tr>

	<!-- Enable GZIP -->
	<tr>
	<?php if (function_exists('ob_gzhandler') || ini_get('zlib.output_compression')) { // if web server supports GZIP ?>
	<td class="wppsb_onoff">
	<div class="wppsb_onoffswitch">
	<input type="checkbox" name="<?php echo $enable_gzip; ?>" <?php checked( $enable_gzip_val == 'on',true); ?> class="wppsb_onoffswitch-checkbox" id="<?php echo $enable_gzip; ?>" />
	<label class="wppsb_onoffswitch-label" for="<?php echo $enable_gzip; ?>">
		<span class="wppsb_onoffswitch-inner"></span>
		<span class="wppsb_onoffswitch-switch"></span>
	</label>
	</div>
	</td> <td>
	<label for="<?php echo $enable_gzip; ?>" class="wppsb_settings" style="display: inline;"> <?php _e('Enable GZIP compression <i>(compress text, html, javascript, css, xml and so on)</i>', 'wp-performance-score-booster'); ?> </label>
	</td>
    <?php }
    else { // if web server doesn't support GZIP ?>
	<td class="wppsb_onoff">
	<div class="wppsb_onoffswitch">
	<input type="checkbox" name="<?php echo $enable_gzip; ?>" disabled="true" <?php checked( $enable_gzip_val == 'off',true); ?> class="wppsb_onoffswitch-checkbox" id="<?php echo $enable_gzip; ?>" />
	<label class="wppsb_onoffswitch-label" for="<?php echo $enable_gzip; ?>">
		<span class="wppsb_onoffswitch-inner"></span>
		<span class="wppsb_onoffswitch-switch"></span>
	</label>
	</div>
	</td> <td>
	<span class="wppsb_settings"> <?php _e('Enable GZIP compression <i>(compress text, html, javascript, css, xml and so on)</i>', 'wp-performance-score-booster'); ?> </span> <br />
	<span class="wppsb_settings" style="color:RED; font-style: italic; font-size: 13px !important;"> <?php _e('Your web server does not support GZIP compression. Contact your hosting provider to enable it.', 'wp-performance-score-booster'); ?> </span>
	</td>
    <?php } ?>
    </tr>

    <!-- Leverage Browser Caching -->
    <tr> <td class="wppsb_onoff">
	<div class="wppsb_onoffswitch">
    <input type="checkbox" name="<?php echo $expire_caching; ?>" <?php checked( $expire_caching_val == 'on',true); ?> class="wppsb_onoffswitch-checkbox" id="<?php echo $expire_caching; ?>" />
	<label class="wppsb_onoffswitch-label" for="<?php echo $expire_caching; ?>">
		<span class="wppsb_onoffswitch-inner"></span>
		<span class="wppsb_onoffswitch-switch"></span>
	</label>
	</div>
    </td> <td>
	<label for="<?php echo $expire_caching; ?>" class="wppsb_settings" style="display: inline;"> <?php _e('Leverage Browser Caching <i>(set expire caching)</i>', 'wp-performance-score-booster'); ?> </label>
    </td> </tr>

    <!-- Extra Options - must be added in the future version -->

    <!--
    <tr> <td colspan="2"> <h2> <?php _e('More settings <i>(optional)</i>', 'wp-performance-score-booster'); ?> </h2> </td> </tr>

    <tr> <td class="wppsb_onoff">
	<div class="wppsb_onoffswitch">
    <input type="checkbox" name="<?php echo $expire_caching; ?>" <?php checked( $expire_caching_val == 'on',true); ?> class="wppsb_onoffswitch-checkbox" id="<?php echo $expire_caching; ?>" />
	<label class="wppsb_onoffswitch-label" for="<?php echo $expire_caching; ?>">
		<span class="wppsb_onoffswitch-inner"></span>
		<span class="wppsb_onoffswitch-switch"></span>
	</label>
	</div>
    </td> <td>
	<label for="<?php echo $expire_caching; ?>" class="wppsb_settings" style="display: inline;"> <?php _e('Disable Heartbeat API completely', 'wp-performance-score-booster'); ?> </label>
    </td> </tr>

    <tr> <td class="wppsb_onoff">
	<div class="wppsb_onoffswitch">
    <input type="checkbox" name="<?php echo $expire_caching; ?>" <?php checked( $expire_caching_val == 'on',true); ?> class="wppsb_onoffswitch-checkbox" id="<?php echo $expire_caching; ?>" />
	<label class="wppsb_onoffswitch-label" for="<?php echo $expire_caching; ?>">
		<span class="wppsb_onoffswitch-inner"></span>
		<span class="wppsb_onoffswitch-switch"></span>
	</label>
	</div>
    </td> <td>
	<label for="<?php echo $expire_caching; ?>" class="wppsb_settings" style="display: inline;"> <?php _e('Remove WordPress Emoji scripts', 'wp-performance-score-booster'); ?> </label>
    </td> </tr>
    -->

	</table>
    <p><input style="font-size: 15px; color: white; font-weight: bold;" type="submit" value="<?php esc_attr_e('Save Changes', 'wp-performance-score-booster'); ?>" class="button button-primary" name="submit" /></p>
    </form>
	</td>
	<td width="25%" valign="bottom" style="text-align: left;">
	<div class="wppsb_admin_dev_sidebar_div">
	<!-- <img src="//www.gravatar.com/avatar/38b380cf488d8f8c4007cf2015dc16ac.jpg" width="100px" height="100px" /> <br /> -->
	<br />
	<!-- <span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-support-this-16x16.png' . '" > ';  ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3S8BRPLWLNQ38" target="_blank"> <?php _e('Donate and support this plugin', 'wp-performance-score-booster'); ?> </a> </span> -->
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-rate-this-16x16.png' . '" > ';  ?> <a href="//wordpress.org/support/plugin/wp-performance-score-booster/reviews/?rate=5#new-post" target="_blank"> <?php _e('Rate this plugin on WordPress.org', 'wp-performance-score-booster'); ?> </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-wordpress-16x16.png' . '" > ';  ?> <a href="//wordpress.org/support/plugin/wp-performance-score-booster" target="_blank"> <?php _e('Get support on WordPress.org', 'wp-performance-score-booster'); ?> </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-github-16x16.png' . '" > ';  ?> <a href="//github.com/dipakcg/wp-performance-score-booster" target="_blank"> <?php _e('Contribute development on GitHub', 'wp-performance-score-booster'); ?> </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-other-plugins-16x16.png' . '" > ';  ?> <a href="//profiles.wordpress.org/dipakcg#content-plugins" target="_blank"> <?php _e('Get my other plugins', 'wp-performance-score-booster'); ?> </a> </span>
	<span class="wppsb_admin_dev_sidebar"> <?php echo '<img src="' . WPPSB_URL . '/assets/images/wppsb-twitter-16x16.png' . '" > ';  ?>Follow me on Twitter: <a href="//twitter.com/dipakcgajjar" target="_blank">@dipakcgajjar</a> </span>
	<br />
	<span class="wppsb_admin_dev_sidebar" style="float: right;"> <?php _e('Version:', 'wp-performance-score-booster'); ?> <strong> <?php echo $wppsb_plugin_version; ?> </strong> </span>
	</div>
	</td>
	</tr>
	</table>
	</div>
	<hr style="margin: 2em 0 1.5em 0;" />
	<?php
	// Promo - Ad contents
	$promo_content = wp_remote_fopen("https://dipakgajjar.com/public/promos.html");
    echo $promo_content;
	?>
	<?php // Bottom - News and Tweets part ?>
	<hr style="margin: 1.5em 0 2em 0;" />
    <table cellspacing="0" cellpadding="0" class="wppsb_news_section"> <tr>
    <!-- News and Updates -->
    <td width="48%" valign="top">
    <h2><strong>News & Updates from Dipak C. Gajjar</strong></h2>
    <hr />
    <div class="wppsb_rss-widget">
	<?php
    /* Load the news content from site url */
    $news_content = wp_remote_fopen("https://dipakgajjar.com/public/news-and-updates.html");
    echo $news_content;
    ?>
	</div> </td>
	<!-- Referrals -->
	<td width="1%"> &nbsp; </td>
	<td width="51%" valign="top">
	<?php
    /* Load the referrals content from site url */
    $referrals_content = wp_remote_fopen("https://dipakgajjar.com/public/recommendations.html");
    echo $referrals_content;
    ?>
	</td> </tr> </table>
	<?php
}

// Register admin menu
add_action( 'admin_menu', 'wppsb_add_admin_menu' );
function wppsb_add_admin_menu() {
	// add_menu_page( __('WP Performance Score Booster Settings', 'wp-performance-score-booster'), __('WP Performance Score Booster', 'wp-performance-score-booster'), 'manage_options', 'wp-performance-score-booster', 'wppsb_admin_options', plugins_url('assets/images/wppsb-icon-24x24.png', __FILE__) );
	add_options_page( __('WP Performance Score Booster Settings', 'wp-performance-score-booster'), __('WP Performance Score Booster', 'wp-performance-score-booster'), 'manage_options', 'wp-performance-score-booster', 'wppsb_admin_options' );
}
?>
