<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {html_anchor} function plugin                                    |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    html_anchor                                                    |
// | Purpose: Create HTML link anchors.                                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_html_anchor($params, &$smarty)
{
    // Default variables
    $title      = False;
    $attributes = False;
    $protocol   = False;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'uri':
            case 'title':
            case 'attributes':
            case 'protocol':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("html_anchor: unknown attribute '$_key'");
        }
    }

    return html::anchor($uri, $title, $attributes, $protocol);
}

?>
