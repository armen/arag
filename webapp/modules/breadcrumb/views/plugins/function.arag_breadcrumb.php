<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {arag_breadcrumb} breadcrumb plugin                              |
// |                                                                         |
// | Type:    arag_breadcrumb                                                |
// | Name:    arag_history                                                   |
// | Purpose: Generating breadcrumb                                          |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_breadcrumb($params, &$smarty)
{
    $ext             = Kohana::config('smarty.templates_ext');
    $template        = 'breadcrumb';
    $config          = 'config';
    $module          = Null;
    $config_file     = Null;
    $css             = Null;
    $show_next_steps = False;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = '_'.$_val;
                break;

            case 'template':
                $template = $_val;
                $template = rtrim($template, '.'.$ext);
                break;

            case 'config':
                $config = $_val;
                break;

            case 'module':
                $module = $_val;
                break;

            case 'css':
                $css = $_val;
                break;

            case 'show_next_steps':
                $show_next_steps = $_val;
                break;

            default:
                $smarty->trigger_error("arag_breadcrumb: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_breadcrumb');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        list($name) = $name;
    } else if (!is_string($name)) {
        // if name is string then it is set as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('arag_breadcrumb: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    // Returned breadcrumb is an array, we need first element
    $breadcrumb = $smarty->get_template_vars($name);

    if (isset($breadcrumb)) {

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');

        // Config File
        $config_file = $breadcrumb->get_config($config, $module);

        if (!isset($config_file) || empty($config_file)) {
           $smarty->trigger_error('arag_breadcrumb: invalid or empty config file!', E_USER_ERROR);
        }

        $breadcrumb->set_visited_uri($namespace);

        $visited_uris = $breadcrumb->get_visited_uris($namespace);

        $smarty->assign('breadcrumb', $breadcrumb);
        $smarty->assign('name', $name);
        $smarty->assign('css', $css);
        $smarty->assign('current_uri', $breadcrumb->current_uri());
        $smarty->assign('visited_uris', $visited_uris);
        $smarty->assign('number_of_visited_uris', count($visited_uris) - 1);
        $smarty->assign('config', $config_file);
        $smarty->assign('total_items', count($config_file) - 1);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('show_next_steps', $show_next_steps);

        return $smarty->fetch(Arag::find_file('breadcrumb', 'views', $template, False, $ext));
    }

    return Null;
}

?>
