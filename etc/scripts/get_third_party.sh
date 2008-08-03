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

rm -rf ./Smarty-2.6.19/
rm -rf /tmp/Smarty-2.6.19.tar.gz

rm -rf /tmp/pear.tar.bz2

rm -rf /tmp/FCKeditor_2.4.3.tar.gz
