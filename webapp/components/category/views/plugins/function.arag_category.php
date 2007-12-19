<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {arag_category}                                                  |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_category                                                  |
// | Purpose: Generating category                                            |
// +-------------------------------------------------------------------------+
// $Id: function.arag_category.php 445 2007-12-08 14:09:02Z sasan $
// ---------------------------------------------------------------------------

function smarty_function_arag_category($params, &$smarty)
{
    $template = 'directory';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = $_val;
                break;

            case 'template':
                $template = $_val;
                $template = str_replace(Config::item('smarty.templates_ext'), '', $template);

                break;

            default:
                $smarty->trigger_error("arag_category: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_category');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        $name = current($name);

    } else if (!is_string($name)) {
        // if name is string then it setted as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('arag_category: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    // Returned category is an array, we need first element
    $category = $smarty->get_template_vars($name);

    // Get namespace
    $namespace = $smarty->get_template_vars($name.'_namespace');    
 
    if (file_exists(APPPATH . 'components/category/views/' . $template . '.tpl')) {
        $template = APPPATH . 'components/category/views/' . $template . '.tpl';
    } else {
        $template = APPPATH . 'modules/' . Router::$module . '/views/' . $template . '.tpl';
    }

    if (isset($category)) {

        $smarty->assign('category', $category);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('category_templates_path', APPPATH . 'components/category/views/');
        
        return $smarty->fetch($template);
    }

    return Null;
}

?>
