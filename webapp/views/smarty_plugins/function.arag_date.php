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
    $tip_x            = 16;
    $tip_y            = 16;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'value':
            case 'name':
            case 'id':
            case 'type':
            case 'toggle':
            case 'multiple':
            case 'format':
            case 'tip_x':
            case 'tip_y':
			case 'parent':
                $$_key = $_val;
                break;

            case 'valid_dates':
                $dates_to_validate = $_val;
                $is_valid          = true;
                break;

            case 'non_valid_dates':
                $dates_to_validate = $_val;
                $is_valid          = false;
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

    $data['lang']              = $lang;
    $data['type']              = $type;
    $data['name']              = $name;
    $data['prefix']            = str_replace(Array('[', ']'), '_', $name);
    $data['id']                = isset($id) ? $id : $name;
    $data['toggle']            = isset($toggle) ? True : False;
    $data['format']            = isset($format) ? $format : '%Y/%m/%d';
    $data['format_sample']     = str_replace($format_character, $format_map, $data['format']);
    $data['multiple']          = (isset($multiple) && $multiple) ? true : false;
    $data['multiple_value']    = (isset($value) && is_array($value)) ? implode(',', $value) : Null;
    $data['dates_to_validate'] = (isset($dates_to_validate) && is_array($dates_to_validate)) ? json_encode($dates_to_validate) : 'false';
    $data['is_valid']          = isset($is_valid) && $is_valid;
    $data['size']              = $size;
    $data['tip_x']             = $tip_x;
    $data['tip_y']             = $tip_y;
    $data['parent']            = isset($parent) ? $parent : Null;
    if (is_array($value)) {
        foreach($value as &$date) {
            $date = date::timetostr($date, 'Y/m/d', true, $type);
        }
    } elseif($value) {
        $value = date::timetostr($value, 'Y/m/d', true, $type);
    }
    $data['value'] = $value;

    $view = new View('arag_templates/arag_date', $data);
    return $view->render();
}
