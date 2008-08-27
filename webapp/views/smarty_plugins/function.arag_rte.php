<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// |          Emil Sedgh <emilsedgh@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {arag_rte} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_rte                                                       |
// | Purpose: Generating a WYSIWYG widget                                    |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_rte($params, &$smarty)
{
    $width  = '100%';
    $height = '300';
    $value  = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
            case 'value':
            case 'toolbar_set':
            case 'width':
            case 'height':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_rte: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("arag_rte: missing 'name' attribute");
       return Null;
    }

    $session = New Session;
    $session->set('rte_'.$params['name'], Router::$module);

    return '<textarea name="'.$params['name'].'" class="rte" style="width:'.$width.';height:'.$height.'px;" col="'.$width.'" row="'.$height.'">'.
           $value.'</textarea>';
}
