#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

for path in `find . -name 'LC_MESSAGES' -type d | grep -v .svn`
do
    if [ ! -f ./ref.po ]; then

        echo ""
        echo "Extracting messages..."
        find ${path} -type f | grep -v '.svn' | grep -v -e '/\..*' | grep -v 'schemas' | grep -v 'LC_MESSAGES' | grep -e "\.(php)|(tpl)$" | xgettext --keyword=_ --force-po --files-from - --output=ref.po

        sed -i -e 's/CHARSET/utf-8/' ./ref.po
        echo "Merging/Creating messages..."

        if [ -f "${path}/messages.po" ]; then
            echo -n "Mergeing ref.po file with ${path}/messages.po file: "
            msgmerge "${path}/messages.po" ./ref.po -o "${path}/messages.po"
        else
            echo "Creating ${path}/messages.po"
            mv ./ref.po "${path}/messages.po"
        fi

        rm -rf ./ref.po

    else
       echo "Error: ref.po already exists, please remove old file first."
       exit 1;
    fi
done      
