#!/bin/bash

#Exemple invocació: ./prepare_package.sh

function copy_to_new {
    filename=$1
    
    delete_new $filename
	cp wp_old/$filename src/$filename
}

function copy_to_new_dir {
    filename=$1
    
	cp -Rp wp_old/$filename src/$filename
}

function delete_new {
    filename=$1
    
	rm -Rf src/$filename
}

version=15.07.21

git clone https://github.com/projectestac/agora_nodes.git wp_new
pushd wp_new

git submodule update --recursive --init
echo 'Submòduls actualitzats'
find . -name '\.git*' -exec rm -rf {} \;
popd


mv src wp_old
mv wp_new src

#Copy files
copy_to_new .htaccess
copy_to_new CHANGES.txt
copy_to_new config-restricted-dist.php
copy_to_new db-config.php
copy_to_new opcache.php #apc.php
copy_to_new works.php
copy_to_new wp-config-dist.php
cp wp_old/wp-config-dist.php src/wp-config.php
copy_to_new wp-content/advanced-cache.php
copy_to_new wp-content/db.php
copy_to_new wp-content/mu-plugins/xtecblocs-functions.php
#copy_to_new wp-settings.php
#copy_to_new wp-signup.php
copy_to_new xtec-style.css

#Copy directories
copy_to_new_dir wp-content/advanced-cache.php
copy_to_new_dir wp-content/blogs.dir
copy_to_new_dir wp-content/cache
copy_to_new_dir wp-content/db.php
copy_to_new_dir wp-content/wp-cache-config.php
copy_to_new_dir wp-content/jw-flv-player
copy_to_new_dir wp-content/plugins/addthis
copy_to_new_dir wp-content/plugins/anti-spam
copy_to_new_dir wp-content/plugins/hyperdb
copy_to_new_dir wp-content/plugins/link-manager
copy_to_new_dir wp-content/plugins/multisite-clone-duplicator
copy_to_new_dir wp-content/plugins/multisite-plugin-manager
copy_to_new_dir wp-content/plugins/scribd-doc-embedder
copy_to_new_dir wp-content/plugins/simple-local-avatars
copy_to_new_dir wp-content/plugins/simpler-ipaper
copy_to_new_dir wp-content/plugins/vipers-video-quicktags
copy_to_new_dir wp-content/plugins/wp-super-cache
copy_to_new_dir wp-content/plugins/xtec-api
copy_to_new_dir wp-content/plugins/xtec-descriptors
copy_to_new_dir wp-content/plugins/xtec-favorites
copy_to_new_dir wp-content/plugins/xtec-lastest-posts
copy_to_new_dir wp-content/plugins/xtec-ldap-login
copy_to_new_dir wp-content/plugins/xtec-link-player
copy_to_new_dir wp-content/plugins/xtec-maintenance
copy_to_new_dir wp-content/plugins/xtec-settings
copy_to_new_dir wp-content/plugins/xtec-signup
copy_to_new_dir wp-content/plugins/xtec-users
copy_to_new_dir wp-content/plugins/xtec-weekblog2
copy_to_new_dir wp-content/themes/classic-chalkboard
copy_to_new_dir wp-content/themes/delicacy
copy_to_new_dir wp-content/themes/freshy2
copy_to_new_dir wp-content/themes/mystique
copy_to_new_dir wp-content/themes/reddle
copy_to_new_dir wp-content/themes/twentyeleven
copy_to_new_dir wp-content/themes/twentyfourteen
copy_to_new_dir wp-content/themes/twentyten
copy_to_new_dir wp-content/themes/twentythirteen
copy_to_new_dir wp-content/themes/twentytwelve
copy_to_new_dir wp-content/themes/xtec898encurs
copy_to_new_dir wp-content/themes/xtecblocsdefault
copy_to_new_dir wp-content/themes/xtecblocsdefault-formacio
copy_to_new_dir wp-content/themes/xtec-v1.1
copy_to_new_dir ws

#Delete files
delete_new .gitignore
delete_new .gitmodules
delete_new site-config.php
delete_new wp-content/mu-plugins/agora-functions.php
delete_new wp-content/mu-plugins/languages/agora-functions.pot
delete_new wp-content/mu-plugins/languages/agora-functions-ca.mo
delete_new wp-content/mu-plugins/languages/agora-functions-ca.po

#Delete directories
delete_new wp-content/plugins/add-to-any
delete_new wp-content/plugins/bbpress
delete_new wp-content/plugins/bbpress-enable-tinymce-visual-tab
delete_new wp-content/plugins/bp-moderation
delete_new wp-content/plugins/buddypress
delete_new wp-content/plugins/buddypress-activity-plus
delete_new wp-content/plugins/buddypress-docs
delete_new wp-content/plugins/buddypress-group-email-subscription
delete_new wp-content/plugins/buddypress-like
delete_new wp-content/plugins/enllacos-educatius
delete_new wp-content/plugins/grup-classe
delete_new wp-content/plugins/import-users-from-csv-with-meta
delete_new wp-content/plugins/intranet-importer
delete_new wp-content/plugins/invite-anyone
delete_new wp-content/plugins/pending-submission-notifications
delete_new wp-content/plugins/private-bp-pages
delete_new wp-content/plugins/socialmedia
delete_new wp-content/plugins/widget-visibility-without-jetpack
delete_new wp-content/plugins/xtec-stats
delete_new wp-content/plugins/AFEGIT_XTEC
delete_new wp-content/plugins/ELIMINAT_XTEC
delete_new wp-content/themes/reactor
delete_new wp-content/themes/reactor-primaria-1
delete_new wp-content/uploads

rm -Rf wp_old

echo "ATTENTION: For major upgradings it's necessary to check manually if there are new themes and new versions of the specific plugins"
