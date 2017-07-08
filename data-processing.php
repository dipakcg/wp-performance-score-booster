<?php
/* ========================================
   Remove query strings from static content
   ======================================== */
function wppsb_remove_query_strings_q( $src ) {
	if(strpos( $src, '?ver=' ))
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

/* ========================================
   Enable GZIP Compression
   ======================================== */
function wppsb_enable_gzip_filter( $rules = '' ) {
$gzip_htaccess_content = <<<EOD
\n## BEGIN GZIP Compression ##
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
## END GZIP Compression ##
EOD;
    return $gzip_htaccess_content . $rules;
}

// Set Vary: Accept-Encoding Header (as a part of compression)
function wppsb_vary_accept_encoding_filter( $rules = '' ) {
$vary_accept_encoding_header = <<<EOD
\n## BEGIN Vary: Accept-Encoding Header ##
<IfModule mod_headers.c>
<FilesMatch "\.(js|css|xml|gz)$">
Header append Vary: Accept-Encoding
</FilesMatch>
</IfModule>
## END Vary: Accept-Encoding Header ##
EOD;
    return $vary_accept_encoding_header . $rules;
}

/* ================================================
   Enable expire caching (Leverage browser caching)
   ================================================ */
function wppsb_expire_caching_filter( $rules = '' ) {
$expire_cache_htaccess_content = <<<EOD
\n## BEGIN Leverage Browser Caching (Expires Caching) ##
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType text/css "access 1 month"
ExpiresByType text/html "access 1 month"
ExpiresByType image/jpg "access 1 year"
ExpiresByType image/jpeg "access 1 year"
ExpiresByType image/gif "access 1 year"
ExpiresByType image/png "access 1 year"
ExpiresByType image/x-icon "access 1 year"
ExpiresByType application/pdf "access 1 month"
ExpiresByType application/javascript "access 1 month"
ExpiresByType text/x-javascript "access 1 month"
ExpiresByType application/x-shockwave-flash "access 1 month"
ExpiresDefault "access 1 month"
</IfModule>
## END Leverage Browser Caching (Expires Caching) ##
EOD;
    return $expire_cache_htaccess_content . $rules;
}

function wppsb_disable_etag_filter( $rules = '' ) {
$disable_etag_header_content = <<<EOD
\n## BEGIN Disable ETag header ##
Header unset Pragma
Header unset ETag
FileETag None
## END Disable ETag header ##
EOD;
    return $disable_etag_header_content . $rules;
}

// Special thanks to Marin Atanasov ( https://github.com/tyxla ) for contributing this awesome function.
// Updates the htaccess file with the current rules if it is writable.
function wppsb_save_mod_rewrite_rules($enable_gzip_val, $expire_caching_val) {
	if ( is_multisite() )
		return;
	global $wp_rewrite;
	$htaccess_file = get_home_path() . '.htaccess';

	/*
	 * If the file doesn't already exist check for write access to the directory
	 * and whether we have some rules. Else check for write access to the file.
	 */
	if ((!file_exists($htaccess_file) && is_writable( get_home_path() ) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
    	$mod_rewrite_enabled = function_exists('got_mod_rewrite') ? got_mod_rewrite() : false;
		if ( $mod_rewrite_enabled ) {
			$rules = explode( "\n", $wp_rewrite->mod_rewrite_rules() );
		    $enable_gzip = 'wppsb_enable_gzip';
		    $expire_caching = 'wppsb_expire_caching';
		    // $enable_gzip_val = get_option($enable_gzip);
		    // $expire_caching_val = get_option($expire_caching);
		    $rules = array();
			if ($enable_gzip_val == 'on') {
				$rules = array_merge($rules, explode("\n", wppsb_enable_gzip_filter()));
				$rules = array_merge($rules, explode("\n", wppsb_vary_accept_encoding_filter()));
			}
			if ($expire_caching_val == 'on') {
				$rules = array_merge($rules, explode("\n", wppsb_expire_caching_filter()));
				$rules = array_merge($rules, explode("\n", wppsb_disable_etag_filter()));
			}

			return insert_with_markers( $htaccess_file, 'WP Performance Score Booster Settings', $rules );
		}
	}
	return false;
}
?>