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

function smarty_function_show_arag_headers($params, &$smarty)
{
    $headers = isset($GLOBALS['headers']) ? $GLOBALS['headers'] : Array();

    $smarty->assign('headers', $headers);

    // Remove all headers, this will prevent duplication of already loaded scripts
    unset($GLOBALS['headers']);

    return $smarty->fetch(Kohana::find_file('views', 'arag_templates/show_headers', True, 'tpl'));
}

?>
