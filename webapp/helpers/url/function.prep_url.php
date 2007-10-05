<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {prep_url} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    prep_url                                                       |
// | Purpose: Complete the http:// of url if missed                          |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_prep_url($params, &$smarty)
{
    // Default variables
    $string = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'string':       
                $$_key = (string)$_val;
                break;
            
            default:
                $smarty->trigger_error("prep_url: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('url');

    return prep_url($string);
}

?>
