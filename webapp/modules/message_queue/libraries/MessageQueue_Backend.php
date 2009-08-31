<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MessageQueue_Backend extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'Message Queue';

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Message Queue"));
    }
    // }}}
}
