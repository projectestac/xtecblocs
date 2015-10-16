<?php

require_once('wp-config.php');
require_once('testlib/testlib.php');

// Get DB_NAME
if (MULTISITE === true) $dbname = DB_NAME;
else $dbname = DB_NAME.'_global';

// Get DB_HOST and DB_PORT
if (strrpos(DB_HOST, ':') > 0) {
	$dbhost = substr(DB_HOST, 0, strrpos(DB_HOST, ':'));
	$dbport = substr(DB_HOST, strrpos(DB_HOST, ':')+1);
} else {
	$dbhost = DB_HOST;
	$dbport = 3306;
}
checkMySQL ($dbhost, $dbport, $dbname, DB_USER, DB_PASSWORD, 'wp_users');

test_mail('XTECBLOCS',  false, ENVIRONMENT, get_site_option('xtec_mail_logpath'));

test_ldap(false, ENVIRONMENT);

test_proxy('http://www.google.com');

test_server('UTC');

