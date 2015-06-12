<?php
/*
Plugin Name: WP-reCAPTCHA
Description: Integrates reCAPTCHA anti-spam solutions with wordpress
Version: 4.1
Email: support@recaptcha.net
*/

// this is the 'driver' file that instantiates the objects and registers every hook

define('ALLOW_INCLUDE', true);

require_once('recaptcha.php');

// XTEC ************ MODIFICAT - All the sites are going to use the same site and secret keys for recaptcha.
// 2015.04.14 @vsaavedr
//switch_to_blog('1');
$recaptcha = new ReCAPTCHAPlugin('recaptcha_options');
//restore_current_blog();
//************ ORIGINAL
/*
$recaptcha = new ReCAPTCHAPlugin('recaptcha_options');
*/
//************ FI
?>
