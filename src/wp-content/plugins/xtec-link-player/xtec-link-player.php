<?php

/*
Plugin Name: XTEC Link Player
Description: Easily embed mp3, flv, mov, mp4, m4v, m4a, m4b, 3gp, avi, asf and wmv hypertext links directly on your webpage. Based in xlp Media Player by An-xlp.
Version: 1.1
Author: Germán Antolin Priotto
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/*  Copyright 2011  Germán Antolin Priotto

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('wp_head', 'xtec_link_player_wp_head');

/**
 * Add the calls to filters and xlp.js
 */
function xtec_link_player_wp_head()
{
    echo '<script> var xtec_link_player_url = "' . get_option('siteurl') . '/wp-content/plugins/xtec-link-player' . '" </script>' ;
    echo '<script src="' . WP_PLUGIN_URL . '/xtec-link-player/xtec-link-player.js" type="text/javascript"></script>' . "\n";
}