#!/bin/bash

clear;

echo $PWD

## setup database
##/var/www/opencounter-slim-codenv/bin/phinx migrate -c /var/www/opencounter-slim-codenv/phinx.yml --environment testing
docker exec opencounter-slim-codenv-php-fpm /var/www/opencounter-slim-codenv/bin/phinx migrate -c /var/www/opencounter-slim-codenv/phinx.yml
docker exec opencounter-slim-codenv-php-fpm /var/www/opencounter-slim-codenv/bin/phinx seed:run -c /var/www/opencounter-slim-codenv/phinx.yml

# PHPUnit tests
docker exec opencounter-slim-codenv-php-fpm /var/www/opencounter-slim-codenv/bin/phpunit --configuration /var/www/opencounter-slim-codenv/tests/phpunit/phpunit.xml
PHPUNIT_RETURN_CODE=$?

# PHPSpec tests
docker exec opencounter-slim-codenv-php-fpm /var/www/opencounter-slim-codenv/bin/phpspec run --format=pretty --config /var/www/opencounter-slim-codenv/tests/phpspec/phpspec.yml -v
PHPSPEC_RETURN_CODE=$?

# Behat default suite tests (default)
docker exec opencounter-slim-codenv-php-fpm /var/www/opencounter-slim-codenv/bin/behat --config /var/www/opencounter-slim-codenv/behat.yml;
BEHAT_DEFAULT_RETURN_CODE=$?



## Behat service-level tests (rest)
#./vendor/bin/behat -c tests/behat/behat.yml --suite servicelevel;
#BEHAT_SERVICELEVEL_RETURN_CODE=$?
#
## Start webserver, run Behat tests, stop webserver
#pushd src-dev/sample-application/public/;
#php -S localhost:8081 &> /dev/null &
#WEBSERVER_PROCESS_ID=$!;
#popd;
#./vendor/bin/behat --config tests/behat/behat.yml --suite webserver;
#BEHAT_WEBSERVERL_RETURN_CODE=$?
#kill -9 $WEBSERVER_PROCESS_ID;

# Print results so you don't have to scroll
echo;
echo -n 'PHPUnit return code:';
echo $PHPUNIT_RETURN_CODE;

echo -n 'PHPSpec return code:';
echo $PHPSPEC_RETURN_CODE;
#
#echo -n 'Behat serivce-level return code: ';
#echo $BEHAT_SERVICELEVEL_RETURN_CODE;
#
#echo -n 'Behat webserver return code:     ';
#echo $BEHAT_WEBSERVERL_RETURN_CODE;
#echo;

echo -n 'Behat Default return code: ';
echo $BEHAT_DEFAULT_RETURN_CODE;


# Work out an exit code, and exit
OVERALL_EXIT_CODE=$((PHPUNIT_RETURN_CODE + PHPSPEC_RETURN_CODE + BEHAT_DEFAULT_RETURN_CODE))
exit $OVERALL_EXIT_CODE;
