<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Peyman Karimi <zeegco@yahoo.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Theme_manager_Model extends Model
{
    // {{{ set
    public function set($styles, $namespace)
    {
        Arag_Config::set('styles', $styles, $namespace);
    }
    // }}}
    // {{{ get
    public function getStyles($namespace)
    {
        return Arag_Config::get('styles', array(), $namespace);
    }
    // }}}
    // {{{ getDefaults
    public function getDefaults()
    {
        return Arag_Config::get('config.default_styles', array(), 'theme_manager', true);
    }
    // }}}
}
