<?php

/*
Plugin Name: Scribd Doc Embedder
Plugin URI: http://ericbol.es/
Description: Uses the Scribd API to embed supported Scribd documents (e.g. PDF, MS Office, ePub, and many others) into a web page using
the Scribd Docs Reader.  
Author: Eric Boles
Author URI: http://www.ericbol.es/
Version: 2.0
License: GPLv2
*/

/**
 * LICENSE
 * This file is part of Scribd Doc Embedder.
 *
 * Scribd Doc Embedder is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed  WITHOUT ANY WARRANTY; without even the 
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package    scribd-doc-embedder
 * @author     Eric Boles
 * @copyright  Copyright 2014 Eric Boles
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @link       http://www.ericbol.es/
 */

//XTEC ************ AFEGIT - It adds a new function to replicate the Simpler iPaper plugin behaviour
//2015.26.01 @jmiro227

function scribd_id($atts) {  
    extract(shortcode_atts(array(  
        "id" => '',
        "key" => '',
        "width" => '0',
        "height" => '0',
        "page" => '1',
        "mode" => '',
        "seamless" => 'false',
        "share" => ''
    ), $atts));  

static $instance_count = 0; 
$instance_count++;

if ($instance_count == 1) {
	$output .= "<script type='text/javascript' src='http://www.scribd.com/javascripts/scribd_api.js'></script>";
} 

$output .= "<div id='embedded_doc_id".$instance_count."'><p>Loading...</p></div>
<script type='text/javascript'>
  var scribd_doc = scribd.Document.getDoc(".$id.", 'key-".$key."');
  var onDocReady = function(e){
   scribd_doc.api.setPage(".$page.");
  }
  scribd_doc.addParam('jsapi_version', 2);
  scribd_doc.addEventListener('docReady', onDocReady);";

	if ( !empty ( $width) ) {
		$output .= "scribd_doc.addParam('width', ".$width.");";
	}
	if ( !empty( $height ) ) {
		$output .= "scribd_doc.addParam('height', ".$height.");";
	}
	if ( !empty ( $mode) ) {
		$output .= "scribd_doc.addParam('mode', '".$mode."');";
	}
	if ($share == 'true' || $share == 'false') {
		$output .= "scribd_doc.addParam('allow_share', ".$share.");";
	}
	if ($seamless == 'true') {	
		$output .= "  scribd_doc.seamless('embedded_doc_id".$instance_count."');</script>";
	} else {	
		$output .= "  scribd_doc.write('embedded_doc_id".$instance_count."');</script>";
	}

    return $output;  

}

//************ FI

function scribd_doc($atts) {  
    extract(shortcode_atts(array(  
        "doc" => '',
        "key" => '',
        "width" => '0',
        "height" => '0',
        "page" => '1',
        "mode" => '',
        "seamless" => 'false',
        "share" => ''
    ), $atts));  

static $instance_count = 0; 
$instance_count++;

if ($instance_count == 1) {
	$output .= "<script type='text/javascript' src='http://www.scribd.com/javascripts/scribd_api.js'></script>";
} 

$output .= "<div id='embedded_doc_".$instance_count."'><p>Loading...</p></div>
<script type='text/javascript'>
  var scribd_doc = scribd.Document.getDoc(".$doc.", 'key-".$key."');
  var onDocReady = function(e){
   scribd_doc.api.setPage(".$page.");
  }
  scribd_doc.addParam('jsapi_version', 2);
  scribd_doc.addEventListener('docReady', onDocReady);";

	if ( !empty ( $width) ) {
		$output .= "scribd_doc.addParam('width', ".$width.");";
	}
	if ( !empty( $height ) ) {
		$output .= "scribd_doc.addParam('height', ".$height.");";
	}
	if ( !empty ( $mode) ) {
		$output .= "scribd_doc.addParam('mode', '".$mode."');";
	}
	if ($share == 'true' || $share == 'false') {
		$output .= "scribd_doc.addParam('allow_share', ".$share.");";
	}
	if ($seamless == 'true') {	
		$output .= "  scribd_doc.seamless('embedded_doc_".$instance_count."');</script>";
	} else {	
		$output .= "  scribd_doc.write('embedded_doc_".$instance_count."');</script>";
	}
	
    return $output;  

}

function scribd_url($atts) {  
    extract(shortcode_atts(array(  
        "url" => '',
        "pubid" => '',
        "width" => '0',
        "height" => '0',
        "page" => '1',
        "mode" => '',
        "share" => ''  
    ), $atts));  

static $instance_count = 0; 
$instance_count++; 

if ($instance_count == 1) {
	$output .= "<script type='text/javascript' src='http://www.scribd.com/javascripts/scribd_api.js'></script>";
} 

$output .= "<div id='embedded_doc_".$instance_count."'><p>Loading...</p></div><script type='text/javascript'>
  var url = '".$url."';
  var pub_id = 'pub-".$pubid."';
  var scribd_doc = scribd.Document.getDocFromUrl(url, pub_id);  
  var onDocReady = function(e){
   scribd_doc.api.setPage(".$page.");
  }
  scribd_doc.addParam('jsapi_version', 2);";

	if ( !empty ( $width) ) {
		$output .= "scribd_doc.addParam('width', ".$width.");";
	}
	if ( !empty( $height ) ) {
		$output .= "scribd_doc.addParam('height', ".$height.");";
	}
	if ( !empty ( $mode) ) {
		$output .= "scribd_doc.addParam('mode', '".$mode."');";
	}
	if ($share == 'true' || $share == 'false') {
		$output .= "scribd_doc.addParam('allow_share', ".$share.");";
	}
	
$output .= "scribd_doc.addEventListener('docReady', onDocReady);
  scribd_doc.addParam('public', true);
  scribd_doc.write('embedded_doc_".$instance_count."');
</script>";
   

    return $output;  

}

add_shortcode("scribd-url", "scribd_url"); 
add_shortcode("scribd-doc", "scribd_doc");

//XTEC ************ AFEGIT - It adds a new shortcode to replicate the Simpler iPaper plugin behaviour
//2015.26.01 @jmiro227
add_shortcode("scribd", "scribd_id"); 
//************ FI

// Hooks your functions into the correct filters
function scribd_doc_add_mce_button() {
	// check user permissions
	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'scribd_doc_tinymce' );
		add_filter( 'mce_buttons', 'scribd_doc_mce_button' );
	}
}
add_action('admin_head', 'scribd_doc_add_mce_button');

// Declare script for new button
function scribd_doc_tinymce( $plugin_array ) {
$wp_version = get_bloginfo( 'version' );
	if ( version_compare( $wp_version, '3.9', '>=' ) ) {
// XTEC ************ MODIFICAT - Show a deprecated function message instead of the standard selection box 
// 2014.10.09 @aginard  
 	$plugin_array['scribd_doc_mce_button'] = plugins_url( '/js/scribd-doc-mce-button-xtec.js' , __FILE__ );
//************ ORIGINAL
/*
	$plugin_array['scribd_doc_mce_button'] = plugins_url( '/js/scribd-doc-mce-button.js' , __FILE__ );
*/
//************ FI
	return $plugin_array;
	}
}

// Register new button in the editor
function scribd_doc_mce_button( $buttons ) {
	array_push( $buttons, 'scribd_doc_mce_button' );
	return $buttons;
}

function scribd_doc_mce_css() {
	wp_enqueue_style('scribd-doc-sc', plugins_url('/css/scribd-doc-mce-style.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'scribd_doc_mce_css' );

?>
