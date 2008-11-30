<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors:Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_date} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_user_search                                               |
// | Purpose: Generating a user search input with autocomplete feature       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_user_search($params, &$smarty)
{

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'value':
            case 'name' :
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_rte: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("arag_user_search: missing 'name' attribute");
       return Null;
    }
    $smarty->assign('name', $name);
    if (isset($value)) {
        $smarty->assign('value', $value);
    }
    return $smarty->fetch(Arag::find_file('user', 'views', 'frontend/search', True, Kohana::config('smarty.templates_ext')));
}
