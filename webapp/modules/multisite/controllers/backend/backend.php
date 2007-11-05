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

        // Load the model
        $this->load->model('MultiSite');        
       
        // Backend decorator
        $this->load->decorator('backend/decorator');

        // Default page title
        $this->load->vars(Array('page_title' => 'MultiSite'));

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("MultiSite"));
        $this->global_tabs->addItem(_("Sites"), 'multisite/backend/site');
        $this->global_tabs->addItem(_("Install"), 'multisite/backend/site/install');
        $this->global_tabs->addItem(_("Delete Site"), 'multisite/backend/site/delete/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Edit Site"), 'multisite/backend/site/edit/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Preview Site"), 'multisite/backend/site/preview/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Settings"), 'multisite/backend/settings');
    }
    // }}}
}

?>
