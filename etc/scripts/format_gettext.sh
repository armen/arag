#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

global=`find . -name 'global.po' -type f | grep 'fa_IR.utf8/LC_MESSAGES' | grep -v .svn | grep -v -e "/\..*"`

for name in `find . -name 'messages.po' -type f | grep 'fa_IR.utf8/LC_MESSAGES' | grep -v .svn | grep -v -e "/\..*"`
do
    path=`dirname $name`
    if [ -f "${path}/messages.po" ]; then

        echo "Processing '${path}/messages.po'"

        if [ -f $global ]; then
            echo -n "    Meging the message.po with global.po"
            msgmerge --no-fuzzy-matching --multi-domain --previous $global "${path}/messages.po" -o "${path}/temp.po"
        fi

        if [ -f "${path}/temp.po" ]; then
            echo -n "    Formating the messages.po file:"
            sed -i -e "s/^#~ //" "${path}/temp.po"
            msgfmt "${path}/temp.po" -o "${path}/messages.mo"
            echo " ...done."
            rm -f "${path}/temp.po"
        else
            echo -n "    Formating the messages.po file:"
            msgfmt "${path}/messages.po" -o "${path}/messages.mo"
            echo " ...done."
        fi
    fi
done
