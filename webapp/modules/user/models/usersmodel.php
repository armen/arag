<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org                       |
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
 
            return (boolean)($status & self::USER_OK);  // Check if USER_OK flag is set
        }

        $status = self::USER_NOT_FOUND; // Status is 0
        return False;
    }
    // }}}
    // {{{ & getUser
    function & getUser($username)
    {
        $this->db->select('appname, '.$this->db->dbprefix.$this->tableNameGroups.'.name as group_name, username, privileges, redirect,'.
                          $this->db->dbprefix.$this->tableNameUsers.'.name as name, lastname, email');
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameGroups, $this->tableNameGroups.'.id = '.$this->tableNameUsers.'.group_id');
        $this->db->where('username', $username);
        $this->db->where('verified', True);
        $this->db->where('blocked',  False);

        $user = $this->db->get()->row_array();

        $user['privileges'] = unserialize($user['privileges']);

        return $user;
    }
    // }}}
    // {{{ & getAnonymousUser
    function & getAnonymouseUser($appname)
    {
        $CI =& get_instance();
        $CI->load->model(Array('GroupsModel', 'user'), 'Groups');

        $anonymous = $CI->Groups->getAnonymousGroup($appname);

        $anonymous['groupname'] = $anonymous['name'];
        $anonymous['username']  = $anonymous['name'];
        $anonymous['name']      = ucfirst($anonymous['name']);
        
        return $anonymous;
    }
    // }}}
    // {{{ getUserProfile
    function getUserProfile($username)
    {
        // XXX: Not implemented but it should same as getUser
    }
    // }}}
}

?>
