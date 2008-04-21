<?php 
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once 'MDB2/Schema.php';

// {{{ execSchemaFiles
function execSchemaFiles(&$manager, $schemaFiles, $dataFiles, $data, $schemaVars = Array())
{
    $lastDirectory = Null;
    echo "\n";

    // Foreach through files and execute schemas
    foreach ($schemaFiles as $schema) {

        $output   = Null;
        $output  .= "executing '".basename($schema)."': ";
        $hasData  = false;

        if (is_readable($schema) && preg_match('/^[_A-Za-z0-9]+\.schema$/', basename($schema))) {

            // Create schema
            $result = $manager->updateDatabase($schema, Null, $schemaVars);
            //$result = $manager->updateDatabase($schema, Null, $schemaVars);
            //$result = $manager->updateDatabase($schema, "{$schema}.before", $schemaVars);

            if (PEAR::isError($result)) {
                $output .= ("Failed! (" . $result->getMessage() . ")\n" . $result->getUserInfo());
            } else {
                $output .= "Done!";
            }

            $dataFileName = str_replace('.schema', "{$data}.data", basename($schema));
            $dataFileName = (!empty($data) && isset($dataFiles[$dataFileName])) ? $dataFileName : str_replace('.schema', '.data', basename($schema));

            // Insert data
            if (isset($dataFiles[$dataFileName]) && is_readable($dataFiles[$dataFileName])) {
                
                $dataFile = $dataFiles[$dataFileName];
                
                $output .= "\n";
                $output .= "executing '".$dataFileName."': ";        
                $result =& $manager->writeInitialization($dataFile, $schema, $schemaVars);
                
                if (PEAR::isError($result)) {
                    $output .= ("Failed! (" . $result->getMessage() . ")\n" . $result->getUserInfo());
                } else {
                    $output .= "Done!";
                }
            }

        } else {
            $output .= "Skiped! (I don't understand what's this!)";
        }

        $output .= "\n";
        
        if (dirname($schema) != $lastDirectory) {
            $lastDirectory = dirname($schema);        
            $output = "\n" . $output;
        }
        
        // Show result
        echo $output;
    }

    echo "\n";
}
// }}}
// {{{ getSchameFilesList 
function getSchemaFilesList($module, $pattern)
{
    $modulesPath = realpath(dirname(__FILE__).'/../../webapp');
    $files       = Array();

    foreach (glob($modulesPath . '/modules/'.$module) as $path) {

        if (file_exists($path . '/config/module.php')) {
            include($path . '/config/module.php');

            $schemaPath = $path . '/schemas/v' . $config['version'];
            
            // Is module enabled?
            if (strtolower($config['enabled']) && is_dir($schemaPath)) {
                $files = array_merge($files, glob($schemaPath.'/'.$pattern));
            }

        } else {
            $moduleName = substr(strrchr($path, '/'), 1);
            echo "\nWARNING: module.php does not exists for '$moduleName' module: Skipped!";
        }
    }

    $result = array();

    foreach ($files as $file) {
        $result[basename($file)] = $file;
    }

    return $result;
}
// }}}
