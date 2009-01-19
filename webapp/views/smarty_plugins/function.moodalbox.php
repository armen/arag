<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {asterisk} function plugin                                       |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    asterisk                                                       |
// | Purpose: shaws an asterisk                                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_function_moodalbox($params, &$smarty)
{
    $title   = Null;
    $height  = Null;
    $width   = Null;
    $class   = Null;
    $id      = Null;
    $text    = Null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'title':
            case 'height':
            case 'width':
            case 'html_id':
            case 'class':
            case 'href':
            case 'text':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("moodalbox: Unknown attribute '$_key'");
        }
    }

    if (!isset($href)) {
       $smarty->trigger_error("moodalbox: missing 'href' attribute");
       return Null;
    }

    if (!isset($text)) {
       $smarty->trigger_error("moodalbox: missing 'text' attribute");
       return Null;
    }

    $data['href']    = $href;
    $data['title']   = $title;
    $data['width']   = $width;
    $data['height']  = $height;
    $data['html_id'] = $id;
    $data['class']   = $class;
    $data['text']    = $text;

    $view = new View('arag_templates/moodalbox', $data);
    return $view->render();
}

?>
