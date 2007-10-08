<?php 
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once 'MDB2/Schema.php';

// {{{ execSchema
function execSchema(&$manager, $schema, $data, $schemaVars = Array())
{
    $output = "\n";

    // don't any thing for .data files 
    if (preg_match('/^[_A-Za-z0-9\.]+\.data$/', basename($schema))) {
        return;
    }
    
    $output  .= "executing '".basename($schema)."': ";
    $hasData  = false;

    if (is_readable($schema) && preg_match('/^[_A-Za-z0-9]+\.schema$/', basename($schema))) {

        // Create schema
        $result = $manager->updateDatabase($schema, Null, $schemaVars);
        //$result = $manager->updateDatabase($schema, Null, $schemaVars);
        //$result = $manager->updateDatabase($schema, "{$schema}.before", $schemaVars);

        if (PEAR::isError($result)) {
            $output .= ("Failed! (" . $result->getMessage() . ")\n" . $result->getUserInfo());
            return $output;
        } else {
            $output .= "Done!";
        }

        // Schema has data?    
        if (isset($data) && is_readable(str_replace('.schema', ".{$data}.data", $schema))) {
            $dataFile = str_replace('.schema', ".{$data}.data", $schema);
            $hasData  = true;
        } else if (is_readable(str_replace('.schema', '.data', $schema))) {
            $dataFile = str_replace('.schema', '.data', $schema);
            $hasData  = true;
        }
        
        // Insert data
        if ($hasData) {
            
            $output .= "\n";
            $output .= "executing '".basename($dataFile)."': ";        
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

    return $output;
}
// }}}

