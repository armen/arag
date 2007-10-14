<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {html_selected} function plugin                                  |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    html_selected                                                  |
// | Purpose: A helper to set selected attribute of a field                  |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_html_selected($params, &$smarty)
{
    $assign = Null;
    
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $$_key = $_val;
                break;
                
            default:
                $smarty->trigger_error("html_selected: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("html_selected: missing 'name' attribute");
       return;
    }

    if ($smarty->get_template_vars($name)) {
        return 'selected="selected"';
    }

    return Null;
}

?>
