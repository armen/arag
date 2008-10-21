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
    $theme = Kohana::config('theme.default');
    $smarty->assign('theme', $theme);
    return $smarty->fetch(Kohana::find_file('views', 'arag_templates/head', True, 'tpl'));
}

?>
