<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class GroupsModel extends Model 
{
    // {{{ Properties
    
    var $tableNameApps;
    var $tableNameGroups;
    var $tableNameUsers;

    // }}}
    // {{{ Constructor
    function GroupsModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // set tables' names
        $this->tableNameApps   = "user_applications";
        $this->tableNameGroups = "user_groups";
        $this->tableNameUsers  = "user_users";
    }
    // }}}
    // {{{ getGroups
    function getGroups($appName = NULL)
    {
        $this->db->select('id, name, modified_by, modify_date, create_date, appname');
        $this->db->from($this->tableNameGroups);

        if ($appName != NULL) {
            $this->db->where('appname', $appName);
        }
        
        $this->db->orderby('id', 'asc');

        $query = $this->db->get();
        
        $retval = $query->result_array();
        return $retval;
    }
    // }}}
    // {{{ & getGroup
    function & getGroup($id = NULL, $appname = NULL, $groupname = NULL)
    {
        $this->db->select('*');
        $this->db->from($this->tableNameGroups);

        if ($id != NULL) {
            $this->db->where('id', $id);
        } else {
            $this->db->where(array('appname' => $appname,
                                   'name'    => $groupname));
        }


        $group = $this->db->getwhere()->row_array();

        return $group;
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
    // {{{ setGroup
    function setGroup($appName, $group)
    {
       $row = Array('default_group' => $group); 
       
       $this->db->where('name', $appName);
       $this->db->update($this->tableNameApps, $row);         
    }
    // }}}
    // {{{ defaultGroup
    function defaultGroup($appName)
    {
        $this->db->select('default_group');
        $this->db->from($this->tableNameApps);
        $this->db->where('name', $appName);

        $query = $this->db->get();
        $row   = $query->result_array();

        return $row;        
    }
    // }}}
    // {{{ allAppGroups
    function allAppGroups($appName)
    {
        $allgroups = array();
        
        $this->db->select('name');
        $this->db->from($this->tableNameGroups);
        $this->db->where('appname', $appName);
        
        $query = $this->db->get();
        
        foreach ($query->result_array() as $row) {
            array_push($allgroups, $row['name']);
        }

        return $allgroups;
    }
    // }}}
    // {{{ newGroup
    function newGroup($appname, $newgroup, $modifier)
    {
        $row = Array('modified_by' => $modifier, 
                     'create_date' => time(),
                     'modify_date' => time(),
                     'appname'     => $appname,
                     'name'        => $newgroup);

        $this->db->insert($this->tableNameGroups, $row);
    }
    // }}}
    // {{{ hasGroup
    function hasGroup($name = NULL, $appname = NULL, $id = NULL)
    {
        $this->db->select('id');
        if ($id == NULL) {
            $query = $this->db->getwhere($this->tableNameGroups, Array('name'    => $name,
                                                                       'appname' => $appname)); 
        } else {
            $query = $this->db->getwhere($this->tableNameGroups, Array('id' => $id));
        }
        return (boolean)$query->num_rows();
    }
    // }}}
}
?>
