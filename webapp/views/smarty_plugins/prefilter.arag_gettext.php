<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty plugin                                                           |
// |                                                                         |
// | Type:    prefilter                                                      |
// | Name:    arag_gettext                                                   |
// | Purpose: wraper for php gettext function                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_prefilter_arag_gettext($tpl, &$smarty)
{
    return preg_replace_callback('/(_|gettext)\("(.+?)"\)/', '_gettext', $tpl);
}

function _gettext($match)
{
    return gettext($match[2]);
}
