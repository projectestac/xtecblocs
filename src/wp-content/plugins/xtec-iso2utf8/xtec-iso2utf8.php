<?php

/*
Plugin Name: XTEC ISO2UTF8
Plugin URI: 
Description: Allows network admin to convert the charset of the tables of a specified blog to UTF8.
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI: 
*/

add_action('network_admin_menu', 'iso2utf8_menu');

/**
 * Adds plugin menu.
 */
function iso2utf8_menu()
{
	add_menu_page('ISO2UTF8','ISO2UTF8','manage_network_options','iso2utf8','iso2utf8_function');
}

/**
 * Displays a form that allows to convert the charset of the tables of a specified blog to UTF8.
 */
function iso2utf8_function()
{
	$show_form = true;
	
	echo '<div class="wrap">';
		echo '<h2>ISO to UTF8</h2>';
		if (isset($_POST['convert_tables'])) {
			$blog_id = $_POST['blog_id'];
			$show_form = false;
			
			if ($blog_id) {
				if ($blog_url = get_blog_option($blog_id,'siteurl')) {					
					echo '<form action="?page=iso2utf8" method="post">';
						echo '<p>Esteu segur que voleu convertir la codificació de caràcters de les taules del blog amb ID = ' .
								$blog_id . ' i url = <a href="' . $blog_url . '">' . $blog_url . '</a> ?</p>';
						echo '<p>En cas de dubte millor que ho deixeu córrer.';
						echo '<input id="blog_id" type="hidden" name="blog_id" value="' . $blog_id . '"/>';
						echo '<p class="submit">';
							echo '<input type="submit" value="Continua »" name="convert_tables_confirm"/>';
							echo '<input type="submit" value="Millor que ho deixi córrer »" name="convert_tables_abort" />';
						echo '</p>';
					echo '</form>';
				}
				else {
					echo '<div class="error"><p><strong>No s\'ha trobat el bloc que heu especificat. Siusplau assegureu-vos que l\'ID que heu introduït és correcte.</strong></p></div>';
					$show_form = true;
				}				
			}			
			else {
				echo '<div class="error"><p><strong>Has d\'inserir l\'ID del bloc.</strong></p></div>';
				$show_form = true;
			}
		}
		
		if (isset($_POST['convert_tables_confirm'])) {
			$blog_id = $_POST['blog_id'];
			$blog_url = get_blog_option($blog_id,'siteurl');
			$show_form = false;
			
			global $wpdb;

			$sql_tables = 'SHOW TABLES LIKE "' .$wpdb->base_prefix . $blog_id . '_%"';
			$tables = $wpdb->get_results($sql_tables,ARRAY_N);

			echo '<p><strong>Resultats de la conversió de la codificació de caràcters de les taules del bloc amb ID = ' . $blog_id . ' i url = <a href=' . $blog_url . '>' . $blog_url . '</a>.</strong></p>';

			echo "<div class='updated fade'>";
				foreach ($tables as $table) {
					$sql_alter_table = 	"ALTER TABLE `" . $table[0] . "` CONVERT TO CHARACTER SET utf8";
					
					if ($wpdb->query($sql_alter_table) !== FALSE) {
						echo '<p>La codificació de caràcters de la taula <strong>' . $table[0] . '</strong> s\'ha convertit <strong>correctament</strong> a utf8.</p>';
					}
					else {
						echo '<p>Hi ha hagut algun <strong>error</strong> en convertir la codificació de caràcters de la taula <strong>' . $table[0] . '</strong>.</p><br>';
					}									
				}
			echo "</div>";		
		}
		
		if (isset($_POST['convert_tables_abort'])) {}
		
		if ($show_form){
			echo '<form action="?page=iso2utf8" method="post">';
				echo '<h3>Conversió de la codificació de caràcters de les taules d\'un bloc</h3>';
				echo '<p>Aquesta funcionalitat permet convertir la codificació de caràcters de les taules d\'un bloc a utf8.</p>';
		
				echo '<table class="form-table">';
					echo '<tbody>';
						echo '<tr valign="top">';
							echo '<th scope="row">ID del bloc:</th>';
							echo '<td>';
								echo '<input id="blog_id" type="text" size="45" style="width: 95%;" name="blog_id"/>';
								echo '<br/>';
								echo 'L\'ID del bloc en que vulguis dur a terme la conversió de la codificació de caràcters en les taules de la base de dades.';
							echo '</td>';
						echo '</tr>';
					echo '</tbody>';
				echo '</table>';
				
				echo '<p class="submit">';
					echo '<input type="submit" value="Converteix la codificació de caràcters de les taules »" name="convert_tables"/>';
				echo '</p>';
			echo '</form>';
		}
	echo '</div>';
}