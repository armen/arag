<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {url_base} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    url_base                                                       |
// | Purpose: Base URL, with or without the index page.                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_url_base($params, &$smarty)
{
    // Default variables
    $index    = False;
    $protocol = False;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'index':
            case 'protocol':
                $$_key= $_val;
                break;
            
            default:
                $smarty->trigger_error("url_base: unknown attribute '$_key'");
        }
    }

    return url::base($index, $protocol);
}

?>
