<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.kohana_helper.php
 * Type:     function
 * Name:     kohana_helper
 * Author:   Stefan Verstege
 * Purpose:  makes it possible to use the helper functions from
 *           kohanaPHP within smarty templates
 * -------------------------------------------------------------
 */
function smarty_function_kohana_helper($params, &$smarty)
{
    if (empty($params['function'])) {
        $smarty->trigger_error("assign: missing 'function' parameter");
        return;
    }

    $kohanahelper           = $params['function'];
    list($class, $function) = split('::', $kohanahelper);

    // Check if class exists
    if ( !class_exists($class)) {
        $smarty->trigger_error("Unkown kohana helper (".$class.") called");
        return;
    }

    // Check if function exists in class
    $kohanaclass = new $class;
    if ( !method_exists($kohanaclass, $function) ) {
        $smarty->trigger_error("Unkown function (" . $function . ") called in kohana helper ". $class);
        return;
    }

    // Call the kohana helper, passing the argumens
    array_shift($params); // remove the 'function' key from the array

    $result = call_user_func_array(array($class, $function), $params);
    if (isset($params['assign']) && $params['assign']) {
        $smarty->assign($params['assign'], $result);
    } else {
        return $result;
    }
}
