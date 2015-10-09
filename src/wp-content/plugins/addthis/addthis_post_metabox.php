<?php
/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2015 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

class addthis_post_metabox{

    function admin_init()
    {
        $screens = array('post', 'page');

        foreach($screens as $screen) {
            add_meta_box(
                'addthis',
                'AddThis',
                array($this, 'post_metabox'),
                $screen,
                'side',
                'default'
            );
        }

        add_action('save_post', array($this, 'save_post'));

        add_filter('default_hidden_meta_boxes', array($this, 'default_hidden_meta_boxes'));
    }

    function default_hidden_meta_boxes($hidden)
    {
        $hidden[] = 'addthis';
        return $hidden;
    }

    function post_metabox(){
        global $post_id;

//XTEC ************ AFEGIT - Localization support
//2013.05.21 @jmiro227
//load_plugin_textdomain( 'addthis_trans_domain', null, dirname( plugin_basename( __FILE__ )) . '/languages' );
//************ FI

        if ( is_null($post_id) )
            $checked = '';
        else
        {
            $custom_fields = get_post_custom($post_id);
            $checked = ( isset ($custom_fields['addthis_exclude'])   ) ? 'checked="checked"' : '' ;
        }

        wp_nonce_field('addthis_postmetabox_nonce', 'addthis_postmetabox_nonce');
        echo '<label for="addthis_show_option">';
//XTEC ************ MODIFICAT - Localization support
//2013.05.21 @jmiro227
        _e("Remove AddThis:", 'addthis_trans_domain' );
//************ ORIGINAL
/*
        _e("Remove AddThis:", 'myplugin_textdomain' );
*/
//************ FI
        echo '</label> ';
        echo '<input type="checkbox" id="addthis_show_option" name="addthis_show_option" value="1" '.$checked.'>';
    }

    function save_post($post_id)
    {
    	global $post;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( ! isset($_POST['addthis_postmetabox_nonce'] ) ||  !wp_verify_nonce( $_POST['addthis_postmetabox_nonce'], 'addthis_postmetabox_nonce' ) )
            return;

        if ( ! isset($_POST['addthis_show_option']) )
        {
            delete_post_meta($post_id, 'addthis_exclude');
        }
        else
        {
        	delete_post_meta($post_id, 'addthis_exclude');
            $custom_fields = get_post_custom($post_id);
            if (! isset ($custom_fields['addthis_exclude'][0]) && ($post->post_type=="post")  )
            {
                add_post_meta($post_id, 'addthis_exclude', 'true');
            }
            else
            {
                update_post_meta($post_id, 'addthis_exclude', 'true' , $custom_fields['addthis_exclude'][0]  );
            }
        }

    }

}

$addthis_post_metabox = new addthis_post_metabox;
add_action('admin_init', array($addthis_post_metabox, 'admin_init'));

