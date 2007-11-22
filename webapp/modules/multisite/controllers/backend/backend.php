<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
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
        $this->load->model('Users', NULL, 'user');
        $this->load->model('Groups', NULL, 'user');
        $this->load->model('Applications', NULL, 'user');
       
        // Default page title
        $this->layout->page_title = _("MultiSite");

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("MultiSite"));
        $this->global_tabs->addItem(_("Sites"), 'multisite/backend/site');
        $this->global_tabs->addItem(_("Install"), 'multisite/backend/install/index');
        $this->global_tabs->addItem(_("Post Installer"), 'multisite/backend/postinstaller/index', 'multisite/backend/install/index');
        $this->global_tabs->addItem(_("Delete Site"), 'multisite/backend/site/delete/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Edit Site"), 'multisite/backend/site/edit/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Preview Site"), 'multisite/backend/site/preview/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Settings"), 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Page Limits"), 'multisite/backend/settings/index', 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Email Settings"), 'multisite/backend/settings/email', 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Password Settings"), 'multisite/backend/settings/password', 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Expire Time"), 'multisite/backend/settings/expire_time', 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Default Privileges"), 'multisite/backend/settings/privileges', 'multisite/backend/settings/index');
    }
    // }}}
    // {{{ _check_app
    public function _check_app($appname)
    {
        return !$this->Applications->hasApp($appname);
    }
    // }}}
}
?>
