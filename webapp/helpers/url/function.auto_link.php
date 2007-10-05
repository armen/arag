<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {auto_link} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    auto_link                                                      |
// | Purpose: Find URL and Mailto links in a passed string automatically     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_auto_link($params, &$smarty)
{
    // Default variables
    $string = '';
    $type   = 'both';
    $popup  = FALSE;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'string':
            case 'type':
                $$_key = (string)$_val;
                break;

            case 'popup':
                $$_key = (boolean)$_val;                

            default:
                $smarty->trigger_error("auto_link: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('url');
    
    return auto_link($string, $type, $popup);
}

?>
