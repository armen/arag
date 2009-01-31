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
    $ending = $color = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'ending':
                $$_key = '_'.$_val; break;
            case 'color':
                $$_key = $_val; break;
            default:
                $smarty->trigger_error("arag_colorpicker: Unknown attribute '$_key'");
        }
    }

    $view           = new View('backend/arag_colorpicker');
    $view->ending   = $ending;
    $view->color    = $city;

    return $view->render();
}
