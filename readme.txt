=== DT's Simple Share ===
Contributors: MissionMike
Tags: facebook, google plus, twitter, email, share, linkedin, reddit, tumblr, stumbleupon, sharing, social media
Donate link: https://dtweb.design/simple-share/
Requires at least: 2.8
Tested up to: 5.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simplify page/post sharing with an easily added sharebar for users.

== Description ==

DT's Simple Share plugin is designed for quick implementation. Install, activate, and save settings to add a sharebar to help visitors easily share your site's pages, posts or custom post types with ease. Users can share posts with Facebook, Twitter, LinkedIn, Reddit, Tumblr, StumbleUpon, Email, or Pinterest.

This WordPress social media sharing plugin is simple, with minimal options, and no reliance 3rd-party resources (such as the Facebook SDK scripts).

DT's Simple Share does **not** allow you to change any meta titles, keywords, open graph data, etc. It only provides a generally-configurable sharebar of social media platforms for users to quickly share URLs from blog posts, pages, or custom post types of your choosing.

In Settings, sharing platforms/icons can be shown or hidden, and default values can be set for share meta (Twitter "via", hashtags, etc).

## SEO Plugin Title Support

SEO post title support for [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/) and [All-in-one SEO Pack](https://wordpress.org/plugins/all-in-one-seo-pack/) is built in. If no SEO title is detected for the post, the WordPress post title is used. Please note there is not yet support for using Yoast, All-in-one SEO, or other plugins' custom social media post titles.

Other SEO plugin support for post titles can be added upon request.

## AMP Support

In Settings, AMP-specific [amp-social-share](https://amp.dev/documentation/components/amp-social-share/) elements available to be used in lieu of traditional HTML for social media sharing icons. There is out-of-the-box AMP page detection included for the [AMP](https://wordpress.org/plugins/amp/) plugin, and the [AMP for WP - Accelerated Mobile Pages](https://wordpress.org/plugins/accelerated-mobile-pages/) plugin. 

If neither of these plugins are in use on your site, you can enable the setting to detect if the page's URL ends in /amp/ - which is not a foolproof way to detect AMP, but can be used if other options are not available.

If additional AMP plugin support is required, please email [the author](https://profiles.wordpress.org/missionmike/) to request.

## Social Media Platform Support

Currently, the following platforms and share options are supported:

* Facebook
* Twitter
* LinkedIn
* Reddit
* Tumblr
* Email
* Pinterest

## Let's Build

If you'd like to see another platform included, please let me know!

Please kindly report any issues or conflicts to the author by [opening a support request](https://wordpress.org/support/plugin/dts-simple-share/) or emailing the author.

## Special Thanks

Thanks to the contributors of the [social-share-urls GitHub repo](https://github.com/bradvin/social-share-urls), which helped make this plugin development possible.

== Installation ==
Download zip, install, activate!
Check settings to reveal post types and sharebar positioning. Settings need to be saved after activation in order for the sharebar to appear on posts/pages.

== Frequently Asked Questions ==
N/A

== Changelog ==

=v0.5.2=

* Removed StumbleUpon - the share URL no longer works since they've rebranded to Mix

=v0.5.1=

* Fixed missing Settings link

=v0.5=

* Google AMP support
* Reminder notification to save settings after activation
* Added Pinterest
* Code refactoring

=v0.4.4=

* Code cleanup and bugfixes

=v0.3.2=

* Updated settings page styles and layout
* Bugfixes

=v0.3.1=

* Fixed missing text on hover in Rectangle style

=v0.3=

* Replaced images with [FontAwesome](http://fontawesome.io) icons
* CSS fixes

=v0.2.2=

* Fixed "undefined index" PHP warning

=v0.2.1=

* Forced cache refresh with updated query parameter for CSS changes

=v0.2=

* Added Tumblr, StumbleUpon, and Reddit
* New feature: Drag and drop order of icons
* Updated styles: Rectangle and Round
* Removed inline style declarations
* Bugfixes and code cleanup

=v0.1.3=

* Updated Google Plus logo

=v0.1.2=

* Fixed missing ');' to inline background-image CSS rule

=v0.1.1=

* Changed sharer's new tab opening on laptop/desktop to show a new appropriately-sized popup instead

=v0.1=

* Added support for LinkedIn
* Added two display options "standard" and "compact" for sharebar

=v0.0.2=

* CSS fixes (added margin to top/bottom of sharebar, fixed centering)

=v0.0.1=

* Added settings and icons for Facebook, Twitter, Google+ and Email
* Initial commit
