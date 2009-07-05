<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Peyman Karimi <peykar@gmail.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_category} plugin                                           |
// |                                                                         |
// | Type:    category function                                                  |
// | Name:    arag_category                                                  |
// | Purpose: Managing Categories                                            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_category($params, &$smarty)
{
    $ext      = Kohana::config('smarty.templates_ext');
    $template = 'default';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = '_'.$_val;
                break;

            case 'template':
                $template = $_val;
                $template = text::strrtrim($template, '.'.$ext);
                break;

            default:
                $smarty->trigger_error("arag_plist: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_category');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        list($name) = $name;
    } else if (!is_string($name)) {
        // if name is string then it setted as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('arag_plist: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    // Returned list is an array, we need first element
    $category = $smarty->get_template_vars($name);

    if (isset($category)) {

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');

        $smarty->assign('category', $category);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('category_templates_path', MODPATH . 'category_manager/views/');

        return $smarty->fetch(Arag::find_file('category_manager', 'views', $template, False, $ext));
    }

    return Null;
}

?>
