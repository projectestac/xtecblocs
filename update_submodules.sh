#!/bin/bash

source "update_functions.sh"

#El tercer paràmetre només es posa si el repositori és nostre per poder-hi escriure
gitcheckout "src/testlib" "master" "git@github.com:projectestac/testlib_PHP.git"
gitcheckout "src/wp-content/mu-plugins/common" "master" "git@github.com:projectestac/wordpress-mu-common.git"
gitcheckout "src/wp-content/plugins/add-to-any" "master" "git@github.com:projectestac/wordpress-add-to-any.git"
gitcheckout "src/wp-content/plugins/anti-spam" "master" "git@github.com:projectestac/wordpress-anti-spam.git"
gitcheckout "src/wp-content/plugins/blogger-importer" "master" "git@github.com:projectestac/wordpress-blogger-importer.git"
gitcheckout "src/wp-content/plugins/google-analyticator" "master" "git@github.com:projectestac/wordpress-google-analyticator.git"
gitcheckout "src/wp-content/plugins/google-calendar-events" "master" "git@github.com:projectestac/wordpress-gce.git"
gitcheckout "src/wp-content/plugins/raw-html" "master" "git@github.com:projectestac/wordpress-raw-html.git"
gitcheckout "src/wp-content/plugins/slideshow-jquery-image-gallery" "master" "git@github.com:projectestac/wordpress-slideshow-jig.git"
gitcheckout "src/wp-content/plugins/tinymce-advanced" "master" "git@github.com:projectestac/wordpress-tinymce-advanced.git"
gitcheckout "src/wp-content/plugins/wordpress-importer" "master" "git@github.com:projectestac/wordpress-importer.git"
gitcheckout "src/wp-content/plugins/wordpress-php-info" "master" "git@github.com:projectestac/wordpress-php-info.git"
gitcheckout "src/wp-content/plugins/wordpress-social-login" "master" "git@github.com:projectestac/wordpress-social-login.git"
gitcheckout "src/wp-content/plugins/wp-recaptcha" "master" "git@github.com:projectestac/wordpress-recaptcha.git"
gitcheckout "src/wp-content/plugins/wp-super-cache" "master" "git@github.com:projectestac/wordpress-super-cache.git"
gitcheckout "src/wp-content/plugins/xtec-ldap-login" "master" "git@github.com:projectestac/wordpress-xtec-ldap-login.git"
gitcheckout "src/wp-content/plugins/xtec-mail/lib" "master" "git@github.com:projectestac/mailer.git"
gitcheckout "src/wp-content/themes/fukasawa" "master" "git@github.com:projectestac/wordpress-theme-fukasawa.git"
gitcheckout "src/wp-content/themes/reddle" "master" "git@github.com:projectestac/wordpress-theme-reddle.git"
gitcheckout "src/wp-content/themes/twentyeleven" "master" "git@github.com:projectestac/wordpress-theme-twentyeleven.git"
gitcheckout "src/wp-content/themes/twentyten" "master" "git@github.com:projectestac/wordpress-theme-twentyten.git"
gitcheckout "src/wp-content/themes/twentytwelve" "master" "git@github.com:projectestac/wordpress-theme-twentytwelve.git"
gitcheckout "src/wp-includes/xtec" "master" "git@github.com:projectestac/wordpress-xtec.git"

