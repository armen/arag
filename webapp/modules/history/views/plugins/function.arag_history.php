<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_history} history plugin                                    |
// |                                                                         |
// | Type:    history function                                               |
// | Name:    arag_history                                                   |
// | Purpose: Generating history navigation bar                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_history($params, &$smarty)
{
    $ext      = Kohana::config('smarty.templates_ext');
    $template = 'navigation';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = $_val;
                break;

            case 'template':
                $template = $_val;                
                $template = rtrim($template, '.'.$ext);
                break;

            default:
                $smarty->trigger_error("arag_history: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_history');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        list($name) = $name;
    } else if (!is_string($name)) {
        // if name is string then it setted as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('arag_history: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    // Returned history is an array, we need first element
    $history = $smarty->get_template_vars($name);

    if (isset($history)) {

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');        

        $smarty->assign('history', $history);
        $smarty->assign('current_history', $history->get_history());
        $smarty->assign('separator', Kohana::config('history.separator'));
        $smarty->assign('namespace', $namespace);
        $smarty->assign('history_templates_path', MODPATH . 'history/views/');

        return $smarty->fetch(Arag::find_file('history', 'views', $template, False, $ext));
    }

    return Null;
}

?>
