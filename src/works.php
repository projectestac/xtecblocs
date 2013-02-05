<?php

//XTEC ************ FITXER AFEGIT - Fitxer per la monitorització
//2011.05.06 @fbassas

require_once( dirname(__FILE__) . '/wp-config.php' );
require_once( ABSPATH . WPINC . '/registration.php' );
$user_id = username_exists('admin');
if ( $user_id )
	print 'OK';

//************ FI