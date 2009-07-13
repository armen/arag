<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class UserProfile_Model extends Model
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set table name
        $this->tableNameProfiles  = 'user_profiles';
    }
    // }}}
    // {{{ editProfile
    public function editProfile($address, $phone, $cellphone, $postal_code, $username, $location)
    {
        $row = Array(
                     'address'     => $address,
                     'phone'       => $phone,
                     'cellphone'   => $cellphone,
                     'postal_code' => $postal_code,
                     'location'    => $location
                    );
        $this->db->where('username', $username);
        $this->db->where('master_profile', 1);
        $this->db->update($this->tableNameProfiles, $row);
    }
    // }}}
    // {{{ insertProfile
    public function insertProfile($address, $phone, $cellphone, $postal_code, $username, $name, $lastname, $location)
    {
        $row = Array(
                     'address'        => $address,
                     'phone'          => $phone,
                     'cellphone'      => $cellphone,
                     'postal_code'    => $postal_code,
                     'username'       => $username,
                     'master_profile' => 1,
                     'name'           => $name,
                     'lastname'       => $lastname,
                     'location'       => $location
                    );

        $this->db->insert($this->tableNameProfiles, $row);
    }
    // }}}
    // {{{ hasUserName
    public function hasUserName($username)
    {
        $result = $this->db->select('count(username) as count')->getwhere($this->tableNameProfiles, Array('username' => $username))->current();
        return (boolean) $result->count;
    }
    // }}}
    // {{{ getProfile
    public function getProfile($username)
    {
        $this->db->select('id, phone, pan, cellphone, address, postal_code, location');

        return (Array) $this->db->getwhere($this->tableNameProfiles, array('username' => $username))->current();
    }
    // }}}
}
