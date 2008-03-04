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
    function __construct()
    {
        parent::__construct(new Database('default'));

        // Set table name
        $this->tableNameProfiles  = 'user_profiles';
    }
    // }}}
    // {{{ editProfile
    public function editProfile($province, $city, $address, $phone, $cellphone, $postal_code, $username, $name, $lastname)
    {
        $row = Array(
                     'province'    => $province,
                     'city'        => $city,
                     'address'     => $address,
                     'phone'       => $phone,
                     'cellphone'   => $cellphone,
                     'postal_code' => $postal_code,
                     'name'        => $name,
                     'lastname'    => $lastname
                    );
        $this->db->where('username', $username);
        $this->db->where('master_profile', 1);
        $this->db->update($this->tableNameProfiles, $row);
    }
    // }}}
    // {{{ insertProfile
    public function insertProfile($province, $city, $address, $phone, $cellphone, $postal_code, $username, $name, $lastname)
    {
        $row = Array(
                     'province'       => $province,
                     'city'           => $city,
                     'address'        => $address,
                     'phone'          => $phone,
                     'cellphone'      => $cellphone,
                     'postal_code'    => $postal_code,
                     'username'       => $username,
                     'master_profile' => 1,
                     'name'           => $name,
                     'lastname'       => $lastname
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
        $this->db->select('id, city, phone, pan, cellphone, province, address, postal_code');

        return (Array) $this->db->getwhere($this->tableNameProfiles, array('username' => $username))->current();     
    }
    // }}}
}

?>
