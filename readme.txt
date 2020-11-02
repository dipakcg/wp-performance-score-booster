=== WP Performance Score Booster – Optimize Speed, Enable Cache & Page Preload ===
Contributors: dipakcg
Tags: performance, optimize, speed, query string, preload, gzip, gtmetrix, etag, compression, headers, cache, pagespeed
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3S8BRPLWLNQ38
Requires at least: 3.5
Tested up to: 5.5
Requires PHP: 5.6
Stable tag: 2.0
License: GPLv2 or later

Make website faster, speed up page load time and improve performance scores in services like GTmetrix, Pingdom, YSlow and PageSpeed.

== Description ==
**WP Performance Score Booster makes website faster, speeds up page load time, and instantly improves website performance scores in services like GTmetrix, Pingdom, YSlow, and PageSpeed.**

= This plugin will... =
* Remove any query strings from static resources like CSS & JS files
* Enable GZIP compression (compress text, html, javascript, css, xml and so on)
* Leverage browser caching (expires headers) for better cache control
* Preload a page (on mouse hover) right before a user click on a link
* Disable ETag and set Cache-Control headers

[youtube https://www.youtube.com/watch?v=nEFZLFyZNcE]

**Follow the development of this plugin on [GitHub](https://github.com/dipakcg/wp-performance-score-booster)**

**P.S. It is always the best policy to open a [support thread](https://wordpress.org/support/plugin/wp-performance-score-booster#new-topic-0) first before posting any negative review.**

== Installation ==
Just install from your WordPress “Plugins > Add New” screen and all will be well. Manual installation is very straightforward as well:

1. Download the plugin (.zip file).
2. Unzip it, and uplaod `wp-performance-score-booster` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. That’s it!

== Frequently Asked Questions ==
= What does this plugin do? =

This plugin makes website faster, speed-up page load time and instantly improve website scores in services like GTmetrix, Pingdom, YSlow and PageSpeed.

= Any specific requirements for this plugin to work? =

* GZIP compression ( `mod_deflat` module ) should be enabled in your web-server. If not then you can ask your web hosting provider to enable it for you.
* `.htaccess` in your WordPress root folder must have write permissions.

= What if I get 500 Internal Server Error? =

If you get "500 - Internal Server Error" after you activate the plugin, Follow the steps below:

1. Login to your FTP or open File Manager (ask your hosting provider).
2. Go to the WordPress installation root directory and then `wp-content/wp-performance-score-booster` directory.
3. Copy `.htaccess.wppsb` file.
4. Now move back to WordPress installation root directory and Rename the `.htaccess` file (to something like .htaccess.bak).
5. Paste `.htaccess.wppsb` file (copied from step 3) and rename it to `.htaccess`.

That's it! Your site should be up and running now.

_• You can still open a [support thread](https://wordpress.org/support/plugin/wp-performance-score-booster#new-topic-0) if you have any issues._

= This plugin improved the performance of my site, how do I thank you? =

If this plugin has helped you in any way, you can:
- [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3S8BRPLWLNQ38): Donations help me to continue development and user support of this free awesome plugin for which I spend countless hours of my valuable time.
- [Review](https://wordpress.org/support/plugin/wp-performance-score-booster/reviews/?rate=5#new-post): Review this plugin with a 5-star rating.
- Recommend: Recommend this plugin to others.

== Screenshots ==
1. Admin Settings

== Changelog ==
= 2.0, November 02, 2020 =
* New: Redesigned settings page
* New: Instant.page preloader that preload a page (on mouse hover) right before a user click on a link
* Improve: htaccess rules for better GZIP compression and leverage browser caching (expires headers) for better cache control
* Improve: Disable ETag and set Cache-Control headers

= 1.9.2.2, December 19, 2018 =
* Fixed "Remove any query strings from static resources" conflict in admin

= 1.9.2.1, May 26, 2018 =
* Fixed jQuery conflict with Review Plugin notice.
* Fixed minor typo

= 1.9.2, May 11, 2018 =
* Fixed bug with Review Plugin notice.
* - Review Plugin notice is now dismissible (permanently) when a user click the (X) on the top right.
* Improved user experience (by implementing jQuery - Ajax calls).

= 1.9.1, May 02, 2018 =
* Added Review Plugin - admin notice
* Improved Recommendations tab

= 1.9, November 28, 2017 =
* Completely rewritten
* Fixed few CSS bugs (admin settings)
* Removed Donate button

= 1.8, July 03, 2017 =
* Added a feature that auto-backup .htaccess file before appending any rules (for GZIP and browser caching)
* Improved uninstallation process
* Added a topic on 500 - Internal Server Error Fix into FAQ

= 1.7.2, March 21, 2017 =
* Improved Promos, News and Updates, and recommendations area.

= 1.7.1, February 10, 2017 =
* Improved recommendations area.

= 1.7, September 15, 2016 =
* Fixed css conflict with WP Super Minify.
* Improved deactivation and uninstallation hooks.
* Organised file structure (custom MVC Skeleton).
* Added hosting recommendations.

= 1.6, July 26, 2016 =
* Improved UI.
* Fixed minor bugs.
* Minified CSS.

= 1.5, February 02, 2016 =
* Fixed conflict with Divi and Divi Builder.
* Moved Options / Settings under *'Settings'* menu.
* Added *'Settings'* option directly under plugins (installed plugins) page.
* Amended *'News and Updates'* section.

= 1.4, February 28, 2015 =
* Added News and Updates section in admin options.

= 1.3.1, December 30, 2014 =
* Fixed issues with htaccess causing internal server error.

= 1.3, December 29, 2014 =
* Fixed issues with htaccess custom rules overrides.
* WP Performance Score Booster now adds rules to htaccess outside default WordPress block.

= 1.2.2, December 27, 2014 =
* Added support for language translations.

= 1.2.1, November 17, 2014 =
* Removed (temporarily) feature to enqueue scripts to footer.

= 1.2, November 17, 2014 =
* Added feature to enqueue scripts to footer.
* Added support for Vary: Accept-Encoding header.
* Fixed minor issues for remove query strings from static resources.

= 1.1.1, September 02, 2014 =
* Added feature (for urls with &ver) to remove query strings from static resources.

= 1.1, August 31, 2014 =
* Added Admin Options / Settings.

= 1.0, August 26, 2014 =
* Initial release.
* Born of WP Performance Score Booster.

== Upgrade Notice ==

= 2.0 =
This version adds support for page preloading, improves compression and caching rules for faster speed, and has newly designed settings page. Recommended upgrade.

== Credits ==
_• This plugin uses [instant.page](https://instant.page/) library for page preloading._