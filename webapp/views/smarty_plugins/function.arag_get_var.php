<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_get_var} function plugin                                   |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_get_var                                                   |
// | Purpose: Returns variable value                                         |
// +-------------------------------------------------------------------------+
// $Id:$
// ---------------------------------------------------------------------------

function smarty_function_arag_get_var($params, &$smarty)
{
    $assign = False;

    if (is_array($params) && count($params)) {
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'assign':
                case 'var':
                    $$_key = (string)$_val;
                    break;
                    
                default:
                    $smarty->trigger_error("arag_function_arag_get_var: Unknown attribute '$_key'");
            }
        }
    }

    if ($assign && isset($smarty->_tpl_vars[$var])) {
        $smarty->assign($assign, $smarty->_tpl_vars[$var]);
    } else if (isset($smarty->_tpl_vars[$var])) {
        return $smarty->_tpl_vars[$var];
    }
}
