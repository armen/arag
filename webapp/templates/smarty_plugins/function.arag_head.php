<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_head} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_head                                                      |
// | Purpose: displays shared content of head                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_head($params, &$smarty)
{
    return $smarty->fetch(Config::item('arag.templates_path') . '/arag_templates/head.tpl');
}

?>
