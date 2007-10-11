<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// | Smarty {arag_rte} function plugin                                    |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_rte                                                    |
// | Purpose: Generating a FCKeditor                                         |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_populate($params, &$smarty)
{
    $assign = Null;
    $check  = False;
    $select = False;
    
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
            case 'assign':
            case 'check':
            case 'select':
                $$_key = $_val;
                break;
                
            default:
                $smarty->trigger_error("populate: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("populate: missing 'name' attribute");
       return;
    }

    $CI =& get_instance();

    if ($check !== False) {
        return $CI->validation->set_checkbox($name, $check);

    } else if ($select !== False) {
        return $CI->validation->set_select($name, $check);

    } else if (isset($CI->validation->$name)) {
        return $CI->validation->$name;

    } else if (isset($_POST[$name])) {
        return $_POST[$name];

    } else {
        return $smarty->get_template_vars($name);
    }
}

?>
