<?php
/*
Plugin Name: WP-reCAPTCHA
Plugin URI: https://github.com/blaenk/wp-recaptcha
Description: Integrates reCAPTCHA anti-spam solutions with wordpress
Version: 3.1.6
Author: Jorge Peña
Email: support@recaptcha.net
Author URI: https://github.com/blaenk
*/

// this is the 'driver' file that instantiates the objects and registers every hook

define('ALLOW_INCLUDE', true);

require_once('recaptcha.php');

// XTEC ********** ELIMINAT -> Adapt pluggin to Xtecblocs Wordpress
// 2012.04.04 @mmartinez
//require_once('mailhide.php');
//********** FI

$recaptcha = new reCAPTCHA('recaptcha_options');
// XTEC ********** ELIMINAT -> Adapt pluggin to Xtecblocs Wordpress
// 2012.04.04 @mmartinez
//$mailhide = new MailHide('mailhide_options');
// *********** FI

?>
