<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Applications_Model extends Model
{
    // {{{ Properties

    public $tableNameApps;
    public $tableNameGroups;
    public $tableNameUsers;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // set tables' names
        $this->tableNameApps   = 'user_applications';
        $this->tableNameGroups = 'user_groups';
        $this->tableNameUsers  = 'user_users';
    }
    // }}}
    // {{{ getApps
    public function getApps($name)
    {
        $this->db->select('name, default_group, create_date');
        $this->db->from($this->tableNameApps);

        if ($name != "") {
            $this->db->like("name", $name);
        }

        $this->db->orderby('name', 'ASC');

        $query = $this->db->get();

        $retval = $query->result(False);
        return $retval;
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
    // {{{ hasApp
    public function hasApp($name)
    {
        $result = $this->db->select('count(name) as count')->getwhere($this->tableNameApps, Array('name' => $name))->current();
        return (boolean) $result->count;
    }
    // }}}
    // {{{ addApp
    public function addApp($appname, $author, $defaultgroup = "admin", $databaseid = 1)
    {
        $rows = array (
                       'name'          => $appname,
                       'create_date'   => time(),
                       'created_by'    => $author,
                       'default_group' => $defaultgroup,
                       'database_id'   => $databaseid
                      );

        $this->db->insert($this->tableNameApps, $rows);
    }
    // }}}
}
