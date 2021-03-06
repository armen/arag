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
    $ext      = Kohana::config('smarty.templates_ext');
    $template = 'horizontal';

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
        $smarty->assign('plist_templates_path', MODPATH . 'plist/views/');

        $result = $smarty->fetch(Arag::find_file('plist', 'views', $template, False, $ext));

        if ($plist->hasCsv()) {

            $resource = iterator_to_array($plist->getIterator());

            $full_resource = $plist->getFullResource();
            if (!$full_resource) {
                $full_resource = iterator_to_array($plist->getResource());

            }
            if ($full_resource instanceof Database) {
                $full_resource = $full_resource->get_sql();
            }

            Session::instance()->set_flash('plist_'.$namespace, Array
            (
                'resource'      => $resource,
                'full_resource' => $full_resource,
                'columns'       => $plist->getColumns()
            ));
        }

        return $result;
    }

    return Null;
}

?>
