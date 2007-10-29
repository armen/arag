<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class FiltersModel extends Model 
{
    // {{{ Properties
    
    var $tableNameFilters;

    // }}}
    // {{{ Constructor
    function FiltersModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // set tables' names
        $this->tableNameFilters = "user_filters";        
    }
    // }}}
    // {{{ getPrivilegeFilters
    function getPrivilegeFilters($appname)
    {
        $filters = array();

        if ($appname === '_master_') {
            // Sir, you are the master and you don't need any filter \:)
            //return $filters;
        }

        $this->db->select('appname, filter')->from($this->tableNameFilters);
        $this->db->where('appname', $appname)->orwhere('appname', '_default_')->orwhere('appname', '_global_');

        // Get appname, _default_ and _global_ filter.
        $_filters = $this->db->get()->result();

        // Save filters more friendly schema
        foreach ($_filters as $filter) {
            
            $filters[$filter->appname] = ($filter->filter != Null) ? unserialize($filter->filter) : 
                                         // If filter was Null add * as the filter this will disallow
                                         // any kind of access with this filter. The global filter is 
                                         // Null by default to allow any access, so it should not be *
                                         (($filter->appname === '_global_') ? Array() : Array('*')); 
        }

        // If there was no filter for application, use _default_ filter
        $appname = (isset($filters[$appname])) ? $appname : '_default_';
        
        // Merge filters to generate a single filter;
        $filters = array_unique(array_merge($filters[$appname], $filters['_global_']));

        return $filters;
    }
    // }}}
}
?>
