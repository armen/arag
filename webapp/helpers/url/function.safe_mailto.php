<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {safe_mailto} function plugin                                    |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    safe_mailto                                                    |
// | Purpose: Safe Mailto                                                   |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_safe_mailto($params, &$smarty)
{
    //Default Values
    $title      = '';
    $attributes = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'email':
            case 'title':
            case 'attributes':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("safe_mailto: unknown attribute '$_key'");
        }
    }

    if (!isset($email)) {
       $smarty->trigger_error("safe_mailto: missing 'email' attribute");
    }

    $CI =& get_instance();
    $CI->load->helper('url');

    return safe_mailto($email, $title, $attributes);
}

?>
