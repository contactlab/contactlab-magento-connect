#!/usr/bin/env bash

cd $(dirname $0)

P=./app/etc/modules/Contactlab*
tar --exclude='.svn' -cvzf /tmp/contactlab-magento-step2.tgz $P

P=""
for T in $(find -name .svn | grep -v tests)
do
    P="$P $(dirname $T)"
done

tar --exclude='.svn' -cvzf /tmp/contactlab-magento-step1.tgz $P

exit
