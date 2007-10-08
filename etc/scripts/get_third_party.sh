#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

BASE_PATH="`pwd`/`dirname $0`"
LIBS_PATH="${BASE_PATH}/../../libs/"
PUB_PATH="${BASE_PATH}/../../public_html/"

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

# Download ci

if [ -d $LIBS_PATH/ci ]; then
    rm -rf "${LIBS_PATH}/ci"
fi

wget -c -P /tmp http://codeigniter.com/downloads/CodeIgniter_1.5.4.zip
unzip -o /tmp/CodeIgniter_1.5.4.zip

mv ./CodeIgniter_1.5.4/system ${LIBS_PATH}/ci

# Download pear
if [ -d $LIBS_PATH/pear ]; then
    rm -rf "${LIBS_PATH}/pear"
fi

wget -c -P /tmp http://pub.opensourceclub.org/pear.tar.bz2
tar xvfj /tmp/pear.tar.bz2
mv ./pear $LIBS_PATH

# Download FCKeditor

if [ -d $PUB_PATH/scripts/FCKeditor ]; then
    rm -rf "${PUB_PATH}/scripts/FCKeditor"
fi

wget -c -P /tmp http://prdownloads.sourceforge.net/fckeditor/FCKeditor_2.4.3.tar.gz?download
tar xvfz /tmp/FCKeditor_2.4.3.tar.gz

find ./fckeditor | grep -e '/_[a-z_0-9.]*$' | xargs rm -rf
find ./fckeditor/ -maxdepth 1 -name fckeditor.* | grep -v 'js' | grep -v 'php' | xargs rm
find ./fckeditor/ -type f | xargs grep ' xmlns="http:\/\/www.w3.org\/1999\/xhtml"' -l | xargs sed -i -e 's/ xmlns="http:\/\/www.w3.org\/1999\/xhtml"//'
find ./fckeditor/ -type f | xargs grep '<meta.*/>' -l | xargs sed -i -e 's/<meta\(.*\)\/>/<meta\1>/'

# Add comment tab for script tags
sed -i -e 's/<script.*>/&\r\n\t<!--/'    ./fckeditor/editor/fckeditor.html
sed -i -e 's/<\/script>/\/\/-->\r\n\t&/' ./fckeditor/editor/fckeditor.html

mv ./fckeditor ${PUB_PATH}/scripts/FCKeditor

# Cleanup

rm -rf ./Smarty-2.6.18/
rm -rf /tmp/Smarty-2.6.18.tar.gz

rm -rf ./CodeIgniter_1.5.4
rm -rf /tmp/CodeIgniter_1.5.4.zip

rm -rf /tmp/pear.tar.bz2

rm -rf /tmp/FCKeditor_2.4.3.tar.gz
