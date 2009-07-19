<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Emil Sedgh <emilsedgh@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Locations_Model extends Model
{
    const CONTAINMENT = 'containment';
    const COUNTRY     = 'country';
    const PROVINCE    = 'province';
    const STATE       = 'state';
    const CITY        = 'city';
    const VILLAGE     = 'village';
    const AIRPORT     = 'airport';
    const STREET      = 'street';
    const SIGHT       = 'sight';

    private $tableName = 'locations';
    public $names      = Array();
    // {{{ __construct
    public function __construct() {
        parent::__construct();

        $this->names = Array( self::CONTAINMENT => _("Containment"),
                              self::COUNTRY     => _("Country"),
                              self::PROVINCE    => _("Province"),
                              self::STATE       => _("State"),
                              self::CITY        => _("City"),
                              self::VILLAGE     => _("Village"),
                              self::AIRPORT     => _("Airport"),
                              self::STREET      => _("Street"),
                              self::SIGHT       => _("Sight")
                        );
                            
    }
    // }}}
    // {{{ get
    public function get($id) {
        $this->db->select('id', 'parent', 'name', 'english', 'code', 'type', 'latitude', 'longitude')->from($this->tableName)->where('id', $id);
        return current($this->db->get()->result_array(False));
    }
    // }}}
    // {{{ getByParent
    public function getByParent($parent)
    {
        $this->db->select('id', 'parent', 'name', 'english', 'code', 'type', 'latitude', 'longitude')->from($this->tableName)->where('parent', $parent);
        return $this->db->get()->result_array(False);
    }
    // }}}
    // {{{ add
    public function add($parent, $english, $type, $code, $name, $latitude, $longitude)
    {
        $row = Array( 'parent'    => $parent,
                      'name'      => $name,
                      'english'   => $english,
                      'code'      => $code,
                      'latitude'  => $latitude,
                      'longitude' => $longitude,
                      'type'      => $type 
                );
        return $this->db->insert($this->tableName, $row)->insert_id();
    }
    // }}}
    // {{{ edit
    public function edit($id, $parent, $english, $type, $code, $name, $latitude, $longitude)
    {
        $row = Array( 'parent'    => $parent,
                      'name'      => $name,
                      'english'   => $english,
                      'code'      => $code,
                      'latitude'  => $latitude,
                      'longitude' => $longitude,
                      'type'      => $type 
                );
        return $this->db->where('id', $id)->update($this->tableName, $row);
    }
    // }}}
    // {{{ delete
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->tableName);
    }
    // }}}
    // {{{ getCoordinates
    public function getCoordinates($location)
    {
        if ($location['latitude'] && $location['longitude']) {
            return True;
        }
        if (!strlen($location['english'])) {
            return False;
        }

        $url = 'http://maps.google.com/maps/geo?q='.ucfirst($location['english']).'&output=json&key='.$this->getProperKey();

        $result  = json_decode(file_get_contents($url));

        if (isset($result->Placemark[0])) {
            $coordinates = $result->Placemark[0]->Point->coordinates;
            $this->edit($location['id'], $location['parent'], $location['english'], $location['type'], $location['code'], $location['name'], $coordinates[1], $coordinates[0]);
            return True;
        }
        return False;
    }
    // }}}
    // {{{ setKeys
    public function setKeys($keys)
    {
        return Arag_Config::set('google_api_keys', $keys, 'locations', Kohana::config('arag.master_appname'));
    }
    // }}}
    // {{{ getKeys
    public function getKeys()
    {
        return Arag_Config::get('google_api_keys', Array(), 'locations', False, Kohana::config('arag.master_appname'));
    }
    // }}}
    // {{{ addKey
    public function addKey($domain, $key)
    {
        $keys          = Arag_Config::get('google_api_keys', Array());
        $keys[$domain] = Array('domain' => strtolower($domain), 'key' => $key);
        $this->setKeys($keys);
    }
    // }}}
    // {{{ deleteKey
    public function deleteKey($domain)
    {
        $keys = $this->getKeys();
        if (isset($keys[$domain])) {
            unset($keys[$domain]);
        }
        $this->setKeys($keys);
    }
    // }}}
    // {{{ getProperKey
    public function getProperKey()
    {
        $keys    = $this->getKeys();
        $current = Input::instance()->server('HTTP_HOST');
        foreach($keys as $domain => $key) {
            if (stristr($current, $domain) !== False) {
                return $key['key'];
            }
        }
        return '';
        return $this->db->where('id', $id)->delete($this->tableName);
     }
    // }}}
    // {{{ getSelectedLocation
    public function getSelectedLocation($array)
    {
        if (!is_array($array)) {
            return 0;
        }
        $current = '';
        while(!strlen($current)) {
            if (!count($array)) {
                return False;
            }
            $current = array_pop($array);
        }
        return $current;
        
    }
    // }}}
    // {{{ getChildIds
    public function getChildIds($parent)
    {
        $id     = 'childs_'.$parent;
        $cache  = New Cache;
        $cached = $cache->get($id);
        if ($cached) {
            return $cached;
        }
        $childs   = $this->db->select('id')->from($this->tableName)->where('parent', $parent)->get()->result_array(False);
        $grands[] = Array();

        foreach($childs as &$child) {
            $child = current($child);
        }

        foreach($childs as $child) {
            $grands[] = $this->getChildIds($child);
        }

        foreach($grands as $grand) {
            $childs = array_unique(array_merge($childs, $grand));
        }

        $cache->set($id, $childs);
        return $childs;
    }
    // }}}
    // {{{ getCountryByLocation
    public function getCountryByLocation($id)
    {
        $location = $this->get($id);
        if ($location['type'] == Locations_Model::COUNTRY) {
            return $location['id'];
        }

        while($parent = $this->get($location['parent'])) {
            if ($parent['type'] == Locations_Model::COUNTRY) {
                return $parent['id'];
            }
            $location = $parent;
        }
        return False;
    }
    // }}}
    // {{{ convert
    public function convert()
    {
        set_time_limit(0);
        $airports_table = 'ticketing_airports';
        $countries      = Array();
        $states         = Array();
        $cities         = Array();
        $airports       = Array();

        $this->db->select('country_name', 'country_code')->from($airports_table)->groupby('country_code')->orderby('country_name');
        foreach($this->db->get()->result_array(False) as $country) {
            $countries[] = Array('code' => $country['country_code'], 'english' => ucfirst(strtolower($country['country_name'])));
        }

        foreach($countries as $country) {
            $country['type'] = Locations_Model::COUNTRY;
            $this->db->insert($this->tableName, $country);
        }
//         var_dump($countries);

        $this->db->select('state', 'country_code')->from($airports_table)->groupby('state')->orderby('state')->where('state <>', '');
        foreach($this->db->get()->result_array(False) as $state) {
            $country = current($this->db->select('id')->from($this->tableName)->where(Array('type' => Locations_Model::COUNTRY, 'code' => $state['country_code']))->get()->result_array(False));
            $states[] = Array('code' => $state['state'], 'parent' => $country['id']);
        }

        foreach($states as $state) {
            $state['type'] = Locations_Model::STATE;
            $this->db->insert($this->tableName, $state);
        }
//         var_dump($states);

        $this->db->select('state', 'country_code', 'city_code', 'city_name', 'iata_code')->from($airports_table)->groupby('city_code')->orderby('city_name')->where('city_code = iata_code');
        foreach($this->db->get()->result_array(False) as $city) {
            if ($city['state']) {
                $parent = current($this->db->select('id')->from($this->tableName)->where(Array('type' => Locations_Model::STATE, 'code' => $city['state']))->get()->result_array(False));
            } else {
                $parent = current($this->db->select('id')->from($this->tableName)->where(Array('type' => Locations_Model::COUNTRY, 'code' => $city['country_code']))->get()->result_array(False));
            }
            $cities[] = Array('english' => ucfirst(strtolower($city['city_name'])), 'code' => $city['city_code'], 'parent' => $parent['id']);
        }

        foreach($cities as $city) {
            $city['type'] = Locations_Model::CITY;
            $this->db->insert($this->tableName, $city);
        }


        $this->db->select('state', 'country_code', 'city_code', 'airport_name', 'iata_code')->from($airports_table)->orderby('airport_name')->where('airport_name <>', '');
        foreach($this->db->get()->result_array(False) as $airport) {
            $parent = current($this->db->select('id')->from($this->tableName)->where(Array('type' => Locations_Model::CITY, 'code' => $airport['city_code']))->get()->result_array(False));
            $airports[] = Array('english' => ucfirst(strtolower($airport['airport_name'])), 'code' => $airport['iata_code'], 'parent' => $parent['id']);
        }

        foreach($airports as $airport) {
            $airport['type'] = Locations_Model::AIRPORT;
            $this->db->insert($this->tableName, $airport);
        }
    }
    // }}}
 }
