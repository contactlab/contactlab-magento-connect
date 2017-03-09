#!/bin/bash

CURR=$(cd $(dirname $0) && pwd -P)

cd $(dirname $0)/../../../../../../..

tar cvzf /tmp/contactlab-subscribers.tgz -T $CURR/files.txt

exit
