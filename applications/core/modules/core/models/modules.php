<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Modules_Model extends Model 
{
    // {{{ Properties
    
    private $tableNameCoreModules;
    
    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableNameCoreModules = 'core_modules';
    }
    // }}}
    // {{{ getModules
    public function getModules($excludeModules = Array())
    {
        $this->db->select('id, name, module, description');
        $this->db->where('state', 1);

        // Excluse modules
        foreach ($excludeModules as $module) {
            $this->db->where('module !=', $module);
        }

        return $this->db->get($this->tableNameCoreModules)->result(false);
    }
    // }}}
}
?>
