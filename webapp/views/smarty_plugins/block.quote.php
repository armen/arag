<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {quote}{/quote} block plugin                                     |
// |                                                                         |
// | Type:    block function                                                 |
// | Name:    quote                                                          |
// | Purpose: return quote                                                   |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_quote ($params, $content, &$smarty)
{
    return "\"$content\"";
}

?>
