<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty arag_date modifier  plugin                                       |
// |                                                                         |
// | Type:    modifier                                                       |
// | Name:    arag_date                                                      |
// | Purpose: string to timestamp                                            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_modifier_arag_date($string, $input_name = Null, $index = Null, $timestamp = False)
{
    if (!isset($string) || $string == '') {
        return Null;
    }

    $types = Input::instance()->Post('type_'.$input_name);

    if (is_array($types) && isset($index) && isset($types[$index])) {
        return date::strtotime($string, $types[$index] ? $types[$index] : Null);
    } else {
        $type = isset($input_name) ? (Input::instance()->Post('type_'.$input_name) ? Input::instance()->Post('type_'.$input_name) : Null) : Null;

        return $timestamp ? date::timetostr($string, 'Y/m/d', false, $type) : date::strtotime($string, $type);
    }
}
