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
    public function getApps($name=Null)
    {
        $this->db->select('name, default_group, create_date');
        $this->db->from($this->tableNameApps);

        if ($name != "") {
            $this->db->like("name", $name);
        }

        $this->db->orderby('name', 'ASC');

        return $this->db->get()->result(False);
    }
    // }}}
    // {{{ getAppsName
    public function getAppsName()
    {
        $applications = Array();

        $this->db->select('name');
        $this->db->from($this->tableNameApps);
        $this->db->orderby('name', 'ASC');

        $result = $this->db->get()->result(False);

        foreach ($result as $row) {
            $applications[] = $row['name'];
        }

        return $applications;
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
    // {{{ getVerificationStatus
    public function getVerificationStatus($row)
    {
        return isset($row['verified']) && $row['verified'] ? _("Yes") : _("No");
    }
    // }}}
    // {{{ getBlockedStatus
    public function getBlockedStatus($row)
    {
        return isset($row['blocked']) && $row['blocked'] ? _("Yes") : _("No");
    }
    // }}}
    // {{{ hasApp
    public function hasApp($name)
    {
        $result = $this->db->select('count(name) as count')->getwhere($this->tableNameApps, Array('name' => $name))->current();
        return (boolean) $result->count;
    }
    // }}}
    // {{{ get
    public function get($name)
    {
        return $this->db->select('name, default_group')->from($this->tableNameApps)->where('name', $name)->get()->result(False)->current();
    }
    // }}}
    // {{{ addApp
    public function addApp($appname, $author, $defaultgroup = "admin", $databaseid = 1, $template = Null)
    {
        $rows = array (
                       'name'          => $appname,
                       'create_date'   => time(),
                       'created_by'    => $author,
                       'default_group' => $defaultgroup,
                       'database_id'   => $databaseid
                      );

        $this->db->insert($this->tableNameApps, $rows);

        if ($template) { //Template is the application which we are going to copy all groups and privileges and filters from
            $templateApp = $this->get($template);
            $filters     = Model::load('Filters', 'user');
            $groups      = Model::load('Groups', 'user');
            $privileges  = Model::load('Privileges', 'user');

            $this->db->query('INSERT INTO '.$this->db->table_prefix().$filters->tableNameFilters.'(appname, create_date, created_by, filter) SELECT "'.$appname.'", "'.time().'", "'.$author.'", filter FROM '.$this->db->table_prefix().$filters->tableNameFilters.' WHERE appname="'.$template.'"');
            $this->db->query('INSERT INTO '.$this->db->table_prefix().$groups->tableNameGroups.'(name, appname, create_date, created_by, privileges, redirect, deletable) SELECT name, "'.$appname.'", "'.time().'", "'.$author.'", privileges, redirect, deletable FROM '.$this->db->table_prefix().$groups->tableNameGroups.' WHERE appname="'.$template.'"');
            $this->db->where('name', $appname)->update($this->tableNameApps, array('default_group' => $templateApp['default_group']));
        }
    }
    // }}}
}
