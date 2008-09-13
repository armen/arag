<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Filters_Model extends Model
{
    // {{{ Properties

    public $tableNameFilters;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // set tables' names
        $this->tableNameFilters = 'user_filters';
    }
    // }}}
    // {{{ getFilters
    public function getFilters($appname, $flag = false)
    {
        $this->db->select('filter');
        $this->db->from($this->tableNameFilters);
        $this->db->where('appname', $appname);

        $row  = $this->db->get()->result(False);

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
    public function editFilter($filter, $id, $appname, $author)
    {
        $filters = $this->getFilters($appname, true);

        $filters[$id] = strtolower($filter);

        $filter = serialize($filters);

        $this->db->where('appname', $appname);

        $row = array('filter'      => $filter,
                     'modify_date' => time(),
                     'modified_by' => $author);

        $this->db->update($this->tableNameFilters, $row);
    }
    // }}}
    // {{{ addFilter
    public function addFilter($filter, $appname, $author)
    {
        $filters = $this->getFilters($appname, true);

        array_push($filters, strtolower($filter));

        $filter = serialize($filters);

        $this->db->where('appname', $appname);

        $row = array('filter'      => $filter,
                     'modify_date' => time(),
                     'modified_by' => $author);

        $this->db->update($this->tableNameFilters, $row);
    }
    // }}}
    // {{{ getFilterProperties
    public function getFilterProperties($name = "", $flag = true)
    {
        $this->db->select('appname, create_date, created_by, modify_date, modified_by, filter');
        $this->db->from($this->tableNameFilters);

        if ($flag) {
            $this->db->where('appname', $name);
        } else {
            $this->db->like('appname', $name);
        }

        $row = $this->db->get()->result(False);

        return $row;
    }
    // }}}
    // {{{ getDate
    public function getDate($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return format::date($row['modify_date']);
    }
    // }}}
    // {{{ hasFilter
    public function hasFilter($appname, $filter)
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
    public function hasApp($appname)
    {
        $result = $this->db->select('count(appname) as count')->getwhere($this->tableNameFilters, Array('appname' => $appname))->current();
        return (boolean)$result->count;
    }
    // }}}
    // {{{ deleteFilters
    public function deleteFilters($objects, $appname)
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
    public function deleteApps($appnames)
    {
        foreach ($appnames as $appname) {
            if ($appname != "_global_" && $appname != "_default_") {
                $this->db->delete($this->tableNameFilters, array('appname' => $appname));
            }
        }
    }
    // }}}
    // {{{ getPrivilegeFilters
    public function getPrivilegeFilters($appname)
    {
        $filters = array();

        if (defined('MASTERAPP')) {
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
    public function addApp($appname, $author)
    {
        $rows = array('appname'     => $appname,
                      'filter'      => NULL,
                      'create_date' => time(),
                      'modify_date' => time(),
                      'created_by'  => $author,
                      'modified_by' => $author);

        $this->db->insert($this->tableNameFilters, $rows);
    }
    // }}}
    //{{{ isDeletable
    public function isDeletable($row)
    {
        return ($row['appname'] != "_global_" && $row['appname'] != "_default_");
    }
    //}}}
}
