<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'ControlPanel';

        // Load the empty tabbed block
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
    }
    // }}}
    // {{{ index
    public function index_any()
    {
        $modules = Model::load('Modules', 'core');

        $this->layout->content = new View('backend/controlpanel');

        if (MASTERAPP) {
            $modules = $modules->getModules();
        } else {
            $modules = $modules->getModules(Array('multisite', 'core'));
        }

        $accessible_modules = Array();

        foreach ($modules as $module) {
            if (Arag_Auth::is_accessible($module['module'].'/backend')) {
                $module['name']       = _($module['name']);
                $accessible_modules[] = $module;
            }
        }

        $this->layout->content->modules = $accessible_modules;
    }
    // }}}
}
