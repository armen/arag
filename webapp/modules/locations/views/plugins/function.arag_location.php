<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_location} function plugin                                  |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_location                                                  |
// | Purpose: Generating a location picker widget                            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_location($params, &$smarty)
{
    $prefix   = Null;
    $city     = Kohana::config('locale.default_city', 0);
    $province = Kohana::config('locale.default_province', 0);
    $country  = Kohana::config('locale.default_country', 0);

    foreach ($params as $_key => $_val) {

        switch ($_key) {
            case 'prefix':
                $$_key = $_val.'_';
                break;

            case 'accept_null':
                // This is dummy
                break;

            case 'city':
            case 'province':
            case 'country':
                ((isset($params['accept_null']) && $params['accept_null']) || (int) $_val) AND $$_key = (int) $_val;
                break;

            default:
                $smarty->trigger_error("arag_location: Unknown attribute '$_key'");
        }
    }

    $locations = Model::load('Locations', 'locations');
    $view      = new View('frontend/arag_location');

    $view->prefix    = $prefix;
    $view->countries = $locations->getCountries();
    $view->provinces = $locations->getProvinces($country);
    $view->cities    = $locations->getCities($province, $country);
    $view->country   = $country;
    $view->province  = $province;
    $view->city      = $city;

    return $view->render();
}
