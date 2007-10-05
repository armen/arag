#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

BASE_PATH="`pwd`/`dirname $0`"
LIBS_PATH="${BASE_PATH}/../../libs/"

# Check for required commands
for CMD in wget tar unzip; do
   type $CMD &> /dev/null

   if [ $? != "0" ]; then
      echo "The command '$CMD' is required and is not in your path!"
      exit 1
   fi

done

# Download smarty

if [ -d $LIBS_PATH/smarty ]; then
    rm -rf "${LIBS_PATH}/smarty"
fi

wget -c -P /tmp http://smarty.php.net/do_download.php?download_file=Smarty-2.6.18.tar.gz
tar xvfz /tmp/Smarty-2.6.18.tar.gz
mv ./Smarty-2.6.18/libs ${LIBS_PATH}/smarty

rm -rf ./Smarty-2.6.18/
rm -rf /tmp/Smarty-2.6.18.tar.gz

# Download ci

if [ -d $LIBS_PATH/ci ]; then
    rm -rf "${LIBS_PATH}/ci"
fi

wget -c -P /tmp http://codeigniter.com/downloads/CodeIgniter_1.5.4.zip
unzip /tmp/CodeIgniter_1.5.4.zip

mv ./CodeIgniter_1.5.4/system ${LIBS_PATH}/ci

rm -rf ./CodeIgniter_1.5.4
rm -rf /tmp/CodeIgniter_1.5.4.zip
