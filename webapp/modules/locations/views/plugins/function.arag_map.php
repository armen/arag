<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com                                 |
// +-------------------------------------------------------------------------+
// | Smarty {arag_map} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_map                                                       |
// | Purpose: Generates a map                                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_map($params, &$smarty)
{
    $weather = True;
    foreach ($params as $_key => $_val) {

        switch ($_key) {
            default:
                $$_key = $_val;
                break;
        }
    }

    $locations = Model::load('Locations', 'locations');
    $forecast  = Model::load('Forecast', 'forecast');
    $new_path  = Array();

    foreach($path as $id) {
        $location = $locations->get($id);

        if (!$locations->getCoordinates($location)) {
            //We cant get the coordinates of a destinition, be'd better not to show any map.
            return False;
        }
        $new_path[] = $location;
    }

    $minX = 999999;
    $minY = 999999;
    $maxX = -99999;
    $maxY = -99999;

    foreach($new_path as $d) {
        if ($d['latitude'] > $maxX) {
            $maxX = $d['latitude'];
        }

        if ($d['longitude'] > $maxY) {
            $maxY = $d['longitude'];
        }

        if ($d['latitude'] < $minX) {
            $minX = $d['latitude'];
        }

        if ($d['longitude'] < $minY) {
            $minY = $d['longitude'];
        }
    }

    $view          = New View('frontend/arag_map');                 
    $view->id      = $id;
    $view->key     = $locations->getProperKey();
    $view->maxX    = $maxX;
    $view->minX    = $minX;
    $view->maxY    = $maxY;
    $view->minY    = $minY;
    $view->path    = $new_path;
    $view->weather = $weather;
    return $view->render();
}
