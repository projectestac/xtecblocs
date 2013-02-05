=== Simpler iPaper ===
Contributors: freddyware
Tags: documents, Scribd, iPaper, embed, Flash
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 1.3.1
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=759905

This plugin for WordPress 2.5 and above simplifies the embedding of Scribd iPaper documents in blog posts.

== Description ==

The Simpler iPaper plugin was created out of the need for a simpler way of embedding [Scribd](http://www.scribd.com/) iPaper documents
in blog posts. Other existing plugins used inefficient regular patterns instead of the Shortcode API that
is standard in WordPress versions 2.5 and above. This plugin makes use of standard Shortcode syntax as well.

The plugin uses a clear and simple format: **`[scribd]` added in 1.1**

	[ipaper id="integer" accesskey="string"]
	[scribd id="integer" key="string"]

Additionally, `height="integer"` and `width="integer"` are optional parameters.

**IMPORTANT**: in version 1.1, the plugin adds support for the `[scribd]` tag using `key` rather than `accesskey` in order to be compatible with the WordPress.com embed code. Users can now copy the Scribd-provided code for WordPress.com and use it on their WordPress.org blogs. See the screenshots.

Some examples of usage (you can copy these into a blog with this plugin installed to test):

	[ipaper id="6256195" accesskey="key-n4w95bnu9dca1kibehz"]
	[scribd id="6021840" key="key-14q8adj2w58ue6mtwlel" height="650" width="480"]
	
== Installation ==

Here's how to install the plugin and how to make it work.

1. Upload the `scribd-ipaper` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcode to your post: `[scribd id="document ID" key="access key"]`.
If you are using WordPress mu, you can place the plugin file `scribd-ipaper.php` in the `/wp-content/mu-plugins/` directory
so that the plugin is globally enabled.

== Frequently Asked Questions ==

= Is the accesskey="" parameter required? =
Yes, it is. Without the access key, Scribd will refuse to embed the document. This is not a limitation of this
plugin; Scribd enforces it for all Flash embeds.

= Can I change the default height and width? =
Yes, you can. Although there is no administration panel at the moment (we're keeping the plugin *simpler* and light),
you can edit the `scribd-ipaper.php` file and change the height and width variables there.

The default height is 600 pixels, and the default width is 450 pixels.

= Is there any difference between using the `[ipaper]` and the `[scribd]` codes? =
Yes, there is. The `[scribd]` tag was added to support `key` as a shorter form of `accesskey`, to be compatible with the WordPress.com code, and to be future-proof, wheras the `[ipaper]` tag is retained for backwards-compatibility.

In simpler terms, you must specify `accesskey` when using the `[ipaper]` tag, and you must specify `key` instead when using the `[scribd]` tag.

== Screenshots ==

1. An example WordPress.com embed code (compatible with the Simpler iPaper plugin) from Scribd.
2. The embed code pasted into the post editor, in WordPress 2.7.
3. An example output of the plugin showing the iPaper document embedded on a blog page.

== Changelog ==

= 1.3.1 =
* Added ability to style embeds using the `simpler-ipaper-embed` CSS class

= 1.3 =
* Updated compatibility to 2.9's trunk build
* Removed behaviour when a non-null $content parameter is encountered

== Upgrade Notice ==

= 1.3.1 =
* Upgrade for new functionality: positioning and styling the embed using CSS.