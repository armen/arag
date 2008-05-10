<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_is_accessible}{/arag_is_accessible} block plugin           |
// |                                                                         |
// | Type:    block function                                                 |
// | Name:    arag_is_accessible                                             |
// | Purpose: shows the content if it is acceessible by user                 |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_is_accessible($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {

        $uri = Null;
     
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'uri':
                    $$_key = (string)$_val;
                    break;

                default:
                    $smarty->trigger_error("arag_accessible: unknown attribute '$_key'");
            }
        }

        empty($uri) AND $smarty->trigger_error("arag_accessible: uri parameter should be set.");

        if (Arag_Auth::is_accessible($uri)) {
            return $content;
        }

        return Null;
    }
}

?>
