<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {url_site} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    url_site                                                       |
// | Purpose: retuen site's url                                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_url_site($params, &$smarty)
{
    // Default Values
    $uri      = '';
    $protocol = False;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':
            case 'protocol':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("url_site: unknown attribute '$_key'");
        }
    }

    return url::site($uri, $protocol);
}

?>
