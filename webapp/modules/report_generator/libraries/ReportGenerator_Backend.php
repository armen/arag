<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ReportGenerator_Backend extends Controller 
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'Report Generator';

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));
        
        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Report Generator"));
        $this->global_tabs->addItem(_("Reports List"), 'report_generator/backend/reports');
        $this->global_tabs->addItem(_("Generate Report"), 'report_generator/backend/generate_report');
    }
    // }}}
}

?>
