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

add_filter('hidden_meta_boxes', 'blocs_hidden_meta_boxes');
