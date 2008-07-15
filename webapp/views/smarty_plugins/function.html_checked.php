<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {html_checked} function plugin                                   |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    html_checked                                                   |
// | Purpose: A helper to set checked attribute of a field                   |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_html_checked($params, &$smarty)
{
    $assign = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'value':
            case 'exp_value':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("html_checked: Unknown attribute '$_key'");
        }
    }

    if (!isset($value)) {
       $smarty->trigger_error("html_checked: missing 'value' attribute");
       return;
    }

    if (!isset($exp_value)) {
       $smarty->trigger_error("html_checked: missing 'exp_value' attribute");
       return;
    }

    if ($value == $exp_value) {
        return 'checked="checked"';
    }

    return Null;
}
