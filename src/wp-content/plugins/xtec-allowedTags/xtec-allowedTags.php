<?php

/*
Plugin Name: XTEC Allowed Tags
Plugin URI: 
Description: Adds html tags allowed for publication.
Version: 1.0
Author: Germán Antolin Priotto
Author URI:
*/

/*
Little modification by Xavier Belanche (<xbelanch at gmail dot com>)
Changes the display resulsts to an unordered list (more accessible and +cool!)
October, 3 of 2007. 
*/

/*  Copyright 2011  German Antolin Priotto

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


// BEGIN FIX TO ENABLE IFRAME POSTING FROM AUDIOBLOG.COM
$allowedposttags["iframe"] = array("src" => array(),
 "height" => array(), "width" => array(), "frameborder" => array(),
 "scroll" => array(), "scrolling" => array());
 
$allowedposttags["object"] = array("classid"=>array(), "id"=>array(), "height" => array(), "width" => array());

$allowedposttags["param"] = array("name" => array(), "value" => array());

 $allowedposttags["embed"] = array("src" => array(),
 "height" => array(), "width" => array(), "wmode" => array(),
 "type" => array(), "quality" => array(), "name" => array(),
 "align" => array(), "allowScriptAccess" => array(), "flashvars" => array(),
 "pluginspage" => array());
 
 $allowedposttags["a"] = array("href"=>array(),"target" => array(),"title"=>array(),"name"=>array());

// END FIX TO ENABLE IFRAME POSTING FROM AUDIOBLOG.COM