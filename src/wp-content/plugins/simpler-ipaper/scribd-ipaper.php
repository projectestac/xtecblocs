<?php
/*
Plugin Name: Simpler iPaper
Plugin URI: http://simpler.freddyware.com/ipaper/
Description: Easily embed Scribd iPaper in posts
Version: 1.3.1
Author: Frederick Ding
Author URI: http://www.frederickding.com/

Copyright 2008-2009 Frederick Ding

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.

You may obtain a copy of the License at
http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

*/

$ipaper_height = '600'; // default iPaper height
$ipaper_width = '450'; // default iPaper width


function ipaper_shortcode_handler($atts, $content = NULL) {
	global $ipaper_height, $ipaper_width;
	// Initialize variables before extract()
	$id = '';
	$accesskey = '';
	$height = 0;
	$width = 0;
	extract ( shortcode_atts ( array ('id' => 'empty', 'accesskey' => 'empty', 'height' => $ipaper_height, 'width' => $ipaper_width ), $atts ) );

	return "<div id=\"ipaper$id\" class=\"simpler-ipaper-embed\"></div>\n" . '<script type="text/javascript">' . "\n" . "iPaper_embed('$id', '$accesskey', '$height', '$width');\n</script>";
}
add_shortcode ( 'ipaper', 'ipaper_shortcode_handler' );

// Since 1.1
function scribd_shortcode_handler($atts, $content = NULL) {
	global $ipaper_height, $ipaper_width;
	// Initialize variables before extract()
	$id = '';
	$key = '';
	$height = 0;
	$width = 0;
	extract ( shortcode_atts ( array ('id' => 'empty', 'key' => 'empty', 'height' => $ipaper_height, 'width' => $ipaper_width ), $atts ) );

	return "<div id=\"ipaper$id\" class=\"simpler-ipaper-embed\"></div>\n" . '<script type="text/javascript">' . "\n" . "iPaper_embed('$id', '$key', '$height', '$width');\n</script>";
}
add_shortcode ( 'scribd', 'scribd_shortcode_handler' );

function ipaper_head() {
	echo <<<JAVASCRIPT
<script type="text/javascript" src="http://www.scribd.com/javascripts/view.js"></script>
<script type="text/javascript">
//<![CDATA[
function iPaper_embed(id, accesskey, height, width) {
  var scribd_doc = scribd.Document.getDoc(id, accesskey);
  scribd_doc.addParam('height', height);
  scribd_doc.addParam('width', width);
  scribd_doc.write('ipaper'+id);
  }
//]]>
</script>
JAVASCRIPT;
}

add_action ( 'wp_head', 'ipaper_head' );