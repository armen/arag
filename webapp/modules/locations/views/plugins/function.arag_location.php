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
    foreach ($params as $_key => $_val) {
        $value    = Null;
        $type     = Null;
        $parent   = Null;
        $readonly = False;
        $all      = Array();
        $name     = 'location';

        switch ($_key) {
            case 'name':
            case 'value':
            case 'type':
            case 'parent':
            case 'readonly':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_location: Unknown attribute '$_key'");
        }
    }

    $locations = Model::load('Locations', 'locations');
    $view      = new View('frontend/arag_location');

    if ($value) {
        $parent = $value;
        while($location = $locations->get($parent)) {
            $path[$location['id']] = $location;
            $parent                = $location['parent'];
        }
        $world['id'] = 0;
        $path[0] = $world;
        $all = array_reverse($path);
    }

    foreach($all as $index => &$location) {
        $location['children'] = $locations->getByParent($location['id']);
    }

    $view->name     = $name;
    $view->all      = $all;
    $view->path     = $path;
    $view->readonly = $readonly;


    return $view->render();
}
