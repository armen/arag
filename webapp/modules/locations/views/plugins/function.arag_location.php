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
    $value    = Null;
    $type     = Null;
    $readonly = False;
    $all      = Array();
    $name     = 'location';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
            case 'value':
            case 'readonly':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_location: Unknown attribute '$_key'");
        }
    }

    $locations = Model::load('Locations', 'locations');

    if ($readonly) {
        $world['id'] = 0;
        if ($value) {
            $parent = $value;
            while($location = $locations->get($parent)) {
                $path[$location['id']] = $location;
                $parent                = $location['parent'];
            }
        }
        $path[0] = $world;
        $all     = array_reverse($path);
        foreach($all as $index => &$location) {
            $location['children'] = $locations->getByParent($location['id']);
        }
        $path = array_reverse($path);

        $view  = new View('frontend/arag_location_readonly');        
        $view->all      = $all;
        $view->path     = $path;
    } else {
        $view  = new View('frontend/arag_location');
    }
    $view->gname  = $name;
    $view->value = $value;
    return $view->render();
}
