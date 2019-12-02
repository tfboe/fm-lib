#!/bin/bash
set  -e

#set environment default values
CODE_COVERAGE="${CODE_COVERAGE:-0}"
# by default no mysql root password
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-}"
INTEGRATION="${INTEGRATION:-0}"
GITHUB_OAUTH="${GITHUB_OAUTH:-}"
LIB_NAME="tfboe/fm-lib:@dev"

if [ "$GH_TOKEN" != "" ]; then
    echo "using github OAUTH"
    composer config --global -g github-oauth.github.com ${GH_TOKEN}
fi

composer validate --no-check-all --strict

composer global require hirak/prestissimo
composer update $PREFER_LOWEST

if [ "$INTEGRATION" = '1' ]; then
    # modify php.ini for catchmail
    echo "sendmail_path = \/usr\/bin\/env catchmail -f catchmail@php.com/g"  >> \
        ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    # start mailcatcher
    mailcatcher
    # create test database
    if [ "$MYSQL_ROOT_PASSWORD" = "" ]; then
        # don't use a password
        mysql -h localhost -u root -e "CREATE DATABASE \`elo-system-test\`"
    else
        mysql -h localhost -u root -p${MYSQL_ROOT_PASSWORD} -e "CREATE DATABASE \`elo-system-test\`"
    fi
    # setup complete integration testing environment
    directory=${PWD##*/}
    cd ..
    rm -rf fm-lib-test
    composer create-project laravel/lumen:^6.0 fm-lib-test

    cd fm-lib-test/
    cp -r ../${directory}/tests/Helpers/ tests
    cp -r ../${directory}/tests/Integration/ tests
    cp -r ../${directory}/tests/Entity/ tests
    cp -r ../${directory}/config-example config
    cp -r ../${directory}/database .
    cp ../${directory}/phpunit-integration.xml .
    cp ../${directory}/.env.test .env
    composer config repositories.fm-lib path ../${directory}
    composer config repositories.jwt-auth-guard vcs https://github.com/blacktemplar/jwt-auth-guard
    composer require ${LIB_NAME} $PREFER_LOWEST
    composer require phpunit/phpcov:^6.0
    sed -i -e 's/\/\/ $app->withFacades();/$app->withFacades();/g' bootstrap/app.php
    sed -i -e 's/\/\/ $app->register(App\\Providers\\AppServiceProvider::class);'\
'/$app->register(Tfboe\\FmLib\\Providers\\FmLibServiceProvider::class);/g' bootstrap/app.php

    # generate doctrine tables and proxies
    php artisan doctrine:schema:create
    php artisan doctrine:generate:proxies
fi