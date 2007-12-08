<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Installation_Model extends Model 
{
    // {{{ Properties
    
    private $dsn;
    private $tablePrefix;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        $this->setDSN();
        $this->setTablePrefix();
    }
    // }}}
    // {{{ setTablePrefix
    public function setTablePrefix($tablePrefix = Null)
    {
        $this->tablePrefix = ($tablePrefix == Null) ? $this->db->table_prefix() : $tablePrefix;        
    }
    // }}}
    // {{{ setDSN
    public function setDSN($dsn = Null)
    {
        $this->dsn = ($dsn == Null) ? Config::item('database.connection') : $dsn;
    }
    // }}}

    // {{{ createDirectory
    public function createDirectory($directory, $mod = 0755)
    {
        if (!file_exists($directory)) {
            $this->createDirectory(dirname($directory), $mod);
            $oldumask = umask(0);            
            mkdir($directory, $mod);
            umask($oldumask);

            return $oldumask == umask();
        }

        return False;
    }
    // }}}
    // {{{ deleteDirectory
    public function deleteDirectory($directory, $recursive = False)
    {
        if (!is_dir($directory)) {
            return False;
        }

        $directory = realpath($directory);
        if (substr($directory, -1) != '/') {
            $directory .= '/';
        }

        if ($recursive && $dir = opendir($directory)) {

            while ($file = readdir($dir)) {
                if ($file != '.' && $file != '..') {
                    if (is_file($directory . $file)) {
                        unlink($directory . $file);
                    } else {
                        $this->deleteDirectory($directory . $file, $recursive);
                    }
                }
            }

            closedir($dir);
        }

        return rmdir($directory);
    }
    // }}}

    // {{{ & getFromTemplate
    public function & getFromTemplate($template, $parameters = Array(), $baseDirectory = Null)
    {   
        // TODO: baseDirectory sould be validated!!
        $filename = $baseDirectory . '/' . $template;
        $handle   = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        // Do this if it has parameters
        if (!empty($parameters)) {
            foreach ($parameters as $paramName => $paramValue) {
                $contents = str_replace("%$paramName%", $paramValue, $contents);
            }
        }

        return $contents;
    }
    // }}}
    // {{{ createFromTemplate
    public function createFromTemplate($file, $template, $parameters = Array(), $baseDirectory = Null, $mod = 0644)
    {
        $fh = fopen($file, 'w');
        fputs($fh, $this->getFromTemplate($template, $parameters, $baseDirectory));
        fclose($fh);

        chmod($file, $mod);
    }
    // }}}

    // {{{ & executeSchema
    public function & executeSchema($file, $dataSet = Null, $initializeData = False, $oldFile = Null)
    {
        ini_set('include_path', LIBSPATH.'pear'.PATH_SEPARATOR.ini_get('include_path'));
        
        include_once 'pear/MDB2/Schema.php';
        include_once 'pear/MDB2.php';

        // Get database connection
        // XXX: we moved to MDB2 so seqcol_name will not id
        // $mdb =& MDB2::factory($this->dsn, Array('seqcol_name' => 'id'));
        $mdb =& MDB2::factory($this->dsn);
        if (PEAR::isError($mdb)) { return $mdb; }

        // Do not change database name to lowercase
        $mdb->setOption('portability', MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE);
        
        // Get database name
        $dsn    = $mdb->getDSN('array');
        $dbName = $dsn['database'];
        $result = True;

        // Copy old schema file to cache, because MDB_Schema will modify old schema file
        if (is_readable($oldFile)) {
            copy($oldFile, APPPATH . 'cache/' . basename($oldFile));
            $oldFile = APPPATH . 'cache/' . basename($oldFile);
        } else {
            $oldFile = Null;
        }

        // Get manager
        $manager =& MDB2_Schema::factory($mdb);
        if (PEAR::isError($manager)) { return $manager; }

        if (is_readable($file) && preg_match('/^[_A-Za-z0-9]+\.schema$/', basename($file))) {

            $schemaVars = Array('DATABASE_NAME' => $dbName, 'TABLES_PREFIX' => $this->tablePrefix, 'NOW_TIMESTAMP' => time());

            // Create schema
            $result = $manager->updateDatabase($file, $oldFile, $schemaVars);
            if (PEAR::isError($result)) { return $result; }

            $hasData = False;

            if ($initializeData) {
            
                // Schema has data?
                if ($dataSet != Null && is_readable(str_replace('.schema', ".{$dataSet}.data", $file))) {
                    
                    $dataFile = str_replace('.schema', ".{$dataSet}.data", $file);
                    $hasData  = True;
                    
                } else if (is_readable(str_replace('.schema', '.data', $file))) {
                
                    $dataFile = str_replace('.schema', '.data', $file);
                    $hasData  = True;
                }

                // Insert data
                if ($hasData) {
                    $result = $manager->writeInitialization($dataFile, $file, $schemaVars);
                    if (PEAR::isError($result)) { return $result; }
                }    
            }
        }
        
        // We have to unlink it from cache
        if (is_readable($oldFile)) {
            unlink(APPPATH . 'cache/' . basename($oldFile));
        }

        return $result;
    }
    // }}}
    // {{{ executeSchemas
    public function executeSchemas($moduleName, $version, $dataSet = Null, $initializeData = True)
    {
        $schemaPath = APPPATH . 'modules/' . $moduleName . '/schemas/v' . $version;
        
        if (is_dir($schemaPath)) {

            if ($fh = opendir($schemaPath)) {
                while (false !== ($schemaFile = readdir($fh))) {
                    if (is_file($schemaPath . '/' . $schemaFile) && 
                        !preg_match('/^[_A-Za-z0-9\.]+\.data$/', $schemaFile)) {

                        $this->executeSchema($schemaPath . '/' . $schemaFile, $dataSet, $initializeData);
                    }
                }

                closedir($fh);
            }
        }
    }
    // }}}
}

?>
