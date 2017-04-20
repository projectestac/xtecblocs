<?php
/*
Plugin Name: XTECBlocsFunctions
Plugin URI: https://github.com/projectestac/xtecblocs
Description: A pluggin to include specific functions which affects only to XTECBlocs
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

/**
 * Hide screen option's items. Best for usability
 * @author Sara Arjona
 */
function blocs_hidden_meta_boxes($hidden) {
	$hidden[] = 'postimagediv';
	return $hidden;
}

/**
 * Add the 'State' column at the end of the table, to manage the invitations.
 * @param  array $columns The columns of the table.
 * @return array $columns The same array with the column 'Estat' added.
 * @author vsaavedra
 */
function manage_users_columns( $columns ) {
	$columns['user_status'] = 'Estat';
	return $columns;
}
add_filter('manage_users_columns', 'manage_users_columns');


/**
 * Loads XTEC custom CSS
 * @author jmiro227 (2014.11.06)
 */
function register_xtec_common_styles() {
	wp_register_style( 'xtec_common_styles', get_site_url(1).'/xtec-style.css' );
	wp_enqueue_style( 'xtec_common_styles' );
}
add_action( 'wp_enqueue_scripts', 'register_xtec_common_styles' );


/**
* Replace "es.scribd.com" per "www.scribd.com" cause es.scribd.com doesn't work as a oEmbed provider
* I try to add as a oEmbed provider via wp_oembed_add_provider but doesn't work
*
* @author Xavi Meler
*/
function fix_spanish_scribd_oembed ($filtered_data, $raw_data){
	$filtered_data['post_content'] = str_replace('es.scribd.com', 'www.scribd.com', $filtered_data['post_content']);
	return $filtered_data;
}
add_filter('wp_insert_post_data', 'fix_spanish_scribd_oembed', 10, 2);

/**
* To avoid problems with BloggerImporter pluggin when the blog contains embed objects (like Picasa albums)
*
* @author sarjona
*/
remove_filter('force_filtered_html_on_import', '__return_true');

/**
* Change url redirection button add bloc into dashboard to "http://blocs.xtec.cat/wp-signup.php"
*
* @author xavinieto (2016-08-03)
*/
function xtec_change_redirect_button_dashboard( $location ) {
	if ( $location == 'https://agora/blocs/wp-signup.php' ){
		$location = 'https://blocs.xtec.cat/wp-signup.php';
	}

	return $location;
}
add_filter( 'wp_signup_location', 'xtec_change_redirect_button_dashboard' );
