<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

$arag_application = '../../../webapp';
$arag_libraries   = '../../../libs';
$kohana_system       = $arag_libraries . '/kohana';
$docroot             = pathinfo(str_replace('\\', '/', realpath(__FILE__)));

define('APPNAME', ltrim(strrchr($docroot['dirname'], '/'), '/'));
define('EXT', '.php');

define('KOHANA',  $docroot['basename']);
define('DOCROOT', $docroot['dirname'].'/');

define('APPPATH',  str_replace('\\', '/', realpath($arag_application)).'/');
define('LIBSPATH', str_replace('\\', '/', realpath($arag_libraries)).'/');
define('SYSPATH',  str_replace('\\', '/', realpath($kohana_system)).'/');

ini_set('include_path', LIBSPATH.PATH_SEPARATOR.ini_get('include_path'));

unset($docroot, $arag_application, $arag_libraries, $kohana_system);

require_once SYSPATH.'core/Bootstrap'.EXT;
