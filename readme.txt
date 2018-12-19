=== WP Performance Score Booster ===
Contributors: dipakcg
Tags: performance, speed, gzip, booster, query string
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3S8BRPLWLNQ38
Requires at least: 3.5
Tested up to: 5.0
Stable tag: 1.9.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Speed-up page load times and improve website scores in services like PageSpeed, YSlow, Pingdom and GTmetrix.

== Description ==
This plugin speed-up page load times and improve website scores in services like PageSpeed, YSlow, Pingdom and GTmetrix.

= This plugin will... =
* Remove any query strings from static resources like CSS & JS files
* Enable GZIP compression (compress text, html, javascript, css, xml and so on)
* Add Vary: Accept-Encoding header, and
* Set expires caching (leverage browser caching).

[youtube https://www.youtube.com/watch?v=nEFZLFyZNcE]

**Follow the development of this plugin on [GitHub](https://github.com/dipakcg/wp-performance-score-booster)**

**P.S. It is always the best policy to open a [support thread](http://wordpress.org/support/plugin/wp-performance-score-booster#new-topic-0) first before posting any negative review.**

== Installation ==
1. Upload the ‘wp-performance-score-booster’ folder to the ‘/wp-content/plugins/‘ directory
2. Activate the plugin through the ‘Plugins’ menu in WordPress.
3. That’s it!

== Frequently Asked Questions ==
= What does this plugin do? =

It speed-up page load times and improve website scores in services like PageSpeed, YSlow, Pingdom and GTmetrix. It will remove any query strings from static resources like CSS & JS files,  enable GZIP compression (compress text, html, javascript, css, xml and so on), add Vary: Accept-Encoding header and set expires caching (leverage browser caching).

= Any specific requirements for this plugin to work? =

* GZIP compression should be enabled in your web-server (apache?). If not then you can ask your web hosting provider.
* .htaccess in your WordPress root folder must have write permissions.

= What if I get 500 Internal Server Error? =

If you get "500 - Internal Server Error" after you activate the plugin, Follow the steps below:

(1) Login to your FTP or open File Manager (ask your hosting provider)
(2) Go to the WordPress installation folder and then `wp-content/wp-performance-score-booster` folder
(3) Copy `.htaccess.wppsb` file
(4) Now move back to WordPress installation folder and Rename the `.htaccess` file (to something like .htaccess.bak)
(5) Paste `.htaccess.wppsb` file (copied from step 3) and rename it to `.htaccess`

That's it! Your site should be up now.

Alternatively, you can open a [support thread](http://wordpress.org/support/plugin/wp-performance-score-booster#new-topic-0)

== Screenshots ==
1. Admin Settings

== Changelog ==
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
