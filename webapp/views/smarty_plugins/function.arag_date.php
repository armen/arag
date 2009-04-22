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
    $size             = Null;
    $format_character = array('%Y', '%y', '%o', '%m', '%n', '%d', '%j');
    $format_map       = array('yyyy', 'yy', 'o', 'mm', 'm', 'dd', 'd');

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'value':
            case 'name':
            case 'id':
            case 'type':
            case 'toggle':
            case 'multiple':
            case 'format':
            case 'valid_dates':
			case 'parent':
                $$_key = $_val;
                break;

            case 'size':
                $size = 'size="'.$_val.'"';
                break;

            default:
                $smarty->trigger_error("arag_date: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("arag_date: missing 'name' attribute");
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

    $data['lang']           = $lang;
    $data['type']           = $type;
    $data['name']           = $name;
    $data['prefix']         = str_replace(Array('[', ']'), '_', $name);
    $data['id']             = isset($id) ? $id : $name;
    $data['toggle']         = isset($toggle) ? True : False;
    $data['value']          = isset($value) ? $value : Null;
    $data['format']         = isset($format) ? $format : '%Y/%m/%d';
    $data['format_sample']  = str_replace($format_character, $format_map, $data['format']);
    $data['multiple']       = (isset($multiple) && $multiple) ? true : false;
    $data['valid_dates']    = (isset($valid_dates) && is_array($valid_dates)) ? json_encode($valid_dates) : false;
    $data['size']           = $size;
    $data['parent']         = isset($parent) ? $parent : Null;
    if (is_array($value)) {
        foreach($value as &$date) {
            $date = date::timetostr($date, 'Y/m/d', false);
        }
        $data['value'] = $value;
    }

    $view = new View('arag_templates/arag_date', $data);
    return $view->render();
}
