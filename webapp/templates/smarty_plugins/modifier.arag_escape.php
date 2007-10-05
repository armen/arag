<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty plugin                                                           |
// |                                                                         |
// | Type:    modifier                                                       |
// | Name:    arag_escape                                                    |
// | Purpose: Escape the variables with htmlentities                         |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_modifier_arag_escape($string, $char_set = Null)
{
    if ($char_set == Null) {
        $CI       =& get_instance();
        $char_set =  $CI->config->item('charset');
    }

    if (is_string($string)) {
        return htmlentities($string, ENT_QUOTES, $char_set);
    }

    return $string;
}

?>
