<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {left} function plugin                                           |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    left                                                           |
// | Purpose: align left                                                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_left($params, &$smarty)
{
    $assign = Null;

    if (is_array($params) && count($params)) {
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'assign':
                    $$_key = (string)$_val;
                    break;
                    
                default:
                    $smarty->trigger_error("arag_function_left: Unknown attribute '$_key'");
            }
        }
    }

    $align = (Config::item('arag.i18n_language_direction') == 'rtl')?'right':'left';

    if ($assign) {
        $smarty->assign($assign, $align);
        return Null;
    }

    return $align;
}

?>
