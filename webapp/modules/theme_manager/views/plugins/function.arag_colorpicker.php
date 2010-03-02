<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Peyman Karimi <zeegco@yahoo.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_colorpicker} function plugin                               |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_colorpicker                                               |
// | Purpose: Generating a color picker widget                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_colorpicker($params, &$smarty)
{
    $ending = $name = $color = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'ending':
                $$_key = '_'.$_val; break;
           case 'name':
                $$_key = $_val; break;
            case 'color':
                $$_key = $_val; break;
            default:
                $smarty->trigger_error("arag_colorpicker: Unknown attribute '$_key'");
        }
    }

    $smarty->assign('ending', $ending);
    $smarty->assign('name', $name);
    $smarty->assign('color', $color);

    return $smarty->fetch(Arag::find_file('theme_manager', 'views', 'backend/arag_colorpicker', True, Kohana::config('smarty.templates_ext')));
}
