<?php

$kohana_application = '../applications/core';
$arag_libraries     = '../libs';
$kohana_system      = $arag_libraries . '/kohana';

error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', TRUE);
define('EXT', '.php');

$docroot = pathinfo(str_replace('\\', '/', realpath(__FILE__)));

define('MASTERAPP', TRUE);
define('APPNAME',   'arag');

define('KOHANA',  $docroot['basename']);
define('DOCROOT', $docroot['dirname'].'/');

define('APPPATH', str_replace('\\', '/', realpath($kohana_application)).'/');
define('SYSPATH', str_replace('\\', '/', realpath($kohana_system)).'/');
define('LIBSPATH', str_replace('\\', '/', realpath($arag_libraries)).'/');
define('APPSPATH', str_replace('\\', '/', realpath('../applications')).'/');

ini_set('include_path', LIBSPATH.PATH_SEPARATOR.ini_get('include_path'));

unset($docroot, $kohana_application, $kohana_system, $arag_libraries);

require SYSPATH.'core/Bootstrap'.EXT;
