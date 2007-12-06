<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {url_redirect} function plugin                                   |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    url_redirect                                                   |
// | Purpose: Sends a page redirect header.                                  |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_url_redirect($params, &$smarty)
{
    // Default variables
    $uri    = '';
    $method = '302';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':       
            case 'method':
                $$_key= (string)$_val;
                break;
            
            default:
                $smarty->trigger_error("url_redirect: unknown attribute '$_key'");
        }
    }

    return url::redirect($uri, $method);
}

?>
