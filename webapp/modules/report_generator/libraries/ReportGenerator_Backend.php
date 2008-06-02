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
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'Report Generator';

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('id', _("%s should be valid id."));
        $this->validation->message('depends_on', _("Please fill all fields, fields are depend on each other."));
        
        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Report Generator"));
        $this->global_tabs->addItem(_("Reports List"), 'report_generator/backend/reports');
        $this->global_tabs->addItem(_("Generate Report"), 'report_generator/backend/generate_report');
        $this->global_tabs->addItem(_("Delete Report"), 'report_generator/backend/delete_report/%id%', 'report_generator/backend/reports');
        // $this->global_tabs->addItem(_("Edit Report"), 'report_generator/backend/edit_report/%id%', 'report_generator/backend/reports');        
        $this->global_tabs->addItem(_("Execute Report"), 'report_generator/backend/execute_report/%id%', 'report_generator/backend/reports');
    }
    // }}}
}
