#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

for name in `find . -type d | grep "module" | grep 'fa_IR.utf8/LC_MESSAGES' | grep -v .svn`
do

    module_path=$(readlink -f "$name/../../../")
    module=$(basename $module_path)
    cd $module_path

    if [ ! -f ./ref.po ]; then

        echo ""
        echo "Extracting messages of '${module}'..."

        find . -type f | grep -v '.svn' | grep -v -e '/\..*' | grep -v "vendors" | grep -v 'schemas' | grep -v 'LC_MESSAGES' | xgettext --keyword=_ --force-po --files-from - --output=ref.po
        sed -i -e 's/CHARSET/utf-8/' ./ref.po

        echo "Merging/Creating messages of '${module}'..."

        if [ -d ./locale ]; then

            for path in `find ./locale -name 'LC_MESSAGES' -type d | grep -v .svn`
            do
                if [ -f "${path}/messages.po" ]; then
                    echo -n "Mergeing ref.po file with ${path}/messages.po file:"
                    msgmerge "${path}/messages.po" ./ref.po -o "${path}/messages.po"
                else
                    echo "Creating ${path}/messages.po"
                    mv ./ref.po "${path}/messages.po"
                fi
            done

            rm -rf ref.po

        else
            echo "Error: All messages extracted but I can't find locale directory! please create it first."
        fi

    else
        echo "Error: ref.po already exists, please remove old file first."
    fi

    cd - > /dev/null

done
