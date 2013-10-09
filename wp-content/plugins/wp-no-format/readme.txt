=== Plugin Name ===
Contributors: NeoEGM
Donate link: http://www.neoegm.com/donate/
Tags: noformat, html, code, tags, br, p, paragraph, format, disable, wpautop, wptexturize, post, page, plugin, convert_chars
Requires at least: 2.0.2
Tested up to: 2.8.4
Stable tag: 1.1

This plugin lets you prevent Wordpress from modifying the HTML written code.

== Description ==

The idea of this plugin is to let you stop WordPress from formatting
your HTML in specific parts of your posts.

It's very simple to use, you just have to write:

&lt;!-- noformat on --&gt;

on the WordPress Editor, just before the part you want to protect, and
from that point on, WordPress won't touch the HTML code you write. If
you want to resume the standard "formatting" you have to type:

&lt;!-- noformat off --&gt;

And that's all..
Enjoy it!

For any questions, suggestions or comments, just visit:
http://www.neoegm.com/software/wp-no-format/

== Installation ==

1. Upload `wp-no-format.zip` to the `/wp-content/plugins/` directory
2. Uncompress it
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Place the tags "&lt;!-- noformat on --&gt;" and "&lt;!-- noformat off --&gt;" in the HTML code of your posts/pages to control the code formatting
5. Enjoy!

== Screenshots ==

1. The wp-no-format plugin at work

== Changelog ==

= 1.1 =
* Added support for convert_chars function. Before, some characters got converted to HTML entities. <strong>[Recommended update]</strong>
= 1.0 =
* The first version.
