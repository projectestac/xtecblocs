<?php
/*
Plugin Name: XTEC Real Media
Plugin URI: 
Description: Easily embed Real Media files, for references created with Anarchy Media.
Author: Germán Antolin Priotto
Author URI: 
Version: 1.0

/*  Copyright 2011 Germán Antolin Priotto

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once(dirname(__FILE__).'/../../../wp-config.php');

function quicktags_replacer($content) {
	global $rmwidth, $rmheight;
	
	$standard_rm = '<code>[kml_rm movie="$1" width="'.$rmwidth.'" height="'.$rmheight.'"/]</code>';

	$searchfor = array(
		'/\[realmedia](.*?)\[\/realmedia]/i',
		'/\[realmedia width="(.*?)" height="(.*?)"](.*?)\[\/realmedia]/i',
	);
	
	$replacewith = array(
		// Real media
		$standard_rm,
		'<code>[kml_rm movie="$3" width="$1" height="$2"/]</code>',
	);
	
	return preg_replace($searchfor, $replacewith, $content);
}

$kml_request_type	= "";

/***********************************************************************
*	Run the main function 
************************************************************************/
function kml_rm($content) {
	$pattern = '/([\[<]KML_RM.*\/[\]>])|([\[<]KML_RM.*[\]>][\[<]\/KML_RM[\]>])/Ui'; 
	$result = preg_replace_callback($pattern,'kml_parse_kfe_tags_rm',$content);
	return $result;	
}

/***********************************************************************
*	Parse out the KFE Tags for Real Media
************************************************************************/
function kml_parse_kfe_tags_rm($match) {
	
	$r	= "";
			
	# Clean up and untexturize tag
	$strip		= array('[KML_RM',
						'][/KML_RM]',
						'[kml_rm',
						'][/kml_rm]',
						'/]',
						'<KML_RM',
						'></KML_RM>',
						'<kml_rm',
						'></kml_rm>',
						'/>',
						'\n',
						'<br>',
						'<br />'
						);
						
	$elements	= str_replace($strip, '', $match[0]);
	
	$elements	= preg_replace("/=(\s*)\"/", "==`", $elements);
	$elements	= preg_replace("/=(\s*)&Prime;/", "==`", $elements);
	$elements	= preg_replace("/=(\s*)&prime;/", "==`", $elements);
	$elements	= preg_replace("/=(\s*)&#8221;/", "==`", $elements);
	$elements	= preg_replace("/\"(\s*)/", "`| ", $elements);
	$elements	= preg_replace("/&Prime;(\s*)/", "`|", $elements);
	$elements	= preg_replace("/&prime;(\s*)/", "`|", $elements);
	$elements	= preg_replace("/&#8221;(\s*)/", "`|", $elements);
	$elements	= preg_replace("/&#8243;(\s*)/", "`|", $elements);
	$elements	= preg_replace("/&#8216;(\s*)/", "'", $elements);
	$elements	= preg_replace("/&#8217;(\s*)/", "'", $elements);
	
	$attpairs	= preg_split('/\|/', $elements, -1, PREG_SPLIT_NO_EMPTY);
	$atts		= array();
	
	// Create an associative array of the attributes
	for ($x = 0; $x < count($attpairs); $x++) {
		
		$attpair		= explode('==', $attpairs[$x]);
		$attn			= trim(strtolower($attpair[0]));
		$attv			= preg_replace("/`/", "", trim($attpair[1]));
		$atts[$attn]	= $attv;
	}
	
	if (!empty($atts['movie']) && !empty($atts['height']) && !empty($atts['width'])) {
		
		$atts['fversion'] 			= (!empty($atts['fversion'])) ? $atts['fversion'] : 6;
		$atts['height']				= ($height{strlen($atts['height']) - 1} == "%") ? '"' . $atts['height'] . '"' : $atts['height'];
		$atts['width']				= ($width{strlen($atts['width']) - 1} == "%") ? '"' . $atts['width'] . '"' : $atts['width'];
		$atts['useexpressinstall']	= (!empty($atts['useexpressinstall'])) ? $atts['useexpressinstall'] : '""';
		$atts['detectKey']			= (!empty($atts['detectKey'])) ? ',"' . $atts['detectKey'] . '"' : '';
		
		$fvarpair_regex		= "/(?<!([$|\?]\{))\s+;\s+(?!\})/";
		$atts['fvars']		= (!empty($atts['fvars'])) ? preg_split($fvarpair_regex, $atts['fvars'], -1, PREG_SPLIT_NO_EMPTY) : array();
		
		// Convert any quasi-HTML in alttext back into tags
		$atts['alttext']	= (!empty($atts['alttext'])) ? preg_replace("/{(.*?)}/i", "<$1>", $atts['alttext']) : "" ;
		
		// If we're not serving up a feed, generate the script tags
		if ($GLOBALS['kml_request_type'] != "feed") {
			$r	= kml_build_fo_script_rm($atts);
		}
	}
 	return $r; 
}

/***********************************************************************
*	Build the Javascript from the tags for Real Media
************************************************************************/
function kml_build_fo_script_rm($atts) {
	
	$out	= array();	
	if (is_array($atts)) extract($atts);
	
	// Extract the filename minus the extension...
	$swfname			= (strrpos($movie, "/") === false) ?
							$movie :
							substr($movie, strrpos($movie, "/") + 1, strlen($movie));
	$swfname			= (strrpos($swfname, ".") === false) ?
							$swfname :
							substr($swfname, 0, strrpos($swfname, "."));
	
	// ... to use as a default ID if an ID is not defined.
	$id			= (!empty($id)) ? $id : "fm_" . $swfname;
	// ... as well as an empty target if that isn't defined.
	if (empty($target)) {
		$rand		= mt_rand();
		$targname	= "fo_targ_" . $swfname . $rand;
		// Create a target div
		$out[]		= '<div id="' . $targname . '">'.$alttext.'</div>';
		$target	= $targname;
	}				
	
	$out[]='<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" id="'.$id.'" width='.$width.' height='.$height.'>
			<param name="controls" value="ImageWindow,ControlPanel">
			<param name="console" value="'.$id.'">
			<param name="autostart" value="false">
			<param name="src" value="'.$movie.'">
			<embed src="'.$movie.'" type="audio/x-pn-realaudio-plugin" controls="ImageWindow,ControlPanel" console="'.$id.'" width='.$width.' height='.$height.' autostart="false"></embed></object>';


 
	// Loop through and add any name/value pairs in the $fvars attribute
	for ($i = 0; $i < count($fvars); $i++) {
		$thispair	= trim($fvars[$i]);
		$nvpair		= explode("=",$thispair);
		$name		= trim($nvpair[0]);
		$value		= trim($nvpair[1]);
		// Prune out JS or PHP values
		if (preg_match("/^\\$\\{.*\\}/i", $value)) { 		// JS
			$endtrim 	= strlen($value) - 3;
			$value		= substr($value, 2, $endtrim);
			$value		= str_replace(';', '', $value);
		} else if (preg_match("/^\\?\\{.*\\}/i", $value)) {	// PHP
			$endtrim 	= strlen($value) - 3;
			$value 		= substr($value, 2, $endtrim);
			$value 		= '"'.eval("return " . $value).'"';
		} else {
			$value = '"'.$value.'"';
		}
	
	}
	
	// Add NoScript content
	if (!empty($noscript)) {
									$out[] = '<noscript>';
									$out[] = '	' . $noscript;
									$out[] = '</noscript>';
	}
									$out[] = '';
											
	$ret .= join("\n", $out);
	return $ret;
}

/***********************************************************************
*	Trigger Function
************************************************************************/
function kmlDoObStart()
{
	ob_start('kml_rm');
}

/***********************************************************************
*	Add the calls
************************************************************************/
if (preg_match("/(\/\?feed=|\/feed|\/wpmu-feed)/i",$_SERVER['REQUEST_URI'])) {
	// RSS Feeds
	$kml_request_type	= "feed";
} else {
	// Everything else
	$kml_request_type	= "nonfeed";
    if (isset($wp_version)) {
	    add_filter('the_content', 'quicktags_replacer', 11);
    }
}
// Apply all over except the admin section
if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') === false ) {
	add_action('template_redirect','kmlDoObStart');
}