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
    $show_weather = True;
    $show_photos  = True;
    $show_path    = True;
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

    foreach($path as $name) {
        $location['name']        = $name;
        $location['coordinates'] = $locations->getCoordinates($name);
        if (!$location['coordinates']) {
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
        if ($d['coordinates'][1] > $maxX) {
            $maxX = $d['coordinates'][1];
        }

        if ($d['coordinates'][0] > $maxY) {
            $maxY = $d['coordinates'][0];
        }

        if ($d['coordinates'][1] < $minX) {
            $minX = $d['coordinates'][1];
        }

        if ($d['coordinates'][0] < $minY) {
            $minY = $d['coordinates'][0];
        }
    }

    $view               = New View('frontend/arag_map');                 
    $view->id           = $id;
    $view->key          = $locations->getProperKey();
    $view->maxX         = $maxX;
    $view->minX         = $minX;
    $view->maxY         = $maxY;
    $view->minY         = $minY;
    $view->path         = $new_path;
    $view->show_weather = $show_weather ? 'true' : 'false';
    $view->show_photos  = $show_photos  ? 'true' : 'false';
    $view->show_path    = $show_path    ? 'true' : 'false';
    return $view->render();
}
