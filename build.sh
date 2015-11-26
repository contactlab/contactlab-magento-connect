#!/usr/bin/env bash

me=`basename "$0"`

if [ $# -ne 2 ]
  then
      echo "Usage: ${me} <repository-input-path> <tar-output-path>" 
      exit 1
fi

p_in=${1%/}
p_out=${2%/}

conf="${p_in}/app/etc/modules/Contactlab*"
plugin="${p_in}/lib/contactlab \
        ${p_in}/app/code/community/Contactlab \
        ${p_in}/app/design/frontend/base/default/layout/contactlab \
        ${p_in}/app/design/frontend/base/default/template/contactlab \
        ${p_in}/app/locale/en_US/contactlab \
        ${p_in}/app/locale/en_US/template/email/contactlab_commons \
        ${p_in}/app/locale/en_US/template/email/contactlab_subscribers \
        ${p_in}/app/locale/it_IT/contactlab \
        ${p_in}/app/design/adminhtml/default/default/layout/contactlab \
        ${p_in}/app/design/adminhtml/default/default/template/contactlab \
        ${p_in}/js/contactlab_commons ${p_in}/skin/adminhtml/default/default/images/contactlab"

tar -cvzf ${p_out}/contactlab-magento-step1-plugin.tgz $plugin
tar -cvzf ${p_out}/contactlab-magento-step2-conf.tgz $conf

exit
