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
    function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'ControlPanel';

        // Load the empty tabbed block
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
    }
    // }}}
    // {{{ index
    public function index()
    {
        $modules = Model::load('Modules', 'core');

        $this->layout->content = new View('backend/controlpanel');

        if (defined('MASTERAPP')) {
            $this->layout->content->modules = $modules->getModules();
        } else {
            $this->layout->content->modules = $modules->getModules(Array('multisite', 'ta_locator', 'core'));
        }
    }
    // }}}
}

?>
