<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {anchor_popup} function plugin                                   |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    anchor_popup                                                   |
// | Purpose: create an HTML anchore which opens in a popup window           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_anchor_popup($params, &$smarty)
{
    // Default variables
    $uri        = '';
    $title      = '';
    $attributes = FALSE;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':
            case 'title':
                $$_key = (string)$_val;
                break;

            case 'attributes':
                $$_key = (boolean)$_val;
                break;                                

            default:
                $smarty->trigger_error("anchor_popup: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('url');

    return anchor_popup($uri, $title, $attributes);
}

?>
