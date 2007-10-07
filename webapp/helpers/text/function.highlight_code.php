<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {highlight_code} function plugin                                 |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    highlight_code                                                 |
// | Purpose: Colorize a string of code                                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_highlight_code($params, &$smarty)
{
    // Default variables
    $str = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("highlight_code: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('text');

    return highlight_code($str);
}

?>
