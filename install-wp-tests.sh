#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host]"
	exit 1
fi


DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress/}

# Install Wordrpess Core
bash bin/install-wp-tests.sh $DB_NAME $DB_USER "" $DB_HOST $WP_VERSION


setup_wp_core(){
	cd $WP_CORE_DIR;

	if [[ $(uname -s) == 'Darwin' ]]; then
		local ioption='-i .bak'
	else
		local ioption='-i'
	fi

	if [ ! -f wp-config.php ]; then
		mv wp-config-sample.php wp-config.php
	fi

	sed $ioption "s/database_name_here/$DB_NAME/" "$WP_CORE_DIR"/wp-config.php
	sed $ioption "s/username_here/$DB_USER/" "$WP_CORE_DIR"/wp-config.php
	sed $ioption "s/password_here/$DB_PASS/" "$WP_CORE_DIR"/wp-config.php
	sed $ioption "s/wp_/wptests_/" "$WP_CORE_DIR"/wp-config.php
	sed $ioption "s|localhost|${DB_HOST}|" "$WP_CORE_DIR"/wp-config.php
}

setup_wp_core

php  install-wp-wc.php
