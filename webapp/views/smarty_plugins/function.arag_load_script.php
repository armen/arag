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

    $dependencies = Kohana::config('scripts');
    $scripts      = Array($src);
    $stack        = Array();

    while (isset($dependencies[$src])) {
        if (is_array($dependencies[$src])) {
            $scripts =  array_merge($scripts, $dependencies[$src]);
        } else {
            $scripts[] = $dependencies[$src];
        }

        $src = $dependencies[$src];

        if (is_null($src) && !empty($stack)) {
            $src = array_pop($stack);

        } elseif (is_array($src)) {
            $stack = array_merge($stack, $src);
            $src   = array_pop($stack);
        }
    }

    foreach (array_reverse($scripts) as $src) {

        if (!isset($GLOBALS['loaded_headers'][sha1($src)])) {

            $GLOBALS['loaded_headers'][sha1($src)] = True;
            $GLOBALS['headers'][]                  = html::script($src);
        }

    }

    return Null;
}
