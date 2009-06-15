<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Jila Khaghani
 * @since        Version 0.1
 * $Id$
 */

// ------------------------------------------------------------------------

/**
 * url Class
 *
 * @category    Helper
 *
 */

class location {

    // {{{ get_city
    function get_city($row, $field)
    {
        if ($row[$field]) {
            $locations = Model::load('Locations', 'locations');
            $city      = $locations->getCity($row[$field]);

            return $city['city'];
        }

        return '-';
    }
    // }}}
    // {{{ get_city_english
    function get_city_english($row, $field)
    {
        if ($row[$field]) {
            $locations = Model::load('Locations', 'locations');
            $city      = $locations->getCity($row[$field]);

            return $city['english'];
        }
    }
    // }}}
    // {{{ get_country
    function get_country($row, $field)
    {
        if ($row[$field]) {
            $locations = Model::load('Locations', 'locations');
            $country   = $locations->getCountry($row[$field]);

            return $country['country'];
        }

        return _("No Country");
    }
    // }}}
    // {{{ get_country_english
    function get_country_english($row, $field)
    {
        if ($row[$field]) {
            $locations = Model::load('Locations', 'locations');
            $country   = $locations->getCountry($row[$field]);

            return $country['english'];
        }

        return _("No Country");
    }
    // }}}
    // {{{ get_province
    public function get_province($row, $field)
    {
        if ($row[$field]) {
            $locations = Model::load('Locations', 'locations');
            $province  = $locations->getProvince($row[$field]);

            return $province['province'];
        }

        return '-';
    }
    // }}}
}
