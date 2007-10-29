<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_tabbed_block}{/arag_tabbed_block} tabbed block plugin|
// |                                                                         |
// | Type:    tabbed block function                                          |
// | Name:    arag_tabbed_block                                           |
// | Purpose: Generating tabbed block                                        |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_tabbed_block($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {

        require_once $smarty->_get_plugin_filepath('function', 'dir');
        require_once $smarty->_get_plugin_filepath('function', 'left');

        $dir      = smarty_function_dir(Null, $smarty);
        $left     = smarty_function_left(Null, $smarty);
        $name     = $smarty->get_template_vars('_tabbedblock');
        $template = 'arag_tabbed_block';        

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'name':
                    $name = $_val;
                    break;

                case 'template':
                    $template = $_val;
                    $template = str_replace(ARAG_TPL_EXT, '', $template);                    
                    break;
                    
                default:
                    $smarty->trigger_error("arag_tabbed_block: Unknown attribute '$_key'");
            }
        }

        if (is_array($name)) {
            // if name is array then we have to get just first element by default becuase name parameter
            // not specified
            list($name) = $name;
        } elseif (!is_string($name)) {
            $smarty->trigger_error('arag_tabbed_block: can not find name parameter or it is invalid!', E_USER_ERROR);
        }

        // Returned tabbedBlock is an array, we need first element
        list($tabbedBlock) = $smarty->get_template_vars($name);

        $CI        =& get_instance();
        $rsegments =  $CI->uri->rsegment_array();

        // Get selected item
        $selectedItemName = Null;        
        $selectedItem     = Null;

        foreach ($tabbedBlock->getItems() as $key => $item) {

            if (isset($item['uri'])) {

                $segments = explode('/', rtrim($item['uri'], '/'));
                $selected = True;

                foreach ($segments as $id => $segment) {

                    if (!preg_match('|^%[a-zA-Z_0-9]*%$|', $segment) && (!isset($rsegments[$id+1]) || $segment != $rsegments[$id+1])) {
                        $selected = False;
                        break;
                    }
                }

                if (count($segments) < count($rsegments)) {
                    
                    $methodSegmentNum = ($CI->uri->router->fetch_directory() == Null) ? 3 : 4;

                    if (count($segments) < $methodSegmentNum-1 ||       // default method is not present in item uri
                        $rsegments[$methodSegmentNum] != 'index') {     // routed method is not index
                        $selected = False;
                    }
                }

                if ($item['selected'] != True && $selected) {

                    // Set selected to true
                    $tabbedBlock->_items[$key]['selected'] = True;

                    // Set selected item name
                    $selectedItemName = $tabbedBlock->_items[$key]['name'];

                    if ($tabbedBlock->_items[$key]['is_parent']) {
                        // Selected item is parent
                        $selectedItem = $tabbedBlock->_items[$key];
                    } else {

                        $parentUri = $tabbedBlock->_items[$key]['parent_uri'];
                    
                        // Set parent status to selected 
                        $tabbedBlock->_items[md5($parentUri)]['selected']            = True;
                        $tabbedBlock->_items[md5($parentUri)]['has_selected_subtab'] = True;

                        // Selected item is not parent, so set parent as selected item
                        $selectedItem = $tabbedBlock->_items[md5($parentUri)];                        
                    }
                } 
            }
        }

        if (isset($tabbedBlock) && count($tabbedBlock->getItems()) != 0) {

            $moduleIconURL = $CI->config->item('base_url') . 'images/modules/' . $CI->uri->router->fetch_module() . '.png';    

            $smarty->assign_by_ref('tabbedblock', $tabbedBlock);
            $smarty->assign_by_ref('tabbedblock_content', $content);
            $smarty->assign_by_ref('tabbedblock_items', $tabbedBlock->getItems());
            $smarty->assign('tabbedblock_title', $tabbedBlock->getTitle());
            $smarty->assign('tabbedblock_selected_tab_name', $selectedItemName);
            $smarty->assign('tabbedblock_selected_tab', $selectedItem);

            $smarty->assign('tabbedblock_module_icon_url', $moduleIconURL);

            if (file_exists(APPPATH . 'components/tabbedblock/templates/' . $template . '.tpl')) {
                $template = APPPATH . 'components/tabbedblock/templates/' . $template . '.tpl';
            } else {
                $template = $CI->load->get_view_path() . $template . '.tpl';
            }

            return $smarty->fetch($template);
        }

        return Null;
    }        
}

?>
