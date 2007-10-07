<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {word_censor} function plugin                                    |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    word_censor                                                    |
// | Purpose: Enables you to censor words within a text string               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_word_censor($params, &$smarty)
{
    // Default variables
    $str         = '';
    $censored    = '';
    $replacement = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
            case 'replacement':
                $$_key = (string)$_val;
                break;

            case 'censored':
                $$_key = (string)$_val;
                $censored = explode ("|",$censored);
                break;

            default:
                $smarty->trigger_error("word_censor: unknown attribute '$_key'");
        }
    }
 
    $CI =& get_instance();
    $CI->load->helper('text');
    
    return word_censor($str, $censored, $replacement);
}

?>
