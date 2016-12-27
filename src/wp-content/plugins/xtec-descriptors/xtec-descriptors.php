<?php

/*
Plugin Name: XTEC Descriptors
Description: Adds a descriptors taxonomy system for the blogs.
Version: 1.1
Author: Albert Pérez Monfort (aperez16) & Francesc Bassas i Bullich
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/*  Copyright 2007  Albert Pérez Monfort  (email : aperez16@xtec.cat)

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

global $xtec_descriptors_db_version;
$xtec_descriptors_db_version = '1.0';

add_action('network_admin_menu', 'xtec_descriptors_network_admin_menu');
add_action('admin_menu', 'xtec_descriptors_admin_menu');
add_action('update_option_blog_public', 'xtec_descriptors_update_blog_options');
add_action('wp_head', 'xtec_descriptors_head');
add_action('delete_blog', 'xtec_descriptors_delete_blog', 10, 2);

/**
 * Adds plugin network admin menu.
 */
function xtec_descriptors_network_admin_menu()
{
    add_menu_page('Descriptors', 'Descriptors', 'manage_network_options', 'ms-descriptor', 'xtec_descriptors_network_options');
}

/**
 * Displays the network options page of the plugin. 
 */
function xtec_descriptors_network_options()
{
    global $wpdb;
   
    if (isset($_GET['action']) && $_GET['action']=='delete') {
        ?><div id="message" class="updated fade"><p><?php echo "S'ha suprimit el descriptor de tot el lloc." ?></p></div><?php
    }
    
    if (isset($_GET['action']) && $_GET['action']=='update') {
        /** @todo Update action not implemented. */
    }

    $action = isset($_GET['action'])?$_GET['action']:'';
    switch( $action ) {
        case "delete":
            xtec_descriptors_delete_descriptor($_REQUEST['id']);
            break;
        case "update":
        	/** @todo Update action not implemented. */
            break;
        case "descriptors":
            if( isset( $_GET[ 'n' ] ) == false ) {
                $n = 0;
            } else {
                $n = intval( $_GET[ 'n' ] );
            }

            $descriptors = $wpdb->get_results("SELECT id,descriptor,blogs FROM wp_descriptors ORDER BY descriptor DESC LIMIT $n, 5", ARRAY_A);

            if ( empty( $descriptors ) == false ) {                
    			print '<table border="1" cellspacing="0" width="100%">';
                foreach ( $descriptors as $details ) {
                    print "<tr>";
                        print '<td valign="top" width="150">'.$details['descriptor'].'</td>';
                        $details['blogs']=substr($details['blogs'],0,'-1');
                        if ( $details['blogs']=='' ) {
                            print_r("1<br>");
                            $wpdb->get_results("DELETE FROM wp_descriptors WHERE id={$details['id']}");
                            $actionmade = __('Deleted');
                        } else {
                            print_r("2<br>");
                            $blogs=explode('$$',$details['blogs']);
                            array_shift($blogs);
                            $descriptorsrow='$';
                            foreach ( $blogs as $blog )
                            {
                                $blog1=explode('-',$blog);
                                $public = get_blog_details($blog1[0])->public;

                                if ($public!='' ) {
                                    $descriptorsrow.='$'.$blog1[0].'-'.$public.'$';
                                }
                            }
                            print '<td valign="top"><span style="background: #00ff00;">'.$descriptorsrow.'</span><br/><span style="background: #ff0000;">'.$details['blogs'].'$</span></td>';
                            $number = substr_count($descriptorsrow, '-1');
                        }
                        if ( $descriptorsrow=='$' ) {
                            print_r("3<br>");
                            $wpdb->get_results("DELETE FROM wp_descriptors WHERE id={$details['id']}");
                            $actionmade = __('Deleted');
                        } else {
                            print_r("4<br>");
                            if ( $descriptorsrow!=$details['blogs'].'$' ) {
                                $wpdb->get_results("UPDATE wp_descriptors set number=$number, blogs='$descriptorsrow' WHERE id={$details['id']}");
                                $actionmade = __('Updated');
                            } else {
                                $actionmade = __('Not action');
                            }
                        }
                        print '<td valign="top" width="10">'.$number.'</td>';
                        print '<td valign="top" width="80">'.$actionmade.'</td>';
                    print "</tr>";
                }
                print '<tr><td colspan="10"><span style="background: #00ff00;">Ara</span><br/><span style="background: #ff0000;">Abans</span></td></tr>';
                print "</table>";
                ?>
                <p><?php _e("If your browser doesn't start loading the next page automatically click this link:"); ?> <a href="?action=search&n=<?php echo ($n + 5) ?>"><?php _e("Next Blogs"); ?></a> </p>
                <?php /** @todo Enque script with WordPress API. */ ?>
                <script language='javascript' src='../wp-content/plugins/xtec-descriptors/js/xtec-descriptors-regenerate.js'></script>
                <?php
            } else {
                _e("All Done!");
            }
            break;        
    }

    if (isset($_GET['action']) && $_GET['action'] != 'descriptors')
    {	    
	    $sortida = '<div class="wrap">';
	    $sortida .= '<p>'. __("Des d'aquí pots regenerar la taula de cerca dels blocs fent de manera automàtica una crida de cada bloc. Feu clic al següent enllaç per a realitzar l'actualització.") . '</p>';
	    $sortida .= "<p><a href=?page=ms-descriptor&action=descriptors>" . __('Regenera la taula de descriptors') . '</a></p>';
	    $sortida .= '</div>';
	    echo $sortida;
	
	    $descripts = $wpdb->get_results("SELECT id,descriptor,blogs,number FROM wp_descriptors order by descriptor");
	    if ( $descripts ) {
		    print '<div class="wrap">';
		        print '<table border="1" cellpadding="10" cellspacing="10">';
		            foreach ( $descripts as $descriptor ) {
		                print '<tr><td>' . $descriptor->descriptor . '</td><td><strong>' . $descriptor->number . '</strong></td><td>' . $descriptor->blogs . '</td><td><a href=?page=ms-descriptor&action=delete&id=' . $descriptor->id . '>' . __('Delete') . '</a></td><td><a href=?page=ms-descriptor&action=update&id=' . $descriptor->id . '>' . __('Update') . '</a></td></tr>';
		            }
		        print '</table>';
		    print "</div>";
	    }
    }
}

/**
 * Adds plugin admin menu.
 */
function xtec_descriptors_admin_menu()
{    
	$page = add_options_page('Descriptors', 'Descriptors', 'manage_options', 'descriptors', 'xtec_descriptors_options');
    $screen = get_current_screen();
    if( !method_exists( $screen, 'add_help_tab' ) )
        return;
    $screen->add_help_tab(array( 'title' => '', 'id' => 'id1', 'content' => __('This screen helps you manage the descriptors of your blog.')));
	//add_contextual_help( $page, '<p>' . __('This screen helps you manage the descriptors of your blog.') . '</p>');
}

/**
 * Displays the options page of the plugin. 
 */
function xtec_descriptors_options()
{
	global $wpdb;
	/** @todo Enque script with WordPress API. */
	?>
    <script language='javascript' src='../wp-content/plugins/xtec-descriptors/js/xtec-descriptors-autocomp.js'></script>
	<?php    
    
    //Admin menu in Opcions blogs
    if ( isset($_REQUEST['del']) && $_REQUEST['del']!='' ) {
        //Delete blog from descriptor blogs list
        //Get blog blogs
        $descriptorBlogs = $wpdb->get_results("SELECT id,blogs,number,descriptor FROM wp_descriptors where `id` = ".$_REQUEST['del']);
        $newblogs = str_replace('$'.$wpdb->blogid.'-1$','',$descriptorBlogs[0]->blogs);
        $newblogs = str_replace('$'.$wpdb->blogid.'-0$','',$newblogs);

        $public = (get_blog_details($wpdb->blogid)->public)? 1 : 0;
        $number = xtec_descriptors_count_descriptors(utf8_decode($descriptorBlogs[0]->descriptor))-$public;

        $sql = "UPDATE wp_descriptors SET `blogs`='$newblogs', `number`=$number WHERE id=".$descriptorBlogs[0]->id;
        //If is the last blog that have this descriptor delete the descriptor
        if ( $number==0 && $newblogs=='$' ) {
            $sql = "DELETE FROM wp_descriptors WHERE id=".$descriptorBlogs[0]->id;
        }
        //Execute the SQL sentence
        $wpdb->query($sql);    
    }

    if( isset($_REQUEST['descriptor']) && $_REQUEST['descriptor']!='' ){
        $descript = strtolower(utf8_decode($_POST['descriptor']));
        // \p{L} Any kind of letter from any language
        // \p{N} Any kind of numeric character in any script
        // \P is the negated version of \p    
        $descript=preg_replace('/\P{L}\P{N}/','',$descript);
        
        //Add the descriptor in descriptors table
        //Try if descriptor exists
        $descriptorId = $wpdb->get_results("SELECT id,blogs FROM wp_descriptors where `descriptor` like '".utf8_encode($descript)."'");
        //If exists add the blog in blogs list if it isn't

        $public = (get_blog_details($wpdb->blogid)->public)? 1 : 0;
        if ( $descriptorId[0]->id=='' ) {
            //Create descriptor
            $sql="INSERT INTO wp_descriptors (descriptor,number,blogs) VALUES ('".utf8_encode($descript)."',".$public.",'$$".$wpdb->blogid."-".$public."$')";
            $wpdb->query($sql);
        } else {
            //Update the descriptor information. First check if the blog is in descriptor blogs field
            if ( !strpos( utf8_encode($descriptorId[0]->blogs), '$' . $wpdb->blogid.'-1$' ) && !strpos( utf8_encode($descriptorId[0]->blogs), '$' . $wpdb->blogid.'-0$' ) ) {
                $newblogs = $descriptorId[0]->blogs.'$'.$wpdb->blogid.'-'.$public.'$';
                $number = xtec_descriptors_count_descriptors($descript)+$public;
                $sql="UPDATE wp_descriptors SET `blogs`='$newblogs', `number`=$number WHERE id=".$descriptorId[0]->id;
                $wpdb->query($sql);            
            }
        }
    }
    
	?>
	<div class="wrap">
		<h2>Llista de descriptors del bloc</h2>
		<?php
        $descripts = $wpdb->get_results("SELECT id,descriptor FROM wp_descriptors WHERE blogs LIKE '%$".$wpdb->blogid."-1$%' OR blogs LIKE '%$".$wpdb->blogid."-0$%'");
		$have = false;
		print "<table>";
		foreach ( $descripts as $descript ) {
			if ( $descript!='' ) {
				print "<tr><td width=\"150\">".$descript->descriptor."</td><td><a href=\"?del=".$descript->id."&page=descriptors\">Esborra</a></td></tr>";
				$have = true;
			}
		}
		if ( !$have ) { print "<tr><td width=\"100\"><strong>No hi ha descriptors definits.</strong></td></tr>"; }
		print "</table>";
		?>
		<p>Afegeix un descriptor nou</p>
		<form action="#" method="post" autocomplete="on">
			<input id="descriptor" type="text" name="descriptor" maxlength="20" size="20" onKeyPress="autocomplete(this.value,event)" />
			<input type="submit" value="Crea el descriptor" />
			<div id="autocompletediv"></div>
		</form>
	</div>
	<?php
}

/**
 * Updates descriptors according to the privacy of the blog.
 */
function xtec_descriptors_update_blog_options()
{
	global $wpdb;
	$blogId = $wpdb->blogid;
	$blogs = $wpdb->get_results("SELECT blogs,descriptor,id from wp_descriptors where `blogs` like '%$".$blogId."-1$%' or `blogs` like '%$".$blogId."-0$%'");

	foreach ( $blogs as $blog ) {
		$newString = '';
		if ( get_blog_details($blogId)->public ) {
			$newString = str_replace('$'.$blogId.'-0$','$'.$blogId.'-1$',$blog->blogs);
			/** @todo Verify that it's necessary to use utf8_decode. */
			$number = xtec_descriptors_count_descriptors(utf8_decode($blog->descriptor))+1;
		} else {
			$newString = str_replace('$'.$blogId.'-1$','$'.$blogId.'-0$',$blog->blogs);
			/** @todo Verify that it's necessary to use utf8_decode. */
			$number = xtec_descriptors_count_descriptors(utf8_decode($blog->descriptor))-1;
			if( $number < 0 ) { $number = 0; }
		}
		
		$sql="UPDATE wp_descriptors SET `blogs`='$newString', `number`=$number WHERE id=".$blog->id;

		$wpdb->query($sql);	
	}
}

/**
 * Prints meta info.
 */
function xtec_descriptors_head()
{
	global $wpdb;
    $descriptors = '';
	$descript = $wpdb->get_results( "SELECT descriptor FROM wp_descriptors where blogs like '%$" . $wpdb->blogid . "-1$%' or blogs like '%$" . $wpdb->blogid . "-0$%'" );
	foreach ( $descript as $d ) {
		$descriptors .= $d->descriptor.',';
	}
	$descriptors = substr( $descriptors, 0, '-1');	
	if ( $descriptors ) {
		$meta_string = sprintf( "<meta name=\"DC.Subject\" content=\"%s\"/>", $descriptors );
		echo $meta_string . "\n";
	}	
}

/**
 * Deletes a blog of all the descriptors.
 * 
 * @param int $blog_id Blog ID
 * @param bool $drop True if blog's table should be dropped. Default is false.
 */
function xtec_descriptors_delete_blog($blog_id, $drop)
{
    global $wpdb;

    $descriptorId = $wpdb->get_results( "SELECT id FROM wp_descriptors where `blogs` like '%$" . $blog_id . "-%'" );

    foreach ( $descriptorId as $id ) {
        $descriptorBlogs = $wpdb->get_results( "SELECT id,blogs,number FROM wp_descriptors where `id` = " . $id->id );
        
        //delete de reference to the blog public or not
        $keys = array( '$' . $blog_id . '-0$', '$' . $blog_id . '-1$');
        $newblogs = str_replace($keys, '', $descriptorBlogs[0]->blogs);
        
        $sql = "UPDATE wp_descriptors SET `blogs`='$newblogs', `number`=`number`-1 WHERE id=".$descriptorBlogs[0]->id;
        //If is the last blog that have this descriptor delete the descriptor
        if ( $descriptorBlogs[0]->number == '1' ) {
            $sql = "DELETE FROM wp_descriptors WHERE id=" . $descriptorBlogs[0]->id;
        }
        $wpdb->query($sql);
    }
}

/**
 * Creates XTEC Descriptors database tables.
 */
function xtec_descriptors_activation_hook()
{
    global $wpdb;
    global $xtec_descriptors_db_version;

    $table_name = $wpdb->base_prefix . 'descriptors';

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
    	$sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                descriptor varchar(50) NOT NULL DEFAULT '',
                number int(11) NOT NULL DEFAULT '0',
                blogs text NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY descriptor (descriptor));";
    	$sql .= "CREATE TABLE {$table_name}_pre (
    	         id int(10) NOT NULL AUTO_INCREMENT,
                 descriptor varchar(20) NOT NULL DEFAULT '',
                 PRIMARY KEY (id),
                 UNIQUE KEY descriptor (descriptor));";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        //insert default values
        $sql = "INSERT INTO `wp_descriptors_pre` VALUES(1, 'matemàtiques');
                INSERT INTO `wp_descriptors_pre` VALUES(2, 'socials');
                INSERT INTO `wp_descriptors_pre` VALUES(3, 'català');
                INSERT INTO `wp_descriptors_pre` VALUES(4, 'castellà');
                INSERT INTO `wp_descriptors_pre` VALUES(5, 'descoberta');
                INSERT INTO `wp_descriptors_pre` VALUES(6, 'comunicació');
                INSERT INTO `wp_descriptors_pre` VALUES(7, 'literatura');
                INSERT INTO `wp_descriptors_pre` VALUES(8, 'aranès');
                INSERT INTO `wp_descriptors_pre` VALUES(9, 'idiomes');
                INSERT INTO `wp_descriptors_pre` VALUES(10, 'naturals');
                INSERT INTO `wp_descriptors_pre` VALUES(11, 'música');
                INSERT INTO `wp_descriptors_pre` VALUES(12, 'art');
                INSERT INTO `wp_descriptors_pre` VALUES(13, 'visual');
                INSERT INTO `wp_descriptors_pre` VALUES(14, 'plàstica');
                INSERT INTO `wp_descriptors_pre` VALUES(15, 'física');
                INSERT INTO `wp_descriptors_pre` VALUES(16, 'drets');
                INSERT INTO `wp_descriptors_pre` VALUES(17, 'ciutadania');
                INSERT INTO `wp_descriptors_pre` VALUES(18, 'tutoria');
                INSERT INTO `wp_descriptors_pre` VALUES(19, 'religió');
                INSERT INTO `wp_descriptors_pre` VALUES(20, 'tecnologia');
                INSERT INTO `wp_descriptors_pre` VALUES(21, 'clàssiques');
                INSERT INTO `wp_descriptors_pre` VALUES(22, 'filosofia');
                INSERT INTO `wp_descriptors_pre` VALUES(23, 'història');
                INSERT INTO `wp_descriptors_pre` VALUES(24, 'biologia');
                INSERT INTO `wp_descriptors_pre` VALUES(25, 'química');
                INSERT INTO `wp_descriptors_pre` VALUES(26, 'dibuix');
                INSERT INTO `wp_descriptors_pre` VALUES(27, 'economia');
                INSERT INTO `wp_descriptors_pre` VALUES(28, 'organització');
                INSERT INTO `wp_descriptors_pre` VALUES(29, 'empresa');
                INSERT INTO `wp_descriptors_pre` VALUES(30, 'geografia');
                INSERT INTO `wp_descriptors_pre` VALUES(31, 'grec');
                INSERT INTO `wp_descriptors_pre` VALUES(32, 'contemporani');
                INSERT INTO `wp_descriptors_pre` VALUES(33, 'món');
                INSERT INTO `wp_descriptors_pre` VALUES(34, 'electrotècnia');
                INSERT INTO `wp_descriptors_pre` VALUES(35, 'llatí');
                INSERT INTO `wp_descriptors_pre` VALUES(36, 'industrial');
                INSERT INTO `wp_descriptors_pre` VALUES(37, 'mecànica');
                INSERT INTO `wp_descriptors_pre` VALUES(38, 'disseny');
                INSERT INTO `wp_descriptors_pre` VALUES(39, 'imatge');
                INSERT INTO `wp_descriptors_pre` VALUES(40, 'expressió');
                INSERT INTO `wp_descriptors_pre` VALUES(41, 'volum');
                INSERT INTO `wp_descriptors_pre` VALUES(42, 'recerca');
                INSERT INTO `wp_descriptors_pre` VALUES(43, 'primària');
                INSERT INTO `wp_descriptors_pre` VALUES(44, 'batxillerat');
                INSERT INTO `wp_descriptors_pre` VALUES(45, 'secundària');
                INSERT INTO `wp_descriptors_pre` VALUES(46, 'cicles');";
        dbDelta($sql);        
    }
    add_option('$xtec_descriptors_db_version', $xtec_descriptors_db_version);
}

/** @todo Make this plugin can only be activated from the main site. */
register_activation_hook(__FILE__,'xtec_descriptors_activation_hook');

/* XTEC Descriptors tags */

/**
 * Gets all the descriptors with the tag, their weight and font size for a cloud output.
 *
 * @param int $number Total number of descriptors to get.
 * @param int $min_font_size The minimum font size in pixels.
 * @param int $min_font_size The maximum font size in pixels.
 * @return array
 */
function xtec_descriptors_get_descriptors_cloud($number,$min_font_size,$max_font_size)
{
	global $wpdb;
	$cloudArray = array(); // create an array to hold tag code		
	// Pull in tag data		
	$tags = $wpdb->get_results("SELECT descriptor,number FROM wp_descriptors where blogs like '%-1$%' ORDER BY number DESC limit 0,$number");

	$arr=array();
	for($i=0;$i<count($tags);$i++){
    	$arr[$tags[$i]->descriptor] = $tags[$i]->number;
	}	
	ksort($arr);
	
	if(count($arr)>0){
		$minimum_count = min(array_values($arr));
		$maximum_count = max(array_values($arr));			
		$spread = $maximum_count - $minimum_count;
		if($spread == 0) {
			$spread = 1;
		}
        
		//Finally we start the HTML building process to display our tags. For this demo the tag simply searches Google using the provided tag.
		foreach ($arr as $tag => $count) {
			$size = $min_font_size + ($count - $minimum_count) * ($max_font_size - $min_font_size) / $spread;
			$cloudArray[]=array('size'=>floor($size),'tag'=>htmlspecialchars(stripslashes($tag)),'count'=>$count);
		}
	}
    return  $cloudArray;
}

/**
 * Gets the blogs with a specific descriptor.
 * 
 * @param string $descriptor The descriptor to search.
 * @param bool $public True if the search must returns only the public blogs, false if it must returns all the blogs.
 * @return array The IDs of the blogs found.
 */
function xtec_descriptors_get_blogs_by_descriptor($descriptor, $public=true)
{
	global $wpdb;
	$sql = "SELECT blogs from wp_descriptors where `descriptor` = '".$descriptor."'";
	$blogs = $wpdb->get_col($sql);
	$blogs = explode('$$',substr($blogs[0],0,-1));
	array_shift($blogs);
    
	$bbd = array();
	
	foreach ( $blogs as $blog ) {
		$blogInit = $blog;
		$blog = str_replace('-1','',$blog);
		$blog = str_replace('-0','',$blog);
		if ( get_blog_details($blog)->public || $public ) {
			array_push($bbd, $blog);
		}
	}
	return $bbd;
}

/**
 * Gets the descriptors of a blog.
 * 
 * @param int $blog_id ID of the blog.
 * @return array The descriptors of the blog.
 */
function xtec_descriptors_get_descriptors_by_blog($blog_id)
{
	global $wpdb;
	$descriptors = $wpdb->get_results("SELECT descriptor FROM wp_descriptors WHERE blogs LIKE '%$".$blog_id."-1$%'");
	
	$dbb = array();
	foreach ( $descriptors as $descriptor ) {
		array_push($dbb, $descriptor->descriptor);
	}
	return $dbb;
}

/**
 * Counts the number of descriptors of a blog.
 * 
 * @param int $blogId Id of the blog.
 * @return int The number of descriptors of the blog.
 */
function xtec_descriptors_count_bloc_descriptors($blogId)
{
	global $wpdb;
	$descripts = $wpdb->get_results( "SELECT count(*) as number FROM wp_descriptors where blogs like '%$".$blogId."-1$%' or blogs like '%$".$blogId."-0$%'" );
	return $descripts[0]->number;
}

/**
 * Counts the number of blogs with the descriptor.
 *
 * @param string $descriptor The name of the descriptor.
 * @return int The number of blogs with the descriptor $desctiptor.
 */
function xtec_descriptors_count_descriptors($descriptor)
{
	global $wpdb;
	$sql="SELECT blogs FROM wp_descriptors where `descriptor` like '".utf8_encode($descriptor)."'";
	$descriptorId = $wpdb->get_results($sql);
	$number = count(explode('-1$',$descriptorId[0]->blogs))-1;
	return $number;
}

/**
 * Deletes a descriptor.
 * 
 * @param int $id Id of the descriptor.
 */
function xtec_descriptors_delete_descriptor($id)
{
    global $wpdb;
    $descripts = $wpdb->get_results("DELETE from wp_descriptors WHERE id=$id;");
}