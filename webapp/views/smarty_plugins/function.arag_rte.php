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
    if (!isset($params['name'])) {
       $smarty->trigger_error("arag_rte: missing 'name' attribute");
       return;
    }
	print '<textarea name='.$params['name'].' class="rte"></textarea>';
}

?>
