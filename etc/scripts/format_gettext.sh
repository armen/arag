#!/bin/bash
# vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:

for name in `find . -name '*.po' -type f | grep -v .svn`
do
    path=`dirname $name`
    echo -n "Formating the ${path}/messages.po file:"
    msgfmt "${path}/messages.po" -o "${path}/messages.mo"
    echo " ...done."
done
