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

        // Load the model and url helper
        $this->load->model('Blog');

        // Backend decorator
        $this->load->decorator('backend/decorator');

        // Default page title
        $this->decorator->page_title = 'Blog';

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("Blog"));
        $this->global_tabs->addItem(_("Entries"), 'blog/backend/entry');
        $this->global_tabs->addItem(_("Edit Entry"), 'blog/backend/entry/edit/%id%', 'blog/backend/entry');
        $this->global_tabs->addItem(_("Delete Entry"), 'blog/backend/entry/delete/%id%', 'blog/backend/entry');
        $this->global_tabs->addItem(_("Preview Entry"), 'blog/backend/entry/preview/%id%', 'blog/backend/entry');
        $this->global_tabs->addItem(_("New Entry"), 'blog/backend/entry/post');
        // $this->global_tabs->addItem(_("Categories"), 'blog/backend/category');
        // $this->global_tabs->addItem(_("Edit Category"), 'blog/backend/category/edit/%id%', 'blog/backend/category');
        // $this->global_tabs->addItem(_("Delete Category"), 'blog/backend/category/delete/%id%', 'blog/backend/category');
        // $this->global_tabs->addItem(_("New Category"), 'blog/backend/category/create');        
        $this->global_tabs->addItem(_("Settings"), 'blog/backend/settings');
        $this->global_tabs->addItem(_("Preview"), url::site('blog'));
    }
    // }}}
}

?>
