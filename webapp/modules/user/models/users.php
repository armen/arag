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
    const USER_INCORRECT_PASS = 8;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct(new Database('default'));

        // Set table name
        $this->tableNameUsers  = 'user_users';
        $this->tableNameGroups = 'user_groups';
        $this->tableNameApps   = 'user_applications';
    }
    // }}}
    // {{{ check
    public function check($username, $password, &$status = 0, $expiretime = 0)
    {
        $this->db->select('username');
        $this->db->where('username', $username);

        $query = $this->db->get($this->tableNameUsers);
        $user  = $query->current();

        // Don't believe the truth! double check it ;)
        if (count($query) == 1 && $username === $user->username) {
            
            $this->db->select('username, password, verified, blocked, block_date');
            $this->db->where('username', $username);           
            $this->db->where('password', sha1($password));

            $query = $this->db->get($this->tableNameUsers);           
            $user  = $query->current();           

            $status = self::USER_OK;           

            if (count($query) != 1 || sha1($password) !== $user->password) {
                $status |= self::USER_INCORRECT_PASS;
                $status &= ~self::USER_OK;
            } else {

                // Check if user verified
                if (!$user->verified) {
                    $status |= self::USER_NOT_VERIFIED;     // Add verfied to status
                    $status &= ~self::USER_OK;              // Remove the USER_OK flag
                } 
                
                // Check if user blocked
                if ($user->blocked) {
                    if ($user->block_date == 0 || ($expiretime > (time()-$user->block_date))) {
                        $status |= self::USER_BLOCKED;          // Add blocked to status
                        $status &= ~self::USER_OK;              // Remove the USER_OK flag
                    }
                }

            }
 
            return (boolean)($status & self::USER_OK);  // Check if USER_OK flag is set
        }

        $status = self::USER_NOT_FOUND; // Status is 0
        return (boolean)$status;
    }
    // }}}
    // {{{ checkVerify
    public function checkVerify($username, $password, $uri, &$status, $expiretime)
    {
        $this->db->select('username');
        $this->db->where('username', $username);

        $query = $this->db->get($this->tableNameUsers); 
        $user  = $query->current();

        if (count($query) == 1 && $username === $user->username) {

            $this->db->select('username, password, verify_string, blocked, block_date');
            $this->db->where('username', $username);
            $this->db->where('password', sha1($password));
            $this->db->where('verify_string', $uri); 
                    
            if (Arag_Config::get('expire', 0) != 0) {
                $this->db->where('expire_date >',  time()+Arag_Config::get('expire', 0));
            }

            $query = $this->db->get($this->tableNameUsers); 
            $user  = $query->current();

            $status = self::USER_OK;    

            if (count($query) != 1 || sha1($password) !== $user->password || $uri !== $user->verify_string) {
                $status |= self::USER_INCORRECT_PASS;
                $status &= ~self::USER_OK;
            } else if ($user->blocked) {
                if ($user->block_date == 0 || ($expiretime > (time()-$user->block_date))) {
                    $status |= self::USER_BLOCKED;          // Add blocked to status
                    $status &= ~self::USER_OK;              // Remove the USER_OK flag
                }
            }

            return (boolean)($status & self::USER_OK);  // Check if USER_OK flag is set
        }

        $status = self::USER_NOT_FOUND; // Status is 0
        return (boolean)$status;      
    }
    // }}}   
    // {{{ & getUser
    public function & getUser($username)
    {
        $this->db->select('appname, '.$this->tableNameGroups.'.name as groupname, username, privileges, redirect,'.
                          $this->tableNameUsers.'.name as name, lastname, email, group_id');
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameGroups, $this->tableNameGroups.'.id', $this->tableNameUsers.'.group_id');
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
        // Model::load(...); here
        $groups    = Model::load('Groups', 'user');
        $anonymous = $groups->getAnonymousGroup($appname);

        $anonymous['groupname'] = $anonymous['name'];
        $anonymous['username']  = $anonymous['name'];
        $anonymous['name']      = ucfirst($anonymous['name']);

        return $anonymous;
    }
    // }}}
    // {{{ & getUserProfile
    public function & getUserProfile($username)
    {
        $this->db->select('username, name, lastname, password, create_date, created_by, modify_date, modified_by, blocked, block_date, profile_id,
                           email, group_id');
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
        $this->db->select($this->tableNameGroups.".name as group_name");
        $this->db->select($this->tableNameUsers.".name as user_name");
        $this->db->select($this->tableNameUsers.".modify_date");
        $this->db->select($this->tableNameUsers.".create_date");
        $this->db->select($this->tableNameUsers.".modified_by");
        $this->db->select($this->tableNameUsers.".created_by");
        $this->db->join($this->tableNameGroups, $this->tableNameGroups.'.id', $this->tableNameUsers.'.group_id');

        if ($groupID != NULL) {
            $this->db->where('group_id', $groupID);
        }

        if ($groupName != NULL) {
            $this->db->like($this->tableNameGroups.".name", $groupName);
        }
        
        if ($user != NULL) {
            $row = explode(" ", $user);
            foreach ($row as $tag) {
                $this->db->like('(username', $tag);
                $this->db->orlike($this->tableNameUsers.".name", $tag);
                $this->db->orlike('lastname)', $tag);
            }
        }

        if ($appName != NULL) {
            if ($flagappname) {
                $this->db->like('appname', $appName);
            } else {
                $this->db->where('appname', $appName);               
            }
        }

        $this->db->orderby('appname', 'ASC');
        $this->db->orderby('lastname', 'ASC');
        $this->db->orderby('group_name', 'ASC');
        
        $retval = $this->db->get($this->tableNameUsers)->result(False);

        return $retval;
    }
    // }}}
    // {{{ createUser
    public function createUser($appname, $email, $name, $lastname, $groupname, $username, $password, $author, $verify_string = NULL, $verified =  1)
    {
        $controller = new Groups_Model;

        $group = $controller->getGroup(NULL, $appname, $groupname);
        
        $row = Array('username'      => $username, 
                     'create_date'   => time(),
                     'modify_date'   => time(),
                     'expire_date'   => time(),
                     'modified_by'   => $author,
                     'created_by'    => $author,
                     'name'          => $name,
                     'lastname'      => $lastname,
                     'group_id'      => $group['id'],
                     'email'         => $email,
                     'password'      => sha1($password),
                     'verified'      => $verified,
                     'verify_string' => $verify_string);
        
        $controller->changeModifiers($group['id'], $author);
        $this->db->insert($this->tableNameUsers, $row);
    }
    // }}}
    // {{{ editUser
    public function editUser($appname, $email, $name, $lastname, $groupname, $username, $password = "", $blocked, $author)
    {
        $controller = new Groups_Model;

        $group = $controller->getGroup(NULL, $appname, $groupname);

        if ($blocked) {
            $blocked = 1;
        } else {
            $blocked = 0;
        }
        
        $row = Array(
                     'modify_date'   => time(),
                     'modified_by'   => $author,
                     'name'          => $name,
                     'lastname'      => $lastname,
                     'group_id'      => $group['id'],
                     'email'         => $email,
                     'blocked'       => $blocked
                    );

        if ($password != "") {
            $row['password'] = sha1($password);
        }

        $controller->changeModifiers($group['id'], $author);
        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $row);
    }
    // }}}
    // {{{ hasUserName
    public function hasUserName($username, $appname = NULL)
    {
        if ($appname == NULL) {
            
            $result = $this->db->select('count(username) as count')->getwhere($this->tableNameUsers, Array('username' => $username))->current(); 
            return (boolean)$result->count;

        } else {

            $this->db->select('count(username) as count');
            $this->db->from($this->tableNameUsers);
            $this->db->join($this->tableNameGroups, $this->tableNameGroups.'.id', $this->tableNameUsers.'.group_id');           
            $this->db->where('username', $username);
            $this->db->where('appname', $appname);
            $result = $this->db->get()->current();

            return (boolean)$result->count;
        }
    }
    // }}}
    // {{{ deleteUsers
    public function deleteUsers($usernames = NULL, $groupid = NULL, $author)
    {   
        if ($groupid == NULL) {
            $controller = new Groups_Model;
            foreach ($usernames as $username) {
                $row = $this->getUserProfile($username);
                $controller->changeModifiers($row['group_id'], $author);
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
    // {{{ expireDate
    public function expireDate($verify_string)
    {
        $this->db->select('expire_date');
        return $this->db->getwhere($this->tableNameUsers, array('verify_string' => $verify_string))->current()->expire_date;
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
    // {{{ checkEmail
    public function checkEmail($email, $username = NULL, $verify_string = NULL, $verified = 1)
    {
        $this->db->select('username, email');
        $this->db->where('email', $email);
        $this->db->where('verified', $verified);
        $this->db->where('blocked', 0);

        if ($username != NULL) {
            $this->db->where('username', $username);
        }

        if ($verify_string != NULL) {
            $this->db->where('verify_string', $verify_string);
            if (Arag_Config::get('expire', 0) != 0) {
                $this->db->where('expire_date >',  time()+Arag_Config::get('expire', 0));
            }
        }

        $query = $this->db->get($this->tableNameUsers);
        $user  = (array) $query->current();

        $user['status'] = self::USER_OK;

        // Don't believe the truth! double check it ;)
        if (count($query) != 1 || $email !== $user['email']) {
            $user['status'] = self::USER_NOT_FOUND;
        }

        return $user;
    }
    // }}}
    // {{{ hasUri
    public function hasUri($verify_uri, $verified = 0)
    {
        $result = $this->db->select('count(verify_string) as count')->getwhere($this->tableNameUsers, Array(
                                                                                                            'verify_string' => $verify_uri,
                                                                                                            'verified'      => $verified
                                                                                                           ))->current(); 
        return (boolean)$result->count;
    }
    // }}}
    // {{{ changePassword
    public function changePassword($username, $verify_string = "", $password = NULL)
    {
        $this->db->where('username', $username);
        if ($verify_string !== "") {
            $this->db->update($this->tableNameUsers, array('verify_string' => $verify_string,
                                                           'expire_date'   => time()));
        }

        if ($password != NULL) {
            $this->db->update($this->tableNameUsers, array('password' => sha1($password)));
        }
    }
    // }}}
    // {{{ getBlockInfo
    public function getBlockInfo ($username)
    {
        $this->db->select('block_date, block_counter');
        return $this->db->getwhere($this->tableNameUsers, array('username' => $username))->current();       
    }
    // }}}
    // {{{ blockUser
    public function blockUser($username, $block = 0, $counter = 0, $block_date = 0)
    {
        $rows = array (
                       'block_counter' => $counter,
                       'blocked'       => $block,
                       'block_date'    => $block_date
                      );

        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $rows);
    }
    // }}}
}

?>
