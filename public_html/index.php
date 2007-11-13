<?php

// Set the error reporting level
error_reporting(E_ALL);

// Enable or disable error reporting. You should always disable this in production
ini_set('display_errors', TRUE);

// Application directory
$arag_application = '../webapp';

// Libraries directory
$arag_libraries = '../libs';

// Kohana framework directory.
$kohana_system = $arag_libraries . '/kohana';

// Extention
define('EXT', '.php');

$docroot = pathinfo(str_replace('\\', '/', realpath(__FILE__)));

define('APPNAME', '_master_');
define('KOHANA',  $docroot['basename']);
define('DOCROOT', $docroot['dirname'].'/');

define('APPPATH',  str_replace('\\', '/', realpath($arag_application)).'/');
define('LIBSPATH', str_replace('\\', '/', realpath($arag_libraries)).'/');
define('SYSPATH',  str_replace('\\', '/', realpath($kohana_system)).'/');

// Add LIBSPATH to include_path
ini_set('include_path', LIBSPATH.PATH_SEPARATOR.ini_get('include_path'));

unset($docroot, $arag_application, $arag_libraries, $kohana_system);

(is_dir(APPPATH) AND is_dir(APPPATH.'/config')) or die
(
	'Your <code>$arag_application</code> does not exist. '.
	'Set a valid <code>$arag_application</code> in <tt>index.php</tt> and refresh the page.'
);

(is_dir(SYSPATH) AND file_exists(SYSPATH.'/core/'.'Bootstrap'.EXT)) or die
(
	'Your <code>$kohana_system</code> does not exist. '.
	'Set a valid <code>$kohana_system</code> in <tt>index.php</tt> and refresh the page.'
);

require_once SYSPATH.'core/Bootstrap'.EXT;
