<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {word_wrap} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    word_wrap                                                      |
// | Purpose: Wraps text at the specified character count                    |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_word_wrap($params, &$smarty)
{
    // Default variables
    $str     = '';
    $nl2br   = TRUE;
    $charlim = '76';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'str':
            case 'charlim':
                $$_key = (string)$_val;
                break;
            
            case 'nl2br':
                $$_key = (string)$_val;
                if (trim(strtoupper($$_key)) == "FALSE")
                    $$_key = FALSE;
                else
                    $$_key = TRUE;
                break;

            default:
                $smarty->trigger_error("word_wrap: unknown attribute '$_key'");
        }
    }

    $CI =& get_instance();
    $CI->load->helper('text');

    if ($nl2br)
        return nl2br(word_wrap($str, $charlim));
    else
        return word_wrap($str, $charlim);
}

?>
