<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {execute_controller} function plugin                             |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    execute_controller                                             |
// | Purpose: Executes the called controller                                 |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_execute_controller($params, &$smarty)
{
    return Controller::execute($params['uri'], True, True, array(), False);
}
