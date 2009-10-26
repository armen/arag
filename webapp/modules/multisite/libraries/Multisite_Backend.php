<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Multisite_Backend extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("MultiSite");

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("MultiSite"));
        $this->global_tabs->addItem(_("Sites"), 'multisite/backend/site');
        $this->global_tabs->addItem(_("Install"), 'multisite/backend/install/index');
        $this->global_tabs->addItem(_("Post Installer"), 'multisite/backend/postinstaller/index', 'multisite/backend/install/index');
        $this->global_tabs->addItem(_("Delete Site"), 'multisite/backend/site/delete/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Edit Site"), 'multisite/backend/site/edit/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Preview Site"), 'multisite/backend/site/preview/%id%', 'multisite/backend/site');
        $this->global_tabs->addItem(_("Settings"), 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Page Limits"), 'multisite/backend/settings/index', 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("Default Privileges"), 'multisite/backend/settings/privileges', 'multisite/backend/settings/index');
        $this->global_tabs->addItem(_("User Blocking"), 'multisite/backend/settings/user_blocking', 'multisite/backend/settings/index');
    }
    // }}}
    // {{{ _check_app
    public function _check_app($appname)
    {
        $applications = Model::load('Applications', 'user');

        return !$applications->hasApp($appname);
    }
    // }}}
}
