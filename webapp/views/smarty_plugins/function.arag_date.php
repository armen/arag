<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors:Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_date} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_date                                                      |
// | Purpose: Generating a date picker widget                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_date($params, &$smarty)
{

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'value' :
            case 'name'  :
            case 'id'    :
            case 'type'  :
            case 'toggle':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_rte: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("arag_rte: missing 'name' attribute");
       return Null;
    }

    $lang = Kohana::config('locale.lang');

    if (!isset($type) || $type == '') {
        if ($lang == 'fa') {
            $type = 'jalali';
        } else {
            $type = 'gregorian';
        }
    }

    $data['lang']   = $lang;
    $data['type']   = $type;
    $data['name']   = $name;
    $data['id']     = isset($id) ? $id : $name;
    $data['toggle'] = isset($toggle) ? True : False;
    $data['value']  = isset($value) ? $value : "";
    $data['format'] = '%m/%d/%Y';

    $view = new View('arag_templates/arag_date', $data);
    return $view->render();
}
