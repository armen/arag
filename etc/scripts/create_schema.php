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
$options = $consoleGetopt->getopt2($args, 'p:d:', Array('dsn=', 'prefix=', 'data-set=', 'all-modules=='));
if (PEAR::isError($options)) {
    die ($options->getMessage() . "\n" . $options->getUserInfo() . "\n");
}

// Optional options
$prefix = 'arag_';
$data   = 'test';

// Extract options
foreach ($options[0] as $option) {
    
    switch ($option[0]) {
        case 'd':
        case '--data-set':
            $data = $option[1];
            break;
        
        case 'p':
        case '--prefix':
            $prefix = $option[1];
            break;
        
        case '--dsn':
            $dsn = $option[1];
            break;

        case '--all-modules':
            $allModules = True;
            break;            
    }
}

// Check for empty options and missing required options
if (!isset($dsn) || empty($options[0]) || (!isset($allModules) && empty($options[1]))) {
    echo "Usage: ./create_schema.php --dsn=... [-p|--prefix=...] [-d|--data-set=...] [--all-modules] schema_file.schema ".
         "[schema_file.schema]...\n\n";
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

if (isset($allModules)) {

    // {{{ Fetch schema file names of all enabled modules

    $modulesPath = dirname(__FILE__).'/../../webapp/modules';
    $schemaFiles = Array();

    if ($dh = opendir($modulesPath)) {
        while (false !== ($moduleName = readdir($dh))) {
            if ($moduleName != '.' && $moduleName != '..' && $moduleName != 'CVS' && 
                $moduleName != '.svn' && is_dir($modulesPath . '/' . $moduleName)) {

                if (file_exists($modulesPath . '/' . $moduleName . '/config/module.php')) {
                    include_once($modulesPath . '/' . $moduleName . '/config/module.php');

                    $schemaPath = $modulesPath . '/' . $moduleName . '/schemas/v' . $module['version'];
                    
                    // Is module enabled?
                    if (strtolower($module['enabled']) && is_dir($schemaPath)) {

                        if ($fh = opendir($schemaPath)) {
                            while (false !== ($schemaFile = readdir($fh))) {
                                if (is_file($schemaPath . '/' . $schemaFile) && 
                                    !preg_match('/^[_A-Za-z0-9\.]+\.data$/', $schemaFile)) {

                                    $schemaFiles[] = $schemaPath . '/' . $schemaFile;
                                }
                            }

                            closedir($fh);
                        }
                    }

                } else {
                    echo "\nWARNING: module.php does not exists for '$moduleName' module: Skipped!";
                }
            }
        }

        closedir($dh);
    }

    // }}}
    // {{{ Install modules schema files

    $lastDirectory = Null;

    // Foreach through files and execute schemas
    foreach ($schemaFiles as $file) {

        $output = execSchema($manager, $file, $data, Array('DATABASE_NAME' => $db, 'TABLES_PREFIX' => $prefix));

        if (dirname($file) != $lastDirectory) {
            echo "\n";
        }
        
        // Show result
        echo $output;

        // Get last dir
        $lastDirectory = dirname($file);
    }
    echo "\n\n";
    // }}}

} else {
    
    // {{{ Install given schema files

    // Foreach through files and execute schemas
    foreach ($options[1] as $file) {

        echo execSchema($manager, $file, $data, Array('DATABASE_NAME' => $db, 'TABLES_PREFIX' => $prefix));
    }
    echo "\n\n";

    // }}}
}
// }}}

?>
