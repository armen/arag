<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Authors: Armen Baghumian <armen@OpenSourceClub.org                      |
// |          Sasan Rose <sasan.rose@gmail.com>                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Users_Model extends Model 
{
    // {{{ Properties

    const USER_NOT_FOUND      = 0;
    const USER_OK             = 1;
    const USER_NOT_VERIFIED   = 2;
    const USER_BLOCKED        = 4;

    private $tablePrefix;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct('default');

        // Set table prefix
        $this->tablePrefix = $this->db->table_prefix();

        // Set table name
        $this->tableNameUsers  = "user_users";
        $this->tableNameGroups = "user_groups";
        $this->tableNameApps   = "user_applications";

    }
    // }}}
    // {{{ check
    public function check($username, $password, &$status = 0)
    {
        $this->db->select('username, password, verified, blocked');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));

        $query = $this->db->get($this->tableNameUsers);
        $user  = $query->current();

        // Don't believe the truth! double check it ;)
        if ($query->num_rows() == 1 && $username === $user->username && sha1($password) === $user->password) {

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
        return (boolean)$status;
    }
    // }}}
    // {{{ checkVerify
    public function checkVerify($username, $password, $uri)
    {
        $this->db->select('username, password, verify_string');
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));
        $this->db->where('verify_string', $uri);

        $query = $this->db->get($this->tableNameUsers); 
        $user  = $query->current();

        if ($query->num_rows() == 1 && $username === $user->username && 
            sha1($password) === $user->password && $uri === $user->verify_string) {
            return True;
        }

        return False;        
    }
    // }}}   
    // {{{ & getUser
    public function & getUser($username)
    {
        $this->db->select('appname, '.$this->tablePrefix.$this->tableNameGroups.'.name as group_name, username, privileges, redirect,'.
                          $this->tablePrefix.$this->tableNameUsers.'.name as name, lastname, email, group_id');
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameGroups, $this->tablePrefix.$this->tableNameGroups.'.id = '.$this->tablePrefix.$this->tableNameUsers.'.group_id');
        $this->db->where('username', $username);
        $this->db->where('verified', True);
        $this->db->where('blocked',  False);

        $user = (Array) $this->db->get()->current();

        if (isset($user['privileges'])) {
            $user['privileges'] = unserialize($user['privileges']);
        } else {    
            $user['privileges'] = Array();
        }

        return $user;
    }
    // }}}
    // {{{ & getAnonymousUser
    public function & getAnonymouseUser($appname)
    {
        // This will called from Arag_Auth so do not use
        // Kohana::instance()->load->model(); here
        $groups    = Model::load('Groups', 'user');
        $anonymous = $groups->getAnonymousGroup($appname);

        $anonymous['groupname']  = $anonymous['name'];
        $anonymous['username']   = $anonymous['name'];
        $anonymous['name']       = ucfirst($anonymous['name']);

        return $anonymous;
    }
    // }}}
    // {{{ & getUserProfile
    public function & getUserProfile($username)
    {
        $this->db->select('username, name, lastname, password, create_date, created_by, modify_date, modified_by, blocked, profile_id, email, group_id');
        $this->db->from($this->tableNameUsers);
        $this->db->where('username', $username);

        $userProfile = (Array) $this->db->get()->current();

        return $userProfile;        
    }
    // }}}
    // {{{ & getUsers
    public function & getUsers($groupID = NULL, $appName, $groupName, $user, $flagappname)
    {
        $this->db->select('username, lastname, email, appname, id');
        $this->db->select($this->tablePrefix.$this->tableNameGroups.".name as group_name");
        $this->db->select($this->tablePrefix.$this->tableNameUsers.".name as user_name");
        $this->db->select($this->tablePrefix.$this->tableNameUsers.".modify_date");
        $this->db->select($this->tablePrefix.$this->tableNameUsers.".create_date");
        $this->db->select($this->tablePrefix.$this->tableNameUsers.".modified_by");
        $this->db->select($this->tablePrefix.$this->tableNameUsers.".created_by");
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameGroups, $this->tablePrefix.$this->tableNameGroups.".id = ".$this->tablePrefix.$this->tableNameUsers.".group_id");

        if ($groupID != NULL) {
            $this->db->where('group_id', $groupID);
        }

        if ($groupName != NULL) {
            $this->db->like($this->tablePrefix.$this->tableNameGroups.".name", $groupName);
        }
        
        if ($appName != NULL) {
            if ($flagappname) {
                $this->db->like('appname', $appName);
            } else {
                $this->db->where('appname', $appName);
            }
        }

        if ($user != NULL) {
            $row = explode(" ", $user);
            foreach ($row as $tag) {
                $this->db->like('username', $tag);
                $this->db->orlike($this->tablePrefix.$this->tableNameUsers.".name", $tag);
                $this->db->orlike('lastname', $tag);
            }
        }

        $this->db->orderby('appname', 'ASC');
        $this->db->orderby('lastname', 'ASC');
        $this->db->orderby('group_name', 'ASC');

        $retval = $this->db->get()->result(False);

        return $retval;
    }
    // }}}
    // {{{ createUser
    public function createUser($appname, $email, $name, $lastname, $groupname, $username, $password, $author, $verify_string = NULL, $verified =  1)
    {
        $controller = Kohana::instance();
        $controller->load->model('Groups', 'Groups', 'user');

        $group = $controller->Groups->getGroup(NULL, $appname, $groupname);
        
        $row = Array('username'      => $username, 
                     'create_date'   => time(),
                     'modify_date'   => time(),
                     'modified_by'   => $author,
                     'created_by'    => $author,
                     'name'          => $name,
                     'lastname'      => $lastname,
                     'group_id'      => $group['id'],
                     'email'         => $email,
                     'password'      => sha1($password),
                     'verified'      => $verified,
                     'verify_string' => $verify_string);
        
        $controller->Groups->changeModifiers($group['id'], $author);
        $this->db->insert($this->tableNameUsers, $row);
    }
    // }}}
    // {{{ editUser
    public function editUser($appname, $email, $name, $lastname, $groupname, $username, $password = "", $blocked, $author)
    {
        $controller = Kohana::instance();
        $controller->load->model('Groups', 'Groups', 'user');

        $group = $controller->Groups->getGroup(NULL, $appname, $groupname);

        if ($blocked) {
            $blocked = 1;
        } else {
            $blocked = 0;
        }
        
        $row = Array('create_date' => time(),
                     'modify_date' => time(),
                     'modified_by' => $author,
                     'name'        => $name,
                     'lastname'    => $lastname,
                     'group_id'    => $group['id'],
                     'email'       => $email,
                     'blocked'     => $blocked);

        if ($password != "") {
            $row['password'] = sha1($password);
        }

        $controller->Groups->changeModifiers($group['id'], $author);
        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $row);
    }
    // }}}
    // {{{ hasUserName
    public function hasUserName($username, $appname = NULL)
    {
        if ($appname == NULL) {
            $this->db->select('username');
            $query = $this->db->getwhere($this->tableNameUsers, Array('username' => $username)); 
            return (boolean)$query->num_rows();
        } else {
            $this->db->select('username');
            $this->db->from($this->tableNameUsers);
            $this->db->join($this->tableNameGroups, $this->tablePrefix.$this->tableNameGroups.".id = ".
                            $this->tablePrefix.$this->tableNameUsers.".group_id");           
            $this->db->where('username', $username);
            $this->db->where('appname', $appname);
            $query = $this->db->get();
            return (boolean)$query->num_rows();
        }
    }
    // }}}
    // {{{ deleteUsers
    public function deleteUsers($usernames = NULL, $groupid = NULL, $author)
    {   
        if ($groupid == NULL) {
            $controller = Kohana::instance();
            $controller->load->model('Groups', 'Groups', 'user');
            foreach ($usernames as $username) {
                $row = $this->getUserProfile($username);
                $controller->Groups->changeModifiers($row['group_id'], $author);
                $this->db->delete($this->tableNameUsers, Array('username' => $username));
            }
        } else {
            $this->db->delete($this->tableNameUsers, Array('group_id' => $groupid));
        }
    }
    // }}}
    //{{{ isBlock
    public function isBlock($row)
    {
        return boolean($row['block']);
    }
    //}}}
    // {{{ createDate
    function createDate($verify_string)
    {
        $this->db->select('create_date');
        return $this->db->getwhere($this->tableNameUsers, array('verify_string' => $verify_string))->current()->create_date;
    }
    // }}}
    // {{{ verify
    public function verify($username, $password, $uri)
    {
        $this->db->where('username', $username);
        $this->db->where('password', sha1($password));
        $this->db->where('verify_string', $uri);       

        $rows = array (
                       'verify_string' => NULL,
                       'verified'      => 1
                      );

        $this->db->update($this->tableNameUsers, $rows);
    }
    // }}}
    // {{{ hasUri
    public function hasUri($verify_uri)
    {
        $this->db->select('verify_string');
        $query = $this->db->getwhere($this->tableNameUsers, Array('verify_string' => $verify_uri)); 
        return (boolean)$query->num_rows();
    }
    // }}}
}

?>
