=== Scribd Doc Embedder ===
Contributors: ericboles
Tags: scribd, reader, viewer, embed, pdf, doc, docx, ppt, pptx
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Uses the Scribd API to embed supported Scribd documents (e.g. PDF, MS Office, ePub, and many others) into a web page using the Scribd Docs Reader.

== Description ==

Adds 2 shortcodes so Scribd documents or documents supported by Scribd (e.g. PDF, MS Office, ePub, and many others) can
be embedded into a web page using the Scribd Docs Reader.  These shortcodes provide functionality beyond the standard scribd
embed shortcode provided in Jetpack by Wordpress.com.

**Version 2**  Now includes shortcode button and configurator in the editor so you easily enter the information you need to embed you documents. Take a look at the screenshots to see them.

**[scribd-doc]** Shortcode:

* Allows you to embed a document that has been uploaded to Scribd.
* The Scribd document can be public or private.
* Requires both the document ID and the document key:
* [scribd-doc doc="DOCID" key="KEY"]

**[scribd-url]** Shortcode:

* Allows you to create a Scribd Reader from a publicly accessible URL.
* Naturally requires that the document type be supported by Scribd.
* Requires both the document URL and your Scribd Publisher API key:
* [scribd-url url="FULL-URL" pubid="YOUR-SCRIBD-PUBLISHER-ID"]

Both shortcodes support many optional parameters provided by the Scribd API:

* **width**: Define the width of the Reader in pixels.
* **height**: Define the height of the Reader in pixels.
* **page**: Set a page number for the Reader to load on.
* **mode**: Define the player mode (either "list" or "slideshow")
* **share**: Defines whether the share button in the toolbar is shown, overriding the default (either "true" or "false")
* **seamless**: If set to "true", puts the player into seamless mode 
(seamless is only supported by scribd-doc)

If height or width are not set, the Scribd Reader will attempt to size itself correctly.

Note: You will need a Scribd account and a Scribd API key for this plugin to work. 
You can sign up for a Scribd account at http://www.scribd.com.
With a Scribd account, you can get your API key at http://www.scribd.com/developers

== Installation ==

1. Upload the `scribd-doc-embedder` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If using WordPress 3.9 or greater, you’ll now see a shortcode button in your editor that you can use to enter and configure the short codes.
4. You can also manually place a shortcode on any page.

== Frequently Asked Questions ==

= Where do I find the Document ID and Key? =

You will find the document ID and key within your Scribd account after uploading a document to Scribd and going looking at the

= How do I get a Scribd Publisher Key? =

You will need a Scribd account and a Scribd API key for this plugin to work.  You will see your Publisher key in your API settings within your account after getting API access.

You can sign up for a Scribd account at http://www.scribd.com. With a Scribd account, you can get your API key at http://www.scribd.com/developers

= It doesn’t seem to be working. Am I doing something wrong? =

Be sure you’re not prepending your Document Keys or Publisher ID with “key-“ or “pub-“. 

= How come I can’t see the shortcode button in my editor? =

You need to be running WordPress 3.9 or above to use the shortcode button.

== Screenshots ==

1. Shortcode editor to embed a document that has been uploaded to Scribd. Configures [scribd-doc] shortcode.
2. Shortcode editor create a Scribd Reader from a publicly accessible URL. Configures [scribd-url] shortcode.

== Changelog ==

= 1.0 =
* First version

= 2.0 =
* Added shortcode button and configurator to the editor, making it easier to embed documents. (Shortcode button functionality requires WordPress 3.9+)

== Upgrade Notice ==

= 1.0 =
First version

= 2.0 =
* Added shortcode button and configurator to the editor, making it easier to embed documents. (Shortcode button functionality requires WordPress 3.9+)
