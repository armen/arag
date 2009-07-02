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

    if (!isset($GLOBALS['loaded_headers'][sha1($src)])) {

        $GLOBALS['loaded_headers'][sha1($src)] = True;
        if (isset($GLOBALS['controller_execute'])) {
            $GLOBALS['headers'][] = "<script type='text/javascript'>window.addEvent('domready', function() { Asset.javascript('".url::base().'/'.$src."'); });</script>";
        } else {
            $GLOBALS['headers'][] = html::script($src);
        }
    }

    return Null;
}
