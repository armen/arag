<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_comment} comment plugin                                    |
// |                                                                         |
// | Type:    list function                                                  |
// | Name:    arag_comment                                                   |
// | Purpose: comments                                                       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_arag_comment($params, &$smarty)
{
    $ext      = Kohana::config('smarty.templates_ext');
    $template = 'linear';

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'name':
                $name = $_val;
                break;

            case 'template':
                $template = $_val;
                $template = text::strrtrim($template, '.'.$ext);
                break;

            default:
                $smarty->trigger_error("arag_comment: Unknown attribute '$_key'");
        }
    }

    if (!isset($name)) {
        $name = $smarty->get_template_vars('_comment');
    }

    if (is_array($name)) {
        // if name is array then we have to get just first element by default becuase name parameter
        // not specified
        $name = current($name);

    } else if (!is_string($name)) {
        // if name is string then it setted as parameter in plugin but if not we have to trigger error
        $smarty->trigger_error('arag_comment: can not find name parameter or it is invalid!', E_USER_ERROR);
    }

    // Returned comment is an array, we need first element
    $comment = $smarty->get_template_vars($name);

    if (isset($comment)) {

        // Get namespace
        $namespace = $smarty->get_template_vars($name.'_namespace');

        if ($comment->getComments() == Null) {
            $comment->build();
        }

        $session = Session::instance();

        $smarty->assign('component', $comment);
        $smarty->assign('namespace', $namespace);
        $smarty->assign('comment_templates_path', MODPATH . 'comment/views/');
        $smarty->assign('name', $session->get('user.name') . ' ' . $session->get('user.last_name'));
        $smarty->assign('email', $session->get('user.email'));

        return $smarty->fetch(Arag::find_file('comment', 'views', $template, False, $ext));
    }

    return Null;
}

?>
