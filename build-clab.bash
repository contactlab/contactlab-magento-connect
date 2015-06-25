#!/usr/bin/env bash

# DEPRECATED
cd $(dirname $0)

P=./app/etc/modules/Contactlab*
tar --exclude='.svn' -cvzf /tmp/contactlab-magento-step2.tgz $P

P="./lib/contactlab ./app/code/community/Contactlab ./app/locale/en_US/contactlab ./app/locale/en_US/template/email/contactlab_commons ./app/locale/it_IT/contactlab ./app/design/adminhtml/default/default/layout/contactlab ./app/design/adminhtml/default/default/template/contactlab ./js/contactlab_commons ./skin/adminhtml/default/default/images/contactlab"

tar --exclude='.svn' -cvzf /tmp/contactlab-magento-step1.tgz $P

exit
