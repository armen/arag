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

function smarty_block_arag_block($params, $content, &$smarty)
{
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'template':
            case 'title':
            case 'align':
            case 'dir':
                $$_key = (string)$_val;
                break;

            default:
                $smarty->trigger_error("arag_block: unknown attribute '$_key'");
        }
    }
    
    // Set default values
    if (!isset($template)) { $template = 'arag_block'; }
    if (!isset($title)) { $title = Null; }
    if (!isset($align)) { $align = ''; }

    // Find template location  

    if (is_readable(Config::item('arag.templates_path') . 'arag_blocks/' . $template . '.tpl')) {
        $template = Config::item('arag.templates_path') . 'arag_blocks/' . $template . '.tpl';
    
    } else {
        // I can't find it
        $smarty->trigger_error("arag_block: unreachable template file '$template'");
    }
    
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

?>
