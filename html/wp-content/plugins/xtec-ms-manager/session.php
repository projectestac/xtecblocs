<?php

add_action( 'init', 'myStartSession', 1 );
add_action( 'wp_logout', 'myEndSession' );
add_action( 'wp_login', 'myEndSession' );

function myStartSession() {
	if ( ! session_id() ) {
		session_start();
	}
}

function myEndSession() {
	session_destroy();
}
