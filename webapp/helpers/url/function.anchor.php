<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {anchor} function plugin                                         |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    anchor                                                         |
// | Purpose: create an HTML anchore                                         |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_anchor($params, &$smarty)
{
    // Default variables
    $uri        = '';
    $title      = '';
    $attributes = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':
            case 'title':
            case 'attributes':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("anchor: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('url');

    return anchor($uri, $title, $attributes);
}

?>
