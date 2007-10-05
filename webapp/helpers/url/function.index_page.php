<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {index_page} function plugin                                     |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    index_page                                                     |
// | Purpose: retuen site's index page                                       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_index_page($params, &$smarty)
{
    $CI =& get_instance();
    $CI->load->helper('url');
    
    return index_page();
}

?>
