#!/usr/bin/env bash

DIR=$(dirname $0)

if [ -f /usr/local/netbeans-8.0/php/phpunit/NetBeansSuite.php ]
then
    phpunit --colors \
        --log-junit /tmp/nb-phpunit-log.xml \
        --bootstrap $DIR/bootstrap.php \
        --configuration $DIR/phpunit.xml \
        /usr/local/netbeans-8.0/php/phpunit/NetBeansSuite.php \
        "--run=$DIR/unit"
else
    phpunit --colors \
        --bootstrap $DIR/bootstrap.php \
        --configuration $DIR/phpunit.xml \
        $DIR/unit
fi
