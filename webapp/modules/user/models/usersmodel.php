<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Authors: Armen Baghumian <armen@OpenSourceClub.org                      |
// |          Sasan Rose <sasan.rose@gmail.com>                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class UsersModel extends Model 
{
    // {{{ Properties

    const USER_NOT_FOUND      = 0;
    const USER_OK             = 1;
    const USER_NOT_VERIFIED   = 2;
    const USER_BLOCKED        = 4;

    // }}}
    // {{{ Constructor
    function UsersModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // Set table name
        $this->tableNameUsers  = "user_users";
        $this->tableNameGroups = "user_groups";
        $this->tableNameApps   = "user_applications";
    }
    // }}}
    // {{{ check
    function check($username, $password, &$status = 0)
    {
        $this->db->select('username, verified, blocked');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));

        $query = $this->db->get($this->tableNameUsers);

        if ($query->num_rows() == 1) {

            $user   = $query->row();
            $status = self::USER_OK;

            // Check if user verified
            if (!$user->verified) {
                $status |= self::USER_NOT_VERIFIED;     // Add verfied to status
                $status &= ~self::USER_OK;              // Remove the USER_OK flag
            } 
            
            // Check if user blocked
            if ($user->blocked) {
                $status |= self::USER_BLOCKED;          // Add blocked to status
                $status &= ~self::USER_OK;              // Remove the USER_OK flag
            }
 
            //return (boolean)($status & self::USER_OK);  // Check if USER_OK flag is set
            return $status;
        }

        $status = self::USER_NOT_FOUND; // Status is 0
        return $status;
    }
    // }}}
    // {{{ & getUser
    function & getUser($username)
    {
        $this->db->select('appname, '.$this->db->dbprefix.$this->tableNameGroups.'.name as group_name, username, privileges, redirect,'.
                          $this->db->dbprefix.$this->tableNameUsers.'.name as name, lastname, email, group_id');
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameGroups, $this->tableNameGroups.'.id = '.$this->tableNameUsers.'.group_id');
        $this->db->where('username', $username);
        $this->db->where('verified', True);
        $this->db->where('blocked',  False);

        $user = $this->db->get()->row_array();

        if (isset($user['privileges'])) {
            $user['privileges'] = unserialize($user['privileges']);
        } else {    
            $user['privileges'] = Array();
        }

        return $user;
    }
    // }}}
    // {{{ & getAnonymousUser
    function & getAnonymouseUser($appname)
    {
        $CI =& get_instance();
        $CI->load->model(Array('GroupsModel', 'user'), 'Groups');

        $anonymous = $CI->Groups->getAnonymousGroup($appname);

        $anonymous['groupname']  = $anonymous['name'];
        $anonymous['username']   = $anonymous['name'];
        $anonymous['name']       = ucfirst($anonymous['name']);

        return $anonymous;
    }
    // }}}
    // {{{ & getUserProfile
    function & getUserProfile($username)
    {
        $this->db->select('*');
        $this->db->from($this->tableNameUsers);
        $this->db->where('username', $username);

        $userProfile = $this->db->get()->row_array();

        return $userProfile;        
    }
    // }}}
    // {{{ & getUsers
    function & getUsers($groupID = NULL, $appName, $groupName, $user)
    {
        $this->db->select('username, lastname, email, appname, id');
        $this->db->select($this->db->dbprefix.$this->tableNameGroups.".name as group_name");
        $this->db->select($this->db->dbprefix.$this->tableNameUsers.".name as user_name");
        $this->db->select($this->db->dbprefix.$this->tableNameUsers.".modify_date");
        $this->db->select($this->db->dbprefix.$this->tableNameUsers.".create_date");
        $this->db->select($this->db->dbprefix.$this->tableNameUsers.".modified_by");
        $this->db->select($this->db->dbprefix.$this->tableNameUsers.".created_by");
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameGroups, $this->tableNameGroups.".id = ".$this->tableNameUsers.".group_id");

        if ($groupID != NULL) {
            $this->db->where('group_id', $groupID);
        }

        if ($groupName != NULL) {
            $this->db->like($this->db->dbprefix.$this->tableNameGroups.".name", $groupName);
        }
        
        if ($appName != NULL) {
            $this->db->like('appname', $groupID);
        }

        if ($user != NULL) {
            $row = explode(" ", $user);
            foreach ($row as $tag) {
                $this->db->like('username', $tag);
                $this->db->orlike($this->db->dbprefix.$this->tableNameUsers.".name", $tag);
                $this->db->orlike('lastname', $tag);
            }
        }

        $this->db->orderby('appname, lastname, group_name asc');

        $retval = $this->db->get()->result_array();

        return $retval;
    }
    // }}}
    // {{{ createUser
    function createUser($appname, $email, $name, $lastname, $groupname, $username, $password)
    {
        $CI =& get_instance();
        $CI->load->model(Array('GroupsModel', 'user'), 'Groups');

        $group = $CI->Groups->getGroup(NULL, $appname, $groupname);
        
        $row = Array('username'    => $username, 
                     'create_date' => time(),
                     'modify_date' => time(),
                     'modified_by' => $this->session->userdata('username'),
                     'created_by'  => $this->session->userdata('username'),
                     'name'        => $name,
                     'lastname'    => $lastname,
                     'group_id'    => $group['id'],
                     'email'       => $email,
                     'password'    => sha1($password),
                     'verified'    => 1);
        
        $CI->Groups->changeModifiers($group['id']);
        $this->db->insert($this->tableNameUsers, $row);
    }
    // }}}
    // {{{ editUser
    function editUser($appname, $email, $name, $lastname, $groupname, $username, $password = "", $blocked)
    {
        $CI =& get_instance();
        $CI->load->model(Array('GroupsModel', 'user'), 'Groups');

        $group = $CI->Groups->getGroup(NULL, $appname, $groupname);

        if ($blocked) {
            $blocked = 1;
        } else {
            $blocked = 0;
        }
        
        $row = Array('username'    => $username, 
                     'create_date' => time(),
                     'modify_date' => time(),
                     'modified_by' => $this->session->userdata('username'),
                     'name'        => $name,
                     'lastname'    => $lastname,
                     'group_id'    => $group['id'],
                     'email'       => $email,
                     'blocked'     => $blocked);

        if ($password != "") {
            $row['password'] = sha1($password);
        }

        $CI->Groups->changeModifiers($group['id']);
        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $row);
    }
    // }}}
    // {{{ hasUserName
    function hasUserName($username)
    {
        $this->db->select('username');
        $query = $this->db->getwhere($this->tableNameUsers, Array('username' => $username)); 
        return (boolean)$query->num_rows();
    }
    // }}}
    // {{{ deleteUsers
    function deleteUsers($usernames = NULL, $groupid = NULL)
    {   
        if ($groupid == NULL) {
            $CI =& get_instance();
            $CI->load->model(Array('GroupsModel', 'user'), 'Groups');
            foreach ($usernames as $username) {
                $row = $this->getUserProfile($username);
                $CI->Groups->changeModifiers($row['group_id']);
                $this->db->delete($this->tableNameUsers, Array('username' => $username));
            }
        } else {
            $this->db->delete($this->tableNameUsers, Array('group_id' => $groupid));
        }
    }
    // }}}
    //{{{ isBlock
    function isBlock($row)
    {
        return boolean($row['block']);
    }
    //}}}
}

?>
