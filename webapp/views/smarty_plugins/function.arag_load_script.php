<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_load_script} function plugin                               |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_load_script                                               |
// | Purpose: load a scrip                                                   |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

static $_loaded = Array();

function smarty_function_arag_load_script($params, &$smarty)
{
    if (is_array($params) && count($params)) {
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'src':
                    $$_key = (string)$_val;
                    break;

                default:
                    $smarty->trigger_error("arag_function_arag_load_script: Unknown attribute '$_key'");
            }
        }
    }

    if (!isset($src)) {
        $smarty->trigger_error("arag_function_arag_load_script: You have to set 'src' attribute.");
    }

    global $_loaded;

    if (!isset($_loaded[sha1($src)])) {
        $_loaded[sha1($src)] = True;
        return html::script($src);
    }

    return Null;
}
