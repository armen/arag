#!/usr/bin/php -q
<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

// {{{ Set include path

if(!defined ('PATH_SEPARATOR')) {
    if(defined ('DIRECTORY_SEPARATOR') && DIRECTORY_SEPARATOR == '\\') {
        define ('PATH_SEPARATOR', ';');
    } else {
        define ('PATH_SEPARATOR', ':');
    }
}

$include_path  = str_replace('etc/scripts', '', dirname(__FILE__)) . 'libs/pear' . PATH_SEPARATOR;
ini_set('include_path', $include_path . ini_get('include_path'));

// }}}
// {{{ Includes

include_once dirname(__FILE__).'/../libs/exec_schema.php';
include_once 'MDB2.php';
include_once 'Console/Getopt.php';

// }}}
// {{{ Get options

// Create Console_Getopt
$consoleGetopt  =& new Console_Getopt;
$args           =& $consoleGetopt->readPHPArgv();

// Just shift arguments
array_shift($args);

// Get options
$options = $consoleGetopt->getopt2($args, 'p:d:m:a::', Array('dsn=', 'prefix=', 'data-set=', 'all-modules==', 'module='));
if (PEAR::isError($options)) {
    die ($options->getMessage() . "\n" . $options->getUserInfo() . "\n");
}

// Optional options
$prefix = 'arag_';
$data   = '';

// Extract options
foreach ($options[0] as $option) {

    switch ($option[0]) {
        case 'd':
        case '--data-set':
            $data = '.'.$option[1];
            break;

        case 'p':
        case '--prefix':
            $prefix = $option[1];
            break;

        case '--dsn':
            $dsn = $option[1];
            break;

        case 'a':
        case '--all-modules':
            $module = '*';
            break;

        case 'm':
        case '--module':
            $module = $option[1];
            break;
    }
}

// Check for empty options and missing required options
if (!isset($dsn) || empty($options[0]) || (!isset($module) && empty($options[1]))) {
    echo "Usage: ./create_schema.php --dsn=... [-p|--prefix=...] [-d|--data-set=...] schema_file.schema [schema_file.schema]...\n".
         "       ./create_schema.php --dsn=... [-p|--prefix=...] [-d|--data-set=...] [[-a|--all-modules] -m|--module=...] \n\n";
    exit;
}

// }}}
// {{{ Get database connection/name and manager

// XXX: we moved to MDB2 so seqcol_name will not id
// $mdb =& MDB2::factory($dsn, Array('seqcol_name' => 'id'));
$mdb =& MDB2::factory($dsn);
if (PEAR::isError($mdb)) {
    die ($mdb->getMessage() . "\n" . $mdb->getUserInfo() . "\n");
}

// Get database name
$temp = $mdb->getDSN('array');
$db   = $temp['database'];
unset($temp);

// Get manager
$manager =& MDB2_Schema::factory($mdb);
if (PEAR::isError($manager)) {
    die ($manager->getMessage() . "\n" . $manager->getUserInfo() . "\n");
}

// }}}
// {{{ Install schema files

if (isset($module)) {
    $schemaFiles = getSchemaFilesList($module, '*.schema');
    $dataFiles   = getSchemaFilesList($module, '*.data');
    $dataFiles   = array_merge($dataFiles, getSchemaFilesList($module, '*'.$data.'.data'));

} else {
    $schemaFiles = $options[1];
    $dataFiles   = array();

    foreach ($options[1] as $file) {
         $dataFiles[] = str_replace('.schema', $data.'.data', $file);
         $dataFiles[] = str_replace('.schema', '.data', $file);
    }
}

execSchemaFiles($manager, $schemaFiles, $dataFiles, $data, Array('DATABASE_NAME' => $db, 'TABLES_PREFIX' => $prefix, 'NOW_TIMESTAMP' => time()));

// }}}

?>
