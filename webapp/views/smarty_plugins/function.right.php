<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {right} function plugin                                          |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    right                                                          |
// | Purpose: align right                                                    |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_right($params, &$smarty)
{
    $assign = Null;

    if (is_array($params) && count($params)) {
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'assign':
                    $$_key = (string)$_val;
                    break;
                    
                default:
                    $smarty->trigger_error("arag_function_right: Unknown attribute '$_key'");
            }
        }
    }

    $lang  = Config::item('gettext.language');
    $align = (Config::item('gettext.languages.'.$lang.'.direction') == 'rtl') ? 'left' : 'right';

    if ($assign) {
        $smarty->assign($assign, $align);
        return Null;
    }

    return $align;
}

?>
