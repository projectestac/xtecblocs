#!/bin/bash

function gitcheckout {
    dir=$1
    branch=$2
    remote=$3

    if [ ! -d "$dir" ]; then
        update_exec "git submodule update --init $dir"

        if [ ! -d "$dir" ]; then
            echo 'ERROR: el directori $dir no existeix'
            exit -1
        fi
    fi

    echo "Entrant $dir BRANCH $branch REPO $remote ..."
    pushd $dir > /dev/null
    if [ ! -z "$remote" ]; then
        update_exec "git remote set-url origin $remote"
        update_exec "git fetch"
    fi

    update_exec "git checkout $branch"

    git_pull $branch

    popd > /dev/null
    echo 'OK'
}

function git_pull {
    branch=$1
    if [[ $action == 'stash' ]]
    then
        update_exec "git stash"
    fi

    if [[ $action == 'reset' ]]
    then
        update_exec "git reset --hard origin/$branch"
    fi

    update_exec "git pull"

    if [[ $action == 'stash' ]]
    then
        if [[ -n $(git stash list) ]]; then
            echo ' >>>> Aplicat Stash'
            update_exec "git stash pop"
        fi
    fi
}

function update_exec {
    if ! $1 > /dev/null
    then
        echo >&2 "ERROR: on $1"
        exit -2
    fi
}

############## SCRIPT START
tempaction=$1
if [[ $tempaction == 'reset' ]]
then
    echo 'Demanat RESET'
    action=$tempaction
elif [[ $tempaction == 'stash' ]]
then
    echo 'Demanat STASH'
    action=$tempaction
else
    action=""
fi

echo 'Pull inicial'
git_pull master

if [[ $action != 'reset' ]]
then
    echo 'Inicialitzant submòduls...'
    update_exec "git submodule update --recursive --init"

    echo 'Sincronitzant submòduls...'
    update_exec "git submodule sync"
fi

#El tercer paràmetre només es posa si el repositori és nostre per poder-hi escriure
gitcheckout "src/testlib" "master" "git@github.com:projectestac/testlib_PHP.git"
gitcheckout "src/wp-content/mu-plugins/common" "master" "git@github.com:projectestac/wordpress-mu-common.git"
gitcheckout "src/wp-content/plugins/blogger-importer" "master" "git@github.com:projectestac/wordpress-blogger-importer.git"
gitcheckout "src/wp-content/plugins/google-analyticator" "master" "git@github.com:projectestac/wordpress-google-analyticator.git"
gitcheckout "src/wp-content/plugins/google-calendar-events" "master" "git@github.com:projectestac/wordpress-gce.git"
gitcheckout "src/wp-content/plugins/raw-html" "master" "git@github.com:projectestac/wordpress-raw-html.git"
gitcheckout "src/wp-content/plugins/slideshow-jquery-image-gallery" "master" "git@github.com:projectestac/wordpress-slideshow-jig.git"
gitcheckout "src/wp-content/plugins/tinymce-advanced" "master" "git@github.com:projectestac/wordpress-tinymce-advanced.git"
gitcheckout "src/wp-content/plugins/wordpress-importer" "master" "git@github.com:projectestac/wordpress-importer.git"
gitcheckout "src/wp-content/plugins/wordpress-php-info" "master" "git@github.com:projectestac/wordpress-php-info.git"
gitcheckout "src/wp-content/plugins/wordpress-social-login" "master" "git@github.com:projectestac/wordpress-social-login.git"
gitcheckout "src/wp-content/plugins/xtec-mail/lib" "master" "git@github.com:projectestac/mailer.git"
gitcheckout "src/wp-includes/xtec" "master" "git@github.com:projectestac/wordpress-xtec.git"

echo "Garbage collecting..."
git gc

echo "That's all folks!"
