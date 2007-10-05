<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {redirect} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    redirect                                                       |
// | Purpose: Does a "header redirect" to the local URI specified            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_redirect($params, &$smarty)
{
    // Default variables
    $uri    = '';
    $method = 'location';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':       
            case 'method':
                $$_key= (string)$_val;
                break;
            
            default:
                $smarty->trigger_error("redirect: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('url');

    return redirect($uri, $method);
}

?>
