<?php

Event::add_before('system.ready', current(Event::get('system.ready')), 'multisite_fetch_appname');

function multisite_fetch_appname()
{
    (PHP_SAPI == 'cli') AND $_SERVER['SERVER_NAME'] = Null;

    // Fetch application name
    preg_match('/^(.*)\.[^.]++\.[^.]++$/', $_SERVER['SERVER_NAME'], $appname);
    $appname = isset($appname[1]) ? $appname[1] : Kohana::config('arag.master_appname');

    if ($appname == Kohana::config('arag.master_appname') ||
        in_array($appname, Kohana::config('arag.master_appaliases')) ||
        in_array('.*', Kohana::config('arag.master_appaliases'))) {

        define('MASTERAPP', TRUE);
        define('APPNAME', Kohana::config('arag.master_appname'));
        define('APPALIAS', $appname);

    } else {
        define('MASTERAPP', FALSE);
        define('APPNAME', $appname);
    }
}
