<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {asterisk} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    asterisk                                                       |
// | Purpose: shaws an asterisk                                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_asterisk($params, &$smarty)
{
    $assign  = Null;
    $message = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'assign':
            case 'message':
                $$_key = (string)$_val;
                break;
                
            default:
                $smarty->trigger_error("asterisk: Unknown attribute '$_key'");
        }
    }

    $output = '<span class="asterisk">&nbsp;*&nbsp;</span>';
    
    if ($message != Null && strpos($message, '%s')) {
        $output = sprintf($message, $output);
    }

    if ($assign != Null) {
        $smarty->assign($assign, $output);
    } else {
        return $output;
    }
}

?>
