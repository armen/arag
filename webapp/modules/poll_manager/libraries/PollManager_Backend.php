<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class PollManager_Backend extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = _("Poll Manager");

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Poll Manager"));
        $this->global_tabs->addItem(_("Polls"), 'poll_manager/backend/list/');
        $this->global_tabs->addItem(_("List"), 'poll_manager/backend/list/', 'poll_manager/backend/list/');
        $this->global_tabs->addItem(_("Add"), 'poll_manager/backend/list/add', 'poll_manager/backend/list/');
        $this->global_tabs->addItem(_("Settings"), 'poll_manager/backend/settings');
    }
    // }}}
}
