<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_plist} list plugin                                         |
// |                                                                         |
// | Type:    list function                                                  |
// | Name:    arag_plist                                                     |
// | Purpose: Generating plist                                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_plist($params, &$smarty)
{
    $ext      = '.'.Config::item('smarty.templates_ext');
    $template = 'horizontal'.$ext;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = $_val;
                break;

            case 'template':
                $template = $_val;                
                $template = rtrim($template, $ext).$ext;
                break;

            default:
                $smarty->trigger_error("arag_plist: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_plist');
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
    $plist = $smarty->get_template_vars($name);

    if (isset($plist)) {

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');        

        $smarty->assign('plist', $plist);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('plist_templates_path', APPPATH . 'modules/plist/views/');

        // Change include_once to this component and current module path
        Config::set('core.include_paths', array_unique(array_merge(Config::include_paths(), Array(APPPATH.'modules/plist'))));
        
        return $smarty->fetch(Kohana::find_file('views', $template, False, True));
    }

    return Null;
}

?>
