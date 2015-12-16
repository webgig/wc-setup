#!/usr/bin/env bash

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
