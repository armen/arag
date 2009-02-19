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
        $class_name = 'tooltip';

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'activator':
                case 'title':
                case 'class_name':
                case 'tooltip':
                    $$_key = $_val;
                    break;

                default:
                    $smarty->trigger_error("arag_tooltip: Unknown attribute '$_key'");
            }
        }
        $content = str_replace("\n", '', $content);
        $content = str_replace('"', "'", $content);
        $smarty->assign('title', $title);
        $smarty->assign('activator', $activator);
        $smarty->assign('content', $content);
        $smarty->assign('class_name', $class_name);

        // Find template location
        $template = Kohana::find_file('views', 'arag_templates/arag_tooltip', True, 'tpl');

        return $smarty->fetch($template);
    }
}
