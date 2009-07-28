<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Groups_Model extends Model
{
    // {{{ Properties

    private $tableNameUsers       = 'user_users';
    public  $tableNameGroups      = 'user_groups'; //Public because applicaion model needs it when cloning
    private $tableNameApps        = 'user_applications';
    private $tableNameUsersGroups = 'user_users_groups';

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ & getAnonymousGroup
    public function & getAnonymousGroup($appname, $defaultGroup = False)
    {
        $groupname = 'anonymous';

        if ($defaultGroup) {
            $groupname = $this->getDefaultGroup($appname);
        }

        $this->db->select('id as group_id, name, appname, privileges, redirect');
        $this->db->from($this->tableNameGroups);
        $this->db->where('appname', $appname);
        $this->db->where('name', $groupname);

        $group = $this->db->getwhere()->current();

        if ($group) {
            $group               = (Array) $group;
            $group['privileges'] = unserialize($group['privileges']);
        } else {

            // XXX: Okay, there is no anonymous group for this appname so just construct a Null
            //      anonymous group
            $group               = Array();
            $group['name']       = 'anonymous';
            $group['appname']    = $appname;
            $group['privileges'] = Null;
            $group['redirect']   = False;
        }

        // Save privilege grouped by application name
        $privileges                    = $group['privileges'];
        $group['privileges']           = Null;
        $group['privileges'][$appname] = $privileges;

        return $group;
    }
    // }}}
    // {{{ getGroups
    public function getGroups($appName = NULL)
    {
        $this->db->select('id, name, modified_by, created_by, modify_date, create_date, appname');
        $this->db->from($this->tableNameGroups);

        if ($appName != NULL) {
            $this->db->where('appname', $appName);
        }

        $this->db->orderby('id', 'ASC');

        $query = $this->db->get();

        $retval = $query->result(False);
        return $retval;
    }
    // }}}
    // {{{ getGroupsName
    public function getGroupsName($appName = NULL)
    {
        $this->db->select('name');
        $this->db->from($this->tableNameGroups);

        if ($appName != NULL) {
            $this->db->where('appname', $appName);
        }

        $this->db->orderby('id', 'ASC');

        $result = $this->db->get()->result(False);
        $groups = Array();

        foreach ($result as $row) {
            $groups[] = $row['name'];
        }

        return array_unique($groups);
    }
    // }}}
    // {{{ & getGroup
    public function & getGroup($id = NULL, $appname = NULL, $groupname = NULL)
    {
        $this->db->select('id, name, modified_by, created_by, modify_date, create_date, appname, privileges');
        $this->db->from($this->tableNameGroups);

        if ($id != NULL) {
            $this->db->where('id', $id);
        } else {
            $this->db->where(array('appname' => $appname,
                                   'name'    => $groupname));
        }


        $group = (Array) $this->db->getwhere()->current();

        return $group;
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
    // {{{ setGroup
    public function setGroup($appName, $group)
    {
       $row = Array('default_group' => $group);

       $this->db->where('name', $appName);
       $this->db->update($this->tableNameApps, $row);
    }
    // }}}
    // {{{ getDefaultGroup
    public function getDefaultGroup($appName)
    {
        $this->db->select('default_group');
        $this->db->from($this->tableNameApps);
        $this->db->where('name', $appName);

        return $this->db->get()->current()->default_group;
    }
    // }}}
    // {{{ getAllAppGroups
    public function getAllAppGroups($appName)
    {
        $allgroups = array();

        $this->db->select('name');
        $this->db->from($this->tableNameGroups);
        $this->db->where('appname', $appName);

        $query = $this->db->get();

        foreach ($query->result(False) as $row) {
            array_push($allgroups, $row['name']);
        }

        return $allgroups;
    }
    // }}}
    // {{{ newGroup
    public function newGroup($appname, $newgroup, $author, $privileges = Null, $expire_date = Null)
    {
        $row = Array('modified_by' => $author,
                     'create_date' => time(),
                     'modify_date' => time(),
                     'created_by'  => $author,
                     'appname'     => $appname,
                     'name'        => $newgroup,
                     'privileges'  => $privileges,
                     'expire_date' => $expire_date,
                     'deletable'   => 1);

        $this->db->insert($this->tableNameGroups, $row);
    }
    // }}}
    // {{{ hasGroup
    public function hasGroup($name = NULL, $appname = NULL, $id = NULL)
    {
        $this->db->select('count(id) as count')->from($this->tableNameGroups);

        if ($id != NULL) {
            $this->db->where('id', $id);
        }

        if ($appname != NULL) {
            $this->db->where('appname', $appname);
        }

        if ($name != Null) {
            $this->db->where('name', $name);
        }

        $result = $this->db->get()->current();

        return (boolean) $result->count;
    }
    // }}}
    // {{{ deleteGroups
    public function deleteGroups($groups, $author)
    {
        foreach ($groups as $group) {
            $this->db->delete($this->tableNameGroups, Array('id' => $group, 'deletable' => True));

            $controller = new Users_Model;

            $anonymous = $controller->deleteUsers(NULL, $group, $author);
        }
    }
    // }}}
    // {{{ isDeletetable
    public function isDeletable($group_id)
    {
        $result = $this->db->select('deletable')->from($this->tableNameGroups)->where('id', $group_id)->get()->result(False)->current();
        return (boolean) $result['deletable'];
    }
    // }}}
    // {{{ changeModifiers
    public function changeModifiers($groupid, $author)
    {
        $this->db->where('id', $groupid);
        $this->db->update($this->tableNameGroups, array('modify_date' => time(), 'modified_by' => $author));
    }
    // }}}
    // {{{ getNumberOfUsers
    public function getNumberOfUsers($row)
    {
        return $this->db->select('count(username) as count')->from($this->tableNameUsersGroups)->where('group_id', $row['id'])->get()->current()->count;
    }
    // }}}
    // {{{ isExpired
    public function isExpired($group_id)
    {
        $expiration_date = $this->db->select('expire_date')->from($this->tableNameGroups)->where('id', $group_id)->get(False)->current()->expire_date;
        if ( $expiration_date == 0 ) {
            return False;
        } else {
            return $expiration_date <= time();
        }
    }
    // }}}
}
