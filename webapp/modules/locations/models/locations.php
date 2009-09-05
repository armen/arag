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
        $cache_id = 'location_'.$id;
        $cache    = New Cache;
        $cached   = $cache->get($cache_id);
        if ($cached) {
            return $cached;
        }

        $this->db->select('id', 'parent', 'name', 'english', 'code', 'type', 'latitude', 'longitude')->from($this->tableName)->where('id', $id);
        $loc            = current($this->db->get()->result_array(False));
        if ($loc['english']) {
            $loc['english'] = ucwords($loc['english']);
        }

        if ($loc['code']) {
            $loc['code'] = strtoupper($loc['code']);
        }

        $cache->set($cache_id, $loc);
        return $loc;
    }
    // }}}
    // {{{ getByParent
    public function getByParent($parent = 0)
    {
        $this->db->select('*')->from($this->tableName)->where('parent', $parent)->where('name <>', '')->orderby('name', 'english', 'code');
        $localized = $this->db->get()->result_array(False);

        $this->db->select('*')->from($this->tableName)->where('parent', $parent)->where('name', '')->orderby('name', 'english', 'code');
        $unlocalized = $this->db->get()->result_array(False);

        return array_merge($localized, $unlocalized);
    }
    // }}}
    // {{{ getSiblings
    public function getSiblings($id = 1)
    {
        return $this->getByParent(current(current($this->db->select('parent')->from($this->tableName)->where('id', $id)->get()->result_array(False))));
    }
    // }}}
    // {{{ search
    public function search($name = Null, $english = Null, $code = Null, $type = Null, $orderOfTypes = array(), $takeCareOfCode = true)
    {
        $where = '';

        $name    = strtolower($name);
        $english = strtolower($english);
        $code    = strtolower($code);

        if ($name) {
            $where = "name LIKE '%$name%' ";
        }

        if ($english) {
            $where .= $where ? " OR english LIKE '%$english%' " : " english LIKE '%$english%' ";
        }

        if ($code) {
            $where .= $where ? " OR code = '$code' " : " code = '$code' ";
        }

        if ($type) {
            $where = $where ? "($where) AND type = '$type' " : " type = '$type' ";
        }

        $order_by = '';

        $takeCareOfCode and $order_by = "oct(code REGEXP '$code') * 100 + ";

        $orderOfTypes = is_array($orderOfTypes) ? $orderOfTypes : array($orderOfTypes);
        $index = count($orderOfTypes);
        foreach($orderOfTypes as $order) {
            $order_by .= "oct(type = '$order') * $index + ";
            $index--;
        }
        $order_by .= '0 DESC, name, english, code, type';

        $ids = $this->db->query("SELECT id FROM ".$this->db->table_prefix().$this->tableName.' '.'WHERE '.$where.'ORDER BY '.$order_by)
                    ->result_array(False);

        $results = Array();
        foreach($ids as $id) {
            $results[] = $this->get($id['id']);
        }

        return $results;
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
        $this->db->where('id', $id)->delete($this->tableName);

        foreach($this->getByParent($id) as $child) {
            $this->delete($child['id']);
        }
    }
    // }}}
    // {{{ getCoordinates
    public function getCoordinates($location)
    {
        if ($location['latitude'] && $location['longitude']) {
            return True;
        }
        if (strlen($location['english']) < 2) {
            return False;
        }

        $url = 'http://maps.google.com/maps/geo?q='.urlencode(ucfirst($location['english'])).'&output=json&key='.urlencode($this->getProperKey());
        $result  = json_decode(@file_get_contents($url));

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
    // {{{ getChildIds
    public function getChildIds($parent)
    {
        $id     = 'childs_'.$parent;
        $cache  = New Cache;
        $cached = $cache->get($id);
        if ($cached) {
//             return $cached;
        }
        $all    = Array();
        $childs = $this->db->select('id')->from($this->tableName)->where('parent', $parent)->get()->result_array(False);

        foreach($childs as $child) {
            $id = current($child);
            $all[] = $id;

            foreach($this->getChildIds($id) as $grand) {
                $grandId = $grand;
                $all[]   = $grandId;
            }
        }
        $all[] = $parent;
        $all   = array_unique($all);
        $cache->set($id, $all);
        return $all;
    }
    // }}}
    // {{{ getCountryByLocation
    public function getCountryByLocation($id) //TODO: To be deprecated.
    {
        return $this->getParentOfType($id, Locations_Model::COUNTRY);
    }
    // }}}
    // {{{ getParentOfType
    public function getParentOfType($id, $type)
    {
        $location = $this->get($id);
        if ($location['type'] == $type) {
            return $location;
        }

        while($parent = $this->get($location['parent'])) {
            if ($parent['type'] == $type) {
                return $parent;
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

        var_dump('DELETING');
        foreach($this->getByParent(96) as $lo) {
            var_dump($lo);
            $this->delete($lo['id']);
        }

        $provinces = MODPATH.'/locations/schemas/v0.1/locations_provinces.csv';
        $file      = file_get_contents($provinces);
        $lines     = explode("\n", $file);

        $provinces = Array();
        foreach($lines as $line) {
            $columns            = explode(';', $line);
            if (isset($columns[1])) {
                $province['parent'] = 96; //Iran.
                $province['name']   = str_replace('"', Null, $columns[1]);
                $province['type']   = Locations_Model::PROVINCE;
                $provinces[] = $province;
                $this->db->insert($this->tableName, $province);
            }
        }

        var_dump($provinces);



        $cities = MODPATH.'/locations/schemas/v0.1/locations_cities.csv';
        $file      = file_get_contents($cities);
        $lines     = explode("\n", $file);

        foreach($lines as $line) {
            $columns            = explode(';', $line);
            $city['parent']   = $columns[3] + 11600;
            $city['name']   = str_replace('"', Null, $columns[1]);
            $city['type']   = Locations_Model::CITY;
            var_dump($city);
            $this->db->insert($this->tableName, $city);
        }

        die('e');
    }
    // }}}
 }
