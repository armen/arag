<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Emil Sedgh <emilsedgh@gmail.com>
 * $Id$
 */

// ------------------------------------------------------------------------


class locations {
    // {{{
    public function required($arg)
    {
        $selected = locations::get($arg);
        return !empty($selected);
    }
    // }}}
    // {{{ get
    public function get($array)
    {
        if (!is_array($array)) {
            return 0;
        }
        $current = '';
        while(!strlen($current)) {
            if (!count($array)) {
                return False;
            }
            $current = array_pop($array);
        }
        return $current;
    }
    // }}}
    // {{{ display
    public function display($row, $field)
    {
        $view = New View('arag_templates/location');
        $view->location = $row[$field];
        return $view->render();
    }
    // }}}
}
