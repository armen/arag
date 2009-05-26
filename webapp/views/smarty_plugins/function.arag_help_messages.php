<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author:  Emil Sedgh <emilsedgh@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {arag_help_messages} function plugin                             |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    Arag Help Messages                                             |
// | Purpose: Showing help messages of help module                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_help_messages($params, &$smarty)
{
    $helpMan = Model::load('HelpManager', 'help');
    $uri     = isset($params['uri']) ? $params['uri'] : $helpMan->encode(url::current(False, True));
    $helps   = $helpMan->getByUri($uri);
    $smarty->assign('helps', $helps);

    return $smarty->fetch(APPPATH.'views/arag_templates/helps.tpl');
}
?>
