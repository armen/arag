<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {site_url} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    site_url                                                       |
// | Purpose: retuen site's url                                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_site_url($params, &$smarty)
{
    // Default Values
    $uri = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("site_url: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('url');

    return site_url($uri);
}

?>
