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

    $lang  = Config::item('locale.lang');
    $align = (Config::item('locale.languages_direction.'.$lang) == 'rtl') ? 'right' : 'left';

    if ($assign) {
        $smarty->assign($assign, $align);
        return Null;
    }

    return $align;
}
