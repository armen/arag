<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {url_current} function plugin                                    |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    url_current                                                    |
// | Purpose: Fetches the current URI.                                       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_url_current($params, &$smarty)
{
    return url::current();
}

?>
