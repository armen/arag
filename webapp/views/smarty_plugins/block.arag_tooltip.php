<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors:Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// | Smarty {arag_tooltip} function plugin                                   |
// |                                                                         |
// | Type:    function                                                       |
// | Name:    arag_tooltip                                                   |
// | Purpose: Generating an stylish tool tip                                 |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_tooltip($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {
        $class  = Null;
        $style  = Null;
        $tip_class = 'tool-tip';
        $inline = False;
        $text   = '&nbsp;';

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'type'     :
                case 'inline'   :
                case 'class'    :
                case 'id'       :
                case 'href'     :
                case 'style'    :
                case 'tip_text' :
                case 'tip_title':
                case 'tip_class':
                    $$_key = $_val;
                    break;

                default:
                    $smarty->trigger_error("arag_tooltip: Unknown attribute '$_key'");
            }
        }

        if (!isset($id)) {
           $smarty->trigger_error("arag_tooltip: missing 'id' attribute");
           return Null;
        }

        if (!isset($type)) {
           $smarty->trigger_error("arag_tooltip: missing 'type' attribute");
           return Null;
        } else if (strtolower($type) != 'link' && strtolower($type) != 'block') {
           $smarty->trigger_error("arag_tooltip: '$type' is an uknown 'type'. Valid types are 'link' and 'block'");
           return Null;
        }

        if (strtolower($type) == 'link' && !isset($href)) {
           $smarty->trigger_error("arag_tooltip: You are using 'link' as type. Therefore you have to specify href");
           return Null;
        }

        $smarty->assign_by_ref('content', $content);
        $smarty->assign('type', strtolower($type));
        $smarty->assign('tip_id', $id);
        // If it should be a span
        $smarty->assign('inline', $inline);
        $smarty->assign('class', $class);
        $smarty->assign('href', isset($href) ? $href : NULL);
        $smarty->assign('style', $style);
        $smarty->assign('tip_class', $tip_class);
        $smarty->assign('tip_text', (isset($tip_text) ? $tip_text : $text));
        $smarty->assign('tip_title', (isset($tip_title) ? $tip_title : $text));

        // Find template location
        $template = Kohana::find_file('views', 'arag_templates/arag_tooltip', True, 'tpl');

        return $smarty->fetch($template);
    }
}
