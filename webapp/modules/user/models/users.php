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
    const USER_GROUP_EXPIRED  = 16;

    private $tableNameUsers       = 'user_users';
    private $tableNameGroups      = 'user_groups';
    private $tableNameApps        = 'user_applications';
    private $tableNameUsersGroups = 'user_users_groups';

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ check
    public function check($username, $password, &$status = 0, $expiretime = 0, $plain = True)
    {
	    if ($plain) {
            $password = sha1($password);
	    }

        $this->db->select('username, verified, blocked, block_date');
        $this->db->where('username', $username);

        $query = $this->db->get($this->tableNameUsers);
        $user  = $query->current();

        // Don't believe the truth! double check it ;)
        if (count($query) == 1 && $username === $user->username) {

            $this->db->select('username, password, verified, blocked, block_date');
            $this->db->where('username', $username);
            $this->db->where('password', $password);

            $query      = $this->db->get($this->tableNameUsers);
            $secureUser = $query->current();

            $status = self::USER_OK;

            if (count($query) != 1 || $password !== $secureUser->password) {
                $status |= self::USER_INCORRECT_PASS;
                $status &= ~self::USER_OK;
            }

            // Check if user verified
            if (!$user->verified) {
                $status |= self::USER_NOT_VERIFIED;     // Add verfied to status
                $status &= ~self::USER_INCORRECT_PASS;  // Do not show incorrect password message
                $status &= ~self::USER_OK;              // Remove the USER_OK flag
            }

            // Check if user blocked
            if ($user->blocked) {
                if ($user->block_date == 0 || ($expiretime > (time() - $user->block_date))) {
                    $status |= self::USER_BLOCKED;          // Add blocked to status
                    $status &= ~self::USER_INCORRECT_PASS;  // Do not show incorrect password message
                    $status &= ~self::USER_OK;              // Remove the USER_OK flag
                }
            }

            $result = (boolean)($status & self::USER_OK);  // Check if USER_OK flag is set

            if ($result) {
                $profile = $this->getUser($user->username);

                if (isset($profile['group_id'])) {
                    $group   = $profile['group_id'];
                    $groups  = Model::load('Groups', 'user');

                    if ($groups->isExpired($group)) {
                        $status |= self::USER_GROUP_EXPIRED;
                        $status &= ~self::USER_OK;
                        return False;
                    }
                }

                return $status;
            }
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
    public function & getUser($username, $appname = APPNAME, $verified = True, $blocked = False)
    {
        if ($appname) {
            $this->db->select('appname, '.$this->tableNameGroups.'.name as groupname, '.$this->tableNameUsers.'.username, privileges, redirect,'.
                              $this->tableNameUsers.'.name as name, lastname, email, '.$this->tableNameUsersGroups.'.group_id');
            $this->db->from($this->tableNameUsers);
            $this->db->join(Array($this->tableNameUsersGroups, $this->tableNameGroups), Array($this->tableNameUsers.'.username' => $this->tableNameUsersGroups.'.username',
                                                                                              $this->tableNameGroups.'.id' => $this->tableNameUsersGroups.'.group_id'));
            $this->db->where($this->tableNameUsers.'.username', $username);
            ($verified !== Null) AND $this->db->where('verified', $verified);
            ($blocked !== Null) AND $this->db->where('blocked',  $blocked);
            $this->db->where('appname',  $appname);

            $users = $this->db->get()->result_array(False);

            if (empty($users)) {
                $user = array(False);
            } else {
                foreach ($users as $usr) {
                    if (empty($user)) {
                        $user               = $usr;
                        $user['groupname']  = Array();
                        $user['group_id']   = Array();
                        if (isset($user['privileges'])) {
                            $user['privileges'] = unserialize($user['privileges']);
                        } else {
                            $user['privileges'] = Array();
                        }
                    }
                    $user['groupname'][] = $usr['groupname'];
                    $user['group_id'][]  = $usr['group_id'];
                    $user['privileges']  = array_merge($user['privileges'], isset($usr['privileges']) ? unserialize($usr['privileges']) : Array());

                }

                $user['groupname']  = array_unique($user['groupname']);
                $user['group_id']   = array_unique($user['group_id']);
                $user['privileges'] = array_unique($user['privileges']);

            }


            // Save privilege grouped by application name
            $privileges                   = isset($user['privileges']) ? $user['privileges'] : Array();
            $user['privileges']           = Null;
            $user['privileges'][$appname] = $privileges;

        } else {

            // There is no appname so just fetch user informations
            $this->db->select('username, name, lastname, email');
            $this->db->from($this->tableNameUsers);
            $this->db->where('username', $username);
            ($verified !== Null) AND $this->db->where('verified', $verified);
            ($blocked !== Null) AND $this->db->where('blocked',  $blocked);

            $user = (Array) $this->db->get()->current();
        }

        return $user;
    }
    // }}}
    // {{{ & getAnonymousUser
    public function & getAnonymousUser($appname, $defaultGroup = False)
    {
        // This will called from Arag_Auth so do not use
        // Model::load(...); here
        $groups = Model::load('Groups', 'user');
        $group  = $groups->getAnonymousGroup($appname, $defaultGroup);

        $group['groupname'] = $group['name'];

        if ($group['name'] == 'anonymous') {
            $group['username'] = $group['name'];
            $group['name']     = ucfirst($group['name']);
        }

        return $group;
    }
    // }}}
    // {{{ & getUserProfile
    public function & getUserProfile($username)
    {
        $this->db->select($this->tableNameUsers.'.username, name, lastname, password, create_date, created_by, modify_date, modified_by, blocked, block_date, profile_id,
                           email, verified, '.$this->tableNameUsersGroups.'.group_id');
        $this->db->from($this->tableNameUsers);
        $this->db->join($this->tableNameUsersGroups, $this->tableNameUsers.'.username', $this->tableNameUsersGroups.'.username');
        $this->db->where($this->tableNameUsers.'.username', $username);

        $userProfiles = $this->db->get()->result_array(False);

        if (!empty($userProfiles)) {
            foreach ($userProfiles as $profile) {
                if (empty($userProfile)) {
                    $userProfile             = $profile;
                    $userProfile['group_id'] = Array();
                }

                $userProfile['group_id'][] = $profile['group_id'];
            }

            $userProfile['group_id'] = array_unique($userProfile['group_id']);
        }

        return $userProfile;
    }
    // }}}
    // {{{ & getUsers
    public function & getUsers($groupID = NULL, $appName = Null, $groupName = Null, $user = Null, $flagappname = True, $email = Null, $is_blocked = Null, $is_not_verified = Null)
    {
        $this->db->select('distinct '.$this->tableNameUsers.'.username, lastname, email, id');
        $this->db->select($this->tableNameUsers.".name as user_name");
        $this->db->select($this->tableNameUsers.".modify_date");
        $this->db->select($this->tableNameUsers.".create_date");
        $this->db->select($this->tableNameUsers.".modified_by");
        $this->db->select($this->tableNameUsers.".created_by");
        $this->db->join(Array($this->tableNameUsersGroups, $this->tableNameGroups), Array($this->tableNameUsers.'.username' => $this->tableNameUsersGroups.'.username',
                                                                                          $this->tableNameGroups.'.id' => $this->tableNameUsersGroups.'.group_id'));
        if ($groupID != NULL) {
            $this->db->where($this->tableNameGroups.'.id', $groupID);
        }

        if ($groupName != NULL) {
            $this->db->like($this->tableNameGroups.".name", $groupName);
        }

        if ($user != NULL) {
            $row = explode(" ", $user);
            foreach ($row as $tag) {
                $this->db->like($this->tableNameUsers.'.username', $tag);
                /* This will be commented until pranthises support implements
                $this->db->orlike($this->tableNameUsers.".name", $tag);
                $this->db->orlike('lastname', $tag);*/
            }
        }

        if ($appName != NULL) {

            $this->db->select($this->tableNameGroups.".appname as appname");
            $this->db->select($this->tableNameGroups.".name as groupname");

            if ($flagappname) {
                $this->db->like('appname', $appName);
            } else {
                $this->db->where('appname', $appName);
            }
        }

        if ($email != NULL) {
            $this->db->like('email', $email);
        }

        if ($is_blocked != Null) {
            $this->db->where('blocked', 1);
        }

        if ($is_not_verified != Null) {
            $this->db->where('verified', 0);
        }

        $this->db->orderby('appname', 'ASC');
        $this->db->orderby('lastname', 'ASC');
        $this->db->groupby('username');

        $retval = $this->db->get($this->tableNameUsers)->result_array(False);

        return $retval;
    }
    // }}}
    // {{{ createUser
    public function createUser($appname, $email, $name, $lastname, $groupname, $username, $password, $author, $verify_string = NULL, $verified =  1)
    {
        $groups = new Groups_Model;
        $group  = $groups->getGroup(NULL, $appname, $groupname);
        $row    = Array(
                         'username'      => $username,
                         'create_date'   => time(),
                         'modify_date'   => time(),
                         'expire_date'   => time(),
                         'modified_by'   => $author,
                         'created_by'    => $author,
                         'name'          => $name,
                         'lastname'      => $lastname,
                         'email'         => $email,
                         'password'      => sha1($password),
                         'verified'      => $verified,
                         'verify_string' => $verify_string
                       );

        $groups->changeModifiers($group['id'], $author);
        $this->db->insert($this->tableNameUsers, $row);
        $this->db->insert($this->tableNameUsersGroups, Array('username' => $username, 'group_id' => $group['id']));
    }
    // }}}
    // {{{ editUser
    public function editUser($appname, $email, $name, $lastname, $groups, $username, $password = "", $blocked, $author, $verified)
    {
        $groups = is_array($groups) ? array_unique($groups) : array($groups);

        $row = Array(
                     'modify_date' => time(),
                     'modified_by' => $author,
                     'name'        => $name,
                     'lastname'    => $lastname,
                     'email'       => $email,
                     'blocked'     => (int) $blocked
                    );

        if ($verified) {
            $row['verified']      = 1;
            $row['verify_string'] = Null;
        }

        if ($password != "") {
            $row['password'] = sha1($password);
        }

        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $row);

        $groupsMan = new Groups_Model;

        $selectedGroups = Array();

        $userGroups = $groupsMan->getUserGroups($username);

        foreach ($groups as $groupname) {
            $group  = $groupsMan->getGroup(NULL, $appname, $groupname);

            $selectedGroups[] = $group['id'];

            if (!$groupsMan->isInGroup($username, $group['id'])) {
                $groupsMan->changeModifiers($group['id'], $author);

                $this->db->insert($this->tableNameUsersGroups, array('group_id' => $group['id'], 'username' => $username));
            }
        }

        foreach ($userGroups as $userGroup) {
            if (!in_array($userGroup, $selectedGroups)) {
                $this->db->delete($this->tableNameUsersGroups, array('group_id' => $userGroup, 'username' => $username));
            }
        }
    }
    // }}}
    // {{{ hasUserName
    public function hasUserName($username, $appname = NULL, $verified = True, $blocked = False)
    {
        $user = $this->getUser($username, $appname, $verified, $blocked);

        return isset($user['username']);
    }
    // }}}
    // {{{ isRegistered
    public function isRegistered($username)
    {
        return (boolean) $this->db->select('count(username) as count')->getwhere($this->tableNameUsers, array('username' => $username))->current()->count;
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
                $this->db->delete($this->tableNameUsersGroups, Array('username' => $username));
            }
        } else {
            $rows = $this->db->select('username')->from($this->tableNameUsersGroups)->where('group_id', $groupid)->get()->result_array(False);

            foreach ($rows as $row) {
                $this->db->delete($this->tableNameUsers, Array('username' => $row['username']));
            }

            $this->db->delete($this->tableNameUsersGroups, Array('group_id' => $groupid));
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
    public function changePassword($username, $verify_string = "", $password = NULL, $sha1 = True)
    {
        $this->db->where('username', $username);
        if ($verify_string !== "") {
            $this->db->update($this->tableNameUsers, array('verify_string' => $verify_string,
                                                           'expire_date'   => time()));
        }

        if ($password != NULL) {
            $password = $sha1 ? sha1($password) : $password;
            $this->db->update($this->tableNameUsers, array('password' => $password));
        }
    }
    // }}}
    // {{{ getBlockInfo
    public function getBlockInfo ($username)
    {
        $this->db->select('blocked, block_date, block_counter');
        return $this->db->getwhere($this->tableNameUsers, array('username' => $username))->current();
    }
    // }}}
    // {{{ blockUser
    public function blockUser($username)
    {
        $rows = array (
                       'block_counter' => 0,
                       'blocked'       => True,
                       'block_date'    => time()
                      );

        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $rows);
    }
    // }}}
    // {{{ addUserBlockCounter
    public function addUserBlockCounter($username)
    {
        $user = $this->getBlockInfo($username);

        $rows = array (
                       'block_counter' => ++$user->block_counter,
                       'blocked'       => False,
                       'block_date'    => 0
                      );

        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $rows);
    }
    // }}}
    // {{{ unBlockUser
    public function unBlockUser($username)
    {
        $rows = array (
                        'block_counter' => 0,
                        'blocked'       => False,
                        'block_date'    => 0
                      );

        $this->db->where('username', $username);
        $this->db->update($this->tableNameUsers, $rows);
    }
    // }}}
    // {{{ getUserAppname
    public function getUserAppname($data)
    {
        $this->db->select('appname, '.$this->tableNameGroups.'.name as groupname');
        $this->db->from($this->tableNameUsers);
        $this->db->join(Array($this->tableNameUsersGroups, $this->tableNameGroups), Array($this->tableNameUsers.'.username' => $this->tableNameUsersGroups.'.username',
                                                                                          $this->tableNameGroups.'.id' => $this->tableNameUsersGroups.'.group_id'));
        $this->db->where($this->tableNameUsers.'.username', $data['username']);

        $rows     = $this->db->get()->result_array(False);
        $appnames = Array();

        foreach ($rows as $row) {
            $appnames[] = $row['appname'].'::'.$row['groupname'];
        }

        return !empty($appnames) ? implode(', ', $appnames) : '-';
    }
    // }}}
    // {{{ getUserGroups
    public function getUserGroups($username = Null, $appname = Array(), $include = True)
    {
        ($username === Null) AND $username = Session::instance()->get('user.username');
        !is_array($appname)  AND $appname = Array($appname);

        $this->db->select('appname, '.$this->tableNameGroups.'.name as groupname, '.$this->tableNameGroups.'.id as group_id');
        $this->db->from($this->tableNameUsers);
        $this->db->join(Array($this->tableNameUsersGroups, $this->tableNameGroups), Array($this->tableNameUsers.'.username' => $this->tableNameUsersGroups.'.username',
                                                                                          $this->tableNameGroups.'.id' => $this->tableNameUsersGroups.'.group_id'));
        $this->db->where($this->tableNameUsers.'.username', $username);

        if ($include && !empty($appname)) {
            $this->db->in('appname', $appname);
        } else if (!empty($appname)) {
            $this->db->notin('appname', $appname);
        }

        $rows     = $this->db->get()->result_array(False);
        $appnames = Array();

        foreach ($rows as $row) {
            $appnames[] = Array ('appname' => $row['appname'], 'groupname' => $row['groupname'], 'group_id' => $row['group_id']);
        }

        return $appnames;
    }
    // }}}
    // {{{ addUserToGroup
    public function addUserToGroup($username, $groupId = Null, $groupName = Null, $appName = Null)
    {
        if (!isset($groupId) && (!isset($groupName) && !isset($appName))) {
            return False;
        } elseif (isset($groupId)) {
            $groups = new Groups_Model;
            if (!$groups->isInGroup($username, $groupId)) {
                $this->db->insert($this->tableNameUsersGroups, array('username' => $username, 'group_id' => $groupId));
            } else {
                Return False;
            }
        } else {
            $groups = new Groups_Model;
            $group  = $groups->getGroup(NULL, $appName, $groupName);
            if (!$groups->isInGroup($username, $group['id'])) {
                $this->db->insert($this->tableNameUsersGroups, array('username' => $username, 'group_id' => $group['id']));
            } else {
                return False;
            }
        }

        return True;
    }
    // }}}
}
