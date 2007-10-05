<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {slot_head} function plugin                                      |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    slot                                                           |
// | Purpose: load a slot.                                                   |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_slot($params, &$smarty)
{
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = $_val;
                break;

            default:
                $smarty->trigger_error("slot: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
       $smarty->trigger_error("slot: missing 'name' attribute");
    }

    $slots = $smarty->get_template_vars('_slots');
    
    return isset($slots[$name]) ? $slots[$name] : Null;
}

?>
