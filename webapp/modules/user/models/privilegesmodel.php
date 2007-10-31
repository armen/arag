<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class PrivilegesModel extends Model 
{
    // {{{ Properties
    
    var $tableNamePrivileges;

    // }}}
    // {{{ Constructor
    function PrivilegesModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // set tables' names
        $this->tableNamePrivileges = "user_privileges";        
    }
    // }}}
    // {{{ getFilteredPrivileges
    function getFilteredPrivileges($appname, $parentId = 0)
    {
        $CI =& get_instance();
        $CI->load->model(Array('FiltersModel', 'user'), 'Filters');

        $filters = $CI->Filters->getPrivilegeFilters($appname);
        
        $this->db->select('id, parent_id, label, privilege')->from($this->tableNamePrivileges);
        $privileges = $this->db->where(Array('parent_id' => $parentId))->get()->result_array();
        
        if (is_array($filters)) {

            foreach ($filters as $filter) {
            
                // It contains four section which every section separated with a /.
                // It should contain at least two section. Each section contains * 
                // or lower case character(s)
                if ($filter === '*' || preg_match('/^([a-z_]+)(\/([a-z_]+|\*)){1,3}$/', $filter)) {

                    $filter = '|^'.str_replace('*', '.*', $filter).'$|';

                    foreach ($_privileges as $key => $row) {

                        if (preg_match($filter, $row['privilege'])) {
                            unset($_privileges[$key]);
                        }
                    }                        
                }
            }
        }

        return $privileges;
    }
    // }}}
}

?>
