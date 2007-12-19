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
    $template = 'horizontal';

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

    // Get namespace
    $namespace = $smarty->get_template_vars($name.'_namespace');    
 
    if (file_exists(APPPATH . 'components/plist/views/' . $template . '.tpl')) {
        $template = APPPATH . 'components/plist/views/' . $template . '.tpl';
    } else {
        $template = APPPATH . 'modules/' . Router::$module . '/views/' . $template . '.tpl';
    }

    if (isset($plist)) {

        $smarty->assign('plist', $plist);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('plist_templates_path', APPPATH . 'components/plist/views/');
        
        return $smarty->fetch($template);
    }

    return Null;
}

?>
