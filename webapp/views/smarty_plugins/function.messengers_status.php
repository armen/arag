<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {messengers_status} function plugin                              |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    messengers_status                                              |
// | Purpose: messengers_status                                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_messengers_status($params, &$smarty)
{
    return Controller::execute('messengers_status/frontend/status/index', True, False, array(), False);
}
