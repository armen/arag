<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {entities_to_ascii} function plugin                              |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    entities_to_ascii                                              |
// | Purpose: It turns character entities back into ASCII                    |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_entities_to_ascii($params, &$smarty)
{
    // Default variables
    $str = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("entities_to_ascii: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('text');

    return entities_to_ascii($str);
}

?>
