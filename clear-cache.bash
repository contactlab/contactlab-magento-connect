#!/usr/bin/env bash


THEPATH=$(cd `dirname $0` && pwd -P)

rm -Rfv $THEPATH/var/logs/*
rm -Rfv $THEPATH/var/session/*
rm -Rfv $THEPATH/var/cache/*

sudo service apache2 reload

exit
