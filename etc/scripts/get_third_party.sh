#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

BASE_PATH="`pwd`/`dirname $0`"
LIBS_PATH="${BASE_PATH}/../../libs/"
PUB_PATH="${BASE_PATH}/../../public_html/"
MODS_PATH="${PUB_PATH}/modpub/"

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
    svn co http://svn.kohanaphp.com/trunk/system ${LIBS_PATH}/kohana
fi

# Download smarty

if [ -d $LIBS_PATH/smarty ]; then
    rm -rf "${LIBS_PATH}/smarty"
fi

wget -c -P /tmp http://www.smarty.net/do_download.php?download_file=Smarty-2.6.19.tar.gz
tar xvfz /tmp/Smarty-2.6.19.tar.gz
mv ./Smarty-2.6.19/libs ${LIBS_PATH}/smarty

# Download pear
if [ -d $LIBS_PATH/pear ]; then
    rm -rf "${LIBS_PATH}/pear"
fi

wget -c -P /tmp http://pub.vardump.org/pear-1.6.2.tar.bz2
tar xvfj /tmp/pear-1.6.2.tar.bz2
mv ./pear $LIBS_PATH

# Download TinyMce

if [ -d $MODS_PATH/tinymce ]; then
    rm -rf "$MODS_PATH/tinymce"
fi

wget -c -P /tmp http://prdownloads.sourceforge.net/tinymce/tinymce_2_1_3.tgz?download
tar xvfz /tmp/tinymce_2_1_3.tgz

mkdir ${MODS_PATH}/tinymce
mv ./tinymce ${MODS_PATH}/tinymce

# Cleanup

rm -rf ./Smarty-2.6.19/
rm -rf /tmp/Smarty-2.6.19.tar.gz

rm -rf /tmp/pear.tar.bz2

rm -rf /tmp/FCKeditor_2.4.3.tar.gz
