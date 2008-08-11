<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Roham Rafii Tehrani <roham.rafii@gmail.com>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // load global Tabs
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Contacts Management"));

        $this->global_tabs->addItem(_("Contacts"), 'contact_us/backend/contacts');
        $this->global_tabs->addItem(_("List"), 'contact_us/backend/contacts', 'contact_us/backend/contacts');

        $this->global_tabs->addItem(_("Setting"), 'contact_us/backend/setting/top_template');
        $this->global_tabs->addItem(_("Top Template"), 'contact_us/backend/setting/top_template', 'contact_us/backend/setting/top_template');
        $this->global_tabs->addItem(_("Bottom Template"), 'contact_us/backend/setting/bottom_template', 'contact_us/backend/setting/top_template');

        // Default page title
        $this->layout->page_title = _("Contacts Management");

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('_check_contact', _("This contact does not exists!"));

        // Get the appname
        $this->appname = $this->session->get('user.appname');
    }
    // }}}
}
