#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

BASE_PATH="`pwd`/`dirname $0`"
LIBS_PATH="${BASE_PATH}/../../libs/"
PUB_PATH="${BASE_PATH}/../../public_html/"
MODS_PATH="${PUB_PATH}/modpub/"
WEBAPP_PATH="${BASE_PATH}/../../webapp/"

# Check for required commands
for CMD in wget tar unzip; do
   type $CMD &> /dev/null

   if [ $? != "0" ]; then
      echo "The command '$CMD' is required and is not in your path!"
      exit 1
   fi

done

# Checkout kohana

if [ -d $LIBS_PATH/kohana ]; then
    svn update ${LIBS_PATH}/kohana
else
    mkdir -p ${LIBS_PATH}/kohana
    svn co http://source.kohanaframework.org/svn/kohana2/tags/2.2.1/system ${LIBS_PATH}/kohana
fi

# Checkout kohana's payment module
if [ -d $LIBS_PATH/kohana_payment ]; then
    svn update ${LIBS_PATH}/kohana_payment
else
    mkdir -p ${LIBS_PATH}/kohana_payment
    svn co http://source.kohanaphp.com/svn/kohana2/tags/2.2.1/modules/payment ${LIBS_PATH}/kohana_payment
fi

# Download smarty

if [ -d $LIBS_PATH/smarty ]; then
    rm -rf "${LIBS_PATH}/smarty"
fi

wget -c -P /tmp http://www.smarty.net/files/Smarty-2.6.26.tar.gz
tar xvfz /tmp/Smarty-2.6.26.tar.gz
mv ./Smarty-2.6.26/libs ${LIBS_PATH}/smarty

# Download pear
if [ -d $LIBS_PATH/pear ]; then
    rm -rf "${LIBS_PATH}/pear"
fi

wget -c -P /tmp http://pub.vardump.org/pear-1.6.2.tar.bz2
tar xvfj /tmp/pear-1.6.2.tar.bz2
mv ./pear $LIBS_PATH

# Download TinyMce

find $MODS_PATH/tinymce | grep -r tinymce.css | xargs rm -rf #Delete tinymce, but not the css file

wget -c --no-check-certificate -P /tmp https://www.github.com/downloads/tinymce/tinymce/tinymce_3_3_9_3.zip
unzip -u /tmp/tinymce_3_3_9_3.zip -d /tmp

rm -rf ${MODS_PATH}/tinymce
mkdir ${MODS_PATH}/tinymce
mv /tmp/tinymce/jscripts/tiny_mce/* ${MODS_PATH}/tinymce
cp -rf ${WEBAPP_PATH}/modules/tinymce/other/easyUpload/ ${MODS_PATH}/tinymce/plugins/

# Cleanup

rm -rf ./Smarty-2.6.26/
rm -rf /tmp/Smarty-2.6.26.tar.gz

rm -rf /tmp/pear.tar.bz2

rm -rf /tmp/tinymce_3_1_1.zip
rm -rf /tmp/tinymce
