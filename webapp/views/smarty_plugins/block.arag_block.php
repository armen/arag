<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_block}{/arag_block} block plugin                           |
// |                                                                         |
// | Type:    block function                                                 |
// | Name:    arag_block                                                     |
// | Purpose: Generating block                                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_block($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {
        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'template':
                case 'title':
                case 'align':
                case 'dir':
                    $$_key = (string)$_val;
                    break;

                default:
                    if (valid::id($_key)) {
                       $smarty->assign($_key, security::xss_clean($_val));
                    } else {
                        $smarty->trigger_error("arag_block: unknown attribute '$_key'");
                    }
            }
        }

        // Set default values
        if (!isset($template)) { $template = 'arag_block'; }
        if (!isset($title)) { $title = Null; }
        if (!isset($align)) { $align = ''; }

        // Find template location
        $template = Kohana::find_file('views', 'arag_blocks/' . $template, True, 'tpl');

        // Set alignment
        if (($align == 'right' || $align == 'left') && !isset($dir)) {
            include_once $smarty->_get_plugin_filepath('function', $align);
            $function = "smarty_function_$align";
            $align    = $function(Null, $smarty);
        }

        if (trim($align)) {
            $align = ' align="'.$align.'"'; // there is a space at begining
        }

        // Set direction
        $direction = Null;

        if (!isset($dir) || $dir == Null) {
            include_once $smarty->_get_plugin_filepath('function', 'dir');
            $dir       = smarty_function_dir(Null, $smarty);
            $direction = ' dir="'.$dir.'"'; // there is a space at begining
        }

        $iconAlign = 'left';
        if ($dir == 'rtl') {
            $iconAlign = 'right';
        }

        $smarty->assign_by_ref('arag_block_content', $content);
        $smarty->assign('arag_block_title', $title);
        $smarty->assign('arag_block_direction', $direction);
        $smarty->assign('arag_block_align', $align);
        $smarty->assign('arag_icon_align', $iconAlign);

        return $smarty->fetch($template);
    }
}

?>
