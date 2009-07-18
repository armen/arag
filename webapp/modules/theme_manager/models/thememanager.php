<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Peyman Karimi <zeegco@yahoo.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ThemeManager_Model extends Model
{
    // {{{ set
    public function set($styles)
    {
        Arag_Config::set('styles', $styles);
        Arag_Config::set('makeup_time', time());
    }
    // }}}
    // {{{ get
    public function get()
    {
        return Arag_Config::get('styles', array());
    }
    // }}}
    // {{{ getMakeupTime
    public function getMakeupTime()
    {
        return Arag_Config::get('makeup_time', 0, 'theme_manager');
    }
    // }}}
    // {{{ getDefaults
    public function getDefaults()
    {
        $default_styles = Arag_Config::get('config.default_styles', array(), 'theme_manager', true);

        foreach ($default_styles as &$styl) {
            $styl['description'] = _($styl['description']);
        }

        return $default_styles;
    }
    // }}}
}
