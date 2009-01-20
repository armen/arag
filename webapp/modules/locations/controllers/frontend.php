<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        // Do not call parent constructor
    }
    // }}}
    // {{{ get_cities_of
    public function get_cities_of($province, $country)
    {
        $entries = Array();
        $cache   = Cache::instance();
        $cities  = $cache->get($province.$country);

        if (!$cities) {
            // Load the model
            $locations = new Locations_Model;
            $cities    = $locations->getCities($province, $country);

            $cache->set($province.$country, $cities);
        }

        foreach ($cities as $city) {
            $entries[] = Array('key' => $city['code'], 'value' => $city['city']);
        }

        echo json_encode(Array('entries' => $entries));
    }
    // }}}
    // {{{ get_provinces_of
    public function get_provinces_of($country)
    {
        $entries   = Array();
        $cache     = Cache::instance();
        $provinces = $cache->get($country);

        if (!$provinces) {
            // Load the model
            $locations = new Locations_Model;
            $provinces = $locations->getProvinces($country);

            $cache->set($country, $provinces);
        }

        foreach ($provinces as $province) {
            $entries[] = Array('key' => $province['id'], 'value' => $province['province']);
        }

        echo json_encode(Array('entries' => $entries));
    }
    // }}}
}
