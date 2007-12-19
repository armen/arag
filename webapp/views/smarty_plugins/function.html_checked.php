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
            case 'name':
                $$_key = $_val;
                break;
                
            default:
                $smarty->trigger_error("html_checked: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("html_checked: missing 'name' attribute");
       return;
    }

    if ($smarty->get_template_vars($name)) {
        return 'checked="checked"';
    }

    return Null;
}

?>
