<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Help_Backend extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'Help';

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Help"));
        $this->global_tabs->addItem(_("Nodes"), 'help/backend/node');
        $this->global_tabs->addItem(_("Edit Node"), 'help/backend/node/edit/%id%', 'help/backend/node');
        $this->global_tabs->addItem(_("Delete Node"), 'help/backend/node/delete/%id%', 'help/backend/node');
        $this->global_tabs->addItem(_("Settings"), 'help/backend/settings');
    }
    // }}}
}
