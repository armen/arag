<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
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
    // {{{ getFilters
    function getFilters($appname, $flag = false)
    {
        $this->db->select('filter');
        $this->db->from($this->tableNameFilters);
        $this->db->where('appname', $appname);

        $row  = $this->db->get()->result_array();

        if ($row[0]['filter'] == NULL) {
            return $filter = array();
        }
        
        $rows = unserialize($row[0]['filter']);

        if ($flag) {
            return $rows;
        }
        
        $filters = array();

        foreach ($rows as $key => $filter) {
            $filters[$key]['filter'] = $filter;
            $filters[$key]['id']     = $key;
        }

        return $filters;
    }
    // }}}
    // {{{ editFilter
    function editFilter($filter, $id, $appname)
    {
        $filters = $this->getFilters($appname, true);
        
        $filters[$id] = strtolower($filter);

        $filter = serialize($filters);

        $this->db->where('appname', $appname);

        $row = array('filter'      => $filter,
                     'modify_date' => time(),
                     'modified_by' => $this->session->userdata('username'));

        $this->db->update($this->tableNameFilters, $row);
    }
    // }}}
    // {{{ addFilter
    function addFilter($filter, $appname)
    {
        $filters = $this->getFilters($appname, true);
        
        array_push($filters, strtolower($filter));

        $filter = serialize($filters);

        $this->db->where('appname', $appname);

        $row = array('filter'      => $filter,
                     'modify_date' => time(),
                     'modified_by' => $this->session->userdata('username'));

        $this->db->update($this->tableNameFilters, $row);
    }
    // }}}
    // {{{ getFilterProperties
    function getFilterProperties($name = "", $flag = true)
    {
        $this->db->select('*');
        $this->db->from($this->tableNameFilters);        
        
        if ($flag) {
            $this->db->where('appname', $name);
        } else {
            $this->db->like('appname', $name);
        }

        $row = $this->db->get()->result_array();

        return $row;
    }
    // }}}
    // {{{ getDate
    function getDate($row)
    {
        return date('Y-m-d H:i:s', $row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    function getModifyDate($row)
    {
        return date('Y-m-d H:i:s', $row['modify_date']);
    }
    // }}}
    // {{{ hasFilter
    function hasFilter($appname, $filter)
    {
        $filters = $this->getFilters($appname);
        
        foreach ($filters as $key) {
            if ($key['filter'] == $filter) {
                return true;
            }
        }
        
        return false;
    }
    // }}}
    // {{{ hasApp
    function hasApp($appname)
    {
        $this->db->select('appname');
        $query = $this->db->getwhere($this->tableNameFilters, Array('appname' => $appname)); 
        return (boolean)$query->num_rows();
    }
    // }}}
    // {{{ deleteFilters
    function deleteFilters($objects, $appname)
    {
        $filters = $this->getFilters($appname, true);
        
        foreach ($objects as $object) {
            unset ($filters[$object]);
        }
        
        if (count($filters) == 0) {
            $filters = NULL;
        } else {
            $filters = serialize($filters);
        }

        $this->db->where('appname', $appname);
        $this->db->update($this->tableNameFilters, array('filter' => $filters));
    }
    // }}}
    // {{{ deleteApps
    function deleteApps($appnames)
    {
        foreach ($appnames as $appname) {
            if ($appname != "_global_" && $appname != "_default_") {
                $this->db->delete($this->tableNameFilters, array('appname' => $appname));
            }
        }
    }
    // }}}
    // {{{ getPrivilegeFilters
    function getPrivilegeFilters($appname)
    {
        $filters = array();

        if ($appname === '_master_') {
            // Sir, you are the master and you don't need any filter \:)
            return $filters;
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
    // {{{ addApp
    function addApp($appname)
    {
        $rows = array('appname'     => $appname,
                      'filter'      => NULL,
                      'create_date' => time(),
                      'modify_date' => time(),
                      'created_by'  => $this->session->userdata('username'),
                      'modified_by' => $this->session->userdata('username'));
        
        $this->db->insert($this->tableNameFilters, $rows);
    }
    // }}}
    //{{{ isDeletable
    function isDeletable($row)
    {
        if ($row['appname'] != "_global_" && $row['appname'] != "_default_") {
            return false;
        } else {
            return true;
        }
    }
    //}}}
}

?>