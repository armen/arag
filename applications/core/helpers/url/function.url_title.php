<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {url_title} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    url_title                                                      |
// | Purpose: Convert a phrase to a URL-safe title                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_url_title($params, &$smarty)
{
    // Default variables
    $separator = '-';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'title':       
            case 'separator':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("url_title: unknown attribute '$_key'");
        }
    }

    if (!isset($title)) {
       $smarty->trigger_error("url_title: missing 'title' attribute");
    }    

    return url::title($title, $separator);
}

?>
