<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {highlight_phrase} function plugin                               |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    highlight_phrase                                               |
// | Purpose: Colorize a phrase within a string                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_highlight_phrase($params, &$smarty)
{
    // Default variables
    $str       = '';
    $phrase    = '';
    $tag_open  = '<strong>';
    $tag_close = '</strong>';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
            case 'phrase':
            case 'tag_open':
            case 'tag_close':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("highlight_phrase: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('text');

    return highlight_phrase($str,$phrase,$tag_open,$tag_close);
}

?>
