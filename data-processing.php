<?php
/**********************************
* Remove query strings from static content
**********************************/
function wppsb_remove_query_string_init() {
    global $wppsb_remove_query_strings;
    
    // If 'Remove query strings" checkbox ticked, add filter otherwise remove filter
    if ( $wppsb_remove_query_strings == 'on' ) {
    	// Disable the functionality under Admin to avoid any conflicts
    	if ( ! is_admin() ) {
    		add_filter( 'script_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
    		add_filter( 'style_loader_src', 'wppsb_remove_query_strings_q', 15, 1 );
    	}
    } else {
    	remove_filter( 'script_loader_src', 'wppsb_remove_query_strings_q');
    	remove_filter( 'style_loader_src', 'wppsb_remove_query_strings_q');
    }
}
add_action( 'wppsb_remove_query_string_action', 'wppsb_remove_query_string_init' );

function wppsb_remove_query_strings_q( $src ) {
	if(strpos( $src, '?ver=' ))
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

/**********************************
* instant.page preoloader
**********************************/
function wppsb_enqueue_scripts() {
    global $wppsb_instant_page_preload;
    
    // Only enqueu instant.page script if option is selected under plugin settings
    if ( $wppsb_instant_page_preload == 'on' ) {
        wp_enqueue_script( 'wppsb-page-preload', WPPSB_URL . '/assets/js/page-preloader.js', array(), '5.1.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'wppsb_enqueue_scripts' );

// This script loader is needed for instant.page preloader
function wppsb_script_loader_tag( $tag, $handle ) {
    if ( 'wppsb-page-preload' === $handle ) {
        $tag = str_replace( 'text/javascript', 'module', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'wppsb_script_loader_tag', 10, 2 );


/**********************************
* Enable GZIP Compression and Set Vary: Accept-Encoding Header (as a part of compression) 
**********************************/
function wppsb_enable_gzip_filter( $rules = '' ) {
$gzip_htaccess_content = <<<EOD
\n## BEGIN GZIP Compression ##
<IfModule mod_deflate.c>
    SetOutputFilter DEFLATE
    <IfModule mod_setenvif.c>
        <IfModule mod_headers.c>
            SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
            RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
            SetEnvIfNoCase Request_URI \
                \.(?:gif|jpe?g|png|rar|zip|exe|flv|mov|wma|mp3|avi|swf|mp?g|mp4|webm|webp|pdf)$ no-gzip dont-vary
        </IfModule>
    </IfModule>
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE application/atom+xml \
        	                          application/javascript \
        	                          application/json \
        	                          application/rss+xml \
        	                          application/vnd.ms-fontobject \
        	                          application/x-font-ttf \
        	                          application/xhtml+xml \
        	                          application/xml \
        	                          font/opentype \
        	                          image/svg+xml \
        	                          image/x-icon \
        	                          text/css \
        	                          text/html \
        	                          text/plain \
        	                          text/x-component \
        	                          text/xml
    </IfModule>
    <IfModule mod_headers.c>
        Header append Vary: Accept-Encoding
    </IfModule>
</IfModule>
<IfModule mod_mime.c>
    AddType text/html .html_gzip
    AddEncoding gzip .html_gzip
</IfModule>
<IfModule mod_setenvif.c>
    SetEnvIfNoCase Request_URI \.html_gzip$ no-gzip
</IfModule>
## END GZIP Compression ##
EOD;
    return $gzip_htaccess_content . $rules;
}


/**********************************
* Leverage Browser Caching (Expires headers) for better cache control
**********************************/
function wppsb_expire_caching_filter( $rules = '' ) {
$expire_cache_htaccess_content = <<<EOD
\n## BEGIN Leverage Browser Caching (Expires headers) for better cache control ##
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/cache-manifest           "access plus 0 seconds"
    # Media files
    ExpiresByType image/gif                     "access plus 4 months"
    ExpiresByType image/png                     "access plus 4 months"
    ExpiresByType image/jpeg                    "access plus 4 months"
    ExpiresByType image/webp                    "access plus 4 months"
    ExpiresByType video/ogg                     "access plus 1 month"
    ExpiresByType audio/ogg                     "access plus 1 month"
    ExpiresByType video/mp4                     "access plus 1 month"
    ExpiresByType video/webm                    "access plus 1 month"
    ExpiresByType text/x-component              "access plus 1 month"
    # Webfonts
    ExpiresByType font/ttf                      "access plus 4 months"
    ExpiresByType font/otf                      "access plus 4 months"
    ExpiresByType font/woff                     "access plus 4 months"
    ExpiresByType font/woff2                    "access plus 4 months"
    ExpiresByType image/svg+xml                 "access plus 1 month"
    ExpiresByType application/vnd.ms-fontobject "access plus 1 month"
    ExpiresByType text/css                      "access plus 1 year"
    ExpiresByType application/javascript        "access plus 1 year"
    # HTML and Data
    ExpiresByType text/html                     "access plus 0 seconds"
    ExpiresByType text/xml                      "access plus 0 seconds"
    ExpiresByType application/xml               "access plus 0 seconds"
    ExpiresByType application/json              "access plus 0 seconds"
    # Feed
    ExpiresByType application/rss+xml           "access plus 1 hour"
    ExpiresByType application/atom+xml          "access plus 1 hour"
    # Favicon
    ExpiresByType image/x-icon                  "access plus 1 week"
    # Default
    ExpiresDefault "access plus 2 days"
</IfModule>
## END Leverage Browser Caching (Expires headers) for better cache control ##
EOD;
    return $expire_cache_htaccess_content . $rules;
}


/**********************************
* Disable ETag and set Cache-Control headers
**********************************/
function wppsb_etag_headers_filter( $rules = '' ) {
$wppsb_plugin_version = WPPSB_PLUGIN_VERSION;
$etag_headers_content = <<<EOD
\n## BEGIN Disable ETag and set Cache-Control headers ##
<IfModule mod_headers.c>
    Header unset ETag
</IfModule>
# Since we’re sending far-future expires, we don’t need ETags for static content.
FileETag None
<IfModule mod_alias.c>
    <FilesMatch "\.(css|htc|js|asf|asx|wax|wmv|wmx|avi|bmp|class|divx|doc|docx|eot|exe|gif|gz|gzip|ico|jpg|jpeg|jpe|json|mdb|mid|midi|mov|qt|mp3|m4a|mp4|m4v|mpeg|mpg|mpe|mpp|otf|odb|odc|odf|odg|odp|ods|odt|ogg|pdf|png|pot|pps|ppt|pptx|ra|ram|svg|svgz|swf|tar|tif|tiff|ttf|ttc|wav|wma|wri|xla|xls|xlsx|xlt|xlw|zip)$">
        <IfModule mod_headers.c>
            Header unset Pragma
            Header append Cache-Control "public"
        </IfModule>
    </FilesMatch>
    <FilesMatch "\.(html|htm|rtf|rtx|txt|xsd|xsl|xml)$">
        <IfModule mod_headers.c>
            Header set X-Powered-By "WP Performance Score Booster/$wppsb_plugin_version"
            Header unset Pragma
            Header append Cache-Control "public"
            Header unset Last-Modified
        </IfModule>
    </FilesMatch>
</IfModule>
## END Disable ETag and set Cache-Control headers ##
EOD;
    return $etag_headers_content . $rules;
}


// Special thanks to Marin Atanasov ( https://github.com/tyxla ) for contributing this awesome function.
// Updates the htaccess file with the current rules if it is writable.
function wppsb_save_mod_rewrite_rules( $enable_gzip_val, $expire_caching_val ) {
	if ( is_multisite() ) {
		return;
    }
	
	global $wp_rewrite;
	
	$htaccess_file = get_home_path() . '.htaccess';

	/*
	 * If the file doesn't already exist check for write access to the directory
	 * and whether we have some rules. Else check for write access to the file.
	 */
	if ( ( ! file_exists( $htaccess_file ) && is_writable( get_home_path() ) && $wp_rewrite->using_mod_rewrite_permalinks() ) || is_writable( $htaccess_file ) ) {
    	$mod_rewrite_enabled = function_exists( 'got_mod_rewrite' ) ? got_mod_rewrite() : false;
		if ( $mod_rewrite_enabled ) {
			$rules = explode( "\n", $wp_rewrite->mod_rewrite_rules() );
		    // $enable_gzip = 'wppsb_enable_gzip';
		    // $expire_caching = 'wppsb_expire_caching';
		    // $enable_gzip_val = get_option($enable_gzip);
		    // $expire_caching_val = get_option($expire_caching);
		    $rules = array();
			if ( $enable_gzip_val == 'on' ) {
				$rules = array_merge( $rules, explode( '\n', wppsb_enable_gzip_filter() ) );
			}
			if ( $expire_caching_val == 'on' ) {
				$rules = array_merge( $rules, explode( '\n', wppsb_expire_caching_filter() ) );
				$rules = array_merge( $rules, explode( '\n', wppsb_etag_headers_filter() ) );
			}

            // chmod( $htaccess_file, 0777 );
			return insert_with_markers( $htaccess_file, 'WP Performance Score Booster Settings', $rules );
			// chmod( $htaccess_file, 0644 );
		}
	}
	return false;
}
