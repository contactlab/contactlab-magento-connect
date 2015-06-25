#!/bin/bash

CURR=$(cd $(dirname $0) && pwd -P)

cd $(dirname $0)/../../../../../../..

tar cvzf /tmp/contactlab-commons.tgz -T $CURR/files.txt

exit
