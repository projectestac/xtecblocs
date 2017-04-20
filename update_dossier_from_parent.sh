#!/bin/bash

#Exemple invocació: ./update_dossier_from_parent.sh

function copy_to_new {
    filename=$1

    delete_new $filename
	cp dossier_old/$filename dossier/$filename
}

function copy_to_new_dir {
    filename=$1

	cp -Rp dossier_old/$filename dossier/$filename
}

function delete_new {
    filename=$1

	rm -Rf dossier/$filename
}

version=16.11.15

pushd src
git clone https://github.com/projectestac/xtecblocs.git dossier_new
pushd dossier_new

git submodule update --recursive --init
echo 'Submòduls actualitzats'
find . -name '\.git*' -exec rm -rf {} \;
popd

mv dossier dossier_old
mv dossier_new/src dossier


#Copy files
copy_to_new .git
copy_to_new .gitignore
copy_to_new .gitmodules
copy_to_new .htaccess
copy_to_new .htaccess-dist
copy_to_new CHANGES.txt
copy_to_new wp-config-dist.php
copy_to_new wp-config-single.php
cp dossier_old/wp-config.php dossier/wp-config.php
cp dossier_old/wp-content/wp-cache-config.php dossier/wp-content/wp-cache-config.php
#copy_to_new wp-content/mu-plugins/xtecblocs-functions.php

#Copy directories
copy_to_new_dir wp-content/blogs.dir
copy_to_new_dir wp-content/cache
copy_to_new_dir wp-content/themes/travelify
#copy_to_new_dir wp-content/plugins/link-manager
#copy_to_new_dir wp-content/plugins/simple-local-avatars
#copy_to_new_dir wp-content/plugins/xtec-api
#copy_to_new_dir wp-content/plugins/xtec-descriptors
#copy_to_new_dir wp-content/plugins/xtec-favorites
#copy_to_new_dir wp-content/plugins/xtec-link-player
#copy_to_new_dir wp-content/plugins/xtec-maintenance
#copy_to_new_dir wp-content/plugins/xtec-settings
#copy_to_new_dir wp-content/plugins/xtec-signup
#copy_to_new_dir wp-content/plugins/xtec-users
#copy_to_new_dir wp-content/plugins/xtec-widget-data-users

#Delete files
delete_new AFEGIT_XTEC
delete_new config-restricted-dist.php
delete_new login_moodle.php
delete_new opcache.php
delete_new siteoff.html
delete_new testapp.php
delete_new works.php
delete_new wp-config-sample.php
delete_new xtec-style.css

#Delete directories
delete_new testlib
delete_new wp-content/jw-flv-player
delete_new wp-content/plugins/google-calendar-events
delete_new wp-content/plugins/scribd-doc-embedder
delete_new wp-content/plugins/simpler-ipaper
delete_new wp-content/plugins/slideshare
delete_new wp-content/plugins/vipers-video-quicktags
delete_new wp-content/plugins/wordpress-social-login
delete_new wp-content/plugins/xtec-api
delete_new wp-content/plugins/xtec-lastest-posts
delete_new wp-content/plugins/xtec-weekblog2
delete_new wp-content/themes/classic-chalkboard
delete_new wp-content/themes/delicacy
delete_new wp-content/themes/freshy2
delete_new wp-content/themes/mystique
delete_new wp-content/themes/reddle
delete_new wp-content/themes/twentyeleven
delete_new wp-content/themes/twentyfifteen
delete_new wp-content/themes/twentyfourteen
delete_new wp-content/themes/twentysixteen
delete_new wp-content/themes/twentyten
delete_new wp-content/themes/twentythirteen
delete_new wp-content/themes/twentytwelve
delete_new wp-content/themes/xtec898encurs
delete_new wp-content/themes/xtecblocsdefault
delete_new wp-content/themes/xtecblocsdefault-formacio
delete_new wp-content/themes/xtec-v1.1
delete_new ws

rm -Rf dossier_new
rm -Rf dossier_old

echo "ATTENTION: For major upgradings it's necessary to check manually if there are new themes and new versions of the specific plugins"
