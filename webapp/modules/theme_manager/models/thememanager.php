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
    }
    // }}}
    // {{{ get
    public function get()
    {
        return Arag_Config::get('styles', array());
    }
    // }}}
    // {{{ getDefaults
    public function getDefaults()
    {
        return Arag_Config::get('config.default_styles', array(), 'theme_manager', true);
    }
    // }}}
}
