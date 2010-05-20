<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {arag_contacts} contacs plugin                                   |
// |                                                                         |
// | Type:    list function                                                  |
// | Name:    arag_contacts                                                  |
// | Purpose: contacts                                                       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_contacts($params, &$smarty)
{
    $ext      = Kohana::config('smarty.templates_ext');
    $template = 'contacts';

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
                $smarty->trigger_error("arag_contacts: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_contacts');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        $name = current($name);

    } else if (!is_string($name)) {
        // if name is string then it setted as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('arag_contacts: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    // Returned contacts is an array, we need first element
    $contacts = $smarty->get_template_vars($name);

    if (isset($contacts)) {

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');

        if ($contacts->getContacts() == Null) {
            $contacts->build();
        }

        $session = Session::instance();

        $smarty->assign('component', $contacts);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('contacts_templates_path', MODPATH . 'contacts/views/');

        return $smarty->fetch(Arag::find_file('contacts', 'views', $template, False, $ext));
    }

    return Null;
}

?>
