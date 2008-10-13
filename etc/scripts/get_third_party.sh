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
    svn co http://source.kohanaphp.com/tags/2.2.1/system ${LIBS_PATH}/kohana
fi

# Checkout kohana's payment module
if [ -d $LIBS_PATH/kohana_payment ]; then
    svn update ${LIBS_PATH}/kohana_payment
else
    mkdir -p ${LIBS_PATH}/kohana_payment
    svn co http://source.kohanaphp.com/tags/2.2.1/modules/payment ${LIBS_PATH}/kohana_payment
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
find $MODS_PATH/tinymce | grep -r tinymce.css | xargs rm -rf #Delete tinymce, but not the css file

if [ -d $MODS_PATH/tinymce ]; then
    rm -rf "$MODS_PATH/tinymce"
fi

wget -c -P /tmp http://prdownloads.sourceforge.net/tinymce/tinymce_3_1_1.zip?download
unzip -u /tmp/tinymce_3_1_1.zip -d /tmp

mkdir ${MODS_PATH}/tinymce
mv /tmp/tinymce/jscripts/tiny_mce/* ${MODS_PATH}/tinymce
cp -rf ${WEBAPP_PATH}/modules/tinymce/other/easyUpload/ ${MODS_PATH}/tinymce/plugins/

# Download JalaliJSCalendar

if [ -d $PUB_PATH/scripts/JalaliJSCalendar ]; then
    rm -rf "${PUB_PATH}/scripts/JalaliJSCalendar"
fi

wget -c -P /tmp http://farhadi.ir/downloads/JalaliJSCalendar-1.1.5.tar.gz
wget -c -P /tmp http://pub.vardump.org/calendar.patch

mkdir JalaliJSCalendar-1.1.5
tar xvfz /tmp/JalaliJSCalendar-1.1.5.tar.gz -C ./JalaliJSCalendar-1.1.5

rm -r ./JalaliJSCalendar-1.1.5/doc
rm -r ./JalaliJSCalendar-1.1.5/examples

patch ./JalaliJSCalendar-1.1.5/calendar.js /tmp/calendar.patch

mv ./JalaliJSCalendar-1.1.5 ${PUB_PATH}/scripts/JalaliJSCalendar

# Cleanup

rm -rf ./Smarty-2.6.19/
rm -rf /tmp/Smarty-2.6.19.tar.gz

rm -rf /tmp/pear.tar.bz2

rm -rf /tmp/tinymce_3_1_1.zip
rm -rf /tmp/tinymce

rm -rf /tmp/JalaliJSCalendar-1.1.5.tar.gz
rm /tmp/calendar.patch