<?php

/*
Plugin Name: XTEC Info
Plugin URI:
Description: Displays site info.
Version: 1.1
Author: Francesc Bassas i Bullich
Author URI:
*/

add_action('network_admin_menu', 'xtec_info_menu');

/**
 * Adds plugin menu.
 */
function xtec_info_menu()
{
	add_menu_page('Informació','Informació','manage_network','xtec-info','xtec_info_page');
}

/**
 * Displays info of the site: number of blogs, number of users and, in Unix systems, the name of the host where is running.
 */
function xtec_info_page()
{
	?>
	<div class="wrap">
		<h2>Informació del lloc</h2>
		<?php $site_stats = get_sitestats();?>
		<p>Nombre total de blocs: <strong><?php echo $site_stats['blogs']?></strong></p>
		<p>Nombre total d'usuaris: <strong><?php echo $site_stats['users']?></strong></p>
		<hr>
		<p align="right">Esteu executant la instància d'XTECBlocs de <strong><?php system('uname -n') ?></strong>. </p>
	</div>
	<?php
}