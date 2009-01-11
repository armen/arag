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
        $this->layout->page_title = 'OAuth';

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("OAuth"));
        $this->global_tabs->addItem(_("Register Consumers"), 'oauth/backend/consumers');
        $this->global_tabs->addItem(_("Consumers List"), 'oauth/backend/consumers', 'oauth/backend/consumers');
        $this->global_tabs->addItem(_("Register New Consumer"), 'oauth/backend/register', 'oauth/backend/consumers');
    }
    // }}}
    // {{{ consumers
    public function consumers()
    {
    }
    // }}}
    // {{{ register
    public function register()
    {
    }
    // }}}
    // {{{ request_token
    public function request_token()
    {
    }
    // }}}
    // {{{ authorize
    public function authorize()
    {
    }
    // }}}
    // {{{ access_token
    public function access_token()
    {
    }
    // }}}
}

?>
