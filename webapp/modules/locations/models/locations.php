<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Peyman Karimi <peykar@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Locations_Model extends Model
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set table name
        $this->tableNameCities    = 'locations_cities';
        $this->tableNameProvinces = 'locations_provinces';
        $this->tableNameCountries = 'locations_countries';
    }
    // }}}
    // {{{ getCities
    public function getCities($province = null, $country = null)
    {
        $this->db->select('code, city, province, country')->from($this->tableNameCities);

        $this->db->where(array('province' => $province, 'deleted' => false));
        $country != null AND $this->db->where('country', $country);

        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ getProvinces
    public function getProvinces($country)
    {
        $this->db->select('id, province')
                 ->where(array('country' => $country, 'deleted' => false))
                 ->from($this->tableNameProvinces);

        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ getCountries
    public function getCountries()
    {
        $this->db->select('id, country')->from($this->tableNameCountries)->where(array('deleted' => false));

        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ getCity
    public function getCity($code)
    {
        $this->db->select('code, city, province, country, deleted')->from($this->tableNameCities);

        if (is_array($code)) {
            if (isset($code['city']) && $code['city'] != 0) {
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
        $this->db->select('id, province, country, deleted')->from($this->tableNameProvinces);

        if (is_array($id)) {
            if (isset($id['province']) && $id['province'] != 0) {
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
        $this->db->select('id, country, deleted')->from($this->tableNameCountries);

        if (is_array($id)) {
            if (isset($id['id'])) {
                $this->db->where('id', $id['id']);
                return $this->db->get()->current()->country;
            }
        } else {
            $this->db->where('id', $id);
            return $this->db->get()->current();
        }
    }
    // }}}
    // {{{ isCityDefined
    public function isCityDefined($name, $province = null)
    {
        $this->db->select('code, city, province, country, deleted')->from($this->tableNameCities);
        ($province) ? $this->db->where(array('city' => $name, 'province' => $province, 'deleted' => false))
                    : $this->db->where(array('city' => $name, 'deleted' => false));
        $result = $this->db->get()->current();
        return ($result) ? (array) $result : null;
    }
    // }}}
    // {{{ isProvinceDefined
    public function isProvinceDefined($name, $country = null)
    {
        $this->db->select('id, province, country, deleted')->from($this->tableNameProvinces);
        ($country) ? $this->db->where(array('province' => $name, 'country' => $country, 'deleted' => false))
                   : $this->db->where(array('province' => $name, 'deleted' => false));
        $result = $this->db->get()->current();
        return ($result) ? (array) $result : null;
    }
    // }}}
    // {{{ isCountryDefined
    public function isCountryDefined($name)
    {
        $this->db->select('id, country, deleted')->from($this->tableNameCountries);
        $this->db->where(array('country' => $name, 'deleted' => false));
        $result = $this->db->get()->current();
        return ($result) ? (array) $result : null;
    }
    // }}}
    // {{{ createCountry
    function createCountry($country)
    {
        return $this->db->insert($this->tableNameCountries, array('country' => $country))->insert_id();
    }
    // }}}
    // {{{ editCountry
    function editCountry($id, $country)
    {
        $row = array('id'       => $id,
                     'country'  => $country,
                     );

        return $this->db->where(array('id' => $id))->update($this->tableNameCountries, $row);
    }
    // }}}
    // {{{ createProvince
    function createProvince($countryID, $province)
    {
        $row = array('province'    => $province,
                     'country'     => $countryID);
        return $this->db->insert($this->tableNameProvinces, $row)->insert_id();
    }
    // }}}
    // {{{ editProvince
    function editProvince($id, $countryID, $province)
    {
        $row = array('province'     => $province,
                     'country'      => $countryID,
                     );

        return $this->db->where(array('id' => $id))->update($this->tableNameProvinces, $row);
    }
    // }}}
    // {{{ createCity
    function createCity($countryID, $provinceID, $city)
    {
        $row = array('city'         => $city,
                     'province'     => $provinceID,
                     'country'      => $countryID);
        return $this->db->insert($this->tableNameCities, $row)->insert_id();
    }
    // }}}
    // {{{ editCity
    function editCity($id, $countryID, $provinceID, $city)
    {
        $row = array('city'         => $city,
                     'province'     => $provinceID,
                     'country'      => $countryID,
                     );

        return $this->db->where(array('code' => $id))->update($this->tableNameCities, $row);
    }
    // }}}
    // {{{ deleteCountry
    function deleteCountry($countryID)
    {
        $this->db->where('country', $countryID)->update($this->tableNameCities, array('deleted' => 1));
        $this->db->where('country', $countryID)->update($this->tableNameProvinces, array('deleted' => 1));
        $this->db->where('id', $countryID)->update($this->tableNameCountries, array('deleted' => 1));
    }
    // }}}
    // {{{ deleteProvince
    function deleteProvince($provinceID)
    {
        $this->db->where('province', $provinceID)->update($this->tableNameCities, array('deleted' => 1));
        $this->db->where('id', $provinceID)->update($this->tableNameProvinces, array('deleted' => 1));
    }
    // }}}
    // {{{ deleteCity
    function deleteCity($cityID)
    {
        $this->db->where('code', $cityID);
        return $this->db->update($this->tableNameCities, array('deleted' => 1));
    }
    // }}}
}