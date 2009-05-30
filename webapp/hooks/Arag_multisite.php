<?php

Event::add_before('system.ready', current(Event::get('system.ready')), 'multisite_fetch_appname');

function multisite_fetch_appname()
{
    (PHP_SAPI == 'cli') AND $_SERVER['SERVER_NAME'] = Null;

    // Find subdomain and domain name
    preg_match('/^(?:(.*)\.)?([^.]++\.[^.]++)$/', $_SERVER['SERVER_NAME'], $appname);

    // Find appname from domain name
    $domains = Kohana::config('arag.domains');

    if (isset($domains[$appname[2]])) {
        $appname = $domains[$appname[2]];
    } else {
        $appname = ($appname[1]) ? $appname[1] : Kohana::config('arag.master_appname');
    }

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
