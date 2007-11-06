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
    
    public $tableNameApps;
    public $tableNameGroups;
    public $tableNameUsers;
    public $tableNameFilters;
    private $session;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // set tables' names
        $this->tableNameApps   = "user_applications";
        $this->tableNameGroups = "user_groups";
        $this->tableNameUsers  = "user_users";

        $this->session = Kohana::instance()->session;        
    }
    // }}}
    // {{{ & getAnonymousGroup
    public function & getAnonymousGroup($appname)
    {
        $this->db->select('name, appname, privileges, redirect');
        $this->db->from($this->tableNameGroups);
        $this->db->where('appname', $appname);
        $this->db->where('name', 'anonymous');

        $group = (Array) $this->db->getwhere()->current();

        if (!empty($group)) {
            $group['privileges'] = unserialize($group['privileges']);
        } else {

            // XXX: Okay, there is no anonymous group for this appname so just construct a Null
            //      anonymous group
            $group['name']       = 'anonymous';
            $group['appname']    = $appname;
            $group['privileges'] = Null;
            $group['redirect']   = False;
        }

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
    // {{{ & getGroup
    public function & getGroup($id = NULL, $appname = NULL, $groupname = NULL)
    {
        $this->db->select('*');
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
        return date('Y-m-d H:i:s', $row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return date('Y-m-d H:i:s', $row['modify_date']);
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

        $query = $this->db->get();
        $row   = $query->result(False);

        return $row;        
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
    public function newGroup($appname, $newgroup)
    {
        $row = Array('modified_by' => $this->session->get('username'), 
                     'create_date' => time(),
                     'modify_date' => time(),
                     'created_by'  => $this->session->get('username'),
                     'appname'     => $appname,
                     'name'        => $newgroup);

        $this->db->insert($this->tableNameGroups, $row);
    }
    // }}}
    // {{{ hasGroup
    public function hasGroup($name = NULL, $appname = NULL, $id = NULL)
    {
        $this->db->select('id');
        if ($id == NULL) {
            $query = $this->db->getwhere($this->tableNameGroups, Array('name'    => $name,
                                                                       'appname' => $appname)); 
        } else if ($appname == NULL) {
            $query = $this->db->getwhere($this->tableNameGroups, Array('id' => $id));
        } else {
            $query = $this->db->getwhere($this->tableNameGroups, Array('id'      => $id,
                                                                       'appname' => $appname));
        }
        return (boolean)$query->num_rows();
    }
    // }}}
    // {{{ deleteGroups
    public function deleteGroups($groups)
    {
        foreach ($groups as $group) {
            $this->db->delete($this->tableNameGroups, Array('id' => $group));
            
            $controller = Kohana::instance();
            $controller->load->model('Users', 'Users', 'user');

            $anonymous = $controller->Users->deleteUsers(NULL, $group);
        }
    }
    // }}}
    // {{{ changeModifiers
    public function changeModifiers($groupid)
    {
        $this->db->where('id', $groupid);
        $this->db->update($this->tableNameGroups, array('modify_date' => time(), 'modified_by' => $this->session->get('username')));
    }
    // }}}
}

?>
