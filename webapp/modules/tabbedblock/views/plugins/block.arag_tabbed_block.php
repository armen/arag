<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// | Smarty {arag_tabbed_block}{/arag_tabbed_block} tabbed block plugin      |
// |                                                                         |
// | Type:    tabbed block function                                          |
// | Name:    arag_tabbed_block                                              |
// | Purpose: Generating tabbed block                                        |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

function smarty_block_arag_tabbed_block($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {

        require_once $smarty->_get_plugin_filepath('function', 'dir');
        require_once $smarty->_get_plugin_filepath('function', 'left');

        $ext      = Kohana::config('smarty.templates_ext');
        $dir      = smarty_function_dir(Null, $smarty);
        $left     = smarty_function_left(Null, $smarty);
        $name     = isset($params['name']) ? '_'.$params['name'] : $smarty->get_template_vars('_tabbedblock');

        if (is_array($name)) {
            // if name is array then we have to get just first element by default becuase name parameter
            // not specified
            list($name) = $name;
        } elseif (!is_string($name)) {
            $smarty->trigger_error('arag_tabbed_block: can not find name parameter or it is invalid!', E_USER_ERROR);
        }

        // Returned tabbedBlock is an array, we need first element
        $tabbedBlock = $smarty->get_template_vars($name);

        if (empty($tabbedBlock)) {
            // Do nothing just return the content
            return $content;
        }

        $template = $tabbedBlock->getTemplate();
        $params   = array_merge($params, $tabbedBlock->_attributes);

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'name':
                    // name is handled already
                    break;

                case 'template':
                    $template = $_val;
                    $template = text::strrtrim($template, '.'.$ext);
                    break;

                case 'show_icon':
                    $show_icon = $_val;
                default:
                    // Pass all unknown variables to template, its odd but it adds more felexibility
                    // for custom templates
                    $smarty->assign($_key, $_val);
            }
        }

        if (!isset($show_icon)) {
            $show_icon = True;
        }

        // Get selected item
        $selectedItemName = Null;
        $selectedItem     = Null;

        foreach ($tabbedBlock->getItems() as $key => $item) {

            // We can't replace parameters in addItems, just because it's possible that setParameter is called after addItem
            if (isset($item['name'])) {
                $tabbedBlock->_items[$key]['name'] = $tabbedBlock->replaceParameter($tabbedBlock->_items[$key]['name']);
            }

            if (isset($item['uri'])) {

                // Remove the trailing /index and trim /
                $uri = preg_replace('|/index$|', '', trim($item['uri'], '/'));

                // Ignore additional segments based on number of segments in item's uri
                // number of segemens should be at least 4 this is why max used
                $number_of_segs = substr_count($uri, '/') + 1;
                $ruri           = implode('/', array_slice(Router::$rsegments, 0, max($number_of_segs, 4)));
                $ruri           = preg_replace('|/index$|', '', $ruri);

                // Replace configuration variables with a [^/]+ to match everything except /
                $uri = '|^'.preg_replace('|%[a-zA-Z_0-9]*%|', '[^/]+', preg_quote($uri, '|')).'$|';

                if (($item['selected'] != True && preg_match($uri, $ruri)) || $item['selected'] == True) {

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

            $smarty->assign_by_ref('tabbedblock', $tabbedBlock);
            $smarty->assign_by_ref('tabbedblock_content', $content);
            $smarty->assign_by_ref('tabbedblock_items', $tabbedBlock->getItems());
            $smarty->assign('tabbedblock_title', $tabbedBlock->getTitle());
            $smarty->assign('tabbedblock_selected_tab_name', $selectedItemName);
            $smarty->assign('tabbedblock_selected_tab', $selectedItem);
            $smarty->assign('tabbedblock_module', Router::$module);
            $smarty->assign('show_icon', $show_icon);

            return $smarty->fetch(Arag::find_file('tabbedblock', 'views', $template, True, $ext));

        } else {

            // Do nothing just return the content
            return $content;
        }
    }
}

?>
