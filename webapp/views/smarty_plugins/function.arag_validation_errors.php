<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_validation_errors} function plugin                         |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_validation_errors                                         |
// | Purpose: Shows errors occured in validation                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_validation_errors($params, &$smarty)
{
    $prefix   = '<div>';
    $suffix   = '</div>';
    $template = 'error';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'prefix':
            case 'suffix':
            case 'template':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_validation_errors: Unknown attribute '$_key'");
        }
    }

    $controller = Kohana::instance();

    if (isset($controller->validation) && count($errors = $controller->validation->errors())) {

        $names         = $controller->validation->names();
        $error_message = Null;

        foreach ($errors as $field => $error) {

            $name           = (isset($names[$field]) && !empty($names[$field])) ? $names[$field] : $field;
            $message        = $controller->validation->message($field.'_'.$error);
            $message        = empty($message) ? $controller->validation->message($error) : $message;
            $error_message .= $prefix.sprintf($message, $name).$suffix;
        }

        if ($template) {
            include_once $smarty->_get_plugin_filepath('block', 'arag_block');
            $repeat = False;
            $error  = smarty_block_arag_block(Array('template' => $template), $error_message, $smarty, $repeat);
        }

        return $error;
    }

    return Null;
}

?>
