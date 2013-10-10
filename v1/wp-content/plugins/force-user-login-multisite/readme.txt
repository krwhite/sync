=== Force User Login Multisite ===
Contributors: jamesdlow
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=donate%40jameslow%2ecom&item_name=Donation%20to%20jameslow%2ecom&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: force user login, login, password, privacy, private, user level
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: 1.2.1

Makes your wordpress blog private unless the user is logged in, optionally setting a minium user level. Modified from http://wordpress.org/extend/plugins/force-user-login/

== Description ==

Makes your wordpress blog private unless the user is logged in, optionally setting a minium user level. Modified from http://wordpress.org/extend/plugins/force-user-login/

== Installation ==

1. Upload `force-login-multisite.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enable the minium user level required to view content on the settings menu
4. This is set per site for a Wordpress multisite set up.

== Frequently Asked Questions ==

= Can I change where the user is redirected after logging in? =

Yes! A variable called $redirect_to (line 33) is currently set to redirect the user to the page they were trying to access. If you changed that line from

`$redirect_to = $_SERVER['REQUEST_URI'];`

to

`$redirect_to = '/';`

it would redirect the user to the home page.

== Screenshots ==

None taken.. Just a login screen.