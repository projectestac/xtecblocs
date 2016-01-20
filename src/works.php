<?php
//XTEC ************ FITXER AFEGIT - Fitxer per la monitorització
//2011.05.06 @fbassas

require_once( dirname(__FILE__) . '/wp-config.php' );
require_once( ABSPATH . WPINC . '/registration.php' );

$$msg = '';
// Check database connection
$isok = username_exists('admin');
if (!$isok) {
	$msg .= 'ERROR: No es pot connectar a la base de dades<br/><br/>';
}

// Check connection to data files
$wpcontentdir = dirname(__FILE__).'/wp-content';
$blogsdir = $wpcontentdir.'/blogs.dir';
$isok = is_writable($blogsdir);
if ($isok !== TRUE) {
	$msg .= 'ERROR: No es pot accedir al sistema de fitxers. Reviseu el punt de muntatge '.$blogsdir;
	$msg .= exec_command("ls -la $wpcontentdir");
	$msg .= exec_command("ls -la $blogsdir");
	$msg .= exec_command("df -h $wpcontentdir");
}

if (empty($msg)) {
	$msg = 'OK';
}

echo $msg;


function exec_command($command){
	$msg = "<br/><br/>$ $command<br/>";
	$msgtmp = array();
	exec($command, $msgtmp);
	$msg .= implode ($msgtmp, '<br/>');
	return $msg;
}