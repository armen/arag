<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Peyman Karimi <peykar@gmail.com>                                |
// +-------------------------------------------------------------------------+
// | Smarty {arag_categorylist} categorylist plugin                          |
// |                                                                         |
// | Type:    arag_categorylist                                              |
// | Name:    arag_categorylist                                              |
// | Purpose: Show categories                                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_categorylist($params, &$smarty)
{
    $ext         = Kohana::config('smarty.templates_ext');
    $name        = 'category';
    $template    = 'categorylist';
    $category_id = null;
    $extra       = array();
    $uri_schema  = '';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'template':
                $template = $_val;
                $template = rtrim($template, '.'.$ext);
                break;

            case 'category_id':
            case 'uri_schema':
            case 'extra':
            case 'name':
                $$_key = $_val;
                break;

            default:
                $smarty->trigger_error("arag_categorylist: Unknown attribute '$_key'");
        }
    }

    $category_manager = Model::load('Category_Manager', 'category_manager');
    $category         = $category_manager->getCategory($category_id);

    if ($category) {

        $childs = $category_manager->getCategories($category_id);

        $smarty->assign('category_manager', $category_manager);
        $smarty->assign('name', $name);
        $smarty->assign('current_category', $category);
        $smarty->assign('childs', $childs);
        $smarty->assign('uri_schema', $uri_schema);

        foreach($extra as $key => $value) {
            is_int($key) OR $smarty->assign($key, $value);
        }

        return $smarty->fetch(Arag::find_file('category_manager', 'views', $template, False, $ext));
    }

    return Null;
}

?>
