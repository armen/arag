<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {ascii_to_entities} function plugin                              |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    ascii_to_entities                                              |
// | Purpose: Converts ascii values to character entities                    |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_ascii_to_entities($params, &$smarty)
{
    // Default variables
    $str = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("ascii_to_entities: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('text');

    return ascii_to_entities($str);
}

?>
