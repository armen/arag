<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {url_title} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    url_title                                                      |
// | Purpose: Takes a string as input and return a human friendly string     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_url_title($params, &$smarty)
{
    // Default variables
    $separator = 'dash';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'string':       
            case 'separator':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("url_title: unknown attribute '$_key'");
        }
    }

    if (!isset($string)) {
       $smarty->trigger_error("url_title: missing 'string' attribute");
    }    

    $CI =& get_instance();
    $CI->load->helper('url');

    return url_title($string, $separator);
}

?>
