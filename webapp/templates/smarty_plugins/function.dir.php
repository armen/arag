<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {dir} function plugin                                            |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    dir                                                            |
// | Purpose: text direction                                                 |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_dir($params, &$smarty)
{
    $assign = Null;

    if (is_array($params) && count($params)) {
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'assign':
                    $$_key = (string)$_val;
                    break;
                    
                default:
                    $smarty->trigger_error("arag_function_dir: Unknown attribute '$_key'");
            }
        }
    }

    $CI        =& get_instance();
    $direction =  ($CI->config->item('Arag_i18n_language_direction') == 'rtl')?'rtl':'ltr';

    if ($assign) {
        $smarty->assign($assign, $direction);
        return Null;
    }

    return $direction;
}

?>
