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
    $x = 16;
    $y = 16;

    if (!$repeat) {
        $class_name = 'tooltip';

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'activator':
                case 'title':
                case 'class_name':
                case 'x':
                case 'y':
                case 'tooltip':
                    $$_key = $_val;
                    break;

                default:
                    $smarty->trigger_error("arag_tooltip: Unknown attribute '$_key'");
            }
        }
        $title   = str_replace('"', "'", $title);
        $title   = str_replace("\n", '', $title);
        $title   = str_replace('/', "\/", $title);
        $content = str_replace("\n", '', $content);
        $content = str_replace('"', "'", $content);
        $content = str_replace('/', "\/", $content);
        $smarty->assign('title', $title);
        $smarty->assign('activator', $activator);
        $smarty->assign('content', $content);
        $smarty->assign('class_name', $class_name);
        $smarty->assign('x', $x);
        $smarty->assign('y', $y);

        // Find template location
        $template = Kohana::find_file('views', 'arag_templates/arag_tooltip', True, 'tpl');

        return $smarty->fetch($template);
    }
}
