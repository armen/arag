<?php

Event::add_before('system.ready', current(Event::get('system.ready')), 'multisite_fetch_appname');

function multisite_fetch_appname()
{
    // Fetch application name
    preg_match('/^(.*)\.[^.]++\.[^.]++$/', $_SERVER['SERVER_NAME'], $appname);

    if (empty($appname) || in_array($appname[1], Kohana::config('arag.default_appnames'))) {
        define('MASTERAPP', TRUE);
        define('APPNAME', current(Kohana::config('arag.default_appnames')));
    } else {
        define('MASTERAPP', FALSE);
        define('APPNAME', $appname[1]);
    }
}
