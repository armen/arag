<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {character_limiter} function plugin                              |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    character_limiter                                              |
// | Purpose: Turnicates a string to the number of characters specified      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_character_limiter($params, &$smarty)
{
    // Default variables
    $str      = '';
    $number   = 50;
    $end_char = '&#8230;';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
            case 'number':
            case 'end_char':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("character_limiter: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('text');

    return character_limiter($str, $number, $end_char);
}

?>
