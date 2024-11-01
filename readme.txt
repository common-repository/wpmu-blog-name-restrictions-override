=== WPMU Blog Name Restrictions Override ===
Contributors: DeannaS, kgraeme
Tags: WPMU, Blog Name, Site Admin Tools
Requires at least: 2.7
Tested up to: 3.0
Stable tag: trunk



This plugin provides a way for site admins to allow blog creators more options for blog name (less restrictive than built-in defaults).

== Description ==

When non-site admins sign up for a new blog via the wp-signup.php page, they are subjected to the following restrictions on their site name (that site admins are not subjected to):

    * No dashes are allowed.
    * No underscores are allowed.
    * The name can not be all numeric.
    * Name must be at least 4 characters long

This plugin allows site admins to override each of those restrictions, toggling yes/no for the first three settings, and adding a minimum length for the name.

This plugin only affects blog creation via the sign up page. It does not affect blog creation via site admin -> blogs -> new blog


== Installation ==

1. Place the cets\_blog\_name\_restrictions\_override.php file in the wp-content/mu-plugins folder.
1. Set parameters via Site Admin -> Options


== Frequently Asked Questions ==

1. Can I set a minimum length of greater than 4 characters.
No - the plugin works by filtering the error codes returned from Wordpress. No error code is returned if the name is more than 4 characters long.


== Screenshots ==
1. Administrator's option settings.
2. User View of underscore or dash error in blog name creation.
3. User View of default length error in blog name creation.


== Changelog ==
1. Updated the error code checking and error codes to use WP3.0 site language.


