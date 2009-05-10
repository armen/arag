<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_header}{/arag_header} header plugin                        |
// |                                                                         |
// | Type:    header function                                                |
// | Name:    arag_header                                                    |
// | Purpose: Showing something in header                                    |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_header($params, $content, &$smarty, &$repeat)
{
    $key = sha1($content);
    if (!isset($GLOBALS['loaded_headers'][$key])) {

        $GLOBALS['loaded_headers'][$key] = True;
        $GLOBALS['headers'][]            = $content;
    }
}

?>
