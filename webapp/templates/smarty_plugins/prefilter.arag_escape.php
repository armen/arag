<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty plugin                                                           |
// |                                                                         |
// | Type:    pre                                                            |
// | Name:    arag_escape_filter                                             |
// | Purpose: Escape the variables with arag_escape modifier                 |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_prefilter_arag_escape($tpl, &$smarty) 
{
    $exclude = Config::item('smarty.escape_exclude_list');
    $matched = False;

    foreach ($exclude as $pattern) {
        if (preg_match($pattern, $smarty->_current_file)) {
            $matched = True;
            break;
        }
    }

    if (!$matched) {
        $smarty->default_modifiers = Array("arag_escape:'" . Config::item('arag.i18n_language_charset') . "'");
    }

    return $tpl;
}

?>
