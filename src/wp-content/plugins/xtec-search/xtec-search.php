<?php

/*
Plugin Name: XTEC Search
Plugin URI:
Description: Search system for the blogs.
Version: 1.1
Author: Germán Antolin Priotto
Author URI:
*/

/*  Copyright 2011  Germán Antolin Priotto  (email : german_antolin6@ieci.es)

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

global $xtec_search_db_version;
$xtec_search_db_version = '1.0';

add_action('network_admin_menu', 'xtec_search_network_admin_menu');
add_action('wpmu_new_blog', 'xtec_search_new_blog', 10, 4);
add_action('update_option_blogname', 'xtec_search_update_option_blogname', 10, 2);
add_action('update_option_blogdescription', 'xtec_search_update_option_blogdescriptors', 10, 2);
add_action('delete_blog', 'xtec_search_delete_blog', 10, 1);
// XTEC ************ AFEGIT -> Add cron event to rebuild the search index
// 2012.04.02 @mmartinez
add_action('rebuild_index', 'xtec_search_rebuild_index');
// ************* FI

/**
 * Adds plugin network admin menu.
 */
function xtec_search_network_admin_menu() 
{
    add_menu_page('Cerca', 'Cerca', 'manage_network', 'ms-search', 'xtec_search_network_options');
}

/**
 * Displays the network options page of the plugin. 
 */
function xtec_search_network_options($action = '')
{	
    global $wpdb;
    
    if ( isset($_GET['updated']) ) {
        ?><div id="message" class="updated fade"><p><?php _e('Options saved.') ?></p></div><?php
    }

    print '<div class="wrap">';
    ?><h2>Cerca</h2><?php
    switch ( $_GET['action'] ) {
        case "search":
// XTEC ********** MODIFICAT -> Add cron event to rebuild the search index
// 2012.04.02 @mmartinez
			$n = isset($_GET['n']) ? $_GET['n']: '';
			xtec_search_rebuild_index(false, $n);
// *********** ORIGINAL
            /*if ( !isset($_GET['n']) ) {
                $n = 0;
                // Buida la taula
                $sql = "TRUNCATE TABLE `wp_search`;";
                $wpdb->query($sql);
            } else {
                $n = intval($_GET['n']);
            }
            // Processa els registres
            $blogs = $wpdb->get_results( "SELECT * FROM $wpdb->blogs WHERE site_id = '$wpdb->siteid' AND spam = '0' AND archived = '0' ORDER BY registered DESC LIMIT $n, 5", ARRAY_A );

            if ( empty( $blogs ) == false ) {
                print '<table border="0" width="100%">';
                foreach ( $blogs as $details ) {
                    if( $details[ 'spam' ] == 0 &&  $details[ 'archived' ] == 0 ) {
                        print "<tr>";
                        $siteurl = get_blog_option($details['blog_id'], 'blogname');
                        $blogdescription = get_blog_option($details['blog_id'], 'blogdescription');
                        $domain = get_blog_details($details['blog_id'])->domain;
                        $path = get_blog_details($details['blog_id'])->path;
                        print '<td width="40%">'.$details[ 'blog_id' ].' - '.$siteurl.'</td>';
                        print '<td width="55%">'.$blogdescription.'</td>';
                        //Do the row exists in search table
                        $exists = $wpdb->get_var( "SELECT count(blogid) from wp_search WHERE blogid = {$details[ 'blog_id' ]}" );
                        if ( $details[ 'deleted' ] == 0 && $details[ 'public' ] == 1 ) {
                            //If teh row don't exist it is created if not it is updated
                            if ( $exists ) {
                                $wpdb->get_results("UPDATE wp_search SET name='".mysql_real_escape_string($siteurl)."', description='".mysql_real_escape_string($blogdescription)."',domain='".mysql_real_escape_string($domain)."',path='".mysql_real_escape_string($path)."' WHERE blogid = {$details[ 'blog_id' ]}");
                                $actionmade = __('Updated');
                            } else {
                                $wpdb->get_results("INSERT INTO wp_search (blogid,name,description,domain,path) VALUES ({$details[ 'blog_id' ]},'".mysql_real_escape_string($siteurl)."','".mysql_real_escape_string($blogdescription)."','".mysql_real_escape_string($domain)."','".mysql_real_escape_string($path)."')");
                                $actionmade = __('Created');
                            }
                        } else {
                            //Delete the blog information from the search table
                            $sql = "DELETE FROM wp_search where blogid={$details[ 'blog_id' ]}";
                            $wpdb->query($sql);
                        }
                        print '<td width="5%">' . $actionmade . '</td>';
                        print "</tr>";
                    }
                }
                print "</table>";
                ?>
                <p><?php _e("If your browser doesn't start loading the next page automatically click this link:"); ?> <a href="?page=ms-search&action=search&n=<?php echo ($n + 5) ?>"><?php _e("Next Blogs"); ?></a> </p>
                <script language='javascript' src='../wp-content/plugins/xtec-search/js/xtec-search-regenerate.js'></script>
                <?php
            } else {
                _e("All Done!");
            }*/
// ********** FI
        break;

        default:
        ?>
            <p><?php _e("Des d'aquí pots regenerar la taula de cerca dels blocs. Funciona fent de manera automàtica una crida de cada bloc. Feu clic a l'enllaç per a realitzar l'actualització."); ?></p>
            <p><a href="?page=ms-search&action=search"><?php _e("Regenera la taula de cerca"); ?></a></p>
        <?php
        break;
    }    
}

/**
 * Adds a blog to the search table.
 * 
 * @param int $blog_id The new blog ID.
 * @param int $user_id The user ID of the new site's admin.
 * @param string $domain The new site's domain.
 * @param string $path The new site's path.
 */
function xtec_search_new_blog($blog_id, $user_id, $domain, $path)
{
    global $wpdb;
    $blogName = get_blog_option($blog_id, 'blogname');
    $blogDescription = get_blog_option($blog_id, 'blogdescription');
    $sql = "INSERT INTO wp_search (blogid,name,description,domain,path) VALUES ('" . $blog_id . "','" . $blogName . "','" . $blogDescription . "','" . $domain . "','" . $path . "')";
    $wpdb->query($sql);
}

/**
 * Updates a the blog name from the search table.
 */
function xtec_search_update_option_blogname($old_value, $new_value)
{
    global $wpdb;
    $sql = "UPDATE wp_search SET name='$new_value' WHERE blogid=$wpdb->blogid";
    $wpdb->query($sql);
}

/**
 * Updates a the blog description from the search table.
 */
function xtec_search_update_option_blogdescriptors($old_value, $new_value)
{
    global $wpdb;
    $sql = "UPDATE wp_search SET description='$new_value' WHERE blogid=$wpdb->blogid";
    $wpdb->query($sql);
}

/**
 * Deletes a blog from the search table.
 */
function xtec_search_delete_blog($blogId)
{
    global $wpdb;
    $sql = "DELETE FROM wp_search WHERE blogid=$blogId";
    $wpdb->query($sql);
}

/**
 * Gets the blogs that contain the searched string in the name or in the description of the blog.
 * 
 * @param string $word The word to search.
 * @param int $init Number of the first matched blogs to be skipped.
 * @param int $ipp Number of blogs to get.
 * @return stdClass Object The matched blogs, the pager params and the number of blogs.
 */
function xtec_search_search($word, $init = 0, $ipp = 20)
{
    global $wpdb;
    $result = new stdClass();
    $blogsNumber = $wpdb->get_results("SELECT domain FROM wp_search WHERE name<>'' and (`name` like '%$word%' or `description` like '%$word%')");
    $init = (isset($_REQUEST['init']) && $_REQUEST['init']!='')?($_REQUEST['init']-1):0;
    $sql = "SELECT domain,path,name FROM wp_search WHERE name<>'' AND (`name` LIKE '%$word%' OR `description` LIKE '%$word%') ORDER BY `name` LIMIT $init,$ipp";
    $result->blogs = $wpdb->get_results($sql);
    $result->pager = array($init,count($blogsNumber),$word,$ipp);
    $result->blogs_count = count($result->blogs);
    return $result;
}

/**
 * Creates XTEC Search database table.
 */
function xtec_search_activation_hook() {
    global $wpdb;
    global $xtec_search_db_version;

    $table_name = $wpdb->base_prefix . 'search';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
              blogid int(11) NOT NULL DEFAULT '0',
              name varchar(255) NOT NULL DEFAULT '',
              description varchar(255) NOT NULL DEFAULT '',
              domain varchar(200) NOT NULL DEFAULT '',
              path varchar(100) NOT NULL DEFAULT '',
              PRIMARY KEY (blogid));";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');              
        dbDelta($sql);

    }
    add_option('$xtec_sea_db_version', $xtec_sea_db_version);
// XTEC ************ AFEGIT -> Add cron event to rebuild the search index
// 2012.04.02 @mmartinez
    if ( !wp_next_scheduled('rebuild_index') ) {
		//wp_schedule_event(time(), 'daily', 'rebuild_index');
    }
// *********** FI
}

// XTEC ************ AFEGIT -> Add cron event to rebuild the search index
// 2012.04.02 @mmartinez
/**
 * Rebuild the search index
 * 
 * @param bold $cron Is hook from cron
 * @param int  $n    Limit from row num
 */
function xtec_search_rebuild_index ($cron = true, $n = ''){
	global $wpdb;
	
	if ( empty($n) ) {
    	$n = 0;
        // Buida la taula
        $sql = "TRUNCATE TABLE `wp_search`;";
        $wpdb->query($sql);
    } else {
        $n = intval($n);
    }
    
    $limitto = 30;
    $limit   = !$cron ? " LIMIT $n, {$limitto}": '';
    
    // Processa els registres
    $blogs = $wpdb->get_results( "SELECT * FROM $wpdb->blogs WHERE site_id = '$wpdb->siteid' AND spam = '0' AND archived = '0' ORDER BY registered DESC{$limit}", ARRAY_A );
    
	if ( empty( $blogs ) == false ) {
    	if (!$cron){
    		print '<table border="0" width="100%">';
    	}
        foreach ( $blogs as $details ) {
        	if( $details[ 'spam' ] == 0 &&  $details[ 'archived' ] == 0 ) {
        		if (!$cron){
        			print "<tr>";
        		}
                $siteurl = get_blog_option($details['blog_id'], 'blogname');
                $blogdescription = get_blog_option($details['blog_id'], 'blogdescription');
                $domain = get_blog_details($details['blog_id'])->domain;
                $path = get_blog_details($details['blog_id'])->path;
                if (!$cron){
                	print '<td width="40%">'.$details[ 'blog_id' ].' - '.$siteurl.'</td>';
                	print '<td width="55%">'.$blogdescription.'</td>';
                }
                //Do the row exists in search table
                $exists = $wpdb->get_var( "SELECT count(blogid) from wp_search WHERE blogid = {$details[ 'blog_id' ]}" );
                if ( $details[ 'deleted' ] == 0 && $details[ 'public' ] == 1 ) {
                	//If teh row don't exist it is created if not it is updated
                    if ( $exists ) {
                    	$wpdb->get_results("UPDATE wp_search SET name='".mysql_real_escape_string($siteurl)."', description='".mysql_real_escape_string($blogdescription)."',domain='".mysql_real_escape_string($domain)."',path='".mysql_real_escape_string($path)."' WHERE blogid = {$details[ 'blog_id' ]}");
                        $actionmade = __('Updated');
                    } else {
                    	$wpdb->get_results("INSERT INTO wp_search (blogid,name,description,domain,path) VALUES ({$details[ 'blog_id' ]},'".mysql_real_escape_string($siteurl)."','".mysql_real_escape_string($blogdescription)."','".mysql_real_escape_string($domain)."','".mysql_real_escape_string($path)."')");
                        $actionmade = __('Created');
                    }
                } else {
                	//Delete the blog information from the search table
                    $sql = "DELETE FROM wp_search where blogid={$details[ 'blog_id' ]}";
                    $wpdb->query($sql);
                }
                if (!$cron){
                	print '<td width="5%">' . $actionmade . '</td>';
                	print "</tr>";
                }
            }
        }
        if (!$cron){
        	print "</table>";        
        	?>
        	<p><?php _e("If your browser doesn't start loading the next page automatically click this link:"); ?> <a href="?page=ms-search&action=search&n=<?php echo ($n + $limitto) ?>"><?php _e("Next Blogs"); ?></a> </p>
        	<script language='javascript' src='../wp-content/plugins/xtec-search/js/xtec-search-regenerate.js'></script>
        	<?php
        }
    } else {
    	if (!$cron){
        	_e("All Done!");
    	}
    }
}

/**
 * Do action when deactive the search pluging
 */
function xtec_search_deactivation_hook() {
	if ( wp_next_scheduled('rebuild_index') ) {
		wp_clear_scheduled_hook('rebuild_index');
	}
}
// *********** FI

/** @todo Make this plugin can only be activated from the main site. */
register_activation_hook(__FILE__,'xtec_search_activation_hook');

// XTEC ************ AFEGIT -> Add cron event to rebuild the search index
// 2012.04.02 @mmartinez
register_deactivation_hook(__FILE__,'xtec_search_deactivation_hook');
// ************ FI