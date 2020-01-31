<?php

/*
Plugin Name: XTEC Maintenance
Plugin URI:
Description:
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

global $xtec_maintenance_db_version;
$xtec_maintenance_db_version = '1.0';

add_action('network_admin_menu', 'xtec_maintenance_menu');

function xtec_maintenance_menu()
{
	if( is_super_admin() ) {
		add_menu_page('Manteniment','Manteniment','manage_network','xtec-maintenance','xtec_maintenance_page');
		add_submenu_page('xtec-maintenance','Eliminació de blocs','Eliminació de blocs','manage_network','xtec-delblogs','xtec_delblogs_page');
		add_submenu_page('xtec-maintenance','Blocs eliminats per inactivitat','Blocs eliminats per inactivitat','manage_network','xtec-inactivity-deleted-blogs','xtec_inactivity_deleted_blogs_page');
		add_submenu_page('xtec-maintenance','Blocs eliminats pels usuaris','Blocs eliminats pels usuaris','manage_network','xtec-user-deleted-blogs','xtec_user_deleted_blogs_page');
	}
}

function xtec_maintenance_page()
{
	?>
	<div class="wrap">
		<h2>Manteniment del lloc</h2>
		<p><a href="?page=xtec-delblogs">Elimina blocs inactius</a></p>
		<p><a href="?page=xtec-inactivity-deleted-blogs">Llista els blocs eliminats per inactivitat</a></p>
		<p><a href="?page=xtec-user-deleted-blogs">Llista els blocs eliminats pels usuaris</a></p>
		<div class="clear"/>
		<h3>Algunes dades del lloc</h3>
		<?php $site_stats = get_sitestats();?>
		<p>Nombre total de blocs: <strong><?php echo $site_stats['blogs']?></strong></p>
		<?php
		global $wpdb,$wp_db_version;
		$deleted_blogs = $wpdb->get_var("SELECT COUNT(blog_id) FROM $wpdb->blogs  WHERE deleted = 1");
		?>
		<p>Nombre de blocs eliminats pels usuaris: <strong><?php echo $deleted_blogs?></strong></p>
	</div>
	<?php
}

function xtec_delblogs_page()
{
	$inactivity_days = isset($_REQUEST['inactivity_days']) ? $_REQUEST['inactivity_days']: get_site_option('xtec_maintenance_inactivity_days',90);

	$posts_pages = isset($_REQUEST['posts_pages']) ? $_REQUEST['posts_pages']: get_site_option('xtec_maintenance_posts_pages',2);


	if (get_site_option('xtec_maintenance_never_upload_file',1) == 1) { $never_upload_file = true; }
	else { $never_upload_file = false; }

	if (isset($_REQUEST['never_upload_file'])) { $never_upload_file = $_REQUEST['never_upload_file']; }
	else {
		if (isset($_POST['submit']) || isset($_POST['set_as_default']) || isset($_POST['touch'])) {
			$never_upload_file = false;
		}
	}
	?>

	<div class="wrap">

	<h2>Eliminació de blocs</h2>

	<?php
	global $wpdb;

	if (isset($_POST['delete'])) {
		if (!isset($_POST['idblogs'])) {
			?><div class="error below-h2"><p>No has seleccionat cap bloc.</p></div><?php
		}
		else {
			$idblogs = $_POST['idblogs'];
			foreach ($idblogs as $idblog) {
				$blogname = get_blog_option($idblog,'blogname');
				$path = $wpdb->get_var("SELECT `path` FROM `$wpdb->blogs` WHERE `blog_id`='{$idblog}'");
				$del_date = date("Y-m-d H:i:s");

				$sql = "INSERT INTO `wp_delblocs` SET `site_id` = '$idblog', `site_path` = '".$path."', `blogname` = '".$blogname."', `del_date` = '$del_date', `status` = '2';";
				$wpdb->get_results($sql);

				$users = get_users(['blog_id' => $idblog]);
				foreach ($users as $user) {
					$sql = "INSERT INTO `wp_delblocs_users` SET `blog_id` = '$idblog', `user_id` = '".$user->user_id."', `user_login` = '".$user->user_login."', `display_name` = '".$user->display_name."', `user_email` = '".$user->user_email."', `meta_value` = '".$user->meta_value."';";
					$wpdb->get_results($sql);
				}

				// drops data of the blog when deleted
				wp_delete_site($idblog, true);
				echo "El bloc $blogname amb ID $idblog s'ha eliminat correctament.<br />";
			}
		}
		echo "<p><a href=\"?page=xtec-delblogs\">Torna al formulari d'eliminació de blocs</a></p>";
	}
	else if (isset($_POST['touch'])){
		if (!isset($_POST['idblogs'])) {
			?><div class="error below-h2"><p>No has seleccionat cap bloc.</p></div><?php
		}
		else {
			$idblogs = $_POST['idblogs'];
			foreach ($idblogs as $idblog) {
				$blogname = get_blog_option($idblog,'blogname');
				$wpdb->update( $wpdb->blogs, array('last_updated' => current_time('mysql', true)), array('blog_id' => $idblog) );
				refresh_blog_details( $wpdb->blogid );
				echo "La data de la darrera actualització del bloc <strong>$blogname</strong> amb ID <strong>$idblog</strong> ha estat actualitzada a la data actual.<br>";
			}
		}
		echo "<p><a href=\"?page=xtec-delblogs\">Torna al formulari d'eliminació de blocs</a></p>";
	}
	else {
		if (isset($_POST['set_as_default'])) {
			update_site_option( 'xtec_maintenance_inactivity_days', $_POST['inactivity_days'] );
			update_site_option( 'xtec_maintenance_posts_pages', $_POST['posts_pages'] );
			$value = isset($_POST['never_upload_file']) ? 1 : 0;
			update_site_option( 'xtec_maintenance_never_upload_file', $value );
		}
		?>

		<form action="?page=xtec-delblogs" method="post">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="inactivity_days">Dies d'inactivitat</label>
						</th>
						<td>
							<input id="inactivity_days" class="small-text" type="text" value="<?php echo $inactivity_days?>" name="inactivity_days"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="posts_pages">Articles i pàgines</label>
						</th>
						<td>
							<input id="posts_pages" class="small-text" type="text" value="<?php echo $posts_pages?>" name="posts_pages"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="never_upload_file">Mai s'ha pujat cap fitxer</label>
						</th>
						<td>
							<?php if ($never_upload_file) {?>
								<input type="checkbox" checked="checked" value=1 name="never_upload_file"/>
							<?php } else {?>
								<input type="checkbox" value=1 name="never_upload_file"/>
							<?php }?>
						</td>
					</tr>
				</tbody>
			</table>

			<h3>Es llisten tots els blocs que compleixen les següents restriccions:</h3>

			<div  style="background-color: rgb(255, 251, 204); border-color:#E6DB55; border-style: solid;border-width:1px; padding: 0.6em;-moz-border-radius:3px">
				<?php if ($inactivity_days == 1) { ?> - Fa almenys <strong><?php echo $inactivity_days?></strong> dia que no s'actualitzen.<br>
				<?php } else if ($inactivity_days != 0) { ?> - Fa almenys <strong><?php echo $inactivity_days?></strong> dies que no s'actualitzen.<br>
				<?php }?>
				<?php if ($posts_pages == 1) { ?> - Tenen com a molt <strong><?php echo $posts_pages?></strong> article o pàgina.<br>
				<?php } else if ($posts_pages != 0) { ?> - Tenen com a molt <strong><?php echo $posts_pages?></strong> articles o pàgines.<br>
				<?php } else if ($posts_pages == 0) { ?> - No tenen <strong>cap</strong> article ni <strong>cap</strong> pàgina.<br>
				<?php }?>
				<?php if ($never_upload_file) { ?> - <strong>Mai</strong> s'hi ha pujat cap fitxer.<br>
				<?php } else { ?> - S'hi ha pujat <strong>alguna vegada</strong> algun fitxer.<br>
				<?php }?>
			</div>

			<div class="tablenav">
				<div class="alignleft">
						<input class="button-secondary" type="submit" value="Refresca la llista" name="submit"/>
						<input class="button-secondary" type="submit" value="Elimina els blocs seleccionats" name="delete"/>
						<input class="button-secondary" type="submit" value="Actualitza la data dels blocs seleccionats" name="touch"/>
						<input class="button-secondary" type="submit" value="Desa els valors actuals com a predeterminats" name="set_as_default"/>
						<br class="clear" />
				</div>
			</div>
			<br class="clear" />

			<?php $blogname_columns = ( constant( "VHOST" ) == 'yes' ) ? __('Domain') : __('Path');	?>

			<?php

			// Inactivity check
			$date_limit = date("Y-m-d H:i:s",time()-($inactivity_days*86400));

			$apage = isset( $_GET['apage'] ) ? intval( $_GET['apage'] ) : 1;
			$num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : 25;

			$query = 'SELECT `blog_id`, `path`, `registered`, UNIX_TIMESTAMP(`last_updated`) as last_updated FROM '.$wpdb->blogs.' WHERE last_updated<"'.$date_limit.'" AND deleted=0 AND blog_id!=1';

			$total = $wpdb->get_var( 'SELECT count(`blog_id`) FROM '.$wpdb->blogs.' WHERE last_updated<"'.$date_limit.'" AND deleted=0 AND blog_id!=1' );

			//$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );

			$blogs = $wpdb->get_results( $query, ARRAY_A );
			?>
			<table cellspacing="3" cellpadding="3" width="100%" class="widefat">
				<thead>
					<tr>
						<th class="check-column" scope="col"><input type="checkbox"/></th>
						<th scope="col"><?php echo __('ID')?></th>
						<th scope="col"><?php echo $blogname_columns ?></th>
						<th scope="col">Dies des de la darrera actualització</th>
						<th scope="col">Articles i pàgines</th>
						<th scope="col">S'han pujat fitxers algun cop?</th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php
					$show_pagination = 0;
					$items = ($apage-1)*25;
					$cont = 0;

					for($i=0;$i<count($blogs);$i++){

						$blog_id      = $blogs[$i]["blog_id"];
						$path         = $blogs[$i]["path"];
						$registered   = $blogs[$i]["registered"];
						$last_updated = $blogs[$i]["last_updated"];

						$posts_pages_check = false;
						$upload_check = false;

						$now = time();
						$elapsed_days = (int)(($now-$last_updated)/86400);

						// Posts and Pages check
						$posts = $wpdb->get_results( "SELECT count(ID) as posts FROM `wp_".$blog_id."_posts` WHERE post_type='post'", ARRAY_A );
						$pages = $wpdb->get_results( "SELECT count(ID) as pages FROM `wp_".$blog_id."_posts` WHERE post_type='page'", ARRAY_A );

						$post_num = (isset($posts[0]['posts'])) ? $posts[0]['posts'] : 0;
						$pages_num = (isset($pages[0]['pages'])) ? $pages[0]['pages'] : 0;

						if ($post_num+$pages_num <= $posts_pages) {
							$posts_pages_check = true;
						}

						// Upload check
						if (!file_exists(ABSPATH . "wp-content/blogs.dir/$blog_id")) {
							if ($never_upload_file) { $upload_check = true; }
						}
						else {
							if (!$never_upload_file) { $upload_check = true; }
						}

						// Check the rules
						if ( $posts_pages_check && $upload_check) {
							$show_pagination = 1;
							$cont++;

							if( $cont > $items and $cont <= ($items+25)){
								?>
								<tr class="alternate">
									<th class="check-column" scope="row">
										<input type="checkbox" value="<?php echo $blog_id?>" name="idblogs[]" id="blog_<?php echo $blog_id?>"/>
									</th>

									<th scope="row"><?php echo $blog_id?></th>
									<td valign="top">
										<a rel="permalink" href="<?php echo $path?>"><?php echo $path?></a>
									</td>
									<td valign="top">
										<?php echo '<strong>' . $elapsed_days . '</strong>'?>
									</td>
									<td valign="top">
									<?php echo '<strong>' . ($post_num+$pages_num) . '</strong>' . ' ('. $post_num . '+' . $pages_num . ')' ?>
									</td>
									<td valign="top">
										<strong><?php if($never_upload_file) { ?>NO<?php } else { ?>SI<?php }?></strong>
									</td>
								</tr>
								<?php
							}
						}
					}
					?>
				</tbody>
			</table>
			<?php

			//Add pagination
			$blog_navigation = paginate_links( array(
				'base' => add_query_arg( 'apage', '%#%'),
				'format' => '',
				'total' => ceil($cont / $num),
				'current' => $apage,
				'add_args' => array('posts_pages'=>$posts_pages,'inactivity_days'=>$inactivity_days,'never_upload_file'=>$never_upload_file),
			));

			?>

			<div class="tablenav">
				<?php if ( $blog_navigation ) echo "<div class='tablenav-pages'>".$blog_navigation."</div>"; ?>
			</div>
		</form>
		<script>
			var show_pagination = <?php echo $show_pagination ?>;
			if(show_pagination == 0){
				var elements = document.getElementsByClassName('tablenav-pages');
			    for(var i = 0, length = elements.length; i < length; i++) {
					elements[i].style.display = 'none';
			    }
			}
		</script>
		<?php
	}
	?>
	</div>
	<?php
}

function xtec_inactivity_deleted_blogs_page()
{
	?>
	<div class="wrap">
		<h2>Blocs eliminats per inactivitat</h2>

		<?php
		global $wpdb;

		$apage = isset( $_GET['apage'] ) ? intval( $_GET['apage'] ) : 1;
		$num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : 25;

		$query = 'SELECT id, site_id, site_path, blogname, del_date, status FROM wp_delblocs WHERE status=2';

		$total = $wpdb->get_var( "SELECT COUNT(id) FROM wp_delblocs WHERE status=2");

		$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );
		$blog_list = $wpdb->get_results( $query, ARRAY_A );

		$blog_navigation = paginate_links( array(
			'base' => add_query_arg( 'apage', '%#%' ),
			'format' => '',
			'total' => ceil($total / $num),
			'current' => $apage
		));
		?>

		<p>Número de blocs eliminats per inactivitat fins al dia d'avui: <strong><?php echo $total; ?></strong></p>
		<p><a href="?page=xtec-user-deleted-blogs">Llista els blocs eliminats pels usuaris</a></p>

		<div class="tablenav">
			<?php if ( $blog_navigation ) echo "<div class='tablenav-pages'>$blog_navigation</div>"; ?>
		</div>

		<?php $blogname_columns = ( constant( "VHOST" ) == 'yes' ) ? __('Domain') : __('Path');	?>

		<table cellspacing="3" cellpadding="3" width="100%" class="widefat">
			<thead>
				<tr>
					<th scope="col"><?php echo __('ID')?></th>
					<th scope="col">Nom</th>
					<th scope="col"><?php echo $blogname_columns ?></th>
					<th scope="col"><?php echo __('Users') ?></th>
					<th scope="col">Data d'eliminació</th>
					<th scope="col">Estat</th>
				</tr>
			</thead>
			<tbody>

			<?php
			foreach ($blog_list as $blog) {
				$site_id = $blog["site_id"];
				$site_path = $blog["site_path"];
				$blogname = $blog["blogname"];
				$del_date = $blog["del_date"];
				$status = $blog["status"];
				$users = $wpdb->get_results( "SELECT * FROM wp_delblocs_users WHERE blog_id=". $blog['site_id'], ARRAY_A );
				?>
				<tr style='background:#f55' class='alternate'>
					<th scope="row"><?php echo $site_id?></th>
					<td valign="top">
						<?php echo $blogname ?>
					</td>
					<td valign="top">
						<?php echo $site_path ?>
					</td>
					<td valign="top">
						<?php
						foreach ($users as $user) {
							echo "<a href=\"user-edit.php?user_id=".$user[user_id]."\">".$user[display_name]."</a> (".$user[user_email].")<br>";
						}
						?>
					</td>
					<td valign="top">
						<?php echo $del_date?>
					</td>
					<td valign="top">
						Eliminat per inactivitat
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
}

function xtec_user_deleted_blogs_page()
{
	if (isset($_POST['delete'])) {
		if (isset($_POST['idblogs'])) {
			foreach ( (array) $_POST['idblogs'] as $key => $val ) {
				if( $val != '0' && $val != '1' ) {
                    wp_delete_site( $val, true );
				}
			}
			?>
			<div class="updated fade">
				<p>S'han eliminat correctament els blocs seleccionats.</p>
			</div>
			<?php
		}
	}
	?>

	<div class="wrap" style="position:relative;">

		<h2>Blocs eliminats pels usuaris</h2>

		<form action="?page=xtec-user-deleted-blogs" method="post">

			<?php
			global $wpdb;

			$apage = isset( $_GET['apage'] ) ? intval( $_GET['apage'] ) : 1;
			$num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : 25;

			$query = "SELECT * FROM {$wpdb->blogs} WHERE deleted=1";

			$total = $wpdb->get_var( "SELECT COUNT(blog_id) FROM {$wpdb->blogs} WHERE deleted=1");

			$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );
			$blog_list = $wpdb->get_results( $query, ARRAY_A );

			$blog_navigation = paginate_links( array(
				'base' => add_query_arg( 'apage', '%#%' ),
				'format' => '',
				'total' => ceil($total / $num),
				'current' => $apage
			));
			?>

			<p>Número de blocs eliminats pels usuaris: <strong><?php echo $total; ?></strong></p>
			<p><a href="?page=xtec-inactivity-deleted-blogs">Llista els blocs eliminats per inactivitat</a></p>

			<div class="tablenav">
				<?php if ( $blog_navigation ) echo "<div class='tablenav-pages'>$blog_navigation</div>"; ?>
				<div class="alignleft">
					<input type="submit" value="Elimina permanentment els blocs seleccionats" name="delete" class="button-secondary delete" />
					<br class="clear" />
				</div>
			</div>

			<br class="clear" />

			<?php $blogname_columns = ( constant( "VHOST" ) == 'yes' ) ? __('Domain') : __('Path');	?>

			<table width="100%" cellpadding="3" cellspacing="3" class="widefat">

				<thead>
					<tr>
						<th class="check-column" scope="col"><input type="checkbox"/></th>
						<th scope="col"><?php echo __('ID')?></th>
						<th scope="col"><?php echo $blogname_columns ?></th>
						<th scope="col"><?php echo __('Last Updated')?></th>
						<th scope="col"><?php echo __('Registered')?></th>
						<th scope="col"><?php echo __('Actions')?></th>
					</tr>
				</thead>

				<tbody id="the-list">
					<?php
					if ($blog_list) {
						foreach ($blog_list as $blog) { ?>
							<tr style='background:#f55' class='alternate'>
								<th scope="row" class="check-column">
									<input type='checkbox' id='blog_<?php echo $blog['blog_id'] ?>' name='idblogs[]' value='<?php echo $blog['blog_id'] ?>' />
								</th>
								<th scope="row">
									<?php echo $blog['blog_id'] ?>
								</th>
								<td valign="top">
									<a href="http://<?php echo $blog['domain']. $blog['path']; ?>" rel="permalink"><?php echo $blog['path']; ?></a>
								</td>
								<td valign="top">
									<?php echo $blog['last_updated'] ?>
								</td>
								<td valign="top">
									<?php echo $blog['registered'] ?>
								</td>
								<td valign="top">
									<a class='delete' href="wpmu-edit.php?action=confirm&amp;action2=deleteblog&amp;id=<?php echo $blog['blog_id'] ?>&amp;msg=<?php echo urlencode( sprintf( __( "You are about to delete the blog %s" ), $blogname ) ) ?>"><?php _e("Delete") ?></a>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
}

/**
 * Creates XTEC Maintenance database tables.
 */
function xtec_maintenance_activation_hook() {
    global $wpdb;
    global $xtec_maintenance_db_version;

    $table_name = $wpdb->base_prefix . 'delblocs';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                site_id varchar(60) NOT NULL,
                site_path varchar(100) NOT NULL,
                blogname varchar(255) NOT NULL,
                del_date datetime NOT NULL,
                status tinyint(4) NOT NULL DEFAULT 0,
                PRIMARY KEY (id));";

        $sql = $sql . "CREATE TABLE {$table_name}_users (
                id int(11) NOT NULL AUTO_INCREMENT,
                blog_id int(11) NOT NULL,
                user_id int(11) NOT NULL,
                user_login varchar(60) NOT NULL,
                display_name varchar(60) NOT NULL,
                user_email varchar(50) NOT NULL,
                meta_value varchar(255) NOT NULL,
                PRIMARY KEY (id));";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    add_option('$xtec_maintenance_db_version', $xtec_maintenance_db_version);
}

/** @todo Make this plugin can only be activated from the main site. */
register_activation_hook(__FILE__,'xtec_maintenance_activation_hook');