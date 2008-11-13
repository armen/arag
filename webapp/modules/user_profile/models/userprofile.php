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
        $this->tableNameCities    = 'user_profiles_cities';
        $this->tableNameProvinces = 'user_profiles_provinces';
        $this->tableNameCountries = 'user_profiles_countries';
    }
    // }}}
    // {{{ editProfile
    public function editProfile($province, $city, $address, $phone, $cellphone, $postal_code, $username, $country)
    {
        if ($country != Kohana::config('config.default_country')) {
            $province = 0;
            $city     = 0;
        }

        $row = Array(
                     'province'    => $province,
                     'city'        => $city,
                     'address'     => $address,
                     'phone'       => $phone,
                     'cellphone'   => $cellphone,
                     'postal_code' => $postal_code,
                     'country'     => $country
                    );
        $this->db->where('username', $username);
        $this->db->where('master_profile', 1);
        $this->db->update($this->tableNameProfiles, $row);
    }
    // }}}
    // {{{ insertProfile
    public function insertProfile($province, $city, $address, $phone, $cellphone, $postal_code, $username, $name, $lastname, $country)
    {
        if ($country != Kohana::config('config.default_country')) {
            $province = 0;
            $city     = 0;
        }

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
                     'lastname'       => $lastname,
                     'country'        => $country
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
        $this->db->select('id, city, phone, pan, cellphone, province, address, postal_code, country');

        return (Array) $this->db->getwhere($this->tableNameProfiles, array('username' => $username))->current();
    }
    // }}}
    // {{{ Countries, Provinces and Cities :D
    // {{{ getCities
    public function getCities($province = null)
    {
        $this->db->select('code, city, province')->from($this->tableNameCities);

        if ($province != null) {
            $this->db->where('province', $province);
        }

        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ getProvinces
    public function getProvinces()
    {
        $this->db->select('id, province')->from($this->tableNameProvinces);

        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ getCountries
    public function getCountries()
    {
        $this->db->select('id, country')->from($this->tableNameCountries);

        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ getCity
    public function getCity($code)
    {
        $this->db->select('code, city, province')->from($this->tableNameCities);

        if (is_array($code)) {
            if (isset($code['city'])) {
                $this->db->where('code', $code['city']);
                return $this->db->get()->current()->city;
            }
        } else {
            $this->db->where('code', $code);
            return $this->db->get()->current();
        }
    }
    // }}}
    // {{{ getProvince
    public function getProvince($id)
    {
        $this->db->select('id, province')->from($this->tableNameProvinces);

        if (is_array($id)) {
            if (isset($id['province'])) {
                $this->db->where('id', $id['province']);
                return $this->db->get()->current()->province;
            }
        } else {
            $this->db->where('id', $id);
            return $this->db->get()->current();
        }
    }
    // }}}
    // {{{ getCountry
    public function getCountry($id)
    {
        $this->db->select('id, country')->from($this->tableNameCountries);

        if (is_array($id)) {
            if (isset($id['country'])) {
                $this->db->where('id', $id['country']);
                return $this->db->get()->current()->country;
            }
        } else {
            $this->db->where('id', $id);
            return $this->db->get()->current();
        }
    }
    // }}}
    // }}}
}
